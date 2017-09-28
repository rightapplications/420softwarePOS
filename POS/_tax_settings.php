<?php
include_once '../includes/common.php';

checkAccess(array('1'), 'login.php');

if(isset($_POST['sent_tax_settings'])){
    settings::set('tax_amount', floatval($_POST['tax_amount']));
    settings::set('tax_mode', intval($_POST['tax_mode']));
}
$ret = $_POST['return'];
if(!empty($_POST['return'])){
    $ret = $_POST['return'];
}else{
    $ret = 'index.php';
}
header("Location: ".$ret);die;
