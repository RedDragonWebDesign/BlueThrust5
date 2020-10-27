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


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$manageClanCID = $consoleObj->findConsoleIDByName("Diplomacy: Manage Clans");
$consoleObj->select($manageClanCID);

$diplomacyClanObj = new Basic($mysqli, "diplomacy", "diplomacy_id");

if($member->authorizeLogin($_SESSION['btPassword']) && $diplomacyClanObj->select($_POST['dClanID']) && $member->hasAccess($consoleObj)) {
	
	
	$dClanName = $diplomacyClanObj->get_info_filtered("clanname");
	
	if(isset($_POST['confirmDelete'])) {
		$diplomacyClanObj->delete();	
		$member->logAction("Deleted ".$dClanName." from the diplomacy page.");
		
		
		include("main_manageclans.php");
	}
	else {
		
		echo "<p class='main' align='center'>Are you sure you want to delete ".$dClanName." from the diplomacy page?</p>";
		
	}
	
}



?>