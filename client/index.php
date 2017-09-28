<?php
include_once '../includes/common.php';
checkAccess(array('1','2','4'), 'login.php');

include '../templates/client/index_tpl.php';