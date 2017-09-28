<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices']){
    checkAccess(array('1'), 'login.php');
}


$activeMenu = 'inventory';
$sectionName = 'Inventory';

if(isset($_GET['del'])){
    $oInventory->delete_preset_prices($_GET['del']);
    header("Location: inventory_set_prices.php");die;
}

if(!empty($_GET['id'])){//edit price set
    if(isset($_POST['prices_sent'])){
        $res = $oInventory->edit_preset_prices($_GET['id'], $_POST['price']);
        if($res){
            $oInventory->set_price_mode_all(1, 1);
            header("Location: inventory_set_prices.php");die;
        }
    }
    $aPrice = $oInventory->get_preset_price($_GET['id']);
}else{//add price set
    if(isset($_POST['prices_sent'])){
        $res = $oInventory->add_preset_prices($_POST['price']);
        if($res){
            $oInventory->set_price_mode_all(1, 1);
            header("Location: inventory_set_prices.php");die;
        }
    }
}

$aPrices = $oInventory->get_preset_prices();

//round other values
if(isset($_POST['round_sent'])){
    $res = $oInventory->edit_round($_POST['round']);
    if($res){
        if(isset($_POST['autoround'])){
            $ar = 1;
        }else{
            $ar = 0;
        }
        $oInventory->set_price_autoround_all(1, $ar);
        header("Location: inventory_set_prices.php");die;
    }
}
$aRound = $oInventory->get_round();

$autoround = $oInventory->get_price_autoround(1);

$iTimeFrame = $oInventory->get_inactive_timeframe(1);

if(isset($_POST['inactive_time_frame'])){
     $oInventory->set_inactive_timeframe($_POST['inactive_time_frame']);
     header("Location: inventory_set_prices.php");die;
}

include '../templates/POS/inventory_set_prices_tpl.php';
?>