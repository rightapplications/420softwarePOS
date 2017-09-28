<?php
include_once '../includes/common.php';
checkAccess(array('1','2'), 'login.php');

$activeMenu = 'pos';
$sectionName = 'POS'; 

$_SESSION[CLIENT_ID]['return_page'] = 'pos.php';

if(!empty($_SESSION[CLIENT_ID]['next_patient'])){
    $aNextPatient = $oPatient->get_patient($_SESSION[CLIENT_ID]['next_patient']);
}

$aRecentOrders = $oOrder->getRecentOrders(13);
$aDetailedOrders = array();
if(!empty($aRecentOrders)){
    foreach($aRecentOrders as $order){
        $aOrder = $oOrder->getOrder($order['id']);
        $aDetailedOrders[] = $aOrder;
    }
}

if(isset($_GET['delete_from_queue'])){
     $oPatient->delete_from_queue($_GET['delete_from_queue']);
     header("Location: pos.php");die;
}

if(isset($_GET['add_patient_to_order'])){
    $_SESSION[CLIENT_ID]['next_patient'] = intval($_GET['add_patient_to_order']);
    header("Location: pos.php");die;
}

$oPatient->clear_queue();

$aCategories = $oInventory->get_categories();
if(!empty($aCategories)){
    foreach($aCategories as $k=>$v){
        $aCategories[$k]['goods'] = $oInventory->search_goods_by_category($v['id']);
    }
}

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['set_prices'])){
    $allowRound = 1;
}else{
    $allowRound = 0;
}

$autoRound = $oInventory->get_price_autoround(1);
//dump($_SESSION[CLIENT_ID]['cart']);

$allow_open_cashdrawer = false;
if(isset($_POST['cd_pass'])){
    $cashdrawer_password = settings::get('cashdrawer_password');
    if(md5($_POST['cd_pass']) === $cashdrawer_password){
        $allow_open_cashdrawer = true;
    }
}

include '../templates/POS/pos_tpl.php';
?>