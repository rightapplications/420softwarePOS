<?php
include_once '../includes/common.php';
checkAccess(array('1','2','3','4'), 'login.php');

$activeMenu = 'support';
$sectionName = 'Support Request';

include_once '../includes/admin_config.php';
db::connect($aConfAdmin);

//set messages read
db::query("UPDATE ".PREF."support_requests SET `status`= 1 WHERE `is_admin`=1 AND `client_id` = ~~", [CLIENT_ID]);

if(isset($_POST['sent'])){
    $result = db::query("INSERT INTO ".PREF."support_requests SET
                                    client_id = ~~,                                    
                                    message = ~~,
                                    date_received = '".time()."',
                                    status = '0'
                              ", array(CLIENT_ID, $_POST['message']));    
    header("Location: support.php?sent=1");die;
}

$messages = db::get("SELECT * FROM ".PREF."support_requests WHERE `client_id` = ~~ ORDER BY `date_received` DESC", [CLIENT_ID]);

include '../templates/POS/support_tpl.php';