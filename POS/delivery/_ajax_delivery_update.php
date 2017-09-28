<?php
include_once '../../includes/common.php';
include_once '../../includes/driver.php';
include_once '../../includes/delivery_groups.php';

checkAccess(array('1','2'), '');

$orders = db::get('SELECT '.PREF.'orders.`id`, '.PREF.'orders.`date`, '.PREF.'orders.`address`, '.PREF.'orders.`lat`, '.PREF.'orders.`lng`, '.PREF.'orders.`appointment_time`, '.PREF.'delivery_groups.`id` as group_id, '.PREF.'delivery_groups.`status`, '.PREF.'drivers.id as driver, CONCAT('.PREF.'patients.firstname, \' \', '.PREF.'patients.lastname) as patient
	FROM '.PREF.'orders
	LEFT JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`id` = '.PREF.'orders.`group_id`
	LEFT JOIN '.PREF.'drivers ON '.PREF.'delivery_groups.`driver_id` = '.PREF.'drivers.`id`
	LEFT JOIN '.PREF.'patients ON '.PREF.'orders.`client_id` = '.PREF.'patients.`id`
	WHERE '.PREF.'orders.`delivery` = 1 AND '.PREF.'orders.`status` IN ('.ORDER_STATUS_OPEN.', '.ORDER_STATUS_DELIVERED.') ORDER BY group_id ASC, position ASC, id DESC', NULL, false, false, true);

if($orders) {
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
}

$groups = [];
foreach($orders as $key => $order){
	$orders[$key]['date'] = date('m.d.Y', $order['date']);
	$orders[$key]['items'] = !empty($order['items']) ? implode(', ', $order['items']) : '';
	if(!$order['group_id']) continue;
	if(!isset($groups[$order['group_id']])){
		$groups[$order['group_id']] = [
			'status' => $order['status'],
			'driver' => $order['driver'],
			'orders' => []
		];
	}
	$groups[$order['group_id']]['orders'] = $order;
	unset($orders[$key]);
}
header("Content-type: text/json");
echo json_encode(['orders' => array_values($orders), 'groups' => $groups, 'drivers' => get_drivers()]);