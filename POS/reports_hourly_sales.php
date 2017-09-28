<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'hourly_sales';

$ct = getdate();

$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);

$weekStart = 1;
$lastDayOfWeek = $weekStart-1;
if($lastDayOfWeek < 0){
    $lastDayOfWeek = 6;
}
if(@isset($_GET['from'])){//first day of week   
    $tm_from = explode('/',$_GET['from']);
    $date_from = mktime(0, 0, 0, $tm_from[0], $tm_from[1], $tm_from[2]);
    $aDateFrom = getdate($date_from);
    if($weekStart == $aDateFrom['wday']){
        $firstDay = $date_from;
    }else{
        $firstDay = strtotime('last '.$aDays[$weekStart],$date_from);
    }
    $_SESSION[CLIENT_ID]['from'] = $firstDay;
}else{
    if(!isset($_SESSION[CLIENT_ID]['from'])){
        $aDateFrom = getdate($default_date_start);
        if($weekStart == $aDateFrom['wday']){
            $firstDay = $default_date_start;
        }else{
            $firstDay = strtotime('last '.$aDays[$weekStart],$default_date_start);
        }
        $_SESSION[CLIENT_ID]['from'] = $firstDay;
    }else{
        $aDateFrom = getdate($_SESSION[CLIENT_ID]['from']);
        if($weekStart == $aDateFrom['wday']){
            $firstDay = $_SESSION[CLIENT_ID]['from'];
        }else{
            $firstDay = strtotime('last '.$aDays[$weekStart],$_SESSION[CLIENT_ID]['from']);
        }
        $_SESSION[CLIENT_ID]['from'] = $firstDay;
    }
}

$_SESSION[CLIENT_ID]['to'] = strtotime("+1 week",$_SESSION[CLIENT_ID]['from'])-1;

//time interval
if(isset($_GET['workstart'])){
    setcookie('workstart', intval($_GET['workstart']), time()+604800);
    $workStartH = intval($_GET['workstart']);
}else{
    if(isset($_COOKIE['workstart'])){
        $workStartH = $_COOKIE['workstart'];
    }else{
        $workStartH = WORK_START;
    }
}
if(isset($_GET['workend'])){
    setcookie('workend', intval($_GET['workend']), time()+604800);
    $workEndH = intval($_GET['workend']);
}else{
    if(isset($_COOKIE['workend'])){
        $workEndH = $_COOKIE['workend'];
    }else{
        $workEndH = WORK_END;
    }
}
if($workStartH > $workEndH){
    $workStartH = WORK_START;
    $workEndH = WORK_END;
}

        
$workStart = $workStartH*3600;
$workEnd = $workEndH*3600;

$aWeekDays = intervalByDays($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$k=1;
foreach($aWeekDays as $n=>$day){
    if($k > 6){
        $k = 0;
    }
    $aWeekDays[$n]['day'] = $aDays[$k];
    $k++;
    $workStartDaily = 
    $aHours = intervalByHours($day['start']+$workStart, $day['start']+$workEnd); 
    foreach($aHours as $i=>$hour){
        $aHours[$i]['startFormatted'] = strftime("%I:%M %p", $hour['start']);
        $aHours[$i]['endFormatted'] = strftime("%I:%M %p", $hour['end']);
        $aHours[$i]['sales'] = $oOrder->getOrdersAmout($hour['start'], $hour['end']);
    }
    $aWeekDays[$n]['hours'] = $aHours;
}

include '../templates/POS/reports_hourly_sales_tpl.php';