<?php
include_once '../includes/common.php';
checkAccess(array('1','2','3','4'), 'login.php');

$activeMenu = 'messenger';
$sectionName = 'Tasks';

$calledUser = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($_SESSION[CLIENT_ID]['user_superclinic']['id'] === '1' and !$calledUser){
    $aUsers = $oUser->get_users(false);
}else{
    if($_SESSION[CLIENT_ID]['user_superclinic']['id'] === '1'){
        if(isset($_GET['delmessage'])){
            $oMessenger->delete_message($_GET['delmessage']);
            header("Location: messenger.php?id=".$calledUser);die;
        }
    }
    $aCalledUser = $oUser->get_user($calledUser);
    $recepient = $_SESSION[CLIENT_ID]['user_superclinic']['id'] === '1' ? $calledUser : 1;
    if(!empty($_POST['message'])){        
        $result = $oMessenger->send_message($_SESSION[CLIENT_ID]['user_superclinic']['id'], $recepient, $_POST['message']);
        if($result){
            header("Location: ".$_SERVER['REQUEST_URI']);die;
        }
    }    
    $aMessages = $oMessenger->get_conversation($_SESSION[CLIENT_ID]['user_superclinic']['id'], $recepient);
    $oMessenger->set_read_status($_SESSION[CLIENT_ID]['user_superclinic']['id'], $recepient);
}

include '../templates/POS/messenger_tpl.php';