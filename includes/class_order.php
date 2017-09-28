<?php
define('ORDER_STATUS_OPEN', 1);
define('ORDER_STATUS_COMPLETED', 2);
define('ORDER_STATUS_DELIVERED', 3);

class class_order extends class_inventory{

    public function __construct() {
        parent::__construct();
    }
    
    function addCartItem($aData){ //dump($aData);die;
        if(!empty($aData) and is_array($aData)){
            global $aAlternativeWeights;
            $roundingDiscountReason = '';
            $multipleDiscountReason = '';
            foreach($aData as $k=>$v){
                $item = db::get_row("SELECT id, name, image, discount_start, discount_end, discount_type, discount_value FROM ".PREF."goods WHERE id = '".intval($k)."'");
                if($item){
                    foreach($v as $key=>$val){ 
                        $modifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE id = '".intval($key)."'");
                        if($modifier){

                            //calculate qty of already added product
                            $addedQty = 0;
                            if(isset($_SESSION[CLIENT_ID]['cart'][$k]['modifiers'][$key])){
                                foreach($_SESSION[CLIENT_ID]['cart'][$k]['modifiers'][$key] as $altn => $prod){
                                    if(isset($aAlternativeWeights[$altn]['quantity'])){
                                        $mult = $aAlternativeWeights[$altn]['quantity'];
                                    }else{
                                        $mult = 1;
                                    }
                                    $addedQty+= $prod['qty']*$mult;
                                }
                            }
                            if(isset($val['params'])){
                                $aParams = $val['params'];
                                unset($val['params']);
                            }else{
                                $aParams = false;
                            }
                            foreach($val as $altname =>$altval){
                                if($altname != 'rounded'){
                                    if(isset($aAlternativeWeights[$altname]['quantity'])){
                                        $mult = $aAlternativeWeights[$altname]['quantity'];
                                    }else{
                                        $mult = 1;
                                    }
                                    $roundingDiscount = 0;
                                    $addingQty = $altval*$mult + $addedQty;
                                    if($addingQty > $modifier['in_stock']){
                                        continue;
                                    }                                
                                    if(isset($_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname])){
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['qty']+=$altval;
                                    }else{                               
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['id'] = $item['id'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['name'] = $item['name'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['image'] = $item['image'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['id'] = $modifier['id'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['name'] = $modifier['name'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['alt'] = $altname;
                                        if($altname != 'default'){
                                            if($altname != 'other'){
                                                $pr = $modifier['price_'.$altname];
                                            }else{
                                                global $oInventory;
                                                $pr = $oInventory->getOtherPrice($modifier['id'], $altval);
                                                if(!empty($val['rounded'])){
                                                    $nearestUnit = $oInventory->get_nearest_unit(floatval($altval));
                                                    if($nearestUnit['code'] == 'default'){
                                                        $nearestUnitPrice = $modifier['price'];
                                                    }else{
                                                        if(isset($modifier['price_'.$nearestUnit['code']])){
                                                            $nearestUnitPrice = $modifier['price_'.$nearestUnit['code']];
                                                        }else{
                                                            $nearestUnitPrice = 0;
                                                        }  
                                                    }
                                                    if($altval != 0){
							$itemPrice = $nearestUnitPrice/$altval;
						    }else{
							$itemPrice = 0;
						    }  
                                                    $roundingDiscount = $pr-$itemPrice;
                                                    $roundingDiscountReason.= 'Rounded to '.$nearestUnit['name']."\r\n";
                                                }
                                            }
                                        }else{
                                            $pr = $modifier['price'];
                                            if(!empty($modifier['pricemultiple']) and $altval > 1){
                                                $multipleDiscount = $modifier['price']-$modifier['pricemultiple'];
                                                $multipleDiscountReason.= 'Multiple '.$item['name'].' sale'."\r\n";
                                            }else{                                                
                                                $multipleDiscount = 0;
                                            }
                                        }
                                        
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['price'] = $pr;
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['allow_comp'] = $modifier['allow_comp'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['quantity'] = $modifier['quantity'];
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['qty'] = $altval; //dump($roundingDiscount);die;
                                        if(!empty($aParams)){
                                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['params'] = $aParams;
                                        }
                                        if($roundingDiscount){
                                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_percent'] = 0;
                                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_amt'] = $roundingDiscount;
                                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_type'] = 1;                                            
                                        }else{
                                            if($multipleDiscount){
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_percent'] = 0;
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_amt'] = $multipleDiscount;
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_type'] = 1;
                                            }else{
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_percent'] = isset($aData['itemDiscountPercent'][$k][$key][$altname]) ? $aData['itemDiscountPercent'][$k][$key][$altname] : 0;
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_amt'] = isset($aData['itemDiscountAmt'][$k][$key][$altname]) ? $aData['itemDiscountAmt'][$k][$key][$altname] : 0;
                                                $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['discount_type'] = isset($aData['itemDiscountType'][$k][$key][$altname]) ? $aData['itemDiscountType'][$k][$key][$altname] : 2;
                                            }
                                        }
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['comp'] = isset($modifier['comp']) ? 1 : 0;
                                        $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$modifier['id']][$altname]['comp_reason'] = @$aData['comp_reason'][$k][$key];
                                    }
                                }
                            }
                        }
                    }
                    if(!empty($item['discount_start']) and !empty($item['discount_end']) and !empty($item['discount_value'])){
                        $dscstart = $item['discount_start'];
                        $aDscEnd = getdate($item['discount_end']);
                        $dscend = mktime('23', '59', '59', $aDscEnd['mon'], $aDscEnd['mday'], $aDscEnd['year']);
                        if($this->load_time >= $dscstart and $this->load_time <= $dscend){
                            $aDiscount = array();
                            foreach($v as $key=>$val){
                                foreach($val as $altname =>$altval){
                                    $aDiscount['discount'][$k][$key][$altname] = $item['discount_value'];
                                    $aDiscount['discount_type'][$k][$key][$altname] = $item['discount_type'];
                                }
                            }
                            //$aDiscount['discount_reason'] = 'discount of the day';
                            $discount_day = 'discount of the day';
                            $this->addCartDiscount($aDiscount);
                        }
                    }                   
                    
                }
            }
            $_SESSION[CLIENT_ID]['order_discount_amt'] = @$aData['order_discount_amt'];
            $_SESSION[CLIENT_ID]['order_discount_percent'] = @$aData['order_discount_percent'];
            $_SESSION[CLIENT_ID]['order_discount_type'] = @$aData['order_discount_type'];
            $_SESSION[CLIENT_ID]['discount_reason'] = $roundingDiscountReason.$multipleDiscountReason.@$aData['discount_reason'].(isset($discount_day) ? ("\r\n".$discount_day) : '');
            return "ok";
        }else{
            return "Wrong cart data format";
        }
    }
    
    function addCartDiscount($aData){ //dump($aData);die;
        $total = 0;
        //comp  
        foreach($_SESSION[CLIENT_ID]['cart'] as $i=>$item){
            foreach($item['modifiers'] as $m=>$mod){
                foreach($mod as $altname=>$alt){
                    $_SESSION[CLIENT_ID]['cart'][$i]['modifiers'][$m][$altname]['comp'] = 0;
                    $_SESSION[CLIENT_ID]['cart'][$i]['modifiers'][$m][$altname]['comp_reason'] = '';
                }
            }
        }
        if(isset($aData['comp']) and is_array($aData['comp'])) foreach($aData['comp'] as $item=>$d){
            foreach($d as $mod=>$val){ 
                foreach($val as $altname=>$altval){
                    $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['comp'] = 1;
                    $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['comp_reason'] = $aData['comp_reason'][$item][$mod][$altname];
                    $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_amt'] = 0;
                    $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_percent'] = 0;
                }
            }
        }
        if(isset($aData['discount']) and is_array($aData['discount'])) foreach($aData['discount'] as $item=>$d){
            foreach($d as $mod=>$val){ 
                foreach($val as $altname=>$altval){
                    if(@$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price'] > 0 and !@$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['comp']){
                        if($aData['discount_type'][$item][$mod][$altname] == 1){
                            if($altval > $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price']){
                                $altval = $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price'];
                            }
                            $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_amt'] = $altval;
                            $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_percent'] = round($altval/$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price']*100, 2);
                        }else{
                            if($altval > 100){
                                $altval = 100;
                            }
                            $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_percent'] = $altval;
                            $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_amt'] = round($altval*$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price'] / 100, 2);
                        }            
                        $_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_type'] = $aData['discount_type'][$item][$mod][$altname];
                        $total+=$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['qty']*($_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['price']-$_SESSION[CLIENT_ID]['cart'][$item]['modifiers'][$mod][$altname]['discount_amt']);
                    }
                }
            }
        }
        if($total > 0){
            if(isset($aData['order_discount_type']) and $aData['order_discount_type'] == 1){
                if($aData['order_discount'] > $total){
                    $aData['order_discount'] = $total;
                }
                $_SESSION[CLIENT_ID]['order_discount_amt'] = $aData['order_discount'];
                $_SESSION[CLIENT_ID]['order_discount_percent'] = round($aData['order_discount']/$total*100,2);
            }else{
                if(@$aData['order_discount'] > 100){                
                    $aData['order_discount'] = 100;
                }
                $_SESSION[CLIENT_ID]['order_discount_percent'] = isset($aData['order_discount']) ? $aData['order_discount'] : 0;
                $_SESSION[CLIENT_ID]['order_discount_amt'] = isset($aData['order_discount']) ? $aData['order_discount']*$total/100 : 0;
            }
            $_SESSION[CLIENT_ID]['order_discount_type'] = isset($aData['order_discount_type']) ? $aData['order_discount_type'] : 1;
            $_SESSION[CLIENT_ID]['discount_reason'] = isset($aData['discount_reason']) ? $aData['discount_reason'] : '';
        }
    }
    
