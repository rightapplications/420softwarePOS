<?php
include_once '../../includes/common.php';
include_once '../../includes/delivery_groups.php';

if(empty($_GET['token'])) {
	http_response_code(400);
	die('token parameter required to get messages');
}

$messages = db::get('
	SELECT '.PREF.'order_messages.* FROM '.PREF.'order_messages
	INNER JOIN '.PREF.'orders ON '.PREF.'orders.`id` = '.PREF.'order_messages.`order_id` AND '.PREF.'orders.`status`= '.ORDER_STATUS_OPEN.'
	WHERE '.PREF.'orders.`delivery_token`=~~', [$_GET['token']]);
if(!$messages){
	http_response_code(400);
	die('unable to get messages');
}
foreach($messages as $i => $data){
	$messages[$i]['author'] = $data['author'] == AUTHOR_PATIENT ? 'patient' : 'driver';
	$messages[$i]['time'] = date('H.i', strtotime($data['date']));
}
header('Content-type:application/json;charset=utf-8');
die(json_encode($messages)); ?>