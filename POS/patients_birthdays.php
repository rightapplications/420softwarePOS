<?php
include_once '../includes/common.php';
checkAccess(array('1','3'), 'login.php');

$activeMenu = 'patients';
$sectionName = 'Patients';
$pageName = 'patients';

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

$SEARCH_ROWS_MAX = 20;

if(isset($_GET['id'])){
    if(isset($_GET['delete'])){
        $result = $oPatient->delete_patient($_GET['id']);
        if($result == 'ok'){
            header("Location: patients_birthdays.php");die();
        }else{
            $error = $result;
        }
    }
}

//queue
$oPatient->clear_queue();
$aQueue = $oPatient->get_queue();
$aQueueIDs = array();
if(!empty($aQueue)){
    foreach($aQueue as $p){
        $aQueueIDs[] = $p['patient_id'];
    }
}

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
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['patients']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['patients']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['patients']['ordby'];
}else{
    $ordby = '';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['patients']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['patients']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['patients']['ord'];
}else{
    $ord = '';
}

$numDays = floor(($_SESSION[CLIENT_ID]['to'] - $_SESSION[CLIENT_ID]['from'])/86400)+1;
if($numDays <= 92){
    $aPatients = $oPatient->get_birthdays($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $ordby, $ord);
    $iNumResults = count($aPatients);
}else{
    $error = "Sorry, but selected interval can not exceed 3 monthes";
    $iNumResults = 0;
}

include '../templates/POS/patients_birthdays_tpl.php';