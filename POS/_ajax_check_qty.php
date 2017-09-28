<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','4'), '') and !empty($_GET['item']) and !empty($_GET['mod']) and !empty($_GET['q'])){
    $qty = $oInventory->check_quantity(intval($_GET['item']), intval($_GET['mod']));
    if($qty >= floatval($_GET['q'])){     
        $aNearestUnit = $oInventory->get_nearest_unit(floatval($_GET['q']));
        $aItemModifier = db::get_row("SELECT * FROM ".PREF."goods_modifiers WHERE id = '".intval($_GET['mod'])."'");
		if($aNearestUnit['code'] === 'default'){
			$priceMod = 'price';
		}else{
			$priceMod = 'price_'.$aNearestUnit['code'];
		}
        if(isset($aItemModifier[$priceMod]) and $aItemModifier[$priceMod] > 0){
            $aResult = array('result'=>1,'nearestUnitCode'=>$aNearestUnit['code'],'nearestUnitName'=>$aNearestUnit['name']);  
        }else{
            $aResult = array('result'=>1,'nearestUnitCode'=>'','nearestUnitName'=>'');
        }
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output; 
}