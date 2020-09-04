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


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$medalObj = new Medal($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Medals");
$consoleObj->select($cID);
$_GET['cID'] = $cID;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $medalObj->select($_POST['itemID'])) {
		
		define("LOGGED_IN", true);
		
		
		if($_POST['confirm'] == 1) {
			$medalObj->delete();

			$objManageList = new btOrderManageList($medalObj);
			$objManageList->strMainListLink = BASE_DIRECTORY."members/include/admin/medals/main.php";

			include($objManageList->strMainListLink);
			include(BASE_DIRECTORY."members/console.managelist.list.php");
			
		}
		else {
			$medalName = $medalObj->get_info_filtered("name");
			echo "<p align='center'>Are you sure you want to delete the medal <b>".$medalName."</b>?</p>";
		}
		
	}
	elseif(!$medalObj->select($_POST['itemID'])) {
		
		echo "<p align='center'>Unable find the selected medal.  Please try again or contact the website administrator.</p>";
		
	}
	
}



?>