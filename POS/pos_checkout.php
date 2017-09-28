<?php
include_once '../includes/common.php';
if(empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    checkAccess(array('1','2','4'), 'login.php');
}
if(isset($_GET['delete'])){
    unset($_SESSION[CLIENT_ID]['cart'][intval($_GET['delete'])]);
    header("Location: pos_checkout.php"); die;
}

$tax = settings::get('tax_amount');
$tax_mode = settings::get('tax_mode');
if(!$tax){
    $tax=0;
}
if(!$tax_mode){
    $tax_mode=0;
}

if(!$_SESSION[CLIENT_ID]['user_superclinic']['add_disc'] and $_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1){
    $allow_discount = false;
}else{
    $allow_discount = true;
}

if(isset($_POST['printMode'])){
    if(isset($_POST['printMode'])){
        if($_POST['printMode'] == 0){
            settings::set('always_print', '0');
        }else{
            settings::set('always_print', '1');
            if($_POST['printMode'] == 1){
                settings::set('receipt_mode', '1');
            }elseif($_POST['printMode'] == 2){
                settings::set('receipt_mode', '2');
            }
        }
    }
    header("Location: pos_checkout.php");die;
}
$always_print = settings::get('always_print');

$receipt_name = settings::get('receipt_name');
$receipt_address = settings::get('receipt_address');
$receipt_phone = settings::get('receipt_phone');

$receipt_mode = settings::get('receipt_mode');
$receipt_label_text = settings::get('receipt_label_text');

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4 or ($_SESSION[CLIENT_ID]['user_superclinic']['id'] != 1 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc']))){
    $activeMenu = 'cashier';
    $sectionName = 'Cashier';
    
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if(!empty($order_id)){
        $aOrder = $oOrder->getUnprocessedOrder($order_id);  
		if(!isset($_SESSION[CLIENT_ID]['cart'])){			 
			$_SESSION[CLIENT_ID]['cart'] = unserialize($aOrder['cart']);
		} 	
                $_SESSION[CLIENT_ID]['order_employee_id'] = $aOrder['employee_id'];
		if(empty($_SESSION[CLIENT_ID]['order_client'])){
			$_SESSION[CLIENT_ID]['order_client'] = $aOrder['order_client'];
			$_SESSION[CLIENT_ID]['temp_order_id'] = $order_id;
			$_SESSION[CLIENT_ID]['order_discount_type'] = $aOrder['order_discount_type'];
			$_SESSION[CLIENT_ID]['order_discount_amt'] = $aOrder['order_discount_amt'];
			$_SESSION[CLIENT_ID]['order_discount_percent'] = $aOrder['order_discount_percent'];
			$_SESSION[CLIENT_ID]['discount_reason'] = $aOrder['discount_reason'];
		}
    }
    
    if(isset($_GET['clear_cart'])){
        $oOrder->clearCart();
        if(isset($_SESSION[CLIENT_ID]['temp_order_id'])){
            $oOrder->deleteUnprocessedOrder($_SESSION[CLIENT_ID]['temp_order_id']);
            unset($_SESSION[CLIENT_ID]['temp_order_id']);
        }
        header("Location: cashier.php"); die;
    }
}else{
    $activeMenu = 'pos';
    $sectionName = 'POS';
    
    $isCashier = $oUser->isWorkingCashier();
    
    if($isCashier){
        if(!isset($_SESSION[CLIENT_ID]['cart']) or empty($_SESSION[CLIENT_ID]['cart'])){
            header("Location: pos.php");
        }
    }
    
    if(isset($_GET['clear_cart'])){
        $oOrder->clearCart();
        header("Location: pos.php"); die;
    }
}


if(isset($_GET['round'])){
    if($_GET['round'] === 'floor'){
        $oOrder->roundTotal('floor');        
    }else{
        $oOrder->roundTotal('ceil');
    }
    header("Location: pos_checkout.php"); die;
}

if(isset($_POST['sent'])){
    $oOrder->clearCart();
    if(isset($_POST['cartItems'])){
        $oOrder->addCartItem($_POST['cartItems']);
    }
    //sales discount
    if(!empty($_POST['salesDiscount'])){
        $_SESSION[CLIENT_ID]['order_discount_type'] = 2;
        $_SESSION[CLIENT_ID]['order_discount_percent'] = $_POST['salesDiscountValue'];
        $total = 0;
        if(isset($_SESSION[CLIENT_ID]['cart'])) foreach($_SESSION[CLIENT_ID]['cart'] as $item){
            if($item['modifiers']){
                foreach($item['modifiers'] as  $k => $mod){
                    foreach($mod as $altname=>$alt){
                        if($alt['qty'] == 0){
                            continue;
                        }
                        $total+=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                    }
                }
            }
        }
        $_SESSION[CLIENT_ID]['order_discount_amt'] = $_POST['salesDiscountValue']*$total/100;
        $_SESSION[CLIENT_ID]['discount_reason'] = $_POST['salesDiscountReason'];
    }
    header("Location: pos_checkout.php"); die;
}

if(isset($_POST['sent_discount'])){ //dump($_POST);die;
    $oOrder->addCartDiscount($_POST);
    header("Location: pos_checkout.php"); die;
}

