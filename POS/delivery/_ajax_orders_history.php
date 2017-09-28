<?php
include_once '../../includes/common.php';
include_once '../../includes/driver.php';
include_once '../../includes/delivery_groups.php';

checkAccess(array('1','2'), '');

if(empty($_GET['from']) || empty($_GET['to'])) {
	http_response_code(400);
	die('history range parameters not specified');
}

$orders = db::get('SELECT '.PREF.'orders.`id`, '.PREF.'orders.`date`, '.PREF.'orders.`address`, CONCAT('.PREF.'users.firstname, \' \', '.PREF.'users.lastname) as driver, CONCAT('.PREF.'patients.firstname, \' \', '.PREF.'patients.lastname) as patient
	FROM '.PREF.'orders
	LEFT JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`id` = '.PREF.'orders.`group_id`
	LEFT JOIN '.PREF.'drivers ON '.PREF.'delivery_groups.`driver_id` = '.PREF.'drivers.`id`
	LEFT JOIN '.PREF.'users ON '.PREF.'drivers.`user_id` = '.PREF.'users.`id`
	LEFT JOIN '.PREF.'patients ON '.PREF.'orders.`client_id` = '.PREF.'patients.`id`
	WHERE '.PREF.'orders.`delivery` = 1 AND '.PREF.'orders.`status` = '.ORDER_STATUS_COMPLETED.' AND date >= '.intval($_GET['from']).' AND date <= '.intval($_GET['to']).' ORDER BY date ASC', NULL, false, false, true);

if(!$orders) die(json_encode(['orders' => $orders]));

$items = db::get('SELECT `id`, `order_id`, `goods_item_name`, `qty`, `modifier_name`, `price` FROM '.PREF.'orders_items WHERE `order_id` IN('.implode(array_keys($orders), ',').')');
foreach($items as $item) {
	$order = &$orders[$item['order_id']];
	if(!isset($order['items'])) {
		$order['items'] = [];
		$order['total'] = 0;
	}
	$order['items'][] = $item['goods_item_name'].' '.$item['qty'].' '.$item['modifier_name'].' ($'.$item['price']*$item['qty'].')';
	$order['total'] += $item['price']*$item['qty'];
	unset($order);
}

foreach($orders as $key => $order){
	$orders[$key]['date'] = date('m.d.Y', $order['date']);
	$orders[$key]['items'] = !empty($order['items']) ? implode(', ', $order['items']) : '';
}

header("Content-type: text/json");
die(json_encode(['orders' => $orders]));