<?php
class class_email {
    
    private $mailer;
            
    function __construct(){
        $this->mailer = new PHPMailer();
        $this->mailer->CharSet = MAIL_CHARSET;
        $this->mailer->From = MAIL_FROM;
        $this->mailer->FromName = SITE_NAME;
        $this->mailer->isHTML(true);
        if(MAIL_SMTP){
            $this->mailer->isSMTP();
			$this->mailer->Host = SMTP_HOST;
			if(SMTP_AUTH){
				$this->mailer->Username = SMTP_USER;
				$this->mailer->Password = SMTP_PASS;
			}
        }
    }   
    
    function email($to, $subject, $message, $attachment=null,$bcc=false){
        //if(LANG == 'ru'){
            //$this->mailer->Subject  = stripslashes(iconv('utf-8','windows-1251',$subject));
            //$this->mailer->Body = stripslashes(iconv('utf-8','windows-1251',$message));
        //}else{
            $this->mailer->Subject  = stripslashes($subject);
            $this->mailer->Body = stripslashes($message);
        //}
        if(@is_array($attachment)){
            foreach($attachment as $v){
                $this->mailer->AddAttachment($v);
            }
        }
        elseif(@$attachment){
            $this->mailer->AddAttachment($attachment);
        }
        if(is_array($to)){
            if($bcc){
                foreach($to as $v){
                    $this->mailer->AddAddress($v);
                    $this->mailer->Send();
                    $this->mailer->ClearAddresses();
                }
                return true;
            }else{
                foreach($to as $v){
                    $this->mailer->AddAddress($v);
                }
            }
        }
        else{
            $this->mailer->AddAddress($to);
        }        

        $ok = $this->mailer->Send();
        $this->mailer->ClearAddresses();
        if($attachment){
            $this->mailer->ClearAttachments();
        }
        usleep(100000);
        if($ok){
            return true;
        }
        else{
            //dump($mailer->ErrorInfo);
            return false;
        }
    }
    
    function socket_email($to_email, $subject, $message, $attachment_file=null){
        $request = "&to=".$to_email."&subject=".urlencode($subject)."&message=".urlencode($message);
        if($attachment_file){
            $request.= "&attachment=".$attachment_file;
        }
        $header = "POST /Herbal/POS/_send_email.php HTTP/1.0\r\nHost: get-dev.com\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($request) . "\r\n\r\n";
        $fsock = fsockopen('get-dev.com', 80);        
        fputs($fsock, $header . $request);
        stream_set_blocking($fsock, 0);
        fclose ($fsock);
    }
    
    function sms($aAllRecepients, $message, $subject=''){
	    $numPhoneNumbers = 0;
		$aRecepientsGroups =  array_chunk($aAllRecepients, 500);
	    foreach($aRecepientsGroups as $aRecepients){
        $data = array(
          'User'          => SMS_PORTAL_LOGIN,
          'Password'      => SMS_PORTAL_PASS,
          'PhoneNumbers'  => $aRecepients,
          'Subject'       => '',
          'Message'       => ($subject ? ($subject.' ') : '').$message,
          'MessageTypeID' => 1
        );
        $curl = curl_init(SMS_PORTAL_HOST.'sending/messages?format=json');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($curl);
        curl_close($curl); 
        
        $json = json_decode($response);
        $json = $json->Response;
        
        if ( 'Failure' == $json->Status ) {
            $errors = array();
            if ( !empty($json->Errors) ) {
                $errors = $json->Errors;
            } 
            //return 'Errors: ' . implode(', ' , $errors) . "\n";
            //return 0;
        }else{
            if ( !empty($json->Entry->PhoneNumbers) ) {
                $numPhoneNumbers+= count($json->Entry->PhoneNumbers);
            }                       
        }
		}
		return $numPhoneNumbers; 
    }
    
    function get_inbox_sms($page=1, $per_page=10){
        $data = array(
            'User'          => SMS_PORTAL_LOGIN,
            'Password'      => SMS_PORTAL_PASS,
            'sortBy'        => 'ReceivedOn',
            'sortDir'       => 'desc',
            'itemsPerPage'  => intval($per_page),
            'page'          => intval($page)
            );
        $curl = curl_init(SMS_PORTAL_HOST.'incoming-messages?format=json&'.http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($curl);
        curl_close($curl);
        if(!empty($response)){
            $aResult = json_decode($response, true);
            if('Failure' != $aResult['Response']['Status']){
                return $aResult['Response']['Entries'];
            }else{
                return false;
            }
        }else{
            return false;
        }        
    }


    function  __destruct() {
        
    }
}
?>