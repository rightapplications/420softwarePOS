<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2'), '')  or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    $barcode = @$_GET['code'];
    
    $aItem = $oInventory->search_goods_item($barcode);
    
    if($aItem){
        $aResult = array('result'=>1, 'data'=>$aItem);        
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output;    
}

