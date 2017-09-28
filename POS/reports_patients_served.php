<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'reports';
$sectionName = 'Reports';
$reportName = 'employee_sales';


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

if(@isset($_GET['user'])){
    $_SESSION[CLIENT_ID]['employee_explored'] = $_GET['user'];
}else{
    if(!isset($_SESSION[CLIENT_ID]['employee_explored'])){
        $_SESSION[CLIENT_ID]['employee_explored'] = 0;
    }
}

$aUser = $oUser->get_user($_SESSION[CLIENT_ID]['employee_explored']);

if(isset($_POST['sent_transactions_action'])){
    if($_POST['sent_transactions_action'] === 'delete'){
        if(!empty($_POST['selectedTransactions'])){
            foreach($_POST['selectedTransactions'] as $t){
                $oOrder->deleteOrder(intval($t), 1);
            }
        }    
        header("Location: reports_patients_served.php?user=".$_SESSION[CLIENT_ID]['employee_explored'].(isset($_GET['sort']) ? ('&sort='.$_GET['sort']) : '').(isset($_GET['ord']) ? ('&ord='.$_GET['ord']) : ''));die;
    }elseif($_POST['sent_transactions_action'] === 'return'){
        if(!empty($_POST['selectedTransactions'])){
            foreach($_POST['selectedTransactions'] as $t){
                $oOrder->returnOrder(intval($t), 1);
            }
        }
        header("Location: reports_patients_served.php?user=".$_SESSION[CLIENT_ID]['employee_explored'].(isset($_GET['sort']) ? ('&sort='.$_GET['sort']) : '').(isset($_GET['ord']) ? ('&ord='.$_GET['ord']) : ''));die;
    }elseif($_POST['sent_transactions_action'] === 'export'){
        if(!empty($_POST['selectedTransactions'])){
            $aOrders = array();
            $numTrans = 0;
            $totalTrans = 0;
            foreach($_POST['selectedTransactions'] as $orderId){
                $aItem = $oOrder->getOrder($orderId);
                $aOrders[] = $aItem;
                $numTrans++;
                $totalTrans+= round($aItem['total'], 2);
            }
            //dump($aOrders);die;
            require_once "../includes/pear/Spreadsheet/Excel/Writer.php";
            $xls = new Spreadsheet_Excel_Writer();
            $xls->send("transactions_".strftime("%m-%d-%Y",$_SESSION[CLIENT_ID]['from'])."_".strftime("%m-%d-%Y",$_SESSION[CLIENT_ID]['to']).".xls");
            $sheet = $xls->addWorksheet('');
            $sheet->setMargins(0.3);
            $sheet->setColumn(0,5,30);
            $colHeadingFormatName =& $xls->addFormat();
            $colHeadingFormatName->setBold();
            $colHeadingFormatValue =& $xls->addFormat();
            $colHeadingFormatValue->setAlign('left');        
            $cell=& $xls->addFormat();
            $cell->setBorder(1);
            $cellbold = & $xls->addFormat();
            $cellbold->setBorder(1);
            $cellbold->setBold();
            $tableHeader = & $xls->addFormat();
            $tableHeader->setBorder(1);
            $tableHeader->setFgColor(50);
            $tableHeader->setPattern();
                    
            $sheet->write(0,0,"Date From", $colHeadingFormatName);
            $sheet->write(0,1,strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['from']), $colHeadingFormatValue);
            $sheet->write(0,2,"Date To", $colHeadingFormatName);
            $sheet->write(0,3,strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['to']), $colHeadingFormatValue);
            $sheet->write(1,0,"Transactions", $colHeadingFormatName);
            $sheet->write(1,1,$numTrans, $colHeadingFormatValue);
            $sheet->write(2,0,"Total Sales ($)",$colHeadingFormatName);
            $sheet->write(2,1,$totalTrans, $colHeadingFormatValue);
        
            $row = 5;

            foreach($aOrders as $k=>$aOrder){
                $totalDsc = 0;
                $sheet->write($row,0,"Order date", $colHeadingFormatName);
                $sheet->write($row,1,strftime(DATE_FORMAT." %I:%M%p", $aOrder['date']), $colHeadingFormatValue);
                $sheet->write($row,2,"Patient", $colHeadingFormatName);
                $sheet->write($row,3,$aOrder['client_firstname'].' '.$aOrder['client_lastname'], $colHeadingFormatValue);
                $row++;
                if(!empty($aOrder['items'])){
                    $sheet->write($row,0,"Product Name", $tableHeader);
                    $sheet->write($row,1,"QTY", $tableHeader);
                    $sheet->write($row,2,"Discount ($)", $tableHeader);
                    $sheet->write($row,3,"Amount ($)", $tableHeader);
                    $row++;
                    foreach($aOrder['items'] as $m=>$item){
                        $totalDsc+= $item['d'];
                        $sheet->write($row,0,$item['goods_item_name'].(!empty($item['pre_roll']) ? ' (Pre Roll)' : ''), $cell);
                        $sheet->write($row,1,$item['qty'].$item['modifier_name'], $cell);
                        $sheet->write($row,2,($item['d'] > 0 ? number_format($item['d'],2,'.',',') : ''), $cell);
                        $sheet->write($row,3,number_format($item['price']*$item['qty'],2,'.',','), $cell);
                        $row++;
                    }
                    $sheet->write($row,0,"TOTAL", $cellbold);
                    $sheet->write($row,1,"", $cellbold);
                    $sheet->write($row,2,number_format(round($totalDsc, 2),2,'.',','), $cellbold);
                    $sheet->write($row,3,number_format(round($aOrder['total'], 2),2,'.',','), $cellbold);
                    $row++;
                }
                $row+=2;
            }

            $xls->close();
        }
    }
}

