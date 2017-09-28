<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

if(isset($_GET['product'])){
    $product = $_GET['product'];
}else{
    $product = '';
}
if(isset($_GET['qty'])){
    $qty = floatval($_GET['qty']);
}else{
    $qty = 0;
}
if(isset($_GET['unit'])){
    $unit = $_GET['unit'];
}else{
    $unit = 0;
}
if(isset($_GET['vendor'])){
    $vendor = $_GET['vendor'];
}else{
    $vendor = '';
}

if($product and $qty and $vendor and $unit){
    include_once '../includes/admin_config.php';
    $url = VENDORS_HOST.$vendor.'/API/PostOrder/';
    $aOrder = array(
        'products'=>array(
                        array('name'=>$product, 'quantity'=>$qty, 'unit'=>$unit)
                    ),
        'client'=>array('name'=>SITE_NAME, 'code'=>CLIENT_ID)
    );
    $jsonOrder = json_encode($aOrder);
    
    $sign = sha1($jsonOrder.API_KEY);
    
    $data = array('order'=>$jsonOrder, 'sign'=>$sign);
    $result = post_curl_request($url, $data);
    $aResult = @json_decode($result, true);
    if(!empty($_GET['return'])){
        if(isset($aResult['orderId'])){
            header("Location: ".$_GET['return']);
        }else{
            header("Location: ".$_GET['return'].(strpos($_GET['return'], '?') ? '&' : '?')."err=".$aResult['error']);
        }
    }else{
        header("Location: inventory.php");
    }   
}

