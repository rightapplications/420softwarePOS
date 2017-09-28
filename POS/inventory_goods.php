<?php
include_once '../includes/common.php';

if(!@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['update_price']){
    checkAccess(array('1'), 'login.php');
}

if(!isset($_GET['cat'])){
    header("Location: inventory_goods.php?cat=0");
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

$SEARCH_ROWS_MAX = 2000;

$_SESSION[CLIENT_ID]['back_from_details']  = $_SERVER['REQUEST_URI'];

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

$aCategories = $oInventory->get_categories();

//if(!empty($_GET['cat'])){
    if(@$_GET['cat'] != 0){
        $aCategory = $oInventory->get_category(@$_GET['cat']);
    }else{
        $aCategory = array('id'=>0, 'name'=>'all', 'inactive_time_frame'=>$oInventory->get_inactive_timeframe(1));
    }
    $activityPage = 'inventory_goods.php';
    if(!empty($aCategory)){
        
        if(@$aCategory['set_price'] and @$aCategory['measure_type'] == 1){
            $aPrices = $oInventory->get_preset_prices(); //dump($aPrices);
        }
        
        if(isset($_POST['sent_preroll'])){
            $oInventory->setPreRollPrice($_POST['sent_preroll'], $_POST['preroll_price']);
            header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
        }
        
        if(isset($_POST['sent_price'])){
            if(isset($_POST['preset_price_selector'])){
                $aPriceSet = $oInventory->get_preset_price($_POST['preset_price_selector']);
                if(!empty($aPriceSet)){
                    $oInventory->updatePrice($_POST['sent_price'], $aPriceSet, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                    $oInventory->editItemInWeedmaps($_POST['sent_price']);
                    header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
                }
            }elseif(isset($_POST['new_price'])){
                $oInventory->updatePrice($_POST['sent_price'], $_POST['new_price'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);
                $oInventory->editItemInWeedmaps($_POST['sent_price']);
                header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }
        }
        
        if(isset($_GET['unset_preroll'])){
            $oInventory->setPreRollPrice($_GET['unset_preroll'], 0);
            $oInventory->editItemInWeedmaps($_GET['unset_preroll']);
            header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
        }
        
        if(isset($_GET['id'])){
            if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $deactivate_only){
                if(isset($_GET['active'])){
                    $result = $oInventory->activate_item($_GET['id'], 'goods', $_GET['active']);
                    if($result == 'ok'){
                        $oInventory->editItemInWeedmaps($_GET['id']);
                        header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
                    }else{
                        $error = $result;
                    }
                }

                
            }
            if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){
                if(isset($_GET['delete'])){
                    $result = $oInventory->delete_goods_item($_GET['id'], $_SESSION[CLIENT_ID]['user_superclinic']['id']);                    
                    if($result == 'ok'){
                        $oInventory->deleteItemFromWeedmaps($_GET['id']);
                        header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
                    }else{
                        $error = $result;
                    }
                }
            }
        }
        
        //sorting
        if(isset($_GET['ordby'])){
            $_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ordby'] = $_GET['ordby'];
        }
        if(isset($_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ordby'])){
            $ordby = $_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ordby'];
        }else{
            $ordby = '';
        }
        
        if(isset($_GET['ord'])){
            $_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ord'] = $_GET['ord'];
        }
        if(isset($_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ord'])){
            $ord = $_SESSION[CLIENT_ID]['sorting'][$aCategory['id']]['ord'];
        }else{
            $ord = '';
        }
        
        if(isset($_GET['sorting'])){
            $aGoods = $oInventory->get_goods($aCategory['id']); 
            $aGoods = sort_array($aGoods, $ordby, $ord);
        }else{
            $aGoods = $oInventory->get_goods($aCategory['id'], $ordby,$ord);
        }        
        
        if(@$_GET['cat'] != 0){
            $iTimeFrame = $oInventory->get_inactive_timeframe($aCategory['measure_type']);
            
            if(isset($_POST['set_price_sent'])){
                $oInventory->set_price_mode($aCategory['id'], isset($_POST['set_price']) ? 1 : 0);
                header("Location: inventory_goods.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }
        }else{
            $iTimeFrame = $aCategory['inactive_time_frame'];
        }
    }else{
        header("Location: inventory.php");die;
    }
//}else{
    //header("Location: inventory.php");die;
//}

include '../templates/POS/inventory_goods_tpl.php';
?>