<?php
include_once '../../includes/common.php';
if(!checkAccess(array('1','2','4'), '') && empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
	http_response_code(403);
	die('unathorized');
}
if(isset($_GET['remove'])){
	unset($_SESSION[CLIENT_ID]['delivery']);
	die;
}
if(empty($_POST['address'])) {
	http_response_code(400);
	die('no address specified');
}
$_SESSION[CLIENT_ID]['delivery'] = [
	'address' => $_POST['address'],
	'appointment' => $_POST['appointment']
];