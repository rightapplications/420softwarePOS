<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','3'), '')){
    $messages = $oMessenger->get_total_unread_incoming($_SESSION[CLIENT_ID]['user_superclinic']['id']);

    include_once '../includes/admin_config.php';
	db::connect($aConfAdmin);
    $support = db::get_one("SELECT COUNT(*) FROM ".PREF."support_requests WHERE `client_id` = ~~ AND is_admin = 1 AND status = '0'", [CLIENT_ID]);

    $aResult = array('result'=>1, 'messages'=>$messages, 'support' => $support);
    $output = json_encode($aResult);
    echo $output;
}