$aSales = $oOrder->getEmployeesSales($aUser['id'], $_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to']);

$aPatients = $oOrder->getServedPatients($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $_SESSION[CLIENT_ID]['employee_explored']);
if(!empty($aPatients)){
    foreach($aPatients as $k=>$patient){
        $aOrders = $oOrder->getPatientsOrders($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $patient['client_id'], $_SESSION[CLIENT_ID]['employee_explored']);
        if(!empty($aOrders)){
            $aDetailedOrders = array();
            foreach($aOrders as $ord){
                $aOrd = $oOrder->getOrder($ord['id'], isset($_GET['search']) ? $_GET['search'] : '');
                if(!empty($aOrd['items'])){
                    $aDetailedOrders[] = $aOrd;
                }
            }
            if(!empty($aDetailedOrders)){
                $aPatients[$k]['orders'] =$aDetailedOrders;
            }else{
                unset($aPatients[$k]);
            }
        }
    }
}

if(isset($_GET['sort']) and $_GET['sort'] === 'amount'){ 
    $aTempOrders = array();
    if(!empty($aPatients)){
        foreach($aPatients as $p){
            if(!empty($p['orders'])){
                foreach($p['orders'] as $order){
                    $aTempOrders[] = $order;
                }
            }
        }
    }
    if(!empty($aTempOrders)){
        unset($aPatients);
        $aPatients = array();
        $aPatients[0]['client_id'] = 0;
        $aPatients[0]['firstname'] = 'All Patients';
        $aPatients[0]['lastname'] = '';
        if(isset($_GET['ord'])){
            if($_GET['ord'] === 'ASC'){
                $ord = 'ASC';
            }else{
                $ord = 'DESC';
            }
        }else{
            $ord = 'DESC';
        }
        $aPatients[0]['orders'] = sort_array($aTempOrders,'total',$ord);
        unset($aTempOrders);
    }
    $activeSort = 'amount';
}elseif(isset($_GET['sort']) and $_GET['sort'] === 'date'){
    $aTempOrders = array();
    if(!empty($aPatients)){
        foreach($aPatients as $p){
            if(!empty($p['orders'])){
                foreach($p['orders'] as $order){
                    $aTempOrders[] = $order;
                }
            }
        }
    }
    if(!empty($aTempOrders)){
        unset($aPatients);
        $aPatients = array();
        $aPatients[0]['client_id'] = 0;
        $aPatients[0]['firstname'] = 'All Patients';
        $aPatients[0]['lastname'] = '';
        if(isset($_GET['ord'])){
            if($_GET['ord'] === 'ASC'){
                $ord = 'ASC';
            }else{
                $ord = 'DESC';
            }
        }else{
            $ord = 'DESC';
        }
        $aPatients[0]['orders'] = sort_array($aTempOrders,'date',$ord);
        unset($aTempOrders);
    }
    $activeSort = 'date';
}else{
    $activeSort = 'patients';
}


include '../templates/POS/reports_patients_served_tpl.php';