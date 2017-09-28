<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['invoices']){
    checkAccess(array('1'), 'login.php');
}
if($_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1){
    $activeMenu = 'invoices';
}else{
    $activeMenu = 'reports';
}
$sectionName = 'Reports';
$reportName = 'invoices';

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

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    if(isset($_GET['delete'])){
        $result = $oInventory->delete_invoice($_GET['delete']);
        if($result == 'ok'){
            header("Location: reports_invoices.php");die();
        }else{
            $error = $result;
        }
    }
}

$aCategories = $oInventory->get_categories();
$aInvoiceCategories = $oInventory->get_invoice_categories();

$aVendors = $oInventory->get_vendors();
$error = '';
if(isset($_POST['sent_invoice'])){
    $result = $oInventory->add_invoice($_POST['invoice'], $_FILES, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
    if($result == 'ok'){
        header("Location: reports_invoices.php");
    }else{
        $error = $result;
    }
}
if(isset($_POST['sent_category'])){
    $result = $oInventory->add_invoice_category($_POST['invoice_category']);
    if($result == 'ok'){
        header("Location: reports_invoices.php");
    }else{
        $error = $result;
    }
}

$aIntervalByDays = intervalByDays($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

foreach($aIntervalByDays as $k=>$interval){
    $aIntervalByDays[$k]['invoices'] = $oInventory->get_invoices($interval['start'], $interval['end'], null, !empty($_GET['invoice_category']) ? intval($_GET['invoice_category']) : null);
}

include '../templates/POS/reports_invoices_tpl.php';