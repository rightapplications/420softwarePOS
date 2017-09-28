<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory']  and !@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices']){
    checkAccess(array('1'), 'login.php');
}

$activeMenu = 'inventory';
$sectionName = 'Inventory';
$page = 'categories';

$ct = getdate();

$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);

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
    $date_to = mktime('0', '0', '0', $tm_to[0], $tm_to[1], $tm_to[2]);
    $_SESSION[CLIENT_ID]['to'] = $date_to;
}else{
    $date_to = !empty($date_to) ? $date_to : $default_date_end;
    if(!isset($_SESSION[CLIENT_ID]['to'])){
        $_SESSION[CLIENT_ID]['to'] = $date_to;
    }
}

$aCategories = $oInventory->get_categories();
if(isset($_GET['c'])){
    $_SESSION[CLIENT_ID]['phistory_cat'] = $_GET['c'];
}
if(!empty($_SESSION[CLIENT_ID]['phistory_cat'])){
    $selectedCategory = $_SESSION[CLIENT_ID]['phistory_cat'] == 'all' ? '' : $_SESSION[CLIENT_ID]['phistory_cat'];
}else{
    $selectedCategory = $aCategories[0]['name'];
}


$aInStockCategories = $oInventory->get_instock_categories();

$numDays = floor(($_SESSION[CLIENT_ID]['to'] - $_SESSION[CLIENT_ID]['from'])/86400)+1;
if($numDays <= 366){
    $aDays = array();
    for($i=0; $i<$numDays; $i++){
        $aDay = array();
        $aDay['timestamp'] = strtotime("+".$i." day", $_SESSION[CLIENT_ID]['from']);
        $aDay['date'] = strftime("%m/%d/%Y", $aDay['timestamp']);
        $aDays[] = $aDay;
    }
    foreach($aDays as $k=>$d){
        $instock = $oInventory->get_daily_instock($d['timestamp'], $selectedCategory);
        if(!empty($instock)){
            $aDays[$k]['instock'] = $instock;
        }else{
            unset($aDays[$k]);
        }
    }
}else{
    $error = "Sorry, but selected interval can not exceed 1 year";
}

include '../templates/POS/inventory_instock_history_tpl.php';