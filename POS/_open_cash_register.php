<?php
include_once '../includes/common.php';
checkAccess(array('1','2','3', '4'), 'login.php');

include '../templates/POS/_open_cash_register_tpl.php';