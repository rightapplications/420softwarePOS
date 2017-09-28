<?php
include_once '../includes/api.php';

define ('ERROR_CODE_DEVICE_NO_TYPE', 1);
define ('ERROR_CODE_DEVICE_NO_TOKEN', 2);
define ('ERROR_CODE_DEVICE_INVALID_TYPE', 2);

$token = authenticate();

$data = get_post();
validate_params($data, [ERROR_CODE_DEVICE_NO_TYPE => 'type', ERROR_CODE_DEVICE_NO_TOKEN => 'token']);
if(!in_array($data['type'], [DEVICE_TYPE_ANDROID, DEVICE_TYPE_IOS])) api_errors(format_error(ERROR_CODE_DEVICE_INVALID_TYPE, 'invalid device type'));

$existing = db::get_row('SELECT * FROM '.PREF.'user_devices WHERE `user_id`='.$token['user_id'].' AND `type`='.intval($data['type']));
if($existing) {
	db::query('UPDATE '.PREF.'user_devices SET `token`=~~ WHERE `id`='.$existing['id'], [$data['token']]);
} else {
	db::query('INSERT INTO '.PREF.'user_devices (`user_id`, `type`, `token`) VALUES ('.$token['user_id'].', '.intval($data['type']).', ~~)', [$data['token']]);
}
api_success();