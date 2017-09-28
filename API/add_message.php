<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

define ('ERROR_CODE_MESSAGE_NO_MESSAGE', 1);
define ('ERROR_CODE_NO_ORDER', 2);

$token = authenticate();

$data = get_post();
validate_params($data, [ERROR_CODE_MESSAGE_NO_MESSAGE => 'message']);

$driver_data = get_driver_order($token['user_id']);
if(!$driver_data['order_id']) api_errors(format_error(ERROR_CODE_NO_ORDER, 'active order not found'));

if(!add_order_message($driver_data['order_id'], $data['message'], AUTHOR_DRIVER)) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'error adding message'), 500);
echo api_success();