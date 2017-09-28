<?php 
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){
    checkAccess(array('1'), 'login.php');
}

if($_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] and $_SESSION[CLIENT_ID]['user_superclinic']['id'] != 1){
    $add_only = true;
}else{
    $add_only = false;
}

$_SESSION[CLIENT_ID]['back_from_details'] = $_SERVER['REQUEST_URI'];

$activeMenu = 'inventory';
$sectionName = 'Inventory';

$error = '';

if(!empty($_GET['cat'])){
    $aCategory = $oInventory->get_category($_GET['cat']);
    
    if(!empty($aCategory)){
        
        if($aCategory['set_price'] and $aCategory['measure_type'] == 1){
            $aPrices = $oInventory->get_preset_prices();
        }
        
        $aVendors = $oInventory->get_vendors();
        $aCategories = [];
        $aCats = $oInventory->get_categories($aCategory['measure_type']);
        foreach($aCats as $k=>$v){
            $aCategories[$v['id']] = $v['name'];
        }
        if(isset($_GET['id']) and (!$add_only or (isset($_GET['dplDialog']) and $_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']) or (isset($_GET['duplicate']) and $_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']))){//edit goods
            $aGoodsItem = $oInventory->get_goods_item($_GET['id']);
            
            if(isset($_GET['custom'])){//partial from safe
                $custInStock = floatval($_GET['custom']);
                if($aCategory['measure_type'] == 1){
                    $custPurchPrice = $custInStock * $aGoodsItem['purchase_price'] / $aGoodsItem['in_stock'];
                }else{
                    $custPurchPrice = $aGoodsItem['purchase_price'];
                }
                      
                $newVal = $aGoodsItem['in_stock'] - $custInStock;
                if(isset($_POST['sent'])){
                    $oInventory->update_safe_qty($_GET['id'], $custInStock);
                    if($aCategory['measure_type'] == 1){
                        $oInventory->update_safe_purchase_price($_GET['id'], $custPurchPrice);
                    }
                    $newId = $oInventory->duplicate_goods_item($_GET['id'], $newVal, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                    $oInventory->addItemToWeedmaps($newId);
                    if($aCategory['measure_type'] == 1){
                        $amt_new = $newVal*$aGoodsItem['purchase_price']/$aGoodsItem['in_stock'];                    
                        $oInventory->update_safe_purchase_price($newId, $amt_new);
                    }
                }
            }
                      
            if(isset($_GET['duplicate'])){//duplicate  
                if(!empty($_GET['newStart'])){
                    $startVal = $_GET['newStart'];
                }else{
                    $startVal = null;
                }
                $result = $oInventory->duplicate_goods_item($_GET['id'], $startVal, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                if($result){
                    $oInventory->addItemToWeedmaps($result);
                    if(isset($_GET['transfer'])){
                        if($startVal >= $aGoodsItem['in_stock']){
                            $newInstock = 0;
                            $transferedVal = $aGoodsItem['in_stock'];
                        }else{
                            $newInstock = $aGoodsItem['in_stock']-$startVal;
                            $transferedVal = $startVal;                      
                        }                        
                        $oInventory->setItemInStock($_GET['id'], $newInstock);   
                        $oInventory->logItemTransfer($_SESSION[CLIENT_ID]['user_superclinic']['id'], $_GET['id'], $transferedVal);
                        $oInventory->editItemInWeedmaps($_GET['id']);
                    }
                    if(isset($_GET['deactivateCurrent'])){
                        $oInventory->activate_item($_GET['id'], 'goods', false);
                        $oInventory->editItemInWeedmaps($_GET['id']);
                    } 
                    if(isset($_GET['deleteCurrent']) and $_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
                        $oInventory->delete_goods_item($_GET['id'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                        $oInventory->deleteItemFromWeedmaps($_GET['id']);
                    }
                    if(!isset($_GET['losses'])){
                        header("Location: ".(isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : "inventory_goods.php?cat=".intval($_GET['cat'])));die;
                    }
                }
            }else{            
                if(isset($_GET['delete_image'])){//delete image
                    $result = $oInventory->delete_goods_item_img($_GET['id']);
                    if($result == 'ok'){
                        if(!isset($_GET['losses'])){
                            header("Location: inventory_edit_goods_item.php?cat=".$aCategory['id'].'&id='.intval($_GET['id'])); die();
                        }
                    }else{
                        $error = $result;
                    }
                }

                if(isset($_GET['delete_mod_image'])){//delete bar code
                    $result = $oInventory->delete_bar_code_img($_GET['id'], $_GET['delete_mod_image']);
                    if($result == 'ok'){
                        if(!isset($_GET['losses'])){
                            header("Location: inventory_edit_goods_item.php?cat=".$aCategory['id'].'&id='.intval($_GET['id'])); die();
                        }
                    }else{
                        $error = $result;
                    }
                }

                if(isset($_POST['sent'])){
                    if(isset($_GET['to_stock'])){
                        $added_by = $_SESSION[CLIENT_ID]['user_superclinic']['id'];
                    }else{
                        $added_by = 0;
                    }
                    $result = $oInventory->update_goods_item($_GET['id'], $_POST['item'], $_FILES, $added_by);                    
                    if($result == 'ok'){
                        $oInventory->editItemInWeedmaps($_GET['id'], $_POST['item']);
                        if(!isset($_GET['losses'])){
                            header("Location: ".$_SESSION[CLIENT_ID]['return_page']);die();
                        }
                    }else{
                        $error = $result;
                    }
                }            
            }
            if(isset($_GET['losses'])){
                $oInventory->itemLosses($_GET['id'], floatval($_GET['losses']), $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                header("Location: ".(isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : "inventory_goods.php?cat=".intval($_GET['cat'])));die;
            }
        }else{//add goods
            if($aCategory['measure_type'] == 1){
                $aMods = $aWeights;
            }else{
                $aMods = $aQTY;
            }
            if(isset($_POST['sent'])){
                if(!$_POST['nopost']){
                    if(!isset($_POST['item']['safe'])){
                        $added_by = $_SESSION[CLIENT_ID]['user_superclinic']['id'];
                    }else{
                        $added_by = 0;
                    }
                    $result = $oInventory->add_goods_item($_POST['item'], $_FILES, $added_by);
                    if(is_integer($result)){
                        /*if(!$add_only){
                            header("Location: inventory_edit_goods_item.php?cat=".intval($_POST['item']['cat_id'])."&id=".$result);
                        }else{
                            header("Location: inventory_goods.php?cat=".intval($_POST['item']['cat_id']));
                        }*/
                        //Weedmaps
                        $oInventory->addItemToWeedmaps($result, $_POST['item']);
                        header("Location: inventory_goods.php?cat=".intval($_POST['item']['cat_id']));
                    }else{
                        $error = $result;
                    }
                }
            }
        }
    }else{
        header("Location: inventory.php");die;
    }    
}else{
    header("Location: inventory.php");die;
}

include '../templates/POS/inventory_edit_goods_item_tpl.php'
?>