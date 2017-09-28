<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2'), '')){
    $vendorId = @intval($_GET['vendor_id']);
    $p = $oInventory->get_preset_qty_prices_by_vendor($vendorId);
    if($p){
        $aResult = array('result'=>$p);        
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output;    
}