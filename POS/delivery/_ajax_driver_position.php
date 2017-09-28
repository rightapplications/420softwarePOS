<?php
include_once '../../includes/common.php';
include_once '../../includes/driver.php';

if(empty($_GET['token'])) {
	http_response_code(400);
	die('token parameter required to get driver data');
}

$location = db::get_row('
	SELECT '.DRIVERS_TABLE.'.lat, '.DRIVERS_TABLE.'.lng FROM '.DRIVERS_TABLE.'
	INNER JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`driver_id` = '.DRIVERS_TABLE.'.`id`
	INNER JOIN '.PREF.'orders ON '.PREF.'orders.`group_id` = '.PREF.'delivery_groups.`id` AND '.PREF.'orders.`status`= '.ORDER_STATUS_OPEN.' AND '.PREF.'orders.`id`= '.PREF.'delivery_groups.`active_order`
	WHERE '.PREF.'orders.`delivery_token`=~~', [$_GET['token']]);
if(!$location){
	http_response_code(400);
	die('unable to get driver data');
}
header('Content-type:application/json;charset=utf-8');
die(json_encode($location)); ?>