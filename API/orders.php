<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

define ('ERROR_CODE_NO_GROUP', 1);

$token = authenticate();

$driver = get_driver_profile($token['user_id']);
if(!$driver) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'driver profile not found'), 500);
$group = get_driver_group($driver['id']);
if(!$group) api_errors(format_error(ERROR_CODE_NO_GROUP, 'there is no group assigned to you'));

$orders = get_group_orders($group['id']);
if($orders === false) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'database error while retrieving orders'), 500);
api_success(['orders' => array_values($orders)]);