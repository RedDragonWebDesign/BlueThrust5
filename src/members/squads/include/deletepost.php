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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/squad.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$squadObj = new Squad($mysqli);
$arrSquadPrivileges = $squadObj->arrSquadPrivileges;

$pID = "managenews";



// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	
	$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");
	
	
	if($squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], $pID) && $squadNewsObj->select($_POST['nID'])) {
		
		if($_POST['confirm'] == 1) {
			
			$squadNewsObj->delete();
			$_POST['pID'] = $pID;
			include("newslist.php");
			
		}
		else {
			echo "
				<p align='center' class='main'>Are you sure you want to delete the news post?</p>
			";
			
		}
		
		
	}
	
	
	
}



?>