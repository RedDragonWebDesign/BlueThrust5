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
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");
include_once("../../../../classes/consolecategory.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$consoleObj = new ConsoleOption($mysqli);
$consoleCatObj = new ConsoleCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $consoleObj->select($_POST['cID'])) {
		
		define("MEMBERRANK_ID", $memberInfo['rank_id']);
		
		$consoleInfo = $consoleObj->get_info();
		
		
		if($_POST['confirm'] == 1) {
			$consoleObj->delete();
			$consoleObj->resortOrder();
			$_GET['cID'] = $cID;
			include("main.php");
	

		}
		else {
			$consoleName = $consoleObj->get_info_filtered("pagetitle");
			echo "<p align='center'>Are you sure you want to delete the console option <b>".$consoleName."</b>?</p>";
		}
		
	}
	elseif(!$consoleObj->select($_POST['cID'])) {
		
		echo "<p align='center'>Unable find the selected console option.  Please try again or contact the website administrator.</p>";
		
	}
	
}



?>