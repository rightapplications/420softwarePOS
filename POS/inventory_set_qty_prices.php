<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices']){
    checkAccess(array('1'), 'login.php');
}

$_SESSION[CLIENT_ID]['return'] = $_SERVER['REQUEST_URI'];

$activeMenu = 'inventory';
$sectionName = 'Inventory';

if(isset($_GET['del'])){
    $oInventory->delete_preset_qty_prices($_GET['del']);
    header("Location: inventory_set_qty_prices.php");die;
}

if(!empty($_GET['id'])){//edit price set
    if(isset($_POST['prices_sent'])){
        $res = $oInventory->edit_preset_qty_prices($_GET['id'], $_POST['price']);
        if($res){
            $oInventory->set_price_mode_all(2, 1);
            header("Location: inventory_set_qty_prices.php");die;
        }
    }
    $aPrice = $oInventory->get_preset_qty_price($_GET['id']);
}else{//add price set
    if(isset($_POST['prices_sent'])){
        $res = $oInventory->add_preset_qty_prices($_POST['price']);
        if($res){
            $oInventory->set_price_mode_all(2, 1);
            header("Location: inventory_set_qty_prices.php");die;
        }
    }
}

$aVendors = $oInventory->get_vendors();

$aPrices = $oInventory->get_preset_qty_prices();

$iTimeFrame = $oInventory->get_inactive_timeframe(2);

if(isset($_POST['inactive_time_frame'])){
     $oInventory->set_inactive_timeframe($_POST['inactive_time_frame']);
     header("Location: inventory_set_qty_prices.php");die;
}

include '../templates/POS/inventory_set_qty_prices_tpl.php';
?>