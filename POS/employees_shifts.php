<?php
include_once '../includes/common.php';
if(@$_SESSION[CLIENT_ID]['user_superclinic']['id'] != 1){
    checkAccess(array('3'), 'login.php');
}

if(@$_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
    $activeMenu = 'shifts';
}else{
    $activeMenu = 'employees';    
}
$sectionName = 'Employees Shifts';

if(isset($_GET['start']) and!empty($_GET['start'])){
    $aUser = $oUser->get_user($_GET['start']);
    if(!empty($aUser['id'])){
        $oUser->open_shift($aUser['id'], $oUser->load_time, $aUser['role'], $aUser['hwages']);
        header("Location: employees_shifts.php");die;
    }
}

if(isset($_GET['end']) and!empty($_GET['end'])){
    $aUser = $oUser->get_user($_GET['end']);
    if(!empty($aUser['id'])){
        $oUser->close_shift($aUser['id'], $oUser->load_time);
        header("Location: employees_shifts.php");die;
    }
}

$aUsers = $oUser->getEmployeesShifts();

include '../templates/POS/employees_shifts_tpl.php';