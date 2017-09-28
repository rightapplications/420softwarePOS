<?php
include_once '../includes/api.php';
include_once '../includes/driver.php';

define ('ERROR_CODE_LOCATION_NO_LAT', 1);
define ('ERROR_CODE_LOCATION_NO_LNG', 2);

$token = authenticate();

$data = get_post();
validate_params($data, [ERROR_CODE_LOCATION_NO_LAT => 'lat', ERROR_CODE_LOCATION_NO_LNG => 'lng']);

$driver = get_driver_profile($token['user_id']);
if(!$driver) api_errors(format_error(ERROR_CODE_SERVER_ERROR, 'driver profile not found'), 500);

update_driver_profile($driver['id'], ['lat' => $data['lat'], 'lng' => $data['lng']]);
echo api_success();