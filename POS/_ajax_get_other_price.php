<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2'), '')){
    $mod = @intval($_GET['modifier']);
    $val = @floatval($_GET['value']);
    
    $p = $oInventory->getOtherPrice($mod, $val);
    
    if($p){
        $aResult = array('result'=>$p);        
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output;    
}