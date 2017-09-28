<?php
include_once '../includes/common.php';
if(empty($_SESSION[CLIENT_ID]['user_superclinic']['one_day_visits'])){
    checkAccess(array('1','2'), 'login.php');
}


if(!empty($_SESSION[CLIENT_ID]['user_superclinic']['one_day_visits']) or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    $activeMenu = 'reports';
    $sectionName = 'Reports';
    $reportName = 'visits';


    $aTodaysOrders = $oOrder->getTodaysOrders();
    $aDetailedOrders = array();
    if(!empty($aTodaysOrders)){
        foreach($aTodaysOrders as $order){
            $aOrder = $oOrder->getOrder($order['id']);
            $aDetailedOrders[] = $aOrder;
        }
    }
}else{
    header("Location: pos.php");
}




include '../templates/POS/reports_one_day_visits_tpl.php';