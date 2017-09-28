<?
session_start();

require('config.php');

require('app_config.php');
require('func.php');
require('helpers.php');
function __autoload($class_name) {
    if($class_name == 'PHPMailer'){
        require("PHPMailer/class.phpmailer.php"); 
    }elseif($class_name == 'SMTP'){
        require("PHPMailer/class.smtp.php"); 
    }elseif($class_name == 'db'){
        require_once 'db.php';
    }elseif($class_name == 'settings'){
        require_once 'settings.php';
    }elseif(strpos($class_name, 'ricwein\PushNotification') === 0){
    	require_once str_replace('\\', '/', str_replace('ricwein\PushNotification', 'push_notifications', $class_name)) . '.php';
    }else{
        require_once $class_name . '.php';
    }
}

db::connect($aConf);
$oEmail = new class_email();
$oUser = new class_user();
$oPatient = new class_patient();
$oInventory = new class_inventory();
$oOrder = new class_order();
$oMessenger = new class_messenger();

if(isset($_SERVER['HTTPS'])){
    $ssl = 1;
}else{
    $ssl = 0;
}

$timezone = 'America/Los_Angeles';
date_default_timezone_set($timezone); 
$time = new \DateTime('now', new DateTimeZone($timezone));
$offset = $time->format('P');
db::query("SET time_zone = '".$offset."'");

if(isset($_COOKIE['theme'])){
    setcookie('theme', @strval($_COOKIE['theme']), time()+2592000);
}else{
    setcookie('theme', 3, time()+2592000);
}