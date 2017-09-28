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

$aPatient = $oPatient->get_patient(intval(@$_GET['id']));
if(!empty($aPatient['id'])){
    $aOrders = $oOrder->getPatientsOrders($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $aPatient['id']); 
    if(!empty($aOrders)){
        foreach($aOrders as &$ord){
            $aOrd = $oOrder->getOrder($ord['id']);
            if(!empty($aOrd['items'])){
                $ord['items'] = $aOrd['items'];
            }
        }
    }
}else{
    header("Location: reports_patients_history.php");
}

include '../templates/POS/reports_patient_history_tpl.php';