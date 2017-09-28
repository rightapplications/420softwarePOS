<?php
include_once '../includes/common.php';
if(isset($_GET['id'])){
    checkAccess(array('1','3'), 'login.php');
}else{
    checkAccess(array('1','2', '3'), 'login.php');
}

$activeMenu = 'patients';
$sectionName = 'Patients';
$pageName = 'edit_patient';

$curDate = getdate();
$curYear = $curDate['year'];

if(isset($_GET['id'])){
    $aQueue = $oPatient->get_queue();
    $aQueueIDs = array();
    if(!empty($aQueue)){
        foreach($aQueue as $p){
            $aQueueIDs[] = $p['patient_id'];
        }
    }
    if(isset($_GET['delete_image'])){//delete image
        $result = $oPatient->delete_patient_img($_GET['id'], $_GET['delete_image']);
        if($result == 'ok'){
            header("Location: edit_patient.php?id=".intval($_GET['id'])); die();
        }else{
            $error = $result;
        }
    }
    if(isset($_POST['sent'])){        
        $result = $oPatient->update_patient($_GET['id'], $_POST['user'], $_FILES);
        if($result == 'ok'){
            header("Location: ".$_SESSION[CLIENT_ID]['return_page']);die;
        }else{
            $error = $result;
        }
    }
    $aPatient = $oPatient->get_patient($_GET['id']);
}else{
    if(isset($_POST['sent'])){
        $result = $oPatient->add_patient($_POST['user'], $_FILES);
        if($result == 'ok'){
            if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){
                header("Location: patients.php");die();
            }else{
                header("Location: pos_checkout.php");die();
            }
        }else{
            $error = $result;
        }
    }
}

include '../templates/POS/edit_patient_tpl.php';