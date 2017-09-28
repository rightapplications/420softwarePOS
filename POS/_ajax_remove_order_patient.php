<?php
include_once '../includes/common.php';
//header("Content-type: text/json");
if(checkAccess(array('1','2','4'), '')  or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
	$_SESSION[CLIENT_ID]['order_discount_type'] = 2;
    $_SESSION[CLIENT_ID]['order_discount_percent'] = 0;
	$_SESSION[CLIENT_ID]['order_discount_amt'] = 0;
	if(isset($_SESSION[CLIENT_ID]['discount_reason']) and ($_SESSION[CLIENT_ID]['discount_reason'] == 'vip or senior client' or strpos($_SESSION[CLIENT_ID]['discount_reason'],'employee') !== false)){
            foreach($_SESSION[CLIENT_ID]['cart'] as $item){
                if($item['modifiers']){
                    foreach($item['modifiers'] as  $k => $mod){
                        foreach($mod as $altname=>$alt){
                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$k][$altname]['discount_amt'] = 0;
                        }
                    }
                }
            }
            $_SESSION[CLIENT_ID]['discount_reason'] = '';
	}    
    unset($_SESSION[CLIENT_ID]['order_client']);
}

