<?php
include_once '../../includes/common.php';
include_once '../../includes/delivery_groups.php';

checkAccess(array('1','2'), '');

if(empty($_POST['group'])) ajax_error('no group received');
if(empty($_POST['status'])) ajax_error('no status received');
$status = $_POST['status'];
if(!in_array($status, del_group_statuses())) ajax_error('invalid status');
$group = get_group(intval($_POST['group']));
if(!$group) ajax_error('group does not exist');

if(!db::query('UPDATE '.PREF.'delivery_groups SET `status`='.$status.' WHERE `id`='.intval($_POST['group']))) ajax_error('failed to change status');
if($status != GROUP_STATUS_READY) {
	header("Content-type: text/json");
	die(json_encode(['status' => $status]));
}
if($group['driver_id']) {
	$driver = get_driver($group['driver_id']);
	if($driver['status'] == DRIVER_STATUS_FREE) send_push_notification($driver['user_id'], 'you have been assigned to an order group', PUSH_CODE_ORDERS);
} else {
	$avalable_driver = db::get_row('SELECT '.PREF.'drivers.`id` FROM '.PREF.'drivers WHERE `status` = '.DRIVER_STATUS_FREE.' LIMIT 1');
	if($avalable_driver && assign_group(intval($_POST['group']), $avalable_driver['id'])) {
		header("Content-type: text/json");
		die(json_encode(['status' => GROUP_STATUS_READY, 'driver' => $avalable_driver['id']]));
	}
}