<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'labor';

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

$aUsers = $oUser->getWorkedEmployees($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
if($aUsers){
    foreach($aUsers as $k=>$user){
        $aUsers[$k]['grossSales'] = $oOrder->grossSales($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $k)-$user['salary'];
        $aUsers[$k]['netSales'] = $oOrder->netSales($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $k)-$user['salary'];
    }
    //sorting
    if(isset($_GET['ordby'])){
        $_SESSION[CLIENT_ID]['sorting']['labor']['ordby'] = $_GET['ordby'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['labor']['ordby'])){
        $ordby = $_SESSION[CLIENT_ID]['sorting']['labor']['ordby'];
    }else{
        $ordby = '';
    }
    if(isset($_GET['ord'])){
        $_SESSION[CLIENT_ID]['sorting']['labor']['ord'] = $_GET['ord'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['labor']['ord'])){
        $ord = $_SESSION[CLIENT_ID]['sorting']['labor']['ord'];
    }else{
        $ord = '';
    }
    $aEmployees = sort_array($aUsers, $ordby, $ord);
}else{
    $aEmployees = array();
}

include '../templates/POS/reports_labor_tpl.php';