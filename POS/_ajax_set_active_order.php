<?php
include_once '../includes/common.php';

if(checkAccess(array('1','2','4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    
    $redis = new Redis();
    $r_con = @$redis->connect(REDIS_SERVER, REDIS_PORT);
    if(@$r_con){
        $orderInfo = array();
        if(!empty($_SESSION[CLIENT_ID]['cart'])){
            $orderInfo['cart'] = $_SESSION[CLIENT_ID]['cart'];
            $orderInfo['order_client'] = @$_SESSION[CLIENT_ID]['order_client'];
            $orderInfo['temp_order_id'] = @$_SESSION[CLIENT_ID]['temp_order_id'];
            $orderInfo['order_discount_type'] = @$_SESSION[CLIENT_ID]['order_discount_type'];
            $orderInfo['order_discount_amt'] = @$_SESSION[CLIENT_ID]['order_discount_amt'];
            $orderInfo['order_discount_percent'] = @$_SESSION[CLIENT_ID]['order_discount_percent'];
            $orderInfo['discount_reason'] = @$_SESSION[CLIENT_ID]['discount_reason'];
            $orderInfo['cash_given'] = @$_SESSION[CLIENT_ID]['cash_given'];
            $orderInfo['rewards'] = @$_SESSION[CLIENT_ID]['rewards'];
            
            $sOrderInfo = serialize($orderInfo); //dump($orderInfo); dump($sOrderInfo);
            
            $redis_key = 'posorder-'.CLIENT_ID.'-'.$_SESSION[CLIENT_ID]['user_superclinic']['id']; //dump($redis_key);
            $redis->set($redis_key, $sOrderInfo, 30);
        }
        echo 'ok';
    }else{
        echo 'Redis error!';
    }    
    
}
?>