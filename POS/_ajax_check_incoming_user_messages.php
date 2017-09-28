<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','3'), '')){
    $sender = intval($_GET['sender']);
    $numMessages = $oMessenger->get_total_unread_user_incoming($_SESSION[CLIENT_ID]['user_superclinic']['id'], $sender);
    $aResult = array('result'=>1, 'data'=>$numMessages);
    $output = json_encode($aResult);
    echo $output;
}

