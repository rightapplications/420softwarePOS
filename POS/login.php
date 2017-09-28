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
unset($_SESSION[CLIENT_ID]['cashonhands']);

$oOrder->clearCart();

if(isset($_POST['sent'])){
    $ok = $oUser->login($_POST['email'],$_POST['password']);
    if($ok == 'ok'){
        $oInventory->set_current_instock();
        $oInventory->add_common_vendors();
        if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
            header('Location: '.HOST.'POS/reports.php'); die;
        }elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2){
            header('Location: '.HOST.'POS/pos.php'); die;
        }elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){
            header('Location: '.HOST.'POS/patients.php'); die;
        }elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4){
            header('Location: '.HOST.'POS/cashier.php'); die;
        }
    }else{
        $error = $ok;
    }
}

include '../templates/POS/login_tpl.php';
?>