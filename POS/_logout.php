<?php
include_once '../includes/common.php';

$adminEmail = $oUser->get_admin_email();
$subject= 'End of day report '.strftime("%m/%d/%Y",$oUser->load_time);

$employeeName = $_SESSION[CLIENT_ID]['user_superclinic']['firstname'].' '.$_SESSION[CLIENT_ID]['user_superclinic']['lastname'];

$totalVisits = $oOrder->getOrderNumber($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);

$totalDeliveries = $oOrder->deliverySalesCount($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);

$totalDiscounts = $oOrder->getDiscountAmt($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);

$grossSales = $oOrder->grossSales($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);

$netSales = $oOrder->netSales($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);

$message = '<p><b>Employee: '.$employeeName.'</b></p>';
$message.= '<p>Total Visits: '.$totalVisits.'</p>';
$message.= '<p>Deliveries: '.$totalDeliveries.'</p>';
$message.= '<p>Discounts: $'.number_format($totalDiscounts,2,'.',',').'</p>';
$message.= '<p><b>Gross Sales: $'.number_format($grossSales,2,'.',',').'</b></p>';
$message.= '<p><b>Net Sales: $'.number_format($netSales,2,'.',',').'</b></p>';

$oEmail = new class_email();

$oEmail->email($adminEmail, $subject, $message);

$oUser->logout($_SESSION[CLIENT_ID]['user_superclinic']['id']);
$oInventory->set_current_instock();
header("Location: login.php");
