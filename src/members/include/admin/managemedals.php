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
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

$medalObj = new Medal($mysqli);
$objManageList = new btOrderManageList($medalObj);
$objManageList->strMainListLink = BASE_DIRECTORY."members/include/admin/medals/main.php";

if($_GET['mID'] != "" && $medalObj->select($_GET['mID']) && $_GET['action'] == "edit") {
	require_once("medals/edit.php");
}
elseif($_GET['action'] != "move") {
	require_once($objManageList->strMainListLink);
}