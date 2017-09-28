<?php
include_once '../includes/common.php';

checkAccess(array('1'), 'login.php');

$activeMenu = 'inventory';
$sectionName = 'Inventory';

$activityPage = 'inventory_user_history.php';

$aUser = $oUser->get_user($_GET['id']);

$aCategories = $oInventory->get_categories();

$aGoods = $oInventory->get_users_goods($_GET['id']); 

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

include '../templates/POS/inventory_user_history_tpl.php';