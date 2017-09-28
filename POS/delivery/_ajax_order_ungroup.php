<?php
include_once '../../includes/common.php';
checkAccess(array('1','2'), '');

if(!isset($_POST['order'])) {
	http_response_code(400);
	die('no order received');
}

db::query('UPDATE '.PREF.'orders SET `group_id` = 0, `position`=0 WHERE `id`='.intval($_POST['order']));
db::query('DELETE '.PREF.'delivery_groups FROM '.PREF.'delivery_groups
LEFT JOIN '.PREF.'orders ON '.PREF.'orders.`group_id`='.PREF.'delivery_groups.id
WHERE '.PREF.'orders.`id` IS NULL');	//removes empty groups