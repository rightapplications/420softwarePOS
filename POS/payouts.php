<?php
include_once '../includes/common.php';
if(empty($_SESSION[CLIENT_ID]['user_superclinic']['add_petty_cash'])){
    checkAccess(array('1'), 'login.php');
}

$activeMenu = 'payouts';
$sectionName = 'Payouts';

$ct = getdate();
$default_date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
$default_date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);
$grossToday = $oOrder->grossSales($default_date_start, $default_date_end);
if(!isset($_SESSION[CLIENT_ID]['cashonhands'])){
    $_SESSION[CLIENT_ID]['cashonhands'] = 0;
}

$aCurrentPettyCashAdminTotal = $oUser->getPettyCashTotal($default_date_start, $default_date_end, $_SESSION[CLIENT_ID]['user_superclinic']['id']); 
if($aCurrentPettyCashAdminTotal > 0){
    $allowAddPettyCash = false;
}else{
    $allowAddPettyCash = true;
}

$realCash = !empty($_SESSION[CLIENT_ID]['cashonhands']) ? floatval($_SESSION[CLIENT_ID]['cashonhands']) : $grossToday; 

//petty cash
if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_petty_cash'])){    
    
    if($allowAddPettyCash){    
        
        $aIOUProducts = $oInventory->get_iou_goods();
        //add iou priducts to payouts
        if(isset($_GET['pay_iou'])){
            $aProd = $oInventory->get_goods_item($_GET['pay_iou']);
            if(!empty($aProd)){
                $aProdCategory = $oInventory->get_category($aProd['cat_id']);
                $iou_reason = strftime(DATE_FORMAT,$aProd['purchase_date']).'-'.$aProd['name'].'-'.$aProdCategory['name'];
                $oUser->addPettyCash($_SESSION[CLIENT_ID]['user_superclinic']['id'], $iou_reason, $aProd['purchase_price'], $aProd['id']);
                $oInventory->set_iou_status($aProd['id'], 0);
                $oInventory->add_iou_history($_SESSION[CLIENT_ID]['user_superclinic']['id'], $aProd['id']);
            } 
            header("Location: payouts.php");die;
        }
        
        if(!empty($_GET['del_pc'])){
            $oUser->deletePettyCash($_SESSION[CLIENT_ID]['user_superclinic']['id'], $_GET['del_pc']);
            header("Location: payouts.php");die;
        }
        if(isset($_POST['petty_cash_sent'])){
            $_SESSION[CLIENT_ID]['cashonhands'] = isset($_POST['cashOnHands']) ? floatval($_POST['cashOnHands']) : 0;
            $oUser->addPettyCash($_SESSION[CLIENT_ID]['user_superclinic']['id'], $_POST['reason'], isset($_POST['amount']) ? $_POST['amount'] : 0);
            header("Location: payouts.php");die;
        }
        if(!empty($_GET['submit_petty_cash'])){
            unset($_SESSION[CLIENT_ID]['cashonhands']);
            $aAlreadyPosted = $oUser->getPostedPettyCash($default_date_start, $default_date_end);
            $aPettyCash = $oUser->submitPettyCash($_SESSION[CLIENT_ID]['user_superclinic']['id'], $_POST['grossAmt']-floatval($aAlreadyPosted['totalGross']), $_POST['realCash']);
            header("Location: payouts.php");die;
        }    
    }
    $aPettyCash = $oUser->getPettyCash($_SESSION[CLIENT_ID]['user_superclinic']['id']);
    
    //payouts report
    if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
        
        

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
        $aReporters = $oUser->getPettyCashUsers($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);
        if(!empty($aReporters)){
            foreach($aReporters as $reporter){
                $aPettyCashAdmin[$reporter['user_id']] = $oUser->getAdminPettyCash($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $reporter['user_id']);
                $aPettyCashAdminTotal[$reporter['user_id']] = $oUser->getPettyCashTotal($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $reporter['user_id']);      
            }
        }        
          
        $gross = $oOrder->grossSales($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);        
        
        $timeDiff = $_SESSION[CLIENT_ID]['to'] - $_SESSION[CLIENT_ID]['from'];
        
    }    
    if(isset($timeDiff) and $timeDiff > 90000){
        $displayCashField = false;
    }else{
        $displayCashField = true;
    }
        
    include '../templates/POS/payouts_tpl.php';
}else{
    header("Location: login.php");
}