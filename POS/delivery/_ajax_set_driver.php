<?php
include_once '../../includes/common.php';
include_once '../../includes/delivery_groups.php';
checkAccess(array('1','2'), '');

if(empty($_POST['group'])) ajax_error('no group received');
if(empty($_POST['driver'])) ajax_error('no driver received');

if(assign_group(intval($_POST['group']), intval($_POST['driver']))){
	die('success');
} else {
	ajax_error('failed to assign group');
}