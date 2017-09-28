<?php
include_once '../includes/common.php';
include_once '../includes/driver.php';
include_once '../includes/delivery_groups.php';

if(!isset($_GET['token'])) die('unauthorized');
$order = db::get_row('SELECT '.PREF.'orders.* FROM '.PREF.'orders
	LEFT JOIN '.PREF.'driver_ratings ON '.PREF.'driver_ratings.`order_id` = '.PREF.'orders.`id`
	WHERE delivery_token=~~ AND (status='.ORDER_STATUS_OPEN.' OR '.PREF.'driver_ratings.`id` IS NULL)', [$_GET['token']]);
if(!$order) die('access denied');

if(!empty($_POST['message'])){
	add_order_message($order['id'], $_POST['message'], AUTHOR_PATIENT);
	header("Refresh:0");
	die;
}

$driver = db::get_row('
	SELECT '.DRIVERS_TABLE.'.*, '.PREF.'users.firstname, '.PREF.'users.lastname FROM '.DRIVERS_TABLE.' 
	INNER JOIN '.PREF.'users ON '.PREF.'users.`id` = '.DRIVERS_TABLE.'.`user_id` AND '.PREF.'users.`role`= 5 AND '.PREF.'users.`active`= 1
	INNER JOIN '.PREF.'delivery_groups ON '.PREF.'delivery_groups.`driver_id` = '.DRIVERS_TABLE.'.`id` AND '.PREF.'delivery_groups.`id` = '.$order['group_id']
);
if(!$driver) die('access denied');

if(!empty($_POST['rating'])){
	$message = isset($_POST['review']) ? $_POST['review'] : '';
	db::query('INSERT INTO '.PREF.'driver_ratings (`order_id`, `driver_id`, `rating`, `message`) VALUES ('.$order['id'].', '.$driver['id'].', '.intval($_POST['rating']).', ~~)', [$message]);
	$review_completed = true;
}

$messages = db::get('SELECT * FROM '.PREF.'order_messages WHERE `order_id`='.$order['id']);

include '../templates/POS/delivery_status_tpl.php';