<?php
include_once '../includes/common.php';

if(checkAccess(array('1','2','4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    
    $_SESSION[CLIENT_ID]['cash_given'] = isset($_GET['cash_given']) ? floatval($_GET['cash_given']) : 0;
    $_SESSION[CLIENT_ID]['rewards'] = isset($_GET['rewards']) ? floatval($_GET['rewards']) : 0;
    
}