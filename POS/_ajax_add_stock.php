<?php
include_once '../includes/common.php';

if(!checkAccess(array('1','2','4'), null)){
	http_response_code(403);
	die('access denied');
}

if(!isset($_POST['price'], $_POST['stock'])) ajax_error('no data received');
if(empty($_GET['item'])) ajax_error('goods item id is required');
$item = $oInventory->get_goods_item($_GET['item']);
if(!$item) ajax_error('requested item is not found');
if($item['measure_type'] != 1) ajax_error('this operation is applicable only to items with measure type 1');

$errors = [];
if(floatval($_POST['price']) == $item['purchase_price'] && !floatval($_POST['stock'])) $errors['price'] = $errors['stock'] = 'no changes are being made';
if(floatval($_POST['price']) <= 0) $errors['price'] = 'cannot be negative or zero';
if(floatval($_POST['stock']) < 0) $errors['stock'] = 'cannot be negative';
if($errors) {
	http_response_code(400);
	header("Content-type: text/json");
	die(json_encode(['errors' => $errors]));
}

$price = floatval($_POST['price']) ? floatval($_POST['price']) : $item['purchase_price'];
$stock = $item['in_stock'] + floatval($_POST['stock']);
$start = $item['starting'] + floatval($_POST['stock']);
$result = db::query('UPDATE '.PREF.'goods SET `starting`='.$start.', `in_stock`='.$stock.', `purchase_price`='.$price.' WHERE `id`='.$item['id']);
if(!$result){
	http_response_code(500);
	die('update failed');
}
$result = db::query('UPDATE '.PREF.'goods_modifiers SET `in_stock`='.$stock.', `purchase_price`='.$start/$price.' WHERE `goods_item_id`='.$item['id']);
if(!$result){
	http_response_code(500);
	die('update failed');
}
$result = db::query('INSERT INTO '.PREF.'goods_history (`item_id`, `updated_by`, `date`, `old_price`, `new_price`, `stock_added`) VALUES ('.implode(', ', [$item['id'], $_SESSION[CLIENT_ID]['user_superclinic']['id'], time(), $item['purchase_price'], $price, floatval($_POST['stock'])]).')');
if(!$result){
	http_response_code(500);
	die('updated, but failed to create history record');
}
die('success');