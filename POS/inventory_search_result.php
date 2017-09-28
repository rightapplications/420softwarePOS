<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');

$activeMenu = 'inventory';
$sectionName = 'Inventory';

$searchPage = true;

$add_only = false;

$SEARCH_ROWS_MAX = 20;

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

$activityPage = 'inventory_search_result.php';

$aCategory['name'] = 'Search Results';

$aCategory['id'] = 0;

$aCategories = $oInventory->get_categories();

if(isset($_POST['search_sent'])){
    $_SESSION[CLIENT_ID]['search_string'] = $_POST['search'];    
}

if(isset($_GET['id'])){
    if(!$add_only){
        if(isset($_GET['active'])){
            $result = $oInventory->activate_item($_GET['id'], 'goods', $_GET['active']);
            if($result == 'ok'){
                header("Location: inventory_search_result.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }else{
                $error = $result;
            }
        }

        if(isset($_GET['delete'])){
            $result = $oInventory->delete_goods_item($_GET['id']);
            if($result == 'ok'){
                header("Location: inventory_search_result.php?cat=".$aCategory['id'].(!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : '')); die();
            }else{
                $error = $result;
            }
        }
    }
}

//sorting
if(isset($_GET['ordby'])){
    $_SESSION[CLIENT_ID]['sorting']['invsearch']['ordby'] = $_GET['ordby'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['invsearch']['ordby'])){
    $ordby = $_SESSION[CLIENT_ID]['sorting']['invsearch']['ordby'];
}else{
    $ordby = '';
}
if(isset($_GET['ord'])){
    $_SESSION[CLIENT_ID]['sorting']['invsearch']['ord'] = $_GET['ord'];
}
if(isset($_SESSION[CLIENT_ID]['sorting']['invsearch']['ord'])){
    $ord = $_SESSION[CLIENT_ID]['sorting']['invsearch']['ord'];
}else{
    $ord = '';
}
$aGoods = $oInventory->get_goods($aCategory['id'], $ordby, $ord, @$_SESSION[CLIENT_ID]['search_string']);

include '../templates/POS/inventory_goods_tpl.php';
?>