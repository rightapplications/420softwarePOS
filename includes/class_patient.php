<?php
class class_patient extends class_gallery{

    public $load_time;
    
    function __construct() {
        $this->load_time = time();
    }
    
    function get_patients($ordby='', $ord='ASC', $search_str=null){  
        $searchSQL = "";
        if(isset($search_str[0]) and !empty($search_str[0])){
            $searchSQL.= "AND ".PREF."patients.firstname LIKE '%".db::clear(addslashes($search_str[0]))."%' ";
        }
        if(isset($search_str[1]) and !empty($search_str[1])){
            $searchSQL.= "AND ".PREF."patients.lastname LIKE '%".db::clear(addslashes($search_str[1]))."%' ";
        }
        if(isset($search_str[2]) and !empty($search_str[2])){
            $searchSQL.= "AND ".PREF."patients.license LIKE '%".db::clear(addslashes($search_str[2]))."%' ";
        }
        if(isset($search_str[3]) and !empty($search_str[3])){
            $searchSQL.= "AND ".PREF."patients.id = '".intval($search_str[3])."' ";
        }
        if(isset($search_str[4]) and !empty($search_str[4])){
            $searchSQL.= "AND ".PREF."patients.recNumber LIKE '%".db::clear(addslashes($search_str[4]))."%' ";
        }
        if(isset($search_str[5]) and !empty($search_str[5])){
            $searchSQL.= "AND ".PREF."patients.email LIKE '%".db::clear(addslashes($search_str[5]))."%' ";
        }
        if(isset($search_str[6]) and !empty($search_str[6])){
            $searchSQL.= "AND ".PREF."patients.phone LIKE '%".db::clear(addslashes($search_str[6]))."%' ";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "lastname"; 
            $ord = 'ASC';
        }
		$order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
		if($search_str){
			$aUsers = db::get("SELECT * FROM ".PREF."patients WHERE 1 $searchSQL $order_sql");
		}else{			
			$aUsers = db::get_pager("SELECT * FROM ".PREF."patients WHERE 1 $order_sql");
		}
        
        return $aUsers;
    }
    
