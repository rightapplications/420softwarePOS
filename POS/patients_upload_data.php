<?php
include_once '../includes/common.php';
checkAccess(array('1','3'), 'login.php');

$activeMenu = 'patients';
$sectionName = 'Patients';
$pageName = 'patients';


include '../templates/POS/patients_upload_data_tpl.php';