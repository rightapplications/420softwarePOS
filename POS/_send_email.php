<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$to = isset($_POST['to']) ? $_POST['to'] : (isset($_GET['to']) ? $_GET['to'] : '');
$subject = isset($_POST['subject']) ? $_POST['subject'] : (isset($_GET['subject']) ? $_GET['subject'] : '');
$message = isset($_POST['message']) ? $_POST['message'] : (isset($_GET['message']) ? $_GET['message'] : '');
$attachment = isset($_POST['attachment']) ? $_POST['attachment'] : (isset($_GET['attachment']) ? $_GET['attachment'] : '');

if($to){
    if(file_exists(ABS.$attachment)){
        $att = ABS.$attachment;
    }else{
        $att = null;
    }
    $send = $oEmail->email($to, $subject, $message, $att);
    if($send){
        echo "ok";
    }else{
        echo "Mail server error";
    }
}else{
    echo "Recepient is not defined!";
}