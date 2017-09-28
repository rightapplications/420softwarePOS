<?php
include_once '../includes/common.php';
if(empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    checkAccess(array('4'), 'login.php');
}


$activeMenu = 'cashier';
$sectionName = 'Cashier';

$_SESSION[CLIENT_ID]['return_page'] = 'cashier.php';

unset($_SESSION[CLIENT_ID]['temp_order_id']);

$oOrder->clearCart();

$allow_open_cashdrawer = false;
if(isset($_POST['cd_pass'])){
    $cashdrawer_password = settings::get('cashdrawer_password');
    if(md5($_POST['cd_pass']) === $cashdrawer_password){
        $allow_open_cashdrawer = true;
    }
}

$aRecentOrders = $oOrder->getRecentOrders(13);
$aDetailedOrders = array();
if(!empty($aRecentOrders)){
    foreach($aRecentOrders as $order){
        $aOrder = $oOrder->getOrder($order['id']);
        $aDetailedOrders[] = $aOrder;
    }
}

include '../templates/POS/cashier_tpl.php';