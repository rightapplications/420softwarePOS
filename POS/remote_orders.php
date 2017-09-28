<?php
include_once '../includes/common.php';
checkAccess(array('1','2'), 'login.php');

$activeMenu = 'remote_orders';
$sectionName = 'Online Orders';

if(isset($_GET['del'])){
     $oOrder->deleteRemoteOrder(intval($_GET['del']));
     header("Location: remote_orders.php");die;
}

$aOrders = $oOrder->getRemoteOrders(true);


include '../templates/POS/remote_orders_tpl.php';