    function roundTotal($type){        
        if($type === 'floor'){
            $func = 'floor';
        }else{
            $func = 'ceil';
        }
        if(!empty($_SESSION[CLIENT_ID]['cart'])){
            $total = 0;
            foreach($_SESSION[CLIENT_ID]['cart'] as $item_id=>$item){
                if(!empty($item['modifiers'])){
                    foreach($item['modifiers'] as $mod_id=>$modifier){
                        foreach($modifier as $altname=>$alt){
                            $total+= ($alt['price'] - $alt['discount_amt'])*(1 - $_SESSION[CLIENT_ID]['order_discount_percent']/100)*$alt['qty'];
                        }
                    }
                }
            }
            $roundedTotal = $func($total);
            $delta = $total - $roundedTotal;
            if(!empty($_SESSION[CLIENT_ID]['order_discount_amt']) or !empty($_SESSION[CLIENT_ID]['order_discount_percent'])){
                $currAmt = $_SESSION[CLIENT_ID]['order_discount_amt'];
                $_SESSION[CLIENT_ID]['order_discount_amt']+=$delta;
                $newPercent = $_SESSION[CLIENT_ID]['order_discount_amt']*$_SESSION[CLIENT_ID]['order_discount_percent']/$currAmt;
                $_SESSION[CLIENT_ID]['order_discount_percent'] = $newPercent;
            }else{
                foreach($_SESSION[CLIENT_ID]['cart'] as $item_id=>$item){
                    if(!empty($item['modifiers'])){
                        foreach($item['modifiers'] as $mod_id=>$modifier){
                            foreach($modifier as $altname=>$alt){
                                if($delta != 0 and $alt['qty'] >0){
                                    $price_correction = $delta/$alt['qty']; 
                                    if( $func == 'floor'){
                                        if($_SESSION[CLIENT_ID]['cart'][$item_id]['modifiers'][$mod_id][$altname]['price'] >= $price_correction){
                                            $_SESSION[CLIENT_ID]['cart'][$item_id]['modifiers'][$mod_id][$altname]['price']-= $price_correction; 
                                            $delta = 0;
                                            break;
                                        }else{                                
                                            $delta = ($price_correction - $_SESSION[CLIENT_ID]['cart'][$item_id]['modifiers'][$mod_id][$altname]['price'])*$alt['qty'];
                                            $_SESSION[CLIENT_ID]['cart'][$item_id]['modifiers'][$mod_id][$altname]['price']=0;
                                        }
                                    }else{
                                        $_SESSION[CLIENT_ID]['cart'][$item_id]['modifiers'][$mod_id][$altname]['price']-= $price_correction;
                                        $delta = 0;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return;
    }
    
    function clearCart(){
        unset($_SESSION[CLIENT_ID]['cart']);
        unset($_SESSION[CLIENT_ID]['order_discount_amt']);
        unset($_SESSION[CLIENT_ID]['order_discount_percent']);
        unset($_SESSION[CLIENT_ID]['order_discount_type']);
        unset($_SESSION[CLIENT_ID]['order_client']);
        unset($_SESSION[CLIENT_ID]['discount_reason']);
        unset($_SESSION[CLIENT_ID]['cash_given']);
        unset($_SESSION[CLIENT_ID]['delivery']);
    }
    
    function postOrder($user, $cash, $rewards = 0, $delivery=0, $cc=0){
        if($cash >= 0){
            if(!empty($_SESSION[CLIENT_ID]['cart'])){
                $tax = settings::get('tax_amount');
                if(!$tax){
                    $tax=0;
                }
                $tax_mode = settings::get('tax_mode');
                if(!$tax_mode){
                    $tax_mode=0;
                }
                //dump($_SESSION[CLIENT_ID]['cart']);die;
                if(!empty($_SESSION[CLIENT_ID]['order_client'])){
                    $aPatient = db::get_row("SELECT * FROM ".PREF."patients WHERE id = '".intval($_SESSION[CLIENT_ID]['order_client'])."' ");
                    if(isset($aPatient['rewards'])){
                        $patientRewards = $aPatient['rewards'];
                    }else{
                        $patientRewards = 0;
                    }
                    $rewards_amount_needed = settings::get('rewards_amount_needed');
                    $rewards_receive = settings::get('rewards_receive');                        
                }
                $params = array($_SESSION[CLIENT_ID]['discount_reason']);
                $status = ORDER_STATUS_COMPLETED;
                $address = $token = $lat = $lng = $appointment = '';
                if($delivery && !empty($_SESSION[CLIENT_ID]['delivery'])) {
                	$status = ORDER_STATUS_OPEN;
	                $address = $_SESSION[CLIENT_ID]['delivery']['address'];
	                $geocode = geocode($address);
	                if($geocode) {
	                	$lat = $geocode['lat'];
	                	$lng = $geocode['lng'];
	                }
	                $appointment = $_SESSION[CLIENT_ID]['delivery']['appointment'];
	                $token = md5(uniqid());
                }
                $params[] = $address;
                $params[] = $appointment;
                db::query("INSERT INTO ".PREF."orders SET
                               user_id = '".intval($user)."',
                               date = '".$this->load_time."',
                               discount_type = '".intval($_SESSION[CLIENT_ID]['order_discount_type'])."',
                               discount_amt = '".round(floatval($_SESSION[CLIENT_ID]['order_discount_amt']),2)."',
                               discount_percent = '".round(floatval($_SESSION[CLIENT_ID]['order_discount_percent']),2)."',
                               discount_reason = ~~,
                               cc = '".intval($cc)."',
                               delivery = '".intval($delivery)."',
                               delivery_token = '".$token."',
                               status = ".$status.",
                               address = ~~,
                               appointment_time = ~~,
                               lat = ".floatval($lat).",
                               lng = ".floatval($lng).",
                               client_id = '".(isset($_SESSION[CLIENT_ID]['order_client']) ? intval($_SESSION[CLIENT_ID]['order_client']) : '0')."'", 
                               $params);
                $orderId = db::get_last_id();
                if($orderId){                    
                    $itmCount = 0;
                    $orderAmt = 0;
                    $maxRewardsAllowed = 0;
                    global $aAlternativeWeights;
                    foreach($_SESSION[CLIENT_ID]['cart'] as $item_id=>$item){
                        $aProduct = db::get_row("SELECT * FROM ".PREF."goods WHERE id = '".intval($item_id)."'");
                        if($item['modifiers']){
                            foreach($item['modifiers'] as $mod_id=>$modifier){
                                $aModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE id = '".intval($mod_id)."'");                                
                                foreach($modifier as $altname=>$alt){
                                    if($altname == 'default' or $altname == 'other'){
                                        $multiplier = 1;
                                    }else{
                                        $multiplier = $aAlternativeWeights[$altname]['quantity'];
                                    }
                                    $defQTY = $alt['qty']*$multiplier;
                                    if($defQTY > $aModifier['in_stock']){
                                       $q = $aModifier['in_stock'];
                                    }else{
                                       $q = $defQTY;
                                    } 
                                    if($q > 0){                                    
                                        $cat_id = db::get_one("SELECT cat_id FROM ".PREF."goods WHERE id = '".intval($item_id)."'");
                                        $qty_in_stock = $alt['quantity']*$q;                                    
                                        if(!empty($alt['comp'])){
                                            $calculated_price = 0;
                                            $alt['discount_amt'] = 0;
                                            $alt['discount_percent'] = 0;
                                        }else{
                                            $calculated_price = ($alt['price'] - $alt['discount_amt'])*(1 - $_SESSION[CLIENT_ID]['order_discount_percent']/100);
                                        }
                                        $orderAmt+=$calculated_price*$alt['qty'];
                                        if(!$aProduct['dont_allow_rewards']){
                                            $maxRewardsAllowed+= $calculated_price*$alt['qty'];
                                        }
                                        db::query("INSERT INTO ".PREF."orders_items SET
                                                   order_id = '".$orderId."',
                                                   user_id = '".intval($user)."',
                                                   goods_item_id = '".intval($item_id)."',
                                                   cat_id = '".intval($cat_id)."',
                                                   date = '".$this->load_time."',
                                                   goods_item_name = ~~,
                                                   modifier_name = ~~,
                                                   modifier_id = '".intval($mod_id)."',
                                                   alt = ~~,    
                                                   qty = '".floatval($alt['qty'])."',
                                                   qty_in_stock = '".floatval(round($qty_in_stock,2))."',
                                                   purchase_price = '".round(floatval($aModifier['purchase_price']),2)."',
                                                   original_price = '".round(floatval($alt['price']),2)."',
                                                   price = '".floatval($calculated_price)."',
                                                   item_discount_type = '".intval($alt['discount_type'])."',
                                                   item_discount_amt = '".round(floatval($alt['discount_amt']),2)."',
                                                   item_discount_percent = '".round(floatval($alt['discount_percent']),2)."',
                                                   comp = '".(!empty($alt['comp']) ? 1 : 0)."',
                                                   comp_reason = ~~,
                                                   delivery = '".intval($delivery)."'",
                                                   array($item['name'], $alt['name'], $altname, $alt['comp_reason']));
                                        $last_id = db::get_last_id();                                
                                        if($last_id){
                                            $itmCount++;                                    
                                            db::query("UPDATE ".PREF."goods_modifiers SET in_stock = in_stock - ".floatval($qty_in_stock)." WHERE id = '".intval($mod_id)."'");
                                            db::query("UPDATE ".PREF."goods SET in_stock = in_stock - ".floatval($qty_in_stock)." WHERE id = '".intval($item_id)."'");
                                            
                                            if(isset($alt['params']) and !empty($alt['params'])){
                                                foreach($alt['params'] as $pid => $param){
                                                    if($param['qty'] > 0){
                                                        db::query("INSERT INTO ".PREF."orders_params SET
                                                                    param_id = '".intval($pid)."',
                                                                    order_id = '".intval($orderId)."',
                                                                    order_item_id = '". $last_id."',
                                                                    param_name = ~~,
                                                                    qty = '".intval($param['qty'])."',
                                                                    goods_item_id = '".intval($item_id)."',
                                                                    modifier_id = '".intval($mod_id)."'
                                                               ", array($param['name']));
                                                    }
                                                    db::query("UPDATE ".PREF."goods_params SET qty = qty - ".intval($param['qty'])." WHERE id = '".intval($pid)."' AND goods_item_id = '".intval($item_id)."'");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }                    
                    if(!$itmCount){//remove order body if some errors occured
                        db::query("DELETE FROM ".PREF."orders WHERE id = '".intval($orderId)."'");
                        return "Order items creating error";
                    }else{
                        //rewards processing                        
                        if(!empty($aPatient['id'])){
                            if($maxRewardsAllowed > $patientRewards){
                                $maxRewardsAllowed = $patientRewards;
                            }
                            if($maxRewardsAllowed > 0 and $maxRewardsAllowed >= $rewards){
                                db::query("UPDATE ".PREF."orders SET paid_rewards = '".floatval($rewards)."' WHERE id = '".intval($orderId)."'");
                                db::query("UPDATE ".PREF."patients SET rewards = rewards-".floatval($rewards)." WHERE id = '".intval($aPatient['id'])."'");
                            }
                            $paid_money = $orderAmt-$rewards;
                            if($rewards_amount_needed > 0){                                
                                $patientSpentAmt = $aPatient['spent_amount'];
                                $patientSpentAmt+=$paid_money;
                                $numRewardedAmounts = floor($patientSpentAmt/$rewards_amount_needed);
                                if($numRewardedAmounts >= 1){
                                    $points = $numRewardedAmounts*$rewards_receive;
                                    $patientSpentAmt = $patientSpentAmt-$rewards_amount_needed*$numRewardedAmounts - floatval($rewards);
                                    db::query("UPDATE ".PREF."patients SET rewards = rewards + ".floatval($points).", spent_amount = '".floatval($patientSpentAmt)."' WHERE id = '".intval($aPatient['id'])."'");
                                }else{
                                    db::query("UPDATE ".PREF."patients SET spent_amount = '".floatval($patientSpentAmt)."' WHERE id = '".intval($aPatient['id'])."'");
                                }
                            }else{
                                db::query("UPDATE ".PREF."patients SET spent_amount = spent_amount + '".floatval($paid_money)."' WHERE id = '".intval($aPatient['id'])."'");
                            }
                        }
                        //tax adding
                        if($tax_mode > 0){
                            $taxVal = $orderAmt*$tax/100;                           
                            $orderAmt+=$taxVal;
                        }else{
                            $taxVal = $orderAmt - round($orderAmt/(1+$tax/100),2);                            
                        }
                        db::query("UPDATE ".PREF."orders SET tax = '".floatval($taxVal)."', tax_mode = '".intval($tax_mode)."' WHERE id = '".intval($orderId)."'");
                    }
                }else{
                    return "Order creating error";
                }
                $this->set_current_instock();
                $cash_back = $cash - round($orderAmt,2) + $rewards;
                return array('cash_back'=>$cash_back, 'order_id'=>$orderId);
            }else{
                return "Order is empty";
            }
        }else{
            return "Wrong cash amount given";
        }        
    }
    
    function getOrder($order_id, $search=''){
        $aOrder = db::get_row("SELECT ".PREF."orders.*, ".PREF."users.firstname AS employeeFirstName, ".PREF."users.lastname AS employeeLastName
                               FROM ".PREF."orders
                               LEFT JOIN ".PREF."users ON ".PREF."orders.user_id = ".PREF."users.id
                               WHERE ".PREF."orders.id = $order_id");
        if($aOrder){
            if(!empty($aOrder['client_id'])){
                $aClient = db::get_row("SELECT * FROM ".PREF."patients WHERE id = '".intval($aOrder['client_id'])."'");
                $aOrder['client_firstname'] = $aClient['firstname'];
                $aOrder['client_lastname'] = $aClient['lastname'];
            }else{
                $aOrder['client_firstname'] = '';
                $aOrder['client_lastname'] = '';
            }
            if(!empty($search)){
                $search_sql = "AND goods_item_name LIKE '%".addslashes(db::clear($search))."%'";
            }else{
                $search_sql = '';
            }
            $aItems = db::get("SELECT ".PREF."orders_items.* , (original_price-price)*qty AS d FROM ".PREF."orders_items WHERE order_id = '".intval($order_id)."' $search_sql");
            $orderTotal = 0;
            $compReason = '';
            foreach($aItems as $itm){
                $orderTotal+= $itm['price']*$itm['qty'];
                $compReason.=$itm['comp_reason'].'<br />';
            }
            $aOrder['items'] = $aItems;
            $aOrder['total'] = $orderTotal;
            $aOrder['discount_reason'].=('<br />'.$compReason);
            return $aOrder;
        }else{
            return false;
        }
    }
    
    function getOrdersInfo($from, $to){
        $aOrders = db::get("SELECT ".PREF."orders.*, ROUND(orderitems.orderamount, 2) AS amount, ".PREF."patients.firstname AS client_firstname, ".PREF."patients.lastname AS client_lastname
                            FROM ".PREF."orders
                            LEFT JOIN ".PREF."patients ON ".PREF."orders.client_id = ".PREF."patients.id
                            LEFT JOIN (SELECT order_id, SUM(price*qty) AS orderamount FROM ".PREF."orders_items GROUP BY order_id) AS orderitems ON orderitems.order_id = ".PREF."orders.id
                            WHERE ".PREF."orders.date >= '".$from."' AND ".PREF."orders.date <= '".$to."'");
        if($aOrders){
            return $aOrders;
        }else{
            return false;
        }
    }
    
    function addUnprocessedOrder($cart, $patient_id, $employee_id=0, $order_discount_amt=0, $order_discount_percent=0, $order_discount_type=1, $discount_reason=''){
        $ok = db::query("INSERT INTO ".PREF."orders_temp SET
                        cart=~~,
                        order_client = '".intval($patient_id)."',
                        employee_id = '".intval($employee_id)."',
                        order_date = '".$this->load_time."',
                        order_discount_amt = '".floatval($order_discount_amt)."',
                        order_discount_percent = '".floatval($order_discount_percent)."',
                        order_discount_type = '".intval($order_discount_type)."',
                        discount_reason = ~~
                  ", array(serialize($cart), htmlspecialchars($discount_reason)));
        $orderNum = db::get_last_id();
        return $orderNum;
    }
    
    function getUnprocessedOrders(){
        $aOrders = db::get("SELECT * FROM ".PREF."orders_temp ORDER BY order_date ASC");
        foreach($aOrders as &$ord){
            if(!empty($ord['order_client'])){
                $ord['patient'] = db::get_row("SELECT firstname, lastname, midname, image_1 FROM ".PREF."patients WHERE id = '".intval($ord['order_client'])."'");
            }
        }
        return $aOrders;
    }
    
    function getUnprocessedOrder($id){
        $aOrder = db::get_row("SELECT * FROM ".PREF."orders_temp WHERE id = '".intval($id)."'");       
        return $aOrder;
    }
    
    function deleteUnprocessedOrder($id){
        db::query("DELETE FROM ".PREF."orders_temp WHERE id = '".intval($id)."'");  
        return true;
    }
    
    function getRecentOrders($num=3){
        $aOrders = db::get("SELECT * FROM ".PREF."orders WHERE ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." ORDER BY date DESC LIMIT ".intval($num));
        return $aOrders;
    }
    
    function getTodaysOrders(){
        $ct = getdate();
        $date_start = mktime(0, 0, 0, $ct['mon'], $ct['mday'], $ct['year']);
        $date_end = mktime(23, 59, 59, $ct['mon'], $ct['mday'], $ct['year']);        
        $aOrders = db::get("SELECT * FROM ".PREF."orders WHERE date >= '".$date_start."' AND date <= '".$date_end."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." ORDER BY date");
        return $aOrders;
    }
    
    function grossSales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        $amt = db::get_one("SELECT SUM(price*qty) FROM ".PREF."orders_items 
			LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
			WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function netSales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        //employee salary calculation (not finished, algorithm is needed)
        $worked_days =  ceil(($to - $from)/86400);
        //-----------------------------------------
        $amt = db::get_one("SELECT SUM((price-purchase_price)*qty) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function getRewardsPaid($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $amt = db::get_one("SELECT SUM(paid_rewards) FROM ".PREF."orders WHERE date >= '".$from."' AND date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function deliverySalesCount($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        $count = db::get_one("SELECT COUNT(*) FROM ".PREF."orders WHERE delivery = 1 AND date >= '".$from."' AND date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $count;
    }
    
    function grossDeliverySales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        $amt = db::get_one("SELECT SUM(price*qty) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders.delivery = 1 AND ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function netDeliverySales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        //employee salary calculation (not finished, algorithm is needed)
        $worked_days =  ceil(($to - $from)/86400);
        //-----------------------------------------
        $amt = db::get_one("SELECT SUM((price-purchase_price)*qty) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders.delivery = 1 AND ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function ccSalesCount($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        $count = db::get_one("SELECT COUNT(*) FROM ".PREF."orders WHERE cc = 1 AND date >= '".$from."' AND date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $count;
    }
    
    function grossCCSales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        $amt = db::get_one("SELECT SUM(price*qty) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders.cc = 1 AND ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function netCCSales($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        //employee salary calculation (not finished, algorithm is needed)
        $worked_days =  ceil(($to - $from)/86400);
        //-----------------------------------------
        $amt = db::get_one("SELECT SUM((price-purchase_price)*qty) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders.cc = 1 AND ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $amt;
    }
    
    function getEmployeesSales($user, $from, $to){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        $gross = $this->grossSales($from, $to, $user);
        $net = $this->netSales($from, $to, $user);
        
        return array('gross'=>$gross, 'net'=>$net);
    }
    
    function getNumServedPatients($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = " AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $patientsNum = db::get_one("SELECT COUNT(client_id) FROM ".PREF."orders WHERE ".PREF."orders.date >= '".$from."' AND ".PREF."orders.date <= '".$to."' ".$userSQL);
        return $patientsNum;        
    }
    
    function getServedPatients($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = " AND ".PREF."orders.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $aPatients = db::get("SELECT DISTINCT client_id, ".PREF."patients.firstname, ".PREF."patients.lastname FROM ".PREF."orders
                              LEFT JOIN ".PREF."patients ON ".PREF."patients.id = ".PREF."orders.client_id
                              WHERE ".PREF."orders.date >= '".$from."' AND ".PREF."orders.date <= '".$to."' ".$userSQL);
        return $aPatients;        
    }
    
    function getPatientsOrders($from, $to, $patient, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = " AND ".PREF."orders.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $aOrders = db::get("SELECT * FROM ".PREF."orders WHERE client_id = '".intval($patient)."' AND date >= '".$from."' AND date <= '".$to."' ".$userSQL);
        return $aOrders;
    }
    
    function getSoldItems($from, $to, $user=0, $ordby='', $ord='ASC', $search = ''){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        
        if(!empty($search)){
            $search_sql = "AND ".PREF."orders_items.goods_item_name LIKE '%".addslashes(db::clear($search))."%'";
        }else{
            $search_sql = "";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'q'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aSales = db::get("SELECT ".PREF."orders_items.id, ".PREF."orders_items.order_id, goods_item_name, goods_item_id, modifier_name, modifier_id, ".PREF."orders_items.date, ROUND(SUM(qty_in_stock),1) AS q, ROUND(SUM(price*qty),2) AS p, ROUND(SUM((original_price-price)*qty),2) AS d
            FROM ".PREF."orders_items
            LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
            WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL  $search_sql                         
            GROUP BY goods_item_id, modifier_id
            $order_sql");
        
        return $aSales;
    }
    
    function get_discount_reason($id){
        $reason = db::get_one("SELECT discount_reason FROM ".PREF."orders WHERE id = '".intval($id)."'");
        return $reason;
    }
    
    function getTopItems($from, $to, $type, $ordby='', $ord='ASC', $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        if($type == 'qty'){
            $typeSQL = "AND modifier_name = 'qty'";
        }else{
            $typeSQL = "AND modifier_name != 'qty'";
        }
        
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'q'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aSales = db::get("SELECT goods_item_name, goods_item_id, SUM(qty_in_stock) AS q, SUM(price*qty) AS gross, SUM((price-purchase_price)*qty) AS net FROM ".PREF."orders_items
                           WHERE date >= '".$from."' AND date <= '".$to."' $userSQL $typeSQL
                           GROUP BY goods_item_id
                           $order_sql");
        return $aSales;
    } 
    
    function getTopByCategory($from, $to, $cat_id, $ordby='', $ord='DESC', $user=0){
        global $aWeights, $aQTY;
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
               
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'q'; 
            $ord = 'DESC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aCategory = db::get_row("SELECT * FROM ".PREF."goods_categories WHERE id = '".intval($cat_id)."'");
        $aSales = db::get("SELECT goods_item_name, goods_item_id, ROUND(SUM(qty_in_stock),2) AS q, SUM(price*qty) AS gross, SUM((price-purchase_price)*qty) AS net 
        	FROM ".PREF."orders_items LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
            WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." AND cat_id = '".intval($cat_id)."' $userSQL
            GROUP BY goods_item_id
            $order_sql");
        if(!empty($aSales)){
            foreach($aSales as $k=>$itm){
                if($aCategory['measure_type'] == 1){
                    $aParams = $aWeights;
                }else{
                    $aParams = $aQTY;
                }
                foreach($aParams as $p){
                    $aSalesByMods[$p['name']] = db::get_one("SELECT SUM(qty) AS q FROM ".PREF."orders_items
                    	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                        WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."'
                        AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                        AND cat_id = '".intval($cat_id)."' $userSQL
                        AND goods_item_id = '".intval($itm['goods_item_id'])."'
                        AND modifier_name = ~~
                        ", array($p['name']));
                }
                foreach($aSalesByMods as $name=>$q){                    
                    $aSales[$k]['mods'][$name] = floatval($q);  
                    $aSales[$k]['q_'.$name] = floatval($q);                    
                }                
            }
        }
        return $aSales;
    }
    
    function get_item_info($id){
        return db::get_row("SELECT goods_item_name, cat_id FROM ".PREF."orders_items WHERE goods_item_id = '".intval($id)."' LIMIT 1");
    }
    
    function getEmployeeItemSales($user, $item, $from, $to){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        global $aWeights, $aQTY;        
        $aSales = db::get_row("SELECT goods_item_name, goods_item_id, ROUND(SUM(qty_in_stock),2) AS q, SUM(price*qty) AS gross, SUM((price-purchase_price)*qty) AS net FROM ".PREF."orders_items
                           WHERE date >= '".$from."' AND date <= '".$to."'                            
                           AND goods_item_id = '".intval($item)."'
                           AND user_id = '".intval($user)."'
                           GROUP BY goods_item_id");
        if(!empty($aSales)){
            $aProductInfo = $this-> get_item_info($item);
            $aCategory = db::get_row("SELECT * FROM ".PREF."goods_categories WHERE id = '".intval($aProductInfo['cat_id'])."'");            
            if($aCategory['measure_type'] == 1){
                $aParams = $aWeights;
            }else{
                $aParams = $aQTY;
            }
            foreach($aParams as $p){
                $aSalesByMods[$p['name']] = db::get_one("SELECT SUM(qty) AS q FROM ".PREF."orders_items
                                                               WHERE date >= '".$from."' AND date <= '".$to."'
                                                               AND user_id = '".intval($user)."'
                                                               AND goods_item_id = '".intval($aSales['goods_item_id'])."'
                                                               AND modifier_name = ~~
                                                               ", array($p['name']));
            }
            foreach($aSalesByMods as $name=>$q){                    
                $aSales['mods'][$name] = floatval($q);  
                $aSales['q_'.$name] = floatval($q);                    
            }
        }
        return $aSales;
    }
    
    function getPatientsHistory($from, $to, $ordby='', $ord='ASC', $search=''){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'latestOrderDate'; 
            $ord = 'DESC';
        }
        if(!empty($search)){
            $search_sql = "AND (".PREF."patients.firstname LIKE '%".addslashes(db::clear($search))."%' OR
                                ".PREF."patients.lastname LIKE '%".addslashes(db::clear($search))."%' OR
                                CONCAT(".PREF."patients.firstname, ' ', ".PREF."patients.lastname) LIKE '%".addslashes(db::clear($search))."%')";
        }else{
            $search_sql = '';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        $aHistory = db::get("SELECT ".PREF."patients.id, COUNT(*) AS numOrders, ".PREF."patients.firstname, ".PREF."patients.lastname, MAX(".PREF."orders.date) AS latestOrderDate
                FROM ".PREF."orders
                LEFT JOIN ".PREF."patients ON ".PREF."patients.id = ".PREF."orders.client_id
                WHERE ".PREF."orders.date >= '".$from."' AND ".PREF."orders.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." AND ".PREF."orders.client_id != 0 $search_sql
                GROUP BY ".PREF."orders.client_id
                $order_sql");
        return $aHistory;        
    }
    
    function get_item_history($id){        
        $aSalesEmployee = db::get("SELECT DISTINCT(".PREF."orders_items.user_id) AS employee_id, ".PREF."users.firstname, ".PREF."users.lastname
                                   FROM ".PREF."orders_items
                                   LEFT JOIN ".PREF."users ON ".PREF."orders_items.user_id = ".PREF."users.id
                                   WHERE ".PREF."orders_items.goods_item_id = '".intval($id)."'
                                   ORDER BY `date`");
        
        $daysOfSales = db::get("SELECT DISTINCT(DATE_FORMAT(FROM_UNIXTIME(`date`),GET_FORMAT(DATE,'USA'))) AS day FROM ".PREF."orders_items WHERE goods_item_id = '".intval($id)."' ORDER BY `date`");
        $aIntervalByDays = array();
        foreach($daysOfSales as $k=>$day){
            $aDay = explode('.', $day['day']);
            $aIntervalByDays[$k]['start'] = mktime('0', '0', '0', $aDay[0], $aDay[1], $aDay[2]);
            $aIntervalByDays[$k]['end'] = mktime('23', '59', '59', $aDay[0], $aDay[1], $aDay[2]);
            $aIntervalByDays[$k]['day'] = str_replace('.', '/', $day['day']);
        }
        
        foreach($aIntervalByDays as $k => $d){
            $aEmployeeSales = array();
            $i = 0;
            foreach($aSalesEmployee as $e=>$empl){
                $aSales = $this->getEmployeeItemSales($empl['employee_id'], $id, $d['start'], $d['end']);   
                if($aSales){
                    $aEmployeeSales[$i] = $empl;
                    $aEmployeeSales[$i]['sales'] = $aSales;
                    $i++;
                }
            }
            $aIntervalByDays[$k]['employees'] = $aEmployeeSales;
        }
       
        return $aIntervalByDays;
    }
    
    function get_discounts($from, $to, $ordby='', $ord='ASC', $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'date'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        $aDiscounts = db::get("SELECT ".PREF."orders_items.order_id,
                                      ROUND(SUM(".PREF."orders_items.original_price*".PREF."orders_items.qty),2) AS originalAmt,
                                      ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2) AS soldAmt,
                                      ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2) - ROUND(SUM(".PREF."orders_items.purchase_price*".PREF."orders_items.qty),2) AS netProfit,
                                      SUM(ROUND(".PREF."orders_items.original_price*".PREF."orders_items.qty,2) - ROUND(".PREF."orders_items.price*".PREF."orders_items.qty,2)) AS discountAmt,
                                      ROUND((ROUND(SUM(".PREF."orders_items.original_price*".PREF."orders_items.qty),2) - ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2))/ROUND(SUM(".PREF."orders_items.original_price*".PREF."orders_items.qty),2)*100) AS discountPercent,
                                      ".PREF."users.firstname AS userFirstName,
                                      ".PREF."users.lastname AS userLastName,
                                      ".PREF."orders.discount_reason,
                                      ".PREF."orders.client_id,
                                      ".PREF."patients.firstname AS clientFirstName,
                                      ".PREF."patients.lastname AS clientLastName,
                                      ".PREF."orders.date
                              FROM ".PREF."orders_items
                              LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                              LEFT JOIN ".PREF."users ON ".PREF."orders_items.user_id = ".PREF."users.id
                              LEFT JOIN ".PREF."patients ON ".PREF."orders.client_id = ".PREF."patients.id
                              WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."'
                              AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                              AND (".PREF."orders.discount_amt != 0 OR ".PREF."orders_items.item_discount_amt != 0)
                              $userSQL
                              GROUP BY  ".PREF."orders_items.order_id
                              $order_sql");
        return $aDiscounts;
    }
    
    function get_discount_amt($from, $to, $user=0){       
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $discount = db::get_one("SELECT 
                                      SUM(ROUND(".PREF."orders_items.original_price*".PREF."orders_items.qty,2) - ROUND(".PREF."orders_items.price*".PREF."orders_items.qty,2)) AS discountAmt                                      
                                FROM ".PREF."orders_items
                                LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                                WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."'   
                                AND (".PREF."orders.discount_amt != 0 OR ".PREF."orders_items.item_discount_amt != 0) AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                                $userSQL");
        return $discount;
    }
    
    function get_comps($from, $to, $ordby='', $ord='ASC', $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        if($ordby and $ord){
            if($ord != 'ASC'){
                $ord = 'DESC';
            }            
        }else{
            $ordby = 'date'; 
            $ord = 'ASC';
        }
        $order_sql = "ORDER BY ".db::clear(str_replace(',', '', $ordby))." $ord";
        
        $aComps = db::get("SELECT ".PREF."orders_items.order_id,
                                  ".PREF."orders_items.id,
                                  ".PREF."orders_items.date,
                                  ".PREF."orders_items.goods_item_name,
                                  ".PREF."orders_items.modifier_name,
                                  ".PREF."orders_items.comp_reason,
                                  ".PREF."users.firstname AS userFirstName,
                                  ".PREF."users.lastname AS userLastName,
                                  ".PREF."orders_items.qty,
                                  orderTotal.total AS orderAmt
                          FROM ".PREF."orders_items
                          LEFT JOIN (SELECT order_id, ROUND(SUM(price*qty),2) AS total  FROM ".PREF."orders_items GROUP BY order_id) AS orderTotal  ON orderTotal.order_id = ".PREF."orders_items.order_id
                          LEFT JOIN ".PREF."users ON ".PREF."orders_items.user_id = ".PREF."users.id
                          LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                          WHERE ".PREF."orders_items.comp = 1
                          AND ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."'
                          AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                          $order_sql");
        return $aComps;
    }
    
    function getDiscountAmt($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders_items.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $amt = db::get_one("SELECT SUM(ROUND(".PREF."orders_items.original_price,2) - ROUND(".PREF."orders_items.price,2)) AS discountAmt                                      
                              FROM ".PREF."orders_items
                              LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id                              
                              WHERE ".PREF."orders_items.date >= '".$from."' AND ".PREF."orders_items.date <= '".$to."'   
                              AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                              AND (".PREF."orders.discount_amt != 0 OR ".PREF."orders_items.item_discount_amt != 0)
                              $userSQL
                              ");
        return $amt;
        
    }
    
    function getOrderNumber($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $num = db::get_one("SELECT COUNT(*) FROM ".PREF."orders WHERE date >= '".$from."' AND date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $num;
    }
    
    function getOrdersAmout($from, $to, $user=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($user){
            $userSQL = "AND ".PREF."orders.user_id = '".intval($user)."'";
        }else{
            $userSQL = "";
        }
        $sum = db::get_one("SELECT ROUND(SUM(price*qty),2) FROM ".PREF."orders_items 
        	LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
        	WHERE ".PREF."orders.date >= '".$from."' AND ".PREF."orders.date <= '".$to."' AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED." $userSQL");
        return $sum ? $sum : 0;
    }
    
    function getSalesByCategories($from, $to){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        $aResult = db::get("SELECT ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2) AS amt, ".PREF."orders_items.cat_id, ".PREF."goods_categories.name FROM ".PREF."orders_items
                            LEFT JOIN ".PREF."goods ON ".PREF."orders_items.goods_item_id = ".PREF."goods.id
                            LEFT JOIN ".PREF."goods_categories ON ".PREF."orders_items.cat_id = ".PREF."goods_categories.id
                            LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                            WHERE 1
                            AND ".PREF."orders_items.date >= '".$from."'
                            AND ".PREF."orders_items.date <= '".$to."'
                            AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                            GROUP BY ".PREF."orders_items.cat_id");
        return $aResult;
    }
    
    function getSalesByTypes($from, $to, $category=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($category){
            $catFilter = "AND ".PREF."orders_items.cat_id = '".intval($category)."'";
        }else{
            $catFilter = "";
        }
        $aResult = db::get("SELECT ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2) AS amt, ".PREF."goods.meds_type FROM ".PREF."orders_items
                            LEFT JOIN ".PREF."goods ON ".PREF."orders_items.goods_item_id = ".PREF."goods.id
                            LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                            WHERE ".PREF."goods.meds_type != 0
                            AND ".PREF."orders_items.date >= '".$from."'
                            AND ".PREF."orders_items.date <= '".$to."'
                            AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                            $catFilter
                            GROUP BY ".PREF."goods.meds_type");
        return $aResult;
    }
    
    function getSalesByVendors($from, $to, $category=0){
        if(!$from or !$to or !is_numeric($from) or !is_numeric($to)){
            return false;
        }
        if($category){
            $catFilter = "AND ".PREF."orders_items.cat_id = '".intval($category)."'";
        }else{
            $catFilter = "";
        }
        $aResult = db::get("SELECT ROUND(SUM(".PREF."orders_items.price*".PREF."orders_items.qty),2) AS amt,".PREF."vendors.name AS name  FROM ".PREF."orders_items
                            LEFT JOIN ".PREF."goods ON ".PREF."orders_items.goods_item_id = ".PREF."goods.id
                            LEFT JOIN ".PREF."vendors ON ".PREF."goods.vendor = ".PREF."vendors.id
                            LEFT JOIN ".PREF."orders ON ".PREF."orders_items.order_id = ".PREF."orders.id
                            WHERE ".PREF."goods.vendor != 0
                            AND ".PREF."goods.cat_id != 1
                            AND ".PREF."orders_items.date >= '".$from."'
                            AND ".PREF."orders_items.date <= '".$to."'
                            AND ".PREF."orders.status = ".ORDER_STATUS_COMPLETED."
                            $catFilter
                            GROUP BY ".PREF."goods.vendor");
        return $aResult;
    }
    
    function deleteOrder($order_id, $user_role){
        if($user_role == 1){
            db::query("DELETE FROM ".PREF."orders WHERE id = '".intval($order_id)."'");
            return "ok";
        }else{
            return false;
        }
    }
    
    function deleteOrders($from, $to, $user_role){
        if($user_role == 1){
            db::query("DELETE FROM ".PREF."orders WHERE date >= '".$from."' AND date <= '".$to."'");
            return "ok";
        }else{
            return false;
        }
    }
    
    function returnOrder($order_id, $user_id){
        if($user_id == 1){
            $aOrder = $this->getOrder($order_id);
            
            if(!empty($aOrder['items'])){
                foreach($aOrder['items'] as $item){
                    $modifier_exists = db::get_one("SELECT id FROM ".PREF."goods_modifiers WHERE goods_item_id = '".$item['goods_item_id']."' AND id = '".intval($item['modifier_id'])."'");
                    if($modifier_exists){
                        $returned_qty = $item['qty']*$item['qty_in_stock'];
                        db::query("UPDATE ".PREF."goods_modifiers
                                   SET in_stock = in_stock+".floatval($returned_qty)."
                                   WHERE id = '".intval($item['modifier_id'])."' AND goods_item_id = '".$item['goods_item_id']."'");
                        db::query("UPDATE ".PREF."goods
                                   SET in_stock = in_stock+".floatval($returned_qty)."
                                   WHERE id = '".$item['goods_item_id']."'");
                        $this->logReturn($item['goods_item_id'], $item['modifier_id'], $returned_qty, $user_id);
                    }
                }
            }
            $this->deleteOrder($order_id, 1);
            return "ok";
        }else{
            return false;
        }
    }
    
    function logReturn($item_id, $modifier_id, $value, $user_id){
        $aItem = db::get_row("SELECT id, cat_id, name FROM ".PREF."goods WHERE id = '".intval($item_id)."'");
        $cat_name = db::get_one("SELECT name FROM ".PREF."goods_categories WHERE id = '".intval($aItem['cat_id'])."'");
        db::query("INSERT INTO ".PREF."return_history SET
                               user_id = '".intval($user_id)."',
                               item_id = '".intval($item_id)."',
                               modifier_id = '".intval($modifier_id)."',
                               item_name = ~~,
                               cat_id = '".intval($aItem['cat_id'])."',
                               cat_name = ~~,
                               returned_value = '".floatval($value)."',
                               date = '".$this->load_time."'", array($aItem['name'], $cat_name));
        return true;
    }
    
    function getItemReturns($item_id){
        $aReturns = db::get("SELECT * FROM ".PREF."return_history WHERE item_id = '".$item_id."'");
        return $aReturns;
    }
    
    function postRemoteOrder($aOrder){
        if(isset($aOrder['patient']['name'])){
            db::query("INSERT INTO ".PREF."remote_orders SET patient_name = ~~, patient_address = ~~, patient_email = ~~, date = '".$this->load_time."'", array($aOrder['patient']['name'], $aOrder['patient']['address'], $aOrder['patient']['email']));
            $orderId = db::get_last_id();
            if($orderId){
                if(!empty($aOrder['products'])){
                    foreach($aOrder['products'] as $prod){
                        db::query("INSERT INTO ".PREF."remote_orders_items SET
                                       order_id = '".$orderId."',
                                       product=~~, 
                                       quantity=~~, 
                                       unit=~~,
                                       price=~~", 
                        array($prod['name'], $prod['quantity'], $prod['unit'], $prod['price']));
                    }                    
                    return $orderId;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function getRemoteOrders($show_items = false){
        $aOrders = db::get("SELECT * FROM ".PREF."remote_orders");
        if($show_items){
            if($aOrders){
                foreach($aOrders as &$ord){
                    $aItems = db::get("SELECT * FROM ".PREF."remote_orders_items WHERE order_id = '".intval($ord['id'])."'");
                    if($aItems){
                        $ord['items'] = $aItems;
                    }
                }
            }
        }
        return $aOrders;
    }
    
    function getRemoteOrder($id){
        $aOrder = db::get_row("SELECT * FROM ".PREF."remote_orders WHERE id = '".intval($id)."'");
        if($aOrder){
            $aItems = db::get("SELECT * FROM ".PREF."remote_orders_items WHERE order_id = '".intval($aOrder['id'])."'");
            if($aItems){
                $aOrder['items'] = $aItems;
            }
        }
        return $aOrder;
    }
    
    function deleteRemoteOrder($id){
        db::query("DELETE FROM ".PREF."remote_orders WHERE id = '".intval($id)."'");
        return true;
    }
}
?>