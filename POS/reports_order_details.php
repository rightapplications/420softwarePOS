<?php
include_once '../includes/common.php';
checkAccess(array('1','2','4'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = '';

$always_print = settings::get('always_print');

$receipt_name = settings::get('receipt_name');
$receipt_address = settings::get('receipt_address');
$receipt_phone = settings::get('receipt_phone');

$receipt_mode = settings::get('receipt_mode');
$receipt_label_text = settings::get('receipt_label_text');

$aOrder = $oOrder->getOrder(@$_GET['id']);

include '../templates/POS/reports_order_details_tpl.php';