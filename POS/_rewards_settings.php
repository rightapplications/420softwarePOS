<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

settings::set('rewards_amount_needed', floatval($_POST['rewards_amount_needed']));
settings::set('rewards_receive', floatval($_POST['rewards_receive']));


$ret = $_POST['return'];
if(!empty($_POST['return'])){
    $ret = $_POST['return'];
}else{
    $ret = 'index.php';
}
header("Location: ".$ret);die;
