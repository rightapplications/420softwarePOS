<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'patients';
$sectionName = 'Patients';
$pageName = 'adding_report';

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

if($_SESSION[CLIENT_ID]['to'] - $_SESSION[CLIENT_ID]['from'] > 31622400){
    $error = 'Dates interval is too loong.';
}else{
    $aDateRange = array();
    $d = $_SESSION[CLIENT_ID]['from'];
    $i = 0;
    while($d < $_SESSION[CLIENT_ID]['to']){

        $cd = getdate($d);    
        $start = mktime('0', '0', '0', $cd['mon'], $cd['mday'], $cd['year']);
        $end = mktime('23', '59', '59', $cd['mon'], $cd['mday'], $cd['year']);
        $aDateRange[$i]['start'] = $start;
        $aDateRange[$i]['end'] = $end;
        $aDateRange[$i]['count'] = $oPatient->get_added_qty($start, $end);

        $d = strtotime("+1 day", $d);
        $i++;
    }
}
include '../templates/POS/patients_added_qty_tpl.php';