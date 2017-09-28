<?php
include_once '../includes/common.php';

if(!@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['update_price']){
    checkAccess(array('1'), 'login.php');
}

if($_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){
    $add_only = true;
}else{
    $add_only = false;
}

if($_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory']){
    $deactivate_only = true;
}else{
    $deactivate_only = false;
}

$activeMenu = 'inventory';
$sectionName = 'Inventory';

$activityPage = 'inventory_safe_goods.php';

$SEARCH_ROWS_MAX = 2000;

$_SESSION[CLIENT_ID]['back_from_details']  = $_SERVER['REQUEST_URI'];

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

$aCategories = $oInventory->get_categories();

//delete
if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    if(isset($_GET['delete'])){
        $result = $oInventory->delete_goods_item($_GET['id'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);
        if($result == 'ok'){
            header("Location: inventory_safe_goods.php"); die();
        }else{
            $error = $result;
        }
    }
}


//add to stock
/*if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
    if(isset($_GET['instock'])){
        $aProduct = $oInventory->move_safe_to_stock($_GET['instock']);
        $_SESSION[CLIENT_ID]['return_page'] = 'inventory_safe_goods.php';
        header("Location: inventory_edit_goods_item.php?cat=".$aProduct['cat_id']."&id=".$aProduct['id']); die();
    }
}*/


//sorting
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['safe']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['safe']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['safe']['ordby'];
}else{
    $ordby = '';
}

if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['safe']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['safe']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['safe']['ord'];
}else{
    $ord = '';
}

if(isset($_GET['sorting'])){
    $aGoods = $oInventory->get_safe_goods(0); 
    $aGoods = sort_array($aGoods, $ordby, $ord);
}else{
    $aGoods = $oInventory->get_safe_goods(0, $ordby, $ord);
}

if(!empty($aCategories)){
    foreach($aCategories as $c=>$catItem){
        if(!empty($aGoods)){
            foreach($aGoods as $g=>$goodItem){
                if($goodItem['cat_id'] == $catItem['id']){
                    $aCategories[$c]['goods'][] = $goodItem;
                }
            }
        }
    }
}


if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){
    if(isset($_POST['item']) and !isset($_POST['id'])){
        //dump($_POST['item']);
        $oInventory->add_goods_item($_POST['item']);
        header("Location: inventory_safe_goods.php");die();
    }
}
//dump($aGoods);

