<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices']){
    checkAccess(array('1'), 'login.php');
}

$activeMenu = 'reports';
$sectionName = 'Reports';
$page = 'vendors';
$reportName = 'vendors';

$error = '';

if(isset($_GET['id'])){
    if(isset($_POST['sent'])){        
        $result = $oInventory->update_vendor($_GET['id'], $_POST['vendor']);
        if($result == 'ok'){
            header("Location: reports_vendors.php");die();
        }else{
            $error = $result;
        }
    }
    $aVendor = $oInventory->get_vendor($_GET['id']);
}else{
    if(isset($_POST['sent'])){ 
        $result = $oInventory->add_vendor($_POST['vendor']);
        if($result == 'ok'){
            header("Location: reports_vendors.php");die();
        }else{
            $error = $result;
        }
    }
}

include '../templates/POS/reports_edit_vendor_tpl.php';