<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'email';
$sectionName = 'Email';

if(isset($_POST['sent'])){
    
    $birthday_filter = false;
    $active_start = false;
    
    switch(@$_POST['group']){
        case 30:
            $start = $oPatient->load_time - 60*86400-1;
            $end = $oPatient->load_time - 30*86400;
        break;
        case 60:
            $start = $oPatient->load_time - 90*86400-1;
            $end = $oPatient->load_time - 60*86400;
        break;
        case 90:
            $start = 0;
            $end = $oPatient->load_time - 90*86400;
        break;
        case 'b30':
            $active_start = $oPatient->load_time - 30*86400;
        break;
        case 'b60':
            $active_start = $oPatient->load_time - 60*86400;
        break;
        case 'b90':
            $active_start = $oPatient->load_time - 90*86400;
        break;
        case 'bd':
            $birthday_filter = time();
        break;
        case 'bd2':
            $birthday_filter = strtotime('+1 day');
        break;
        case 'bd3':
            $birthday_filter = strtotime('+2 day');
        break;
        case 'bd7':
            $birthday_filter = strtotime('+1 week');
        break;
        default:
            $start = 0;
            $end = 0;
        break;
        
    }
    
    if($_POST['type'] == '1'){
        if($birthday_filter){
            $aPatients = $oPatient->get_patients_by_birthday('email', $birthday_filter);
        }elseif($active_start){
            $aPatients = $oPatient->get_active_patients($active_start);
        }else{
            $aPatients = $oPatient->get_inactive_patients($start, $end);
        }
        $aEmails = array();
        foreach($aPatients as $patient){
            $aEmails[] = $patient['email'];
        }
        $sEmails = implode(',',$aEmails);
        
        $patientsCount = count($aPatients);
        if($patientsCount > 0 and !empty($_POST['message'])){
            $subject = htmlspecialchars($_POST['subject']);
            $message = $_POST['message'];  
            $cFile = '';
            if(!empty($_FILES['attachment']['name'])){
                $is_image = preg_match("/^(image\/)[a-zA-Z]{3,4}/",$_FILES['attachment']['type']);
                $is_pdf = strpos($_FILES['attachment']['name'], ".pdf");
                $is_doc = (strpos($_FILES['attachment']['name'], ".doc") or strpos($_FILES['attachment']['name'], ".docx") or strpos($_FILES['attachment']['name'], ".xls") or strpos($_FILES['attachment']['name'], ".xlsx") or strpos($_FILES['attachment']['name'], ".ppt") or strpos($_FILES['attachment']['name'], ".pptx"));
                if($is_image or $is_pdf or $is_doc){  
                    $file_name = translit($_FILES['attachment']['name']);
                    $full_name = ABS.ATTACHMENT_FOLDER.'/'.$file_name;
                    if(move_uploaded_file($_FILES['attachment']['tmp_name'],$full_name)){
                        $attachedFile = $full_name;
                        $cFile = '@' . realpath($attachedFile);
                    }
                }                
            }
            
            $data = array(
                'sent'          => 1,
                'client_id'          => CLIENT_ID,
                'client_name'      => SITE_NAME,
                'type'  => 'email',
                'subject'       => $subject,
                'message'       => $message,
                'recepients' => $sEmails,
                'num_recepients' => $patientsCount
            );
            if(!empty($cFile)){
                $data['attachment'] = $cFile.';filename='.$_FILES['attachment']['name'].';type='. $_FILES['attachment']['type'];
            }
            $curl = curl_init(MESSAGING_PORTAL_URL);
            curl_setopt($curl, CURLOPT_POST, 1);
            @curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
            $response = curl_exec($curl); 
            //$commError = curl_error($curl);
	    //$commInfo = @curl_getinfo($curl);
            //dump($response);
            //dump($commError);
            curl_close($curl);
            if(!empty($attachedFile)){
                unlink($attachedFile);
            }
            if($response == 'accepted'){
                header("Location: patients_emails.php?queued=1");
            }else{
                $error = "Your emails have not been sent.";
            }

            /*
            $attachedFile = null;
            if(!empty($_FILES['attachment']['name'])){
                $file_sql = "";
                $is_image = preg_match("/^(image\/)[a-zA-Z]{3,4}/",$_FILES['attachment']['type']);
                $is_pdf = strpos($_FILES['attachment']['name'], ".pdf");
                $is_doc = (strpos($_FILES['attachment']['name'], ".doc") or strpos($_FILES['attachment']['name'], ".docx") or strpos($_FILES['attachment']['name'], ".xls") or strpos($_FILES['attachment']['name'], ".xlsx"));
                if($is_image or $is_pdf or $is_doc){  
                    $file_name = translit($_FILES['attachment']['name']);
                    $full_name = ABS.ATTACHMENT_FOLDER.'/'.$file_name;
                    if(move_uploaded_file($_FILES['attachment']['tmp_name'],$full_name)){
                        $attachedFile = $full_name;
                    }
                }
            }

            foreach($aPatients as $p){
                $oEmail->email($p['email'], $subject, $message, $attachedFile);
            } 
            */
            //mailgun
            /*$aRecepientsGroups =  array_chunk($aPatients, 200);            
            $numPatients = 0;
            foreach($aRecepientsGroups as $aRecepients){
                $mail = new PHPMailer;
                $mail->isSMTP();
                $aRecepientVariables = array();
                foreach($aRecepients as $p){
                    $aRecepientVariables[$p['email']]['id'] = $p['id'];
                }
                $jsonRecepientVariables = json_encode($aRecepientVariables); //dump($jsonRecepientVariables);                
                $mail->Host = MAILER_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = MAILER_USERNAME;
                $mail->Password = MAILER_PASSWORD;
                $mail->SMTPSecure = 'tls';
                $mail->addCustomHeader('X-Mailgun-Recipient-Variables: '.$jsonRecepientVariables);
                $mail->From = MAILER_FROM;
                $mail->FromName = MAILER_FROMNAME;
                $mail->WordWrap = 50;
                $mail->Subject = $subject;
                $mail->Body    = $message; 
                $mail->SingleTo = true;
                foreach($aRecepients as $p){
                    $mail->addAddress($p['email']);  
                }
                if($mail->send()) {
                    $numPatients+=count($aRecepients);                    
                }*/
        }
            /*if($numPatients>0){
                header("Location: patients_emails.php?sent=".$numPatients);
            }else{
                $error = "Your emails have not been sent.";
            }*/
    }else{
        if($birthday_filter){
            $aPhones = $oPatient->get_patients_by_birthday('phone', $birthday_filter);
        }elseif($active_start){
            $aPhones = $oPatient->get_active_patients_phones($active_start);
        }else{
            $aPhones = $oPatient->get_inactive_patients_phones($start, $end); 
        }
		
        foreach($aPhones as $k=>$phone){
            if(!preg_match('/[0-9]{10}/',$phone)){
                unset($aPhones[$k]);
            }
        }
		
        $sPhones = implode(',',$aPhones);
        
        $patientsCount = count($aPhones);
        if($patientsCount > 0 and !empty($_POST['message'])){  
            
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            
            $totalMess = !empty($subject) ? ($subject.' '.$message) : $message;
            $numChars = strlen($totalMess);
            if($numChars < 151){
                $data = array(
                    'sent'          => 1,
                    'client_id'          => CLIENT_ID,
                    'client_name'      => SITE_NAME,
                    'type'  => 'sms',
                    'subject'       => $subject,
                    'message'       => $message,
                    'recepients' => $sPhones,
                    'num_recepients' => $patientsCount
                );
                $curl = curl_init(MESSAGING_PORTAL_URL);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
                $response = curl_exec($curl);
                curl_close($curl);
                if($response == 'accepted'){
                    header("Location: patients_emails.php?queued=1");
                }else{
                    $error = "Your sms have not been sent.";
                }
            }else{
                $error = "Your sms length exceed 150 characters.";
            }
            /*if(!empty($message)){
                $numSent = $oEmail->sms($aPhones, $message, $subject);
                header("Location: patients_emails.php?sent=".$numSent);
            }*/
        }
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$aInbox = $oEmail->get_inbox_sms($page, 10);
if($page == 1){
    $prev=0;
}else{
    $prev = $page-1;
}
$next = $page+1;

include '../templates/POS/patients_emails_tpl.php';