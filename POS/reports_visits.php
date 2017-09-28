<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'visits';

if(isset($_GET['mode'])){
    $modeVal = intval($_GET['mode']);
}else{
    $modeVal = 0;
}
if(!empty($modeVal)){
    $_SESSION[CLIENT_ID]['mode'] = $modeVal;
}else{
    if(!isset($_SESSION[CLIENT_ID]['mode'])){
        $_SESSION[CLIENT_ID]['mode'] = 2;
    }
}
$mode = intval($_SESSION[CLIENT_ID]['mode']);

$ct = getdate();

$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);

if($mode === 1){

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

    if(@isset($_GET['to'])){//last day of week
        $tm_to= explode('/',$_GET['to']);
        $date_to = mktime('23', '59', '59', $tm_to[0], $tm_to[1], $tm_to[2]);
        if($date_to > time()){
            $date_to = time();
        }
        $aDateTo = getdate($date_to);
        if($lastDayOfWeek == $aDateTo['wday']){
            $lastDay = $date_to;
        }else{
            $lastDay = strtotime('next '.$aDays[$lastDayOfWeek],$date_to);
        }
        $_SESSION[CLIENT_ID]['to'] = $lastDay;
    }else{
        if(!isset($_SESSION[CLIENT_ID]['to'])){
            $aDateTo = getdate($default_date_end);
            if($lastDayOfWeek == $aDateTo['wday']){
                $lastDay = $default_date_end;
            }else{
                $lastDay = strtotime('next '.$aDays[$lastDayOfWeek],$default_date_end);
            }
            $_SESSION[CLIENT_ID]['to'] = $lastDay;
        }else{
            $aDateTo = getdate($_SESSION[CLIENT_ID]['to']);
            if($lastDayOfWeek == $aDateTo['wday']){
                $lastDay = $_SESSION[CLIENT_ID]['to'];
            }else{
                $lastDay = strtotime('next '.$aDays[$lastDayOfWeek],$_SESSION[CLIENT_ID]['to']);
            }
            $_SESSION[CLIENT_ID]['to'] = $lastDay;
        }
    }    

    $aWeekIntervals = intervalByWeeks($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
    foreach($aWeekIntervals as $i=>$week){
        //foreach($aDays as $n=>$day){
            //$aWeekDays[$n] = intervalByDays($week['start'], $week['end']);         
        //}
        $aWeekDays = intervalByDays($week['start'], $week['end']);
        $k=1;
        foreach($aWeekDays as $n=>$day){
            if($k > 6){
                $k = 0;
            }
            $aWeekDays[$n]['day'] = $aDays[$k];
            $k++;
            $aWeekDays[$n]['orderNum'] = $oOrder->getOrderNumber($day['start'], $day['end']);
        }
        $aWeekIntervals[$i]['days'] = $aWeekDays;
    }
    
}else{
    
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
    
    $aDaysInterval = intervalByDays($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
    foreach($aDaysInterval as $n=>$day){
        $aDaysInterval[$n]['orderNum'] = $oOrder->getOrderNumber($day['start'], $day['end']);
    }
    
}

include '../templates/POS/reports_visits_tpl.php';