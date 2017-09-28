<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['patients_history']){
    checkAccess(array('1'), 'login.php');
}

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    $activeMenu = 'reports';
}else{
    $activeMenu = 'reports-nonmanager';
}
$sectionName = 'Reports';
$reportName = 'history';

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
    $_SESSION[CLIENT_ID]['sorting']['history']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['history']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['history']['ordby'];
}else{
    $ordby = 'latestOrderDate';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['history']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['history']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['history']['ord'];
}else{
    $ord = 'DESC';
}

$_SESSION[CLIENT_ID]['return'] = $_SERVER['REQUEST_URI'];

$aHistory = $oOrder->getPatientsHistory($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $ordby, $ord, isset($_GET['search']) ? $_GET['search'] : ''); //dump($aHistory);

include '../templates/POS/reports_patients_history_tpl.php';