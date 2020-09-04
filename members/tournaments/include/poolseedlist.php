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
$tID = $_POST['tID'];
$arrMembers = array();

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($tID) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();
	$tournamentInfo = $tournamentObj->get_info_filtered();
	
	if($tournamentInfo['playersperteam'] == 1) {
		$dispTeamOrPlayer = "Player";
	}
	else {
		$dispTeamOrPlayer = "Team";
	}
	
	if(($memberInfo['member_id'] == $tournamentInfo['member_id'] || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) && $tournamentInfo['seedtype'] == 3) {
	
		echo "
		
				<table class='formTable' align='center' style='margin-left: auto; margin-right: auto; width: 250px'>
					<tr>
						<td class='formTitle' style='width: 40px'>Seed:</td>
						<td class='formTitle' style='width: 210px'>".$dispTeamOrPlayer.":</td>
					</tr>
				
					";
		
				
				$arrTeams = $tournamentObj->getTeams(true, "ORDER BY seed");
				foreach($arrTeams as $teamID) {	
					$dispName = $tournamentObj->getPlayerName($teamID);
					$tournamentObj->objTeam->select($teamID);
					$dispSeed = $tournamentObj->objTeam->get_info("seed");
					$teamPoolID = $tournamentObj->getTeamPoolID($teamID);
					$tournamentObj->objTournamentPool->select($teamPoolID);
					$tournamentObj->objTournamentPool->getTeamRecord($teamID);
					
					echo "
						<tr>
							<td class='main' align='center'>".$dispSeed.".</td>
							<td class='main' style='padding-left: 5px'><a href='javascript:void(0)' onclick=\"setSeed('".$teamID."')\">".$dispName."</a> (".$tournamentObj->objTournamentPool->getTeamRecord($teamID).")</td>
						</tr>
					";
					
					
					$seedCount++;
					
				}
				
				echo "
					
				</table>
		
		
		
		";

	}

}

?>