<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = '';

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

if(!isset($_GET['df']) or !isset($_GET['dt']) or empty($_GET['df']) or empty($_GET['dt'])){
    $secret =  rand(1000000, 9999999);
    $_SESSION[CLIENT_ID]['secret'] = $secret;    
}else{    
    if(isset($_GET['delete_token']) and !empty($_GET['delete_token'])){        
        if(md5($_SESSION[CLIENT_ID]['secret'].$_GET['df'].$_GET['dt']) === $_GET['delete_token']){        
            //removal functional
            $res = $oOrder->deleteOrders($_GET['df'], $_GET['dt'], $_SESSION[CLIENT_ID]['user_superclinic']['role']);
            unset($_SESSION[CLIENT_ID]['secret']);
        }else{
            header("Location: reports.php");die;
        }
    }else{
        $delete_token = md5($_SESSION[CLIENT_ID]['secret'].$_SESSION[CLIENT_ID]['from'].$_SESSION[CLIENT_ID]['to']);    
    }
}

$numOrders = $oOrder->getOrderNumber($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$amtOrders = $oOrder->getOrdersAmout($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

include '../templates/POS/reports_remove_data_tpl.php';
?>