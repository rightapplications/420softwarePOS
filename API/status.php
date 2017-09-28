<?php
include_once '../includes/api.php';
include_once '../includes/delivery_groups.php';

$token = authenticate();
$data = get_driver_order($token['user_id']);
$result['driver_status'] = $data['status'];
$result['group_assigned'] = (bool)$data['group_id'] && (bool)$data['group_not_empty'];
$result['group_status'] = $result['group_assigned'] ? $data['group_status'] : null;
$result['current_order'] = $data['order_id'];
api_success($result);