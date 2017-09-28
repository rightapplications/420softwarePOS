<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'comps';

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

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

//sorting
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['comps']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['comps']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['comps']['ordby'];
}else{
    $ordby = '';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['comps']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['comps']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['comps']['ord'];
}else{
    $ord = '';
}
$aComps = $oOrder->get_comps($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $ordby, $ord);

include '../templates/POS/reports_comps_tpl.php';