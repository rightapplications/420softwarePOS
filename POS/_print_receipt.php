<?php
include_once '../includes/common.php';
checkAccess(array('1','2','4'), 'login.php');

$dt = strftime(DATE_FORMAT." %I:%M%p");

$cash = floatval($_GET['cash']);
$cashString = '$'.number_format($cash,2,'.',',');

include '../templates/POS/_print_receipt_tpl.php';