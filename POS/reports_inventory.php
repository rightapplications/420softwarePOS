<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$page = 'vendors';
$reportName = 'inventory';

$_SESSION[CLIENT_ID]['back_from_details'] = $_SERVER['REQUEST_URI'];

//sorting
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['inv']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['inv']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['inv']['ordby'];
}else{
    $ordby = '';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['inv']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['inv']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['inv']['ord'];
}else{
    $ord = '';
}
$aStock = $oInventory->get_stock($ordby, $ord); //dump($aStock);

include '../templates/POS/reports_inventory_tpl.php';