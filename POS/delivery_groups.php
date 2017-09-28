<?php
include_once '../includes/common.php';
include_once '../includes/delivery_groups.php';
checkAccess(array('1','2'), 'login.php');

if(isset($_POST['delivery_base'])){
	$home = $_POST['delivery_base'];
	settings::set('delivery_base', $home);
	$coords = geocode($home);
	if($coords) {
		$coords = json_encode($coords);
		settings::set('delivery_base_coords', $coords);
	} else {
		$error = 'unable to determine delivery base coordinates, check if address is valid';
	}
} else {
	$home = settings::get('delivery_base');
	$coords = settings::get('delivery_base_coords');
}

$activeMenu = 'delivery';
$sectionName = 'Delivery';
$color_codes = ['90ff92', 'e9ff90', 'ffd26d', 'ff9090', '90ffd6', '90ccff', '90a3ff', 'bb90ff', 'f890ff'];
$color = 0;

$orders = db::get('SELECT '.PREF.'orders.`id`, '.PREF.'orders.`date`, '.PREF.'orders.`address`, '.PREF.'orders.`lat`, '.PREF.'orders.`lng`, '.PREF.'orders.`appointment_time`, '.PREF.'orders.`tax`, '.PREF.'orders.`tax_mode`, '.PREF.'delivery_groups.`id` as group_id, '.PREF.'delivery_groups.`status`, '.PREF.'drivers.id as driver, CONCAT('.PREF.'patients.firstname, \' \', '.PREF.'patients.lastname) as patient
	FROM '.PREF.'orders
	LEFT JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`id` = '.PREF.'orders.`group_id`
	LEFT JOIN '.PREF.'drivers ON '.PREF.'delivery_groups.`driver_id` = '.PREF.'drivers.`id`
	LEFT JOIN '.PREF.'patients ON '.PREF.'orders.`client_id` = '.PREF.'patients.`id`
	WHERE '.PREF.'orders.`delivery` = 1 AND '.PREF.'orders.`status` IN ('.ORDER_STATUS_OPEN.', '.ORDER_STATUS_DELIVERED.') ORDER BY group_id ASC, position ASC, id DESC', NULL, false, false, true);

$items = [];
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
		if($order['tax_mode'] && $order['tax']) $order['total'] += $order['tax'];
		unset($order);
	}
}

$groups = [];
foreach($orders as $i => $order) {
	$orders[$i]['date'] = $order['date'] = date('m.d.Y', $order['date']);
	$orders[$i]['items'] = $order['items'] = !empty($order['items']) ? implode(', ', $order['items']) : '';
	if(!$order['group_id']) continue;
	if(!isset($groups[$order['group_id']])) {
		$groups[$order['group_id']] = [
			'id' => $order['group_id'],
			'status' => $order['status'],
			'color' => $color_codes[$color],
			'orders' => [],
			'driver' => false
		];
		$color++;
		if($color >= count($color_codes)) $color = 0;
		if($order['driver']) $groups[$order['group_id']]['driver'] = $order['driver'];
	}
	$groups[$order['group_id']]['orders'][] = $order;
	unset($orders[$i]);
}

$drivers = get_drivers();

include '../templates/POS/delivery_groups_tpl.php';