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
include_once("../../../../classes/customform.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Custom Form Pages");
$consoleObj->select($cID);

$customPageObj = new CustomForm($mysqli);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $customPageObj->select($_POST['cpID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
	
		$customPageInfo = $customPageObj->get_info_filtered();
		
		if($_POST['confirm'] == "1") {
			
			$customPageObj->delete();
			include("main_customforms.php");
			
		}
		else {
			echo "<p align='center'>Are you sure you want to delete the custom form <b>".$customPageInfo['name']."</b>?<br><br>All submissions related to this form will also be deleted.";
		}
		
	}
	elseif(!$customPageObj->select($_POST['cpID'])) {
	
		echo "<p align='center'>Unable find the selected custom form.  Please try again or contact the website administrator.</p>";
	
	}
	
	
	
	
}


?>