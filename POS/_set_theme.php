<?php
include_once '../includes/common.php';
checkAccess(array('1','2','3','4'), 'login.php');

$theme = intval($_GET['theme']);

setcookie('theme', $theme, time()+2592000);
//dump($_SERVER['HTTP_REFERER']);

if(!empty($_SERVER['HTTP_REFERER'])){
    header('Location: '.$_SERVER['HTTP_REFERER']);die;
}else{
    header('Location: reports.php');die;
}