<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'top_products';

$_SESSION[CLIENT_ID]['back_from_details'] = $_SERVER['REQUEST_URI'];

$ct = getdate();

$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);

if(@isset($_GET['from'])){
    $tm_from = explode('/',$_GET['from']);
    $date_from = mktime(0, 0, 0, $tm_from[0], $tm_from[1], $tm_from[2]);
    $_SESSION[CLIENT_ID]['from'] = $date_from;
}else{
    $date_from = !empty($date_from) ? $date_from : $default_date_start;
    if(!isset($_SESSION[CLIENT_ID]['from'])){
        $_SESSION[CLIENT_ID]['from'] = $date_from;
    }
}

if(@isset($_GET['to'])){
    $tm_to= explode('/',$_GET['to']);
    $date_to = mktime('23', '59', '59', $tm_to[0], $tm_to[1], $tm_to[2]);
    $_SESSION[CLIENT_ID]['to'] = $date_to;
}else{
    $date_to = !empty($date_to) ? $date_to : $default_date_end;
    if(!isset($_SESSION[CLIENT_ID]['to'])){
        $_SESSION[CLIENT_ID]['to'] = $date_to;
    }
} 

$rewards = $oOrder->getRewardsPaid($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$aCategories = $oInventory->get_categories();
if($aCategories){
    //sorting
    if(isset($_GET['ordby'])){
        $_SESSION[CLIENT_ID]['sorting']['topprod']['ordby'] = $_GET['ordby'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['topprod']['ordby'])){
        $ordby = $_SESSION[CLIENT_ID]['sorting']['topprod']['ordby'];
    }else{
        $ordby = '';
    }
    if(isset($_GET['ord'])){
        $_SESSION[CLIENT_ID]['sorting']['topprod']['ord'] = $_GET['ord'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['topprod']['ord'])){
        $ord = $_SESSION[CLIENT_ID]['sorting']['topprod']['ord'];
    }else{
        $ord = 'DESC';
    }
    
    foreach($aCategories as $k=>$cat){
        if(isset($_GET['sorting'])){
            $sales = $oOrder->getTopByCategory($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id']);
            $aCategories[$k]['sales'] = $sales;
            $aCategories[$k]['sales'] = sort_array($aCategories[$k]['sales'], $ordby, $ord);
        }else{
            $sales = $oOrder->getTopByCategory($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id'], $ordby, $ord);
            $aCategories[$k]['sales'] = $sales;
        }
    }
}

//$aWeightSales =  $oOrder->getTopItems($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], 'weight', @$_GET['ordby'], @$_GET['ord']);

//$aQTYSales =  $oOrder->getTopItems($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], 'qty', @$_GET['ordby'], @$_GET['ord']);


include '../templates/POS/reports_top_products_tpl.php';
?>