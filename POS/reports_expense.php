<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');
if($_SESSION[CLIENT_ID]['user_superclinic']['id'] != 1){
    header("Location: login.php");
}


$activeMenu = 'expense';
$sectionName = 'Reports';
$reportName = 'expense';

if(isset($_GET['del'])){
    $oUser->deleteExpenseItem($_GET['del']);
    header("Location: reports_expense.php");die;
}

if(!empty($_GET['id'])){
    if(isset($_POST['amount_sent'])){
        $res = $oUser->editExpenseItem($_GET['id'], $_POST['expense']);
        if($res){
            header("Location: reports_expense.php");die;
        }
    }
    $aExpenseItem = $oUser->getExpenseItem($_GET['id']);
}else{
    if(isset($_POST['amount_sent'])){
        $res = $oUser->addExpenseItem($_POST['expense']);
        if($res){
            header("Location: reports_expense.php");die;
        }
    }
}

$aExpense = $oUser->getExpense();
$totalExpense = $oUser->getTotalExpense();
$perDay = $oUser->getExpensesPerDay();

include '../templates/POS/reports_expense_tpl.php';
?>