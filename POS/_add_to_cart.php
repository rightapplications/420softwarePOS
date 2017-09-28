<?php
include_once '../includes/common.php';
if(!$_SESSION[CLIENT_ID]['user_superclinic']['add_disc']){
    checkAccess(array('1','2','4'), 'login.php');
}

if(isset($_GET['item'])){
    $item = intval($_GET['item']);
}else{
    $item = 0;
}
if(isset($_GET['mod'])){
    $mod = intval($_GET['mod']);
}else{
    $mod = 0;
}
if(isset($_GET['alt'])){
    $alt = $_GET['alt'];
}else{
    $alt = '';
}

if(isset($_GET['qty'])){
    $qty = intval($_GET['qty']);
}else{
    $qty = 1;
}

$aItem = $oInventory->search_goods_item_by_id($mod, $alt);
if(!empty($aItem)){ 
    
    $aData = array();
    
    $aData[$item][$mod]['default'] = ($alt == '' ? $qty : 0);
    if(!empty($aItem['modifier']['alt'])){
        foreach($aItem['modifier']['alt'] as $altVal){
            $aData[$item][$mod][$altVal['code']] = ($alt == $altVal['code'] ? $qty : 0);
        }
        $aData[$item][$mod]['other'] = 0;
    }  
    $oOrder->addCartItem($aData);
}

header("Location: pos_checkout.php");