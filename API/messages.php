<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

define ('ERROR_CODE_MESSAGES_NO_ORDER', 1);
define ('ERROR_CODE_NO_ORDER', 2);

$token = authenticate();

validate_params($_GET, [ERROR_CODE_MESSAGES_NO_ORDER => 'order']);

$driver = get_driver_profile($token['user_id']);
if(!$driver) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'driver profile not found'), 500);

$order = db::get_row('
	SELECT '.PREF.'orders.* FROM '.PREF.'orders 
	INNER JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`id` = '.PREF.'orders.group_id AND '.PREF.'delivery_groups.`driver_id`='.$driver['id'].'
	WHERE '.PREF.'orders.`id` = '.intval($_GET['order'])
);
if(!$order) api_errors(format_error(ERROR_CODE_NO_ORDER, 'order not found'));

$messages = db::get('SELECT * FROM '.PREF.'order_messages WHERE `order_id` = '.$order['id'].' ORDER BY date DESC');
if($messages === false) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while retrieving messages'), 500);
echo api_success(['messages' => $messages]);