if(isset($_POST['id'])){
    $aData = $_POST['item'];
    if(empty($_POST['item']['qtyType']) and !empty($_POST['item']['qtyVal'])){
        $safeItem = $oInventory->get_goods_item($_POST['id']);
        if($safeItem['in_stock'] > $_POST['item']['qtyVal']){
            $newProdId = $oInventory->duplicate_goods_item($_POST['id'], $_POST['item']['qtyVal'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);
            $newItem = $oInventory->get_goods_item($newProdId);
            $aData['starting'] = floatval($aData['qtyVal']);
            if($newProdId){
                $oInventory->update_safe_qty($_POST['id'], $safeItem['in_stock']-floatval($aData['qtyVal']));
                if($safeItem['measure_type'] == 1){                    
                    $amt = ($safeItem['in_stock']-floatval($aData['qtyVal'])) * $safeItem['purchase_price'] / $safeItem['in_stock'];
                    $oInventory->update_safe_purchase_price($_POST['id'], $amt); 
                    
                    $amt_new = floatval($aData['qtyVal']) * $safeItem['purchase_price'] / $safeItem['in_stock'];
                    $oInventory->update_safe_purchase_price($newProdId, $amt_new);
                    
                    if(!empty($aData['modifiers'])){
                        foreach($aData['modifiers'] as $k=>$m){      
                            $aData['modifiers'][$newItem['modifiers'][0]['id']] = $aData['modifiers'][$k];
                            $aData['modifiers'][$newItem['modifiers'][0]['id']]['in_stock'] = floatval($aData['qtyVal']);
                            unset($aData['modifiers'][$k]);
                        }
                    }
                }else{
                    if(!empty($aData['modifiers'])){
                        foreach($aData['modifiers'] as $k=>$m){                            
                            $aData['modifiers'][$newItem['modifiers'][0]['id']]['price'] = floatval($aData['modifiers'][$k]['price']);
                            $aData['modifiers'][$newItem['modifiers'][0]['id']]['in_stock'] = floatval($aData['qtyVal']);
                            unset($aData['modifiers'][$k]);
                        }
                    }
                }
                $aData['purchase_date'] = $oInventory->load_time;
                $result = $oInventory->update_goods_item($newProdId, $aData, null, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                if($result == 'ok'){
                    header("Location: inventory_safe_goods.php");
                }else{
                    $error = $result;
                }
            }
        }else{
            $error = "Custom value exceeds in stock quantity";
        }       
    }else{
        $_POST['item']['purchase_date'] = $oInventory->load_time;
        $result = $oInventory->update_goods_item($_POST['id'], $_POST['item'], null, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
        if($result == 'ok'){
            header("Location: inventory_safe_goods.php");die();
        }else{
            $error = $result;
        }
    }
} 

//affiliated accounts
$aAffiliatedAccounts = array();
include_once '../includes/admin_config.php';
db::connect($aConfAdmin);
$client = db::get_one("SELECT client_id FROM ".PREF."accounts WHERE folder = '".CLIENT_ID."'");
if(!empty($client)){
	$aAffiliatedAccounts = db::get("SELECT * FROM ".PREF."accounts WHERE client_id = '".intval($client)."' AND folder != '".CLIENT_ID."'  AND (version = '22' OR version = '23')");	
	if(isset($_POST['id_transfer'])){
		if(!empty($_POST['account'])){
			$targetAccount = db::get_row("SELECT * FROM ".PREF."accounts WHERE folder = ~~", array($_POST['account']));	
			if(!empty($targetAccount['folder'])){
				//get local item
				db::connect($aConf);
				$localSafeItem = $oInventory->get_goods_item($_POST['id_transfer']);
				$localCategory = $oInventory->get_category($localSafeItem['cat_id']);
				//add to remote safe
				$aRemoteConf['host']=$aConfAdmin['host'];
				$aRemoteConf['user']=$aConfAdmin['user'];
				$aRemoteConf['password']=$aConfAdmin['password'];
				$aRemoteConf['database']=$targetAccount['folder'];
				$aRemoteConf['encoding']="utf8";
				db::connect($aRemoteConf);
				$aTransData['safe'] = 1;
				$aTransData['measure_type'] = $localSafeItem['measure_type'];
				//check if remote category exists
				$remoteCategory = db::get_row("SELECT * FROM ".PREF."goods_categories WHERE name = ~~ AND measure_type = '".intval($localCategory['measure_type'])."'", array($localCategory['name']));
				if(!empty($remoteCategory)){
					$aTransData['cat_id'] = $remoteCategory['id'];
				}else{
					$oInventory->add_category($localCategory['name'], $localCategory['measure_type']);
					$newCatId = db::get_one("SELECT id FROM ".PREF."goods_categories WHERE name = ~~ AND measure_type = '".intval($localCategory['measure_type'])."'", array($localCategory['name']));
					$aTransData['cat_id'] = $newCatId;
				}
				$aTransData['purchase_date'] = strftime("%m/%d/%Y");
				$aTransData['name'] = $localSafeItem['name'];
				if($localSafeItem['measure_type'] == 1){
					$aTransData['purchase_price'] = floatval($_POST['qtyTransferVal']) * $localSafeItem['purchase_price'] / $localSafeItem['in_stock'];
				}else{
					$aTransData['purchase_price'] = $localSafeItem['purchase_price'];
				}
				$aTransData['starting'] = floatval($_POST['qtyTransferVal']);
				$aTransData['in_stock'] = floatval($_POST['qtyTransferVal']);
				$aTransData['modifiers'][1]['name'] = $localCategory['measure_type'] == 1 ? 'Gram' : 'qty';
				$aTransData['modifiers'][1]['in_stock'] = floatval($_POST['qtyTransferVal']);
				$aTransData['modifiers'][1]['quantity'] = 1;
				$oInventory->add_goods_item($aTransData);
				
				//update local item
				db::connect($aConf);
                                if($localSafeItem['in_stock'] == $_POST['qtyTransferVal']){
                                    $oInventory->delete_goods_item($_POST['id_transfer'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                                }else{
                                    $oInventory->update_safe_qty($_POST['id_transfer'], $localSafeItem['in_stock']-floatval($_POST['qtyTransferVal']));
                                    if($localSafeItem['measure_type'] == 1){
                                        $amt = ($localSafeItem['in_stock']-floatval($_POST['qtyTransferVal'])) * $localSafeItem['purchase_price'] / $localSafeItem['in_stock'];
                                        $oInventory->update_safe_purchase_price($_POST['id_transfer'], $amt); 
                                    }
                                }
				header("Location: inventory_safe_goods.php");die();
			}
		}
	}
}

db::connect($aConf); 

include '../templates/POS/inventory_safe_goods_tpl.php';