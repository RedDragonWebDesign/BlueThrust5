<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {	
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID'] ?? '');
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'] ?? '';

$socialObj = new Social($mysqli);
$objManageList = new btOrderManageList($socialObj);
$objManageList->strMainListLink = BASE_DIRECTORY."members/include/social/include/main.php";

// Check if 'sID' and 'action' are set in the $_GET array
if(isset($_GET['sID'], $_GET['action']) && $_GET['sID'] != "" && $socialObj->select($_GET['sID']) && $_GET['action'] == "edit") {
	require_once("include/edit.php");
}
elseif(isset($_GET['action'], $_POST['itemID']) && $_GET['action'] == "delete" && $socialObj->select($_POST['itemID'])) {
	$socialInfo = $socialObj->get_info_filtered();
	$objManageList->strDeleteName = $socialInfo['name'];
	$objManageList->strDeletePostVarID = "sID";
}
elseif(!isset($_GET['action']) || $_GET['action'] != "move") {	
	require_once($objManageList->strMainListLink);
}
