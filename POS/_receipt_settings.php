<?php
include_once '../includes/common.php';

checkAccess(array('1'), 'login.php');

if(isset($_POST['receipt_mode'])){
    settings::set('receipt_mode', $_POST['receipt_mode']);
}
settings::set('receipt_label_text', $_POST['receipt_label_text']);
settings::set('receipt_name', $_POST['receipt_name']);
settings::set('receipt_address', $_POST['receipt_address']);
settings::set('receipt_phone', $_POST['receipt_phone']);


$ret = $_POST['return'];
if(!empty($_POST['return'])){
    $ret = $_POST['return'];
}else{
    $ret = 'index.php';
}
header("Location: ".$ret);die;

