<?php
include_once '../includes/common.php';
checkAccess(array('1','2', '3'), 'login.php');

header('Location: reports.php');die;

$activeMenu = '';
$sectionName = 'Home';

include '../templates/POS/index_tpl.php';
?>