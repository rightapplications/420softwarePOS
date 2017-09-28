<?php
include_once '../../includes/common.php';
include_once '../../includes/delivery_groups.php';

checkAccess(array('1','2'), '');

if(empty($_POST['orders'])) ajax_error('no orders received');
if(!isset($_POST['group'])) ajax_error('no group received');	//can be 0

$data = [];
foreach($_POST['orders'] as $order){
	if(empty($order['id']) || !isset($order['position'])) ajax_error('invalid orders format');
	$data[intval($order['id'])] = intval($order['position']);
}

if(count($data) == 1) {
	reset($data);
	$condition = '`id` ='.key($data);
} else {
	$condition = '`id` IN('.implode(',', array_keys($data)).')';
}
$orders = db::get('SELECT `id`, `group_id`, `status`, `position` FROM '.PREF.'orders WHERE `status` = '.ORDER_STATUS_OPEN.' AND `delivery` = 1 AND '.$condition);
if(!$orders) ajax_error('specified order(s) not found');

if($_POST['group'] == 0){
	//create new group
	db::query('INSERT INTO '.PREF.'delivery_groups (`status`) VALUES ('.GROUP_STATUS_NEW.')');
	$id = db::get_last_id();
} else {
	$id = intval($_POST['group']);
}

$group = db::get_row('SELECT * FROM '.PREF.'delivery_groups WHERE `id`='.$id);

if(!$group) ajax_error('specified group not found');
if(in_array($group['status'], [GROUP_STATUS_LOADED, GROUP_STATUS_DELIVERED])) ajax_error('this group is not editable');

foreach($orders as $order){
	$update = [];
	if($order['group_id'] !== $group['id']) $update[] = ' `group_id` = '.$group['id'];
	if($order['position'] !== $data[$order['id']]) $update[] = ' `position` = '.$data[$order['id']];
	if($update) {
		if(!db::query('UPDATE '.PREF.'orders SET '.implode(',', $update).' WHERE `id`='.$order['id'])) ajax_error('failed to update order');
	}
}

if($_POST['group'] == 0) {
	header("Content-type: text/json");
	echo json_encode(['group_id' => $group['id']]);
};