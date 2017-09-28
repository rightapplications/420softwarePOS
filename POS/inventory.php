<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory']  and !@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['update_price']){
    checkAccess(array('1'), 'login.php');
}

if(!empty($_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'])){
    $add_only = true;
}else{
    $add_only = false;
}

if(!empty($_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory'])){
    $deactivate_only = true;
}else{
    $deactivate_only = false;
}

$activeMenu = 'inventory';
$sectionName = 'Inventory';
$page = 'categories';

if($add_only or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    if(isset($_POST['sent_add'])){
        $result = $oInventory->add_category($_POST['new_name'], !empty($_POST['measure_type']) ? $_POST['measure_type'] : '');
        if($result == 'ok'){
            header("Location: inventory.php");die();
        }else{
            $error = $result;
        }
    }
}


    
if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    if(isset($_POST['name'])){
        foreach($_POST['name'] as $k=>$v){
            $oInventory->update_category($k, $v);
        }
        header("Location: inventory.php");die();
    }
}

if(isset($_GET['cat'])){
    if($deactivate_only or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
        if(isset($_GET['active'])){
            $result = $oInventory->activate_item($_GET['cat'], 'goods_categories', $_GET['active']);
            if($result == 'ok'){
                header("Location: inventory.php");die();
            }else{
                $error = $result;
            }
        }
    }
    if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
        if(isset($_GET['delete'])){
            $result = $oInventory->delete_category($_GET['cat']);
            if($result == 'ok'){
                header("Location: inventory.php");die();
            }else{
                $error = $result;
            }
        }
    }
}


$aCategories = $oInventory->get_categories();
if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
    foreach($aCategories as &$cat){
        $aStock = $oInventory->get_stock_by_cat($cat['id']);
        $cat['amtInStockPurchase'] = $aStock['amtInStockPurchase'];
        $cat['amtInStockSale'] = $aStock['amtInStockSale'];
    }
}

//total in safe
$safeAmt = $oInventory->get_total_safe();

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){
    if(isset($_POST['item'])){
        //dump($_POST['item']);
        $oInventory->add_goods_item($_POST['item']);
        header("Location: inventory.php");die();
    }
}

$oInventory->set_current_instock();

include '../templates/POS/inventory_tpl.php';
?>