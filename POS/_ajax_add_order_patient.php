<?php
include_once '../includes/common.php';
//header("Content-type: text/json");
if(checkAccess(array('1','2','4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    $aPatient = $oPatient->get_patient($_GET['id']);    
    if($aPatient['vip_discount']){
        $_SESSION[CLIENT_ID]['order_discount_type'] = 2;
        $_SESSION[CLIENT_ID]['order_discount_percent'] = 10;
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
        $_SESSION[CLIENT_ID]['order_discount_amt'] = 10*$total/100;
        $_SESSION[CLIENT_ID]['discount_reason'] = 'vip or senior client';
	echo 'vip';
    }elseif($aPatient['is_employee']){
        if($aPatient['apply_purchase_price']){
            foreach($_SESSION[CLIENT_ID]['cart'] as $item){
                if($item['modifiers']){
                    foreach($item['modifiers'] as  $k => $mod){
                        foreach($mod as $altname=>$alt){
                            if($alt['qty'] == 0){
                                continue;
                            }
                            $aModifier = $oInventory->get_modifier($item['id'], $alt['id']);                            
                            if($altname == 'default'){
                                if($alt['price'] > $aModifier['purchase_price']){
                                    $empl_dsc = $alt['price']-$aModifier['purchase_price']; 
                                    $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$k][$altname]['discount_amt'] = $empl_dsc;
                                }
                            }else{
                                $altPrice = $aModifier['purchase_price']*$aAlternativeWeights[$altname]['quantity'];
                                if($alt['price'] > $altPrice){
                                    $empl_dsc = $alt['price'] - $altPrice;
                                    $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$k][$altname]['discount_amt'] = $empl_dsc;                                    
                                }
                            }
                            $_SESSION[CLIENT_ID]['cart'][$item['id']]['modifiers'][$k][$altname]['discount_type'] = 1;
                        }
                    }
                }
            }
            $_SESSION[CLIENT_ID]['discount_reason'] = 'employee: '.$aPatient['firstname']." ".$aPatient['lastname'];
            echo 'vip';
        }else{
            if($aPatient['employee_discount'] > 0 and $aPatient['employee_discount'] <=100){
                $_SESSION[CLIENT_ID]['order_discount_type'] = 2;
                $_SESSION[CLIENT_ID]['order_discount_percent'] = floatval($aPatient['employee_discount']);
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
                $_SESSION[CLIENT_ID]['order_discount_amt'] = floatval($aPatient['employee_discount'])*$total/100;
                $_SESSION[CLIENT_ID]['discount_reason'] = 'employee: '.$aPatient['firstname']." ".$aPatient['lastname'];
                echo 'vip';
            }
        }
    }

    
    $_SESSION[CLIENT_ID]['order_client'] = intval($_GET['id']);
}