<?php
include_once '../includes/common.php';
checkAccess(array('1','3'), 'login.php');

if(isset($_GET['id'])){
    $oPatient->add_to_queue($_GET['id']);
}

header("Location: patients.php");die;