<?php
include_once '../includes/common.php';
checkAccess(array('1','2'), 'login.php');

$cat=@intval($_GET['cat']);
$goodsId = @intval($_GET['goods_id']);
$modId = @intval($_GET['mod_id']);
$val = @floatval($_GET['value']);

if($val > 0){
    $oInventory->add_to_stock($goodsId, $modId, $val);
}

header("Location: inventory_edit_goods_item.php?cat=".$cat."&id=".$goodsId);