    function get_patients_by_date($date){
        if($date){
            $dateSQL = "AND changed >= '".intval($date)."'";
        }else{
            $dateSQL = "";
        }
        $aUsers = db::get("SELECT id, firstname, lastname, midname, email, phone, license, birthdate, street, city, state, zip, expDate, status, note, recNumber, recExpDate, regdate,
                                  IF(image_1 != '' OR image_1 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_1), '') AS image_1, 
                                  IF(image_2 != '' OR image_2 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_2), '') AS image_2, 
                                  IF(image_3 != '' OR image_3 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_3), '') AS image_3, 
                                  IF(image_4 != '' OR image_4 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_4), '') AS image_4, 
                                  IF(image_5 != '' OR image_5 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_5), '') AS image_5, 
                                  IF(image_6 != '' OR image_6 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_6), '') AS image_6, 
                                  IF(image_7 != '' OR image_7 IS NOT NULL, CONCAT('".HOST.GALLERY_FOLDER."/', image_7), '') AS image_7,
                                  changed
                          FROM ".PREF."patients WHERE 1 $dateSQL");
        return $aUsers;
    }
    
    function get_birthdays($from, $to, $ordby='', $ord='ASC'){
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = "lastname"; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aPatients = array();
        $numDays = floor(($to - $from)/86400)+1;
        if($numDays <= 92){
            $aDays = array();
            for($i=0; $i<$numDays; $i++){
                $aDay = array();
                $iDay = strtotime("+".$i." day", $_SESSION[CLIENT_ID]['from']);
                $aDays[] = $iDay;
            }
            foreach($aDays as $k=>$d){
                $ct = getdate($d);     
                $aDaysPatients = db::get("SELECT * FROM ".PREF."patients WHERE 1 AND MONTH(FROM_UNIXTIME(birthdate)) = '".$ct['mon']."' AND DAY(FROM_UNIXTIME(birthdate)) = '".$ct['mday']."' $order_sql");
                if(!empty($aDaysPatients)){
                    if(!empty($aPatients)){
                        $aPatients = array_merge($aPatients,$aDaysPatients);
                    }else{
                        $aPatients = $aDaysPatients;
                    }
                }
            }            
        }
        return $aPatients;
    }
    
    function search_patients($search_str){
        $aSearch = explode(' ', $search_str);
        $searchSQL = "AND (";
        if(!empty($aSearch)){
            foreach($aSearch as $s){
                $searchSQL.= "".PREF."patients.firstname LIKE '%".db::clear(addslashes($s))."%' OR ".PREF."patients.lastname LIKE '%".db::clear(addslashes($s))."%' OR ".PREF."patients.license LIKE '%".db::clear(addslashes($s))."%' OR ";
            }
            $searchSQL.= "0 )";
        }
        $aUsers = db::get("SELECT * FROM ".PREF."patients WHERE 1 $searchSQL");
        return $aUsers;
    }

    function get_patient($id){
        $aUser = db::get_row("SELECT * FROM ".PREF."patients WHERE id = '".intval($id)."'");
        return $aUser;
    }
    
    function add_patient($aData, $aFiles){
        if(empty($aData['firstname']) or empty($aData['lastname'])){
           return 'Empty required fields!';
        }
        if(!empty($aData['license'])){
            $license_exists = db::get_one("SELECT COUNT(*) FROM ".PREF."patients WHERE license = ~~", array($aData['license']));
            if($license_exists){
                 return 'ID already registered';
            }
        }
        $birth_date = strtotime($aData['birthdate']);
        if(!$birth_date){
            return 'Birth Date is Wrong';
        }
        if(!empty($aData['expDate'])){
            $exp_date = strtotime($aData['expDate']);
            if(!$exp_date){
                return 'Exp. Date is Wrong';
            }
        }else{
            $exp_date = 0;
        }
        if(!empty($aData['recExpDate'])){
            $rec_exp_date = strtotime($aData['recExpDate']);
            if(!$rec_exp_date){
                return 'Exp. Date is Wrong';
            }
        }else{
            $rec_exp_date = 0;
        }
        
        $img_sql = "";
        $cd = getdate();
        $subfolder = $cd['year'].'/'.$cd['mon'];dump(ABS.GALLERY_FOLDER.'/'.$subfolder);
        if(!is_dir(ABS.GALLERY_FOLDER.'/'.$subfolder)){ 
            mkdir(ABS.GALLERY_FOLDER.'/'.$subfolder, 0755, true);
        }
        for($i=1; $i<=7; $i++){
            if(!empty($aFiles['image_'.$i]['name'])){
                $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['image_'.$i]['type']);
                if($is_image){
                    $img_name = $this->load_time.'_'.$i.'_'.translit($aFiles['image_'.$i]['name']);
                    $th_name = "th_".$img_name;
                    $full_name = ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$img_name;
                    if(move_uploaded_file($aFiles['image_'.$i]['tmp_name'],$full_name)){                        
                        $img_sql.= "image_".$i."='".$subfolder."/".$img_name."', ";
                        $this->resize_one(ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$img_name, 90, 'w', ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$th_name);
                    }
                }
            }  
        }
        
        $ok = db::query("INSERT INTO ".PREF."patients SET
                  firstname = ~~,
                  lastname = ~~,
                  midname = ~~,
                  phone = ~~,
                  email = ~~,
                  street = ~~,
                  city = ~~,
                  state = ~~,
                  zip = ~~,
                  license = ~~,
                  $img_sql
                  birthdate = '".$birth_date."',
                  expDate = '".$exp_date."',
                  note = ~~,
                  recNumber = ~~,
                  recExpDate = '".$rec_exp_date."',
                  vip_discount = '".(!empty($aData['vip_discount']) ? 1 : 0)."',
                  regdate = '".$this->load_time."',
                  subscribed = '".(!empty($aData['subscribed']) ? 1 : 0)."',
                  source = '".(!empty($aData['source']) ? intval($aData['source']) : 0)."',
                  changed = '".$this->load_time."'",
            array($aData['firstname'],$aData['lastname'],$aData['midname'],$aData['phone'],$aData['email'],$aData['street'],$aData['city'],@$aData['state'],$aData['zip'],$aData['license'],$aData['note'],$aData['recNumber']));
        if($ok){
            return 'ok';
        }else{
            return 'Database inserting error';
        }
    }
    
    function update_patient($id, $aData, $aFiles){
        if(empty($aData['firstname']) or empty($aData['lastname'])){
           return 'Empty required fields!';
        }        
        if(!empty($aData['license'])){
            $license_exists = db::get_one("SELECT COUNT(*) FROM ".PREF."patients WHERE license = ~~ AND id != '".intval($id)."'", array($aData['license']));
            if($license_exists){
                 return 'ID already registered';
            }
        }
        $birth_date = strtotime($aData['birthdate']);
        if(!$birth_date){
            return 'Birth Date is Wrong';
        }
        if(!empty($aData['expDate'])){
            $exp_date = strtotime($aData['expDate']);
            if(!$exp_date){
                return 'Exp. Date is Wrong';
            }
        }else{
            $exp_date = 0;
        }
        if(!empty($aData['recExpDate'])){
            $rec_exp_date = strtotime($aData['recExpDate']);
            if(!$rec_exp_date){
                return 'Exp. Date is Wrong';
            }
        }else{
            $rec_exp_date = 0;
        }
        
        $img_sql = "";
        $cd = getdate();
        $subfolder = $cd['year'].'/'.$cd['mon'];dump(ABS.GALLERY_FOLDER.'/'.$subfolder);
        if(!is_dir(ABS.GALLERY_FOLDER.'/'.$subfolder)){ 
            mkdir(ABS.GALLERY_FOLDER.'/'.$subfolder, 0755, true);
        }
        for($i=1; $i<=7; $i++){
            if(!empty($aFiles['image_'.$i]['name'])){
                $is_image=preg_match("/^(image\/)[a-zA-Z]{3,4}/",$aFiles['image_'.$i]['type']);
                if($is_image){
                    $img_name = $this->load_time.'_'.$i.'_'.translit($aFiles['image_'.$i]['name']);
                    $th_name = "/th_".$img_name;
                    $full_name = ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$img_name;
                    if(move_uploaded_file($aFiles['image_'.$i]['tmp_name'],$full_name)){
                        $imgToDel = db::get_one("SELECT image_".$i." FROM ".PREF."patients WHERE id = '".intval($id)."'");
                        if(!empty($imgToDel)){
                            @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
                            $sFullThumbName = $this->get_preview($imgToDel);
                            @unlink(ABS.GALLERY_FOLDER.'/'.$sFullThumbName);
                        }
                        $img_sql.= "image_".$i."='".$subfolder."/".$img_name."', ";
                        $this->resize_one(ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$img_name, 90, 'w', ABS.GALLERY_FOLDER.'/'.$subfolder.'/'.$th_name);
                    }
                }
            }  
        }
        $ok = db::query("UPDATE ".PREF."patients SET
                  firstname = ~~,
                  lastname = ~~,
                  midname = ~~,
                  phone = ~~,
                  email = ~~,
                  street = ~~,
                  city = ~~,
                  state = ~~,
                  zip = ~~,
                  license = ~~,
                  $img_sql
                  birthdate = '".$birth_date."',
                  expDate = '".$exp_date."',
                  note = ~~,
                  recNumber = ~~,
                  recExpDate = '".$rec_exp_date."',
                  vip_discount = '".(!empty($aData['vip_discount']) ? 1 : 0)."',
                  subscribed = '".(!empty($aData['subscribed']) ? 1 : 0)."',
                  source = '".(!empty($aData['source']) ? intval($aData['source']) : 0)."',
                  changed = '".$this->load_time."'
                  WHERE id = '".intval($id)."'",
            array($aData['firstname'],$aData['lastname'],$aData['midname'],$aData['phone'],$aData['email'],$aData['street'],$aData['city'],$aData['state'],$aData['zip'],$aData['license'],$aData['note'],$aData['recNumber']));
        if($ok){
            return 'ok';
        }else{
            return 'Database updating error';
        }
    }
    
    function make_employe_patient($id, $apply_purchase_price, $discount){
        $aEmployee = db::get_row("SELECT * FROM ".PREF."users WHERE id = '".intval($id)."' AND id > 1");
        if(!empty($aEmployee)){
            $is_patient = db::get_one("SELECT id FROM ".PREF."patients WHERE email = ~~", array($aEmployee['email']));
            if(!$is_patient){
                db::query("INSERT INTO ".PREF."patients SET
                      firstname = ~~,
                      lastname = ~~,
                      midname = ~~,
                      phone = ~~,
                      email = ~~,
                      street = ~~,
                      city = ~~,
                      state = ~~,
                      zip = ~~,
                      license = ~~,
                      birthdate = '0',
                      expDate = '0',
                      note = ~~,
                      recNumber = ~~,
                      recExpDate = '0',
                      vip_discount = '0',
                      regdate = '".$this->load_time."',
                      subscribed = '0',
                      is_employee = '1',
                      apply_purchase_price = '".intval($apply_purchase_price)."',
                      employee_discount = '".floatval($discount)."',
                      changed = '".$this->load_time."'",
                array($aEmployee['firstname'],$aEmployee['lastname'],'',$aEmployee['phone'],$aEmployee['email'],$aEmployee['address'],'','','','','',''), true);
                $last_id = mysql_insert_id();
                if($last_id){
                    return 'ok';
                }else{
                    return 'Employee has not been added to patients!';
                }
            }else{
                db::query("UPDATE ".PREF."patients SET
                                    is_employee = '1',
                                    apply_purchase_price = '".intval($apply_purchase_price)."',
                                    employee_discount = '".floatval($discount)."'
                                WHERE id = '".intval($is_patient)."'");
                $affected = mysql_affected_rows();
                if($affected){
                    return 'ok';
                }else{
                    return 'Employee has not been updated!';
                }
            }
        }else{
            return 'Employee not found';
        }        
    }
    
    function delete_patient_img($id, $img_id){
        $imgToDel = db::get_one("SELECT image_".intval($img_id)." FROM ".PREF."patients WHERE id = '".intval($id)."'");
        if(!empty($imgToDel)){
            @unlink(ABS.GALLERY_FOLDER.'/'.$imgToDel);
            $sFullThumbName = $this->get_preview($imgToDel);
            @unlink(ABS.GALLERY_FOLDER.'/'.$sFullThumbName);
        }
        db::query("UPDATE ".PREF."patients SET image_".intval($img_id)." = '', changed = '".$this->load_time."' WHERE id = '".intval($id)."'");
        return "ok";
    }
    
    function delete_patient($id){
        $aImages = db::get_row("SELECT image_1, image_2, image_3, image_4, image_5, image_6, image_7 FROM ".PREF."patients WHERE id = '".intval($id)."'");
        for($i=1; $i<=7; $i++){
            if(!empty($aImages['image_'.$i])){
                @unlink(ABS.GALLERY_FOLDER.'/'.$aImages['image_'.$i]);
                $sFullThumbName = $this->get_preview($aImages['image_'.$i]);
                @unlink(ABS.GALLERY_FOLDER.'/'.$sFullThumbName);
            }
        }
        $ok = db::query("DELETE FROM ".PREF."patients WHERE id = '".intval($id)."'");
        if($ok){
            return 'ok';
        }else{
            return 'Database deleting error';
        }
    }
    
    function get_inactive_patients($start=0, $end=0){
        if(!$start and !$end){
            $intervalSQL = '';
        }else{
            $intervalSQL = "AND lastOrder.lastOrderDate <= '".$start."' AND lastOrder.lastOrderDate >= '".$end."'";
        }
        //latest orders
        $aPatients = db::get("SELECT ".PREF."patients.id, ".PREF."patients.email, lastOrder.lastOrderDate  FROM ".PREF."patients 
                              LEFT JOIN (SELECT MAX(date) AS lastOrderDate, client_id FROM ".PREF."orders GROUP BY client_id) AS lastOrder  ON lastOrder.client_id = ".PREF."patients.id
                              WHERE ".PREF."patients.email != '' $intervalSQL");
        //for test only
        /*$aPatients = array(
                            0=>array('id'=>'1', 'email'=>'creastate2009@gmail.com','lastOrderDate'=>'1234567890'),
                            1=>array('id'=>'2', 'email'=>'creastate2011@gmail.com','lastOrderDate'=>'1234567890'),
                          );*/
        return $aPatients;
    }
    
    function get_active_patients($start){
        $intervalSQL = "AND lastOrder.lastOrderDate >= '".$start."'";
        $aPatients = db::get("SELECT ".PREF."patients.id, ".PREF."patients.email, lastOrder.lastOrderDate  FROM ".PREF."patients 
                              LEFT JOIN (SELECT MAX(date) AS lastOrderDate, client_id FROM ".PREF."orders GROUP BY client_id) AS lastOrder  ON lastOrder.client_id = ".PREF."patients.id
                              WHERE ".PREF."patients.email != '' $intervalSQL");
        return $aPatients;
    }
    
    function get_inactive_patients_phones($start=0, $end=0){
        if(!$start and !$end){
            $intervalSQL = '';
        }else{
            $intervalSQL = "AND lastOrder.lastOrderDate <= '".$start."' AND lastOrder.lastOrderDate >= '".$end."'";
        }
        //latest orders
        $aPatients = db::get("SELECT ".PREF."patients.phone, lastOrder.lastOrderDate  FROM ".PREF."patients 
                              LEFT JOIN (SELECT MAX(date) AS lastOrderDate, client_id FROM ".PREF."orders GROUP BY client_id) AS lastOrder  ON lastOrder.client_id = ".PREF."patients.id
                              WHERE ".PREF."patients.phone != '' $intervalSQL");
        $aPhones = array();
        foreach($aPatients as $patient){
            $aToReplace = array(' ','-','/','(',')','+','.');
            $phone = substr(str_replace($aToReplace, '',$patient['phone']), -10);
            if(strlen($phone) >= 10){
                $aPhones[] = $phone;
            }            
        }
		//$aPhones = array(0=>substr(str_replace($aToReplace, '','323 309 9090'), -10), 1=>substr(str_replace($aToReplace, '','323 309 9090'), -10));
        return $aPhones;
    }
    
    function get_active_patients_phones($start){
        $intervalSQL = "AND lastOrder.lastOrderDate >= '".$start."'";
        $aPatients = db::get("SELECT ".PREF."patients.phone, lastOrder.lastOrderDate  FROM ".PREF."patients 
                              LEFT JOIN (SELECT MAX(date) AS lastOrderDate, client_id FROM ".PREF."orders GROUP BY client_id) AS lastOrder  ON lastOrder.client_id = ".PREF."patients.id
                              WHERE ".PREF."patients.phone != '' $intervalSQL");
        $aPhones = array();
        foreach($aPatients as $patient){
            $aToReplace = array(' ','-','/','(',')','+','.');
            $phone = substr(str_replace($aToReplace, '',$patient['phone']), -10);
            if(strlen($phone) >= 10){
                $aPhones[] = $phone;
            }            
        }
        return $aPhones;
    }
    
    function get_patients_by_birthday($contact='email', $day=null){
        if($contact == 'email'){
            $field = 'email';
        }else{
            $field = 'phone';
        }
        if(is_null($day)){
            $ct = getdate($this->load_time);
        }else{
            $ct = getdate($day);
        }        
        $aPatients = db::get("SELECT id, $field FROM ".PREF."patients WHERE `$field` != '' AND MONTH(FROM_UNIXTIME(birthdate)) = '".$ct['mon']."' AND DAY(FROM_UNIXTIME(birthdate)) = '".$ct['mday']."'");
        if($contact == 'email'){            
            return $aPatients;
        }else{
            $aPhones = array();        
            foreach($aPatients as $patient){
                $aToReplace = array(' ','-','/','(',')','+','.');
                $phone = substr(str_replace($aToReplace, '',$patient['phone']), -10);
                if(strlen($phone) >= 10){
                    $aPhones[] = $phone;
                }            
            }
            return $aPhones;
        }       
    }
    
    function get_added_qty($from, $to){
        $count= db::get_one("SELECT COUNT(*) FROM ".PREF."patients WHERE regdate >= ~~ AND  regdate <= ~~", array($from, $to));
        return $count;
    }
    
    function get_sources($from, $to, $ordby='', $ord='ASC'){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'quantity'; 
            $ord = 'DESC';
        }
        $aSources = db::get("SELECT COUNT(*) AS quantity, source FROM ".PREF."patients WHERE source != 0 AND regdate >= ~~ AND  regdate <= ~~ GROUP BY source ORDER BY $ordby $ord", array($from, $to));
        if(!empty($aSources)){
            $total = 0;
            foreach($aSources as $k=>$v){
                $total+=$v['quantity'];
            }
            foreach($aSources as $k=>$v){
                $aSources[$k]['percent'] = round($v['quantity']*100/$total, 2);
            }
        }
        return $aSources;
    }
    
    function add_to_queue($id){
        $is_in_queue = db::get_one("SELECT id FROM ".PREF."queue WHERE patient_id = '".intval($id)."'");
        if(!$is_in_queue){
            db::query("INSERT INTO ".PREF."queue SET patient_id = '".intval($id)."', date = '".$this->load_time."'");
            return true;
        }else{
            return false;
        }        
    }
    
    function get_queue(){
        $aQueue = db::get("SELECT ".PREF."queue.*, ".PREF."patients.firstname, ".PREF."patients.lastname, ".PREF."patients.rewards, ".PREF."patients.image_1
                           FROM ".PREF."queue                           
                           LEFT JOIN ".PREF."patients ON ".PREF."patients.id = ".PREF."queue.patient_id
                           ORDER BY ".PREF."queue.date ASC");
        return $aQueue;
    }
    
    function clear_queue(){
         db::query("DELETE FROM ".PREF."queue WHERE date < '".($this->load_time - 3600)."'");
    }
    
    function delete_from_queue($id){
        db::query("DELETE FROM ".PREF."queue WHERE patient_id = '".intval($id)."'");
        if(isset($_SESSION[CLIENT_ID]['next_patient']) and $_SESSION[CLIENT_ID]['next_patient'] == intval($id)){
            unset($_SESSION[CLIENT_ID]['next_patient']);
        }
        return true;
    }

}