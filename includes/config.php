<?php
define('CLIENT_ID', 'superclinic');

//--------------database connection-------------------------
$aConf['host']="db host";
$aConf['user']="db user";
$aConf['password']="db password";
$aConf['database']="db name";
$aConf['encoding']="utf8";



define('PREF', 'pos_');

//--------------folders------------------------------------
define('ABS', "/path/to/your/project/folder/");
define('HOST', 'http://your.server.address/');
define('SITE_NAME', '420software POS');

//--------------pagination---------------------------------
$SEARCH_ROWS_MAX = 15;

//--------------email---------------------------------------
define('ADMIN_EMAIL', 'admin@example.com');
define('MAIL_SMTP',false);
define('SMTP_HOST','localhost');
define('SMTP_AUTH',false);
define('SMTP_USER','user');
define('SMTP_PASS','pass');
define('MAIL_FROM', 'mailer@example.com');
define('MAIL_CHARSET', 'windows-1251');

define('FILE_PORTAL_URL', 'http://files.portal.server');
define('MESSAGING_PORTAL_URL', 'http://messaging.portal.server');

//----------printer settings---------
define('PRINTER_URL', 'https://local.printer.server:8443');

//------------mailgun----------------
define('MAILER_HOST','smtp.rest.mailgun.org');
define('MAILER_FROM','mail@sandbox.mailgun.org');
define('MAILER_FROMNAME','Mailer');
define('MAILER_USERNAME','test_postmaster@sandbox.mailgun.org');
define('MAILER_PASSWORD','test_password');

define('GALLERY_FOLDER', 'gallery');
define('ATTACHMENT_FOLDER', 'attachments');
define('IMAGE_ORIGINAL_WIDTH', '400');
define('MAX_ALLOWED_IMAGE_SIZE', '200000'); //bytes
define('MAX_ALLOWED_IMAGE_WIDTH', '2000');
define('MAX_ALLOWED_IMAGE_HEIGHT', '2000');

//-----------SMS Provider-----------------------------------
define('SMS_PORTAL_HOST', 'https://app.grouptexting.com/');
define('SMS_PORTAL_LOGIN', 'username');
define('SMS_PORTAL_PASS', 'password');

define('API_KEY', 'REMOTE ORDER API KEY');

define('REDIS_SERVER', 'redis server ip address');
define('REDIS_PORT', 'redis port');

define('WEEDMAPS_API_SERVER','https://weedmaps.com/api/web/v1/integrator_menu_items');

//date format
define('DATE_FORMAT',"%m/%d/%Y");

//main currency
define('CURRENCY',"$");

define('WORK_START','4');
define('WORK_END','21');

?>