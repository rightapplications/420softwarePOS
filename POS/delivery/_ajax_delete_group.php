<?php
include_once '../../includes/common.php';
checkAccess(array('1','2'), '');

if(!isset($_POST['id'])) {
	http_response_code(400);
	die('no group id received');
}

if(!db::query('UPDATE '.PREF.'orders SET `group_id` = 0, `position`=0 WHERE `group_id`='.intval($_POST['id']))){
	http_response_code(400);
	die('failed to unlink orders from group');
}
if(!db::query('DELETE FROM '.PREF.'delivery_groups WHERE `id` ='.intval($_POST['id']))){
	http_response_code(400);
	die('failed to delete group');
}