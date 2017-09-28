<?php
/*
 * Order data format (JSON):
  *{
            "products":[
                        {
                            "name":"Product Name 3",
                            "quantity":"8",
                            "unit":"gram",
                            "price": "9.99"
                        },
                        {
                            "name":"Product Name 4",
                            "quantity":"1",
                            "unit":"fourth",
                            "price": "59.99"
                        },
                        {
                            "name":"Product Name 5",
                            "quantity":"1",
                            "unit":"qty",
                            "price": "4.99"
                        }
                       ],
            "patient": {
                        "name":"Peter Johns",
                        "address": "45 Av. San Francisco, CA",
                        "email":"mail@example.com"
                       }
        }
 * 
 * One string:
 * {"products":[{"name":"Product Name 3","quantity":"8","unit":"gram","price":"9.99"},{"name":"Product Name 4","quantity":"1","unit":"fourth","price":"59.99"},{"name":"Product Name 5","quantity":"1","unit":"qty","price":"4.99"}],"patient":{"name":"Peter Johns","address":"45 Av. San Francisco, CA","email":"mail@example.com"}} 
 */
include_once '../../includes/common.php';

$order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] : '');
$sign = isset($_POST['sign']) ? $_POST['sign'] : (isset($_GET['sign']) ? $_GET['sign'] : '');

if(empty($order) or empty($sign)){
    $aResult['error'] = 'All fields are required';
}else{
    $mySign = sha1($order.API_KEY);
    if($mySign != $sign){
        $aResult['error'] = 'Wrong data sign.';
    }else{
        $aOrder = json_decode($order, true);
        if(empty($aOrder) or !is_array($aOrder)){           
            $aResult['error'] = 'Wrong data format.';
        }else{
            $result = $oOrder->postRemoteOrder($aOrder);
            if(!$result){
                $aResult['error'] = 'Order has not been added.';
            }else{
                $aResult['orderId'] = $result;
            }
        }
    }
}
$output = $aResult;
$json_output = json_encode($output);
echo $json_output;