<?php
include_once '../includes/common.php';

checkAccess(array('1'), 'login.php');

$activeMenu = 'inventory';
$sectionName = 'Inventory';

$aCategories = $oInventory->get_categories();

$aVendor = $oInventory->get_vendor($_GET['id']);

$activityPage = 'inventory_vendors_goods.php';

$_SESSION[CLIENT_ID]['back_from_details'] = $_SERVER['REQUEST_URI'];

if($_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){
    $add_only = true;
}else{
    $add_only = false;
}

$SEARCH_ROWS_MAX = 200;
if(isset($_GET['item_id'])){
    if(!$add_only){
        if(isset($_GET['active'])){
            $result = $oInventory->activate_item($_GET['item_id'], 'goods', $_GET['active']);
            if($result == 'ok'){
                header("Location: inventory_vendors_goods.php?id=".$aVendor['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }else{
                $error = $result;
            }
        }
        if(isset($_GET['delete'])){
            $result = $oInventory->delete_goods_item($_GET['item_id']);
            if($result == 'ok'){
                header("Location: inventory_vendors_goods.php?id=".$aVendor['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }else{
                $error = $result;
            }
        }
    }
}
//sorting
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['invgoods']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['invgoods']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['invgoods']['ordby'];
}else{
    $ordby = '';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['invgoods']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['invgoods']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['invgoods']['ord'];
}else{
    $ord = '';
}

if(isset($_GET['sorting'])){
    $aGoods = $oInventory->get_vendors_goods($_GET['id']); 
    $aGoods = sort_array($aGoods, $ordby, $ord);
}else{
    $aGoods = $oInventory->get_vendors_goods($_GET['id'], $ordby, $ord);
}

include '../templates/POS/inventory_vendors_goods_tpl.php';