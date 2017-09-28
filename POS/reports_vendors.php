<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices']){
    checkAccess(array('1'), 'login.php');
}

$activeMenu = 'reports';
$sectionName = 'Reports';
$page = 'vendors';
$reportName = 'vendors';

$oInventory->add_common_vendors();

if(isset($_GET['delete'])){
    $result = $oInventory->delete_vendor($_GET['delete']);
    if($result == 'ok'){
        header("Location: reports_vendors.php");die();
    }else{
        $error = $result;
    }
}

$aVendors = $oInventory->get_vendors();

include '../templates/POS/reports_vendors_tpl.php';