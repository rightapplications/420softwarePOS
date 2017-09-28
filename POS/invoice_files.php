<?php
include_once '../includes/common.php';
checkAccess(array('1'), 'login.php');
ignore_user_abort(true);

if(!isset($_GET['from'], $_GET['to'])) ajax_error('missing required parameters');
$categorySQL = "";
if(!empty($_GET['category'])) $categorySQL = "AND category_id = '".intval($_GET['category'])."'";
$files = db::get("SELECT ".PREF."invoices.file FROM ".PREF."invoices WHERE ".PREF."invoices.date >= ~~ AND ".PREF."invoices.date <= ~~ $categorySQL", [$_GET['from'], $_GET['to']]);
if(!$files) ajax_error('no files found');

$found = [];
foreach($files as $file){
	if(!file_exists(ABS.GALLERY_FOLDER.'/'.$file['file'])) continue;
	$found[] = $file['file'];
}
if(!$found) ajax_error('no files found');

$zip = new ZipArchive();
$file_name = uniqid().'_invoices.zip';
$path = ABS.ATTACHMENT_FOLDER.'/'.$file_name;
//create the file and throw the error if unsuccessful
if ($zip->open($path, ZIPARCHIVE::CREATE )!==TRUE) {
    ajax_error("cannot open $archive_file_name");
}
//add each files of $file_name array to archive
foreach($found as $file){
    $zip->addFile(ABS.GALLERY_FOLDER.'/'.$file, $file);
}
$zip->close();

//then send the headers to force download the zip file
header("Content-type: application/zip"); 
header("Content-Disposition: attachment; filename=$file_name");
header("Content-length: " . filesize($path));
header("Pragma: no-cache"); 
header("Expires: 0");
readfile($path);
unlink($path);