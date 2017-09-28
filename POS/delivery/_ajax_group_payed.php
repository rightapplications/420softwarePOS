<?php
include_once '../../includes/common.php';

checkAccess(array('1','2'), '');

if(empty($_POST['group'])) {
	http_response_code(400);
	die('no group received');
}
if(!db::query('UPDATE '.PREF.'orders SET `status`='.ORDER_STATUS_COMPLETED.' WHERE `group_id`='.intval($_POST['group']))){
	http_response_code(400);
	die('update failed');
}