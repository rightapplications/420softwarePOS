<?php
include_once '../includes/common.php';
unset($_SESSION[CLIENT_ID]['user_superclinic']);
unset($_SESSION[CLIENT_ID]['from']);
unset($_SESSION[CLIENT_ID]['to']);
unset($_SESSION[CLIENT_ID]['search_string']);
unset($_SESSION[CLIENT_ID]['cart']);
unset($_SESSION[CLIENT_ID]['order_discount_amt']);
unset($_SESSION[CLIENT_ID]['order_discount_percent']);
unset($_SESSION[CLIENT_ID]['order_discount_type']);
unset($_SESSION[CLIENT_ID]['order_client']);
unset($_SESSION[CLIENT_ID]['discount_reason']);
unset($_SESSION[CLIENT_ID]['sorting']);

$oOrder->clearCart();

if(isset($_POST['sent'])){
    $ok = $oUser->login($_POST['email'],$_POST['password']);
    if($ok == 'ok'){
        //if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4){
            header('Location: '.HOST.'client/index.php'); die;
        //}else{
            //$error = 'Your are not allowed to login this area';
        //}
    }else{
        $error = $ok;
    }
}

include '../templates/client/login_tpl.php';
?>