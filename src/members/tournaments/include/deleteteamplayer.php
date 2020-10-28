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


require_once("../../../_setup.php");
require_once("../../../classes/member.php");
require_once("../../../classes/rank.php");
require_once("../../../classes/tournament.php");


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->objPlayer->select($_POST['playerID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();

	$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
	
	$tournamentObj->objTeam->select($playerInfo['team_id']);
	$tournamentObj->select($playerInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$tmemberID = $tournamentInfo['member_id'];
	
	
	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
		
		$tournamentObj->objPlayer->update(array("team_id"), array(0));	

		$_POST['teamID'] = $tournamentObj->objTeam->get_info("tournamentteam_id");
		$_POST['getWhat'] = "playerlist";
		require_once("getteaminfo.php");
		
		
	}
	
	
}