<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    $oOrder->clearCart();
}
?>
