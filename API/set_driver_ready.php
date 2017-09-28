<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

define ('ERROR_CODE_HAVE_GROUP', 1);

$token = authenticate();
$driver = get_driver_profile($token['user_id']);
if(!$driver) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'driver profile not found'), 500);

$group = get_driver_group($driver['id']);
if($group) {
	//api_errors(format_error(ERROR_CODE_HAVE_GROUP, 'you already have group assigned to you'));
	$orders = get_group_orders($group['id']);
	if($orders === false) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while retrieving orders'), 500);
	api_success(['orders' => array_values($orders)]);
}

$free_group = db::get_one('SELECT `id` FROM '.PREF.'delivery_groups WHERE `driver_id` IS NULL AND `status` = '.GROUP_STATUS_READY.' ORDER BY `id` ASC LIMIT 1');
if(!$free_group) {
	//indicate that there is a free driver
	db::query('UPDATE '.PREF.'drivers SET `status`='.DRIVER_STATUS_FREE.' WHERE `id`='.$driver['id']);
	api_success();
}

if(!assign_group($free_group, $driver['id'])) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'failed to assign group'), 500);
$orders = get_group_orders($free_group);
if($orders === false) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while retrieving orders'), 500);
api_success(['orders' => array_values($orders)]);