<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = '';

if(!empty($_GET['id'])){
    $aProductInfo = $oInventory->get_goods_item($_GET['id']);
    $aCategory = db::get_row("SELECT * FROM ".PREF."goods_categories WHERE id = '".intval($aProductInfo['cat_id'])."'");    
    if(!empty($aProductInfo)){
        $productName = $aProductInfo['name'];
        $aDays = $oOrder->get_item_history($_GET['id']);        
        $aLosses = $oInventory->getProductLosses($_GET['id']);
        $transferedVal = $oInventory->getProductTotalTransfers($_GET['id']);
        $aReturns = $oOrder->getItemReturns($_GET['id']);
    }else{
        header("Location: ".(!empty($_SESSION[CLIENT_ID]['back_from_details']) ? $_SESSION[CLIENT_ID]['back_from_details'] : 'reports_top_products.php'));die;
    }
}else{
    header("Location: reports_top_products.php");die;
}

include '../templates/POS/reports_product_details_tpl.php';