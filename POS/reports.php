<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'sales';

$ct = getdate();

$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);

if(@isset($_GET['from'])){
    $tm_from = explode('/',$_GET['from']);
    $date_from = mktime(0, 0, 0, $tm_from[0], $tm_from[1], $tm_from[2]);
    $_SESSION[CLIENT_ID]['from'] = $date_from;
}else{
    $date_from = !empty($date_from) ? $date_from : $default_date_start;
    if(!isset($_SESSION[CLIENT_ID]['from'])){
        $_SESSION[CLIENT_ID]['from'] = $date_from;
    }
}

if(@isset($_GET['to'])){
    $tm_to= explode('/',$_GET['to']);
    $date_to = mktime('23', '59', '59', $tm_to[0], $tm_to[1], $tm_to[2]);
    $_SESSION[CLIENT_ID]['to'] = $date_to;
}else{
    $date_to = !empty($date_to) ? $date_to : $default_date_end;
    if(!isset($_SESSION[CLIENT_ID]['to'])){
        $_SESSION[CLIENT_ID]['to'] = $date_to;
    }
} 

$aCategories = $oInventory->get_categories();

$aChartByCategories = $oOrder->getSalesByCategories($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

//get type chart
$aChartByType = $oOrder->getSalesByTypes($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

//get vendor chart
$aChartByVendors = $oOrder->getSalesByVendors($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
$aVendorsColors = array('"#0040FF"','"#FF0000"','"#00AA00"','"#FE2EF7"','"#FFFF00"','"#FF8000"','"#58D3F7"','"#3B0B24"','"#8181F7"','"#088A29"','"#F78181"','"#0B0B61"','"#58FAD0"','"#00FF00"','"#610B5E"','"#B43104"','"#0404B4"','"#BDBDBD"','"#00BFFF"','"#F781F3"');

$gross = $oOrder->grossSales($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$rewards = $oOrder->getRewardsPaid($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$visits = $oOrder->getNumServedPatients($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$discount = $oOrder->get_discount_amt($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

//petty cash report
$totaPayouts = 0;
$aReporters = $oUser->getPettyCashUsers($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
if(!empty($aReporters)){
    foreach($aReporters as $reporter){
        $aEmplPayouts = $oUser->getAdminPettyCash($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $reporter['user_id']);
        $aPettyCashAdmin[$reporter['user_id']] = $aEmplPayouts;
        $aPettyCashAdminTotal[$reporter['user_id']] = $oUser->getPettyCashTotal($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $reporter['user_id']);   
        foreach($aEmplPayouts as $po){
            $totaPayouts+=$po['amount'];
        }        
    }
}     

$numDaysReported = ceil(($_SESSION[CLIENT_ID]['to'] - $_SESSION[CLIENT_ID]['from'])/86400);

if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
    $aDate = getdate($_SESSION[CLIENT_ID]['from']);
    $reportedMonth = $aDate['mon'];
    $expense = $oUser->getExpensesPerDay($reportedMonth)*$numDaysReported;
}else{
    $expense = 0;
}

$net = $oOrder->netSales($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']) - floatval($rewards) - $totaPayouts - $expense;

include '../templates/POS/reports_tpl.php';
?>