<?php
include_once '../includes/common.php';
if(!isset($_SESSION[CLIENT_ID]) or $_SESSION[CLIENT_ID]['user_superclinic']['id'] !== '1'){
    header("Location: login.php");die;
}

if(!empty($_POST['cashdrawer_password']) and $_POST['cashdrawer_password'] === $_POST['cashdrawer_password_confirm']){
    settings::set('cashdrawer_password', md5($_POST['cashdrawer_password']));
}


$ret = $_POST['return'];
if(!empty($_POST['return'])){
    $ret = $_POST['return'];
}else{
    $ret = 'index.php';
}
header("Location: ".$ret);die;

