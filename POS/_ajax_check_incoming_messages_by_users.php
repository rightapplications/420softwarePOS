<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','3'), '')){
    $aNumMessages = $oMessenger->get_total_unread_incoming_by_users($_SESSION[CLIENT_ID]['user_superclinic']['id']);
    $aResult = array('result'=>1, 'data'=>$aNumMessages);
    $output = json_encode($aResult);
    echo $output;
}