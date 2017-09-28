<?php
use ricwein\PushNotification\PushNotification;
use ricwein\PushNotification\Handler\APNSHandler;
use ricwein\PushNotification\Handler\GCMHandler;

include_once 'common.php';
include_once 'driver.php';

define('API_TOKEN_LIFETIME', 60*60*12);	//in how much time auth token will expire

define('ERROR_CODE_SERVER_ERROR', 1001);
define('ERROR_CODE_NO_AUTH_HEADER', 1002);
define('ERROR_CODE_NOT_AUTENTICATED', 1003);
define('ERROR_CODE_TOKEN_EXPIRED', 1004);

define('DEVICE_TYPE_ANDROID', 1);
define('DEVICE_TYPE_IOS', 2);

define('GCM_SERVER_TOKEN', 'AAAAytNQlWs:APA91bF056GWQj-cJKZi9O_vdWU_BD7LlY8mtrBQOYGwblxAyvFv5k3RPA78BIgI2zFltroAJK62pdSN2SnHRkIltSdqxrjgnuKawNs29ba4RvFD7K_SpvOnyCsL-Q9TNg1NLN0yZFxm');
define('APNS_CERT_PATH', ABS.'includes/push_notifications/PushCertificateAndKey.pem');
define('APNS_SERVER_SANDBOX', 'ssl://gateway.sandbox.push.apple.com:2195');
define('PUSH_CODE_ORDERS', 1);
define('PUSH_CODE_MESSAGE', 2);

define('STATUS_UNATHORIZED', 'unathorized');


/*api request handlers*/
function get_post(){
	return json_decode(file_get_contents('php://input'), true);
}

/*shortcuts for api response formatting*/
function api_success($additional = []){
	die(json_encode(array_merge(['status' => 'success'], $additional)));
}

function api_errors($errors, $code = 400, $status = 'failed'){
	if(isset($errors['error_code'])) $errors = [$errors];	//make single error to be formatted same as array of multiple errors
	http_response_code($code);
	die(json_encode(['status' => $status, 'errors' => $errors]));
}

function format_error($error_code, $error_message){
	return ['error_code' => $error_code, 'error_message' => $error_message];
}

/*authentication*/
function api_login($email, $pass){
    $user = db::get_row("SELECT id, firstname, lastname, role FROM ".PREF."users WHERE email=~~ AND pass=~~ AND active = 1", [db::clear((string)$email),md5((string)$pass)]);
    if(!$user) api_errors([format_error(ERROR_CODE_LOGIN_INVALID, 'authorization failed, check your email and password')], 401, STATUS_UNATHORIZED);
    if($user['role'] != ROLE_DRIVER) api_errors([format_error(ERROR_CODE_LOGIN_NOT_DRIVER, 'you are not registered as driver')], 401, STATUS_UNATHORIZED);
    $shift = db::get_one("SELECT login FROM ".PREF."users_shifts WHERE user_id = '".$user['id']."' AND logout='0'");
    if(!$shift) api_errors([format_error(ERROR_CODE_LOGIN_NO_SHIFT, 'your shift has not been started yet')], 401, STATUS_UNATHORIZED);
    clear_user_tokens($user['id']);
    $user['token'] = create_auth_token($user['id']);
    return $user;
}

function authenticate(){
	$headers = getallheaders();
	if(empty($headers[AUTH_HEADER]) || !preg_match('/^Bearer\s+(.*?)$/', $headers[AUTH_HEADER], $matches)) api_errors([format_error(ERROR_CODE_NO_AUTH_HEADER, 'Authorization header not found in request')]);
	$token = db::get_row('SELECT * FROM '.PREF.'auth_tokens WHERE `token`=~~', [$matches[1]]);
	if(!$token) api_errors([format_error(ERROR_CODE_NOT_AUTENTICATED, 'unauthenticated')], 401, STATUS_UNATHORIZED);
	if($token['created'] + API_TOKEN_LIFETIME < time()) api_errors([format_error(ERROR_CODE_TOKEN_EXPIRED, 'token expired')], 401, STATUS_UNATHORIZED);
	return $token;
}

function create_auth_token($user_id){
	if(!$user_id) die('no user provided for token generation');
	$token = bin2hex(openssl_random_pseudo_bytes(16));
	db::query('INSERT INTO `pos_auth_tokens`(`user_id`, `created`, `token`) VALUES ('.$user_id.', UNIX_TIMESTAMP(), "'.$token.'")');
	return $token;
}

function clear_user_tokens($user_id){
	if(!$user_id) die('no user provided for clearing tokens');;
	db::query('DELETE FROM `pos_auth_tokens` WHERE `user_id` = '.$user_id);
}

function validate_params($data=[], $params=[]){
	$validation = [];
	foreach($params as $code => $param){
		if(empty($data[$param])) $validation[] = format_error($code, $param.' is required');
	}
	if($validation) api_errors($validation);
}

function send_order_notifications($client_id, $delivery_token){
	if(!$client_id) return;
	$client = db::get_row('SELECT * FROM '.PREF.'patients WHERE `id`='.$client_id);
	if(!$client || !isset($GLOBALS['oEmail'])) return;
	$message = 'Your order is being delivered, check delivery progress on '.HOST.'POS/delivery_status.php?token='.$delivery_token;
	if($client['email']){
		$GLOBALS['oEmail']->email(/*$client['email']*/'drvapptes@gmail.com', 'New order', $message);
	}
	if($client['phone']){
		$GLOBALS['oEmail']->sms(['3233099090']/*[$client['phone']]*/,$message, '');
	}
}

function send_push_notification($user_id, $message, $code){
	$devices = db::get('SELECT * FROM '.PREF.'user_devices WHERE `user_id`='.intval($user_id));
	if(!$devices) return;
	$android = $ios = [];
	foreach($devices as $device) {
		if($device['type'] == DEVICE_TYPE_ANDROID) $android[]=$device['token'];
		if($device['type'] == DEVICE_TYPE_IOS) $ios[]=$device['token'];
	}
	if($android){
		$push = new PushNotification(new GCMHandler());
		$push->setServerToken(GCM_SERVER_TOKEN);
		$push->addDevice($android);
		$push->send($message, ['code' => $code]);
	}
	if($ios){
		$push = new PushNotification(new APNSHandler());
		$push->setServer([
			'token' => APNS_CERT_PATH,
			'passphrase' => 'creastate',
			'url'   => APNS_SERVER_SANDBOX,
		]);
		$push->addDevice($ios);
		$push->send($message, ['code' => $code]);
	}
}