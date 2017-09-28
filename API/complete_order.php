<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

define('ERROR_CODE_NO_GROUP', 1);
define('ERROR_CODE_NOT_LOADED', 2);
define('ERROR_CODE_NO_ORDER', 3);

$token = authenticate();

$driver = get_driver_profile($token['user_id']);
if(!$driver) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'driver profile not found'), 500);

$group = get_driver_group($driver['id']);
if(!$group) api_errors(format_error(ERROR_CODE_NO_GROUP, 'you currently have no active group'));
if($group['status'] != GROUP_STATUS_LOADED) api_errors(format_error(ERROR_CODE_NOT_LOADED, 'you should indicate that you have loaded cargo and started delivery before completing orders'));

$current_order = get_current_order($group['id']);
if(!$current_order) api_errors(format_error(ERROR_CODE_NO_ORDER, 'active order not found'));

if(!db::query('UPDATE '.PREF.'orders SET `status`='.ORDER_STATUS_DELIVERED.' WHERE `id`='.$current_order)) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while updating current order'), 500);
$new_order = get_first_order($group['id']);
if($new_order){
	if(!db::query('UPDATE '.PREF.'delivery_groups SET `active_order`='.$new_order['id'].' WHERE `id`='.$group['id'])) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while setting active order'), 500);
	send_order_notifications($new_order['client_id'], $new_order['delivery_token']);
	api_success(['order_completed' => $current_order, 'new_order' => $new_order['id']]);
} else {
	//group delivery completed
	complete_group($group['id']);
	api_success(['order_completed' => $current_order, 'group_completed' => true]);
}