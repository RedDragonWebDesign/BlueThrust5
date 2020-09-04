<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/download.php");
include_once("../../../../classes/downloadcategory.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Downloads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

// Check Login
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info();
}
else {
	exit();
}

$downloadObj = new Download($mysqli);
$downloadCatObj = new DownloadCategory($mysqli);



if($downloadObj->select($_POST['dlID']) && isset($_POST['confirm'])) {
	$downloadInfo = $downloadObj->get_info_filtered();	
	$downloadObj->delete();
	
	unlink("../../../../".$downloadInfo['splitfile1']);
	unlink("../../../../".$downloadInfo['splitfile2']);

	
	include("downloadlist.php");
	
}
elseif($downloadObj->select($_POST['dlID'])) {
	
	$downloadInfo = $downloadObj->get_info_filtered();
	echo "
		<p align='center' class='main'>Are you sure you want to delete the download: <b>".$downloadInfo['name']."</b>?</p>	
	";
	
}
else {
	echo "
		<p align='center' class='main'>Download not found</p>
	";
}


?>