<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
    $main_admin = true;
}else{
    $main_admin = false;
}

$error = '';

$activeMenu = 'employees';
$sectionName = 'Employees';

if(isset($_GET['id'])){
    if(isset($_GET['active'])){
        $result = $oUser->activate_user($_GET['id'], $_GET['active']);
        if($result == 'ok'){
            header("Location: employees.php");die();
        }else{
            $error = $result;
        }
    }
    
    if(isset($_GET['delete'])){
        $result = $oUser->delete_user($_GET['id']);
        if($result == 'ok'){
            header("Location: employees.php");die();
        }else{
            $error = $result;
        }
    }
}

if(isset($_POST['employee']) and $_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
    $result = $oPatient->make_employe_patient($_POST['employee'], $_POST['apply_purchase_price'], $_POST['employee_discount']);
    if($result == 'ok'){
        header("Location: employees.php");die();
    }else{
        $error = $result;
    }
}

$aUsers = $oUser->get_users($main_admin);

include '../templates/POS/employees_tpl.php';
?>