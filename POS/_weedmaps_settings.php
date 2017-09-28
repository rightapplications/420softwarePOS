<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

settings::set('weedmaps_apikey', $_POST['weedmaps_apikey']);

$ret = $_POST['return'];
if(!empty($_POST['return'])){
    $ret = $_POST['return'];
}else{
    $ret = 'index.php';
}
header("Location: ".$ret);die;