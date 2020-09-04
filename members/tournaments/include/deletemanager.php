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
include_once("../../../classes/tournament.php");


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($_POST['tournamentID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();

	$tournamentInfo = $tournamentObj->get_info_filtered();
	$tmemberID = $tournamentInfo['member_id'];
	
	
	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1") {
			
		$tournamentObj->deleteManager($_POST['managerID']);
		
		define("SHOW_MANAGERLIST", true);
		include("managerlist.php");
		
		
	}
	
	
}


?>