if(isset($_SESSION[CLIENT_ID]['next_patient'])){
    $_SESSION[CLIENT_ID]['order_client'] = $_SESSION[CLIENT_ID]['next_patient'];
}

if(!empty($_SESSION[CLIENT_ID]['order_client'])){
    $aPatient = $oPatient->get_patient($_SESSION[CLIENT_ID]['order_client']);
	if($aPatient['vip_discount']){
        $_SESSION[CLIENT_ID]['order_discount_type'] = 2;
        $_SESSION[CLIENT_ID]['order_discount_percent'] = 10;
        $total = 0;
        if(isset($_SESSION[CLIENT_ID]['cart'])) foreach($_SESSION[CLIENT_ID]['cart'] as $item){
            if($item['modifiers']){
                foreach($item['modifiers'] as  $k => $mod){
                    foreach($mod as $altname=>$alt){
                        if($alt['qty'] == 0){
                            continue;
                        }
                        $total+=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                    }
                }
            }
        }
        $_SESSION[CLIENT_ID]['order_discount_amt'] = 10*$total/100;
        $_SESSION[CLIENT_ID]['discount_reason'] = 'vip or senior client';
    }
}

$allow_open_cashdrawer = false;
if(isset($_POST['cd_pass'])){
    $cashdrawer_password = settings::get('cashdrawer_password');
    if(md5($_POST['cd_pass']) === $cashdrawer_password){
        $allow_open_cashdrawer = true;
    }
}

//max amount that can be paid with rewards
$maxRewardsAllowed = 0;
if(!empty($_SESSION[CLIENT_ID]['cart']) and !empty($aPatient)){
    foreach($_SESSION[CLIENT_ID]['cart'] as $item_id=>$item){
        $aProduct = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($item_id)."'");
        if($item['modifiers']){
            foreach($item['modifiers'] as $mod_id=>$modifier){
                foreach($modifier as $altname=>$alt){
                    if(!empty($alt['comp'])){
                        $calculated_price = 0;
                    }else{
                        $calculated_price = ($alt['price'] - $alt['discount_amt'])*(1 - $_SESSION[CLIENT_ID]['order_discount_percent']/100);
                    }
                    if(!$aProduct['dont_allow_rewards']){
                        $maxRewardsAllowed+= $calculated_price*$alt['qty'];
                    }
                }
            }
        }
    }    
    if($aPatient['rewards'] < $maxRewardsAllowed){
        $maxRewardsAllowed = $aPatient['rewards'];
    }
}

$totalDiscount = isset($_SESSION[CLIENT_ID]['order_discount_amt']) ? floatval($_SESSION[CLIENT_ID]['order_discount_amt']) : 0;

if(!empty($isCashier) and $_SESSION[CLIENT_ID]['user_superclinic']['role'] != 4){
	if(isset($_SESSION[CLIENT_ID]['cart'])){
            //sales discount
            if(!empty($_POST['salesDiscount'])){
                $_SESSION[CLIENT_ID]['order_discount_type'] = 2;
                $_SESSION[CLIENT_ID]['order_discount_percent'] = $_POST['salesDiscountValue'];
                $total = 0;
                foreach($_SESSION[CLIENT_ID]['cart'] as $item){
                    if($item['modifiers']){
                        foreach($item['modifiers'] as  $k => $mod){
                            foreach($mod as $altname=>$alt){
                                if($alt['qty'] == 0){
                                    continue;
                                }
                                $total+=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                            }
                        }
                    }
                }
                $_SESSION[CLIENT_ID]['order_discount_amt'] = $_POST['salesDiscountValue']*$total/100;
                $_SESSION[CLIENT_ID]['discount_reason'] = $_POST['salesDiscountReason'];
            }            
            $orderNum = $oOrder->addUnprocessedOrder($_SESSION[CLIENT_ID]['cart'], isset($_SESSION[CLIENT_ID]['order_client']) ? $_SESSION[CLIENT_ID]['order_client'] : 0, $_SESSION[CLIENT_ID]['user_superclinic']['id'], isset($_SESSION[CLIENT_ID]['order_discount_amt']) ? $_SESSION[CLIENT_ID]['order_discount_amt'] : 0, isset($_SESSION[CLIENT_ID]['order_discount_percent']) ? $_SESSION[CLIENT_ID]['order_discount_percent'] : 0, isset($_SESSION[CLIENT_ID]['order_discount_type']) ? $_SESSION[CLIENT_ID]['order_discount_type'] : 1, isset($_SESSION[CLIENT_ID]['discount_reason']) ? $_SESSION[CLIENT_ID]['discount_reason'] : '');
            if(isset($_SESSION[CLIENT_ID]['order_client'])){
                $oPatient->delete_from_queue($_SESSION[CLIENT_ID]['order_client']);				
            }
    }else{
        $orderNum = 0;
    }
    $oOrder->clearCart();
    include '../templates/POS/pos_checkout_sent_tpl.php';
}else{ 
    $aCheckoutGoods = $oInventory->get_checkout_items();
    include '../templates/POS/pos_checkout_tpl.php';
}
?>