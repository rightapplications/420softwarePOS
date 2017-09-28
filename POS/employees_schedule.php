<?php
include_once '../includes/common.php';
checkAccess(array('1','2','3','4'), 'login.php');

$activeMenu = 'schedule';
$sectionName = 'Schedule';

$edit_mode = false;

if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['edit_schedule'])){
    if(isset($_GET['edit'])){
        $edit_mode = true;
        if(!empty($_POST['sent'])){
            //dump($_POST);
            if(!empty($_POST['user'])){
                $result = $oUser->editSchedule($_POST['user']);
                if($result){
                    header("Location: employees_schedule.php");die;
                }
            }            
        }
    }
}else{
    if(isset($_GET['edit'])){
        header("Location: employees_schedule.php");
    }
}

$aUsers = $oUser->getSchedule();

include '../templates/POS/employees_schedule_tpl.php';