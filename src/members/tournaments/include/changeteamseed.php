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

	if(($memberInfo['member_id'] == $tournamentInfo['member_id'] || $memberInfo['rank_id'] == "1")  || $tournamentObj->isManager($memberInfo['member_id']) && $tournamentObj->objTeam->select($_POST['teamID']) && $tournamentObj->objTeam->get_info("tournament_id") == $tID) {
		
		$teamInfo = $tournamentObj->objTeam->get_info_filtered();
		
		if(!isset($_POST['newSeed'])) {
			
			for($i=1; $i<=$tournamentInfo['maxteams']; $i++) {
				
				$dispSelected = "";
				if($teamInfo['seed'] == $i) { 
					$dispSelected = "selected";	
				}
				
				$seedOptions .= "<option value='".$i."' ".$dispSelected.">".$i."</option>";
				
				
			}
			
			
			if($tournamentInfo['playersperteam'] == 1) {
				$tPlayers = $tournamentObj->getTeamPlayers($_POST['teamID'], true);
				$tournamentObj->objPlayer->select($tPlayers[0]);
				$playerInfo = $tournamentObj->objPlayer->get_info();
				
				if($member->select($playerInfo['member_id'])) {
					$dispName = $member->get_info_filtered("username");
				}
				else {
					$tournamentObj->objPlayer->select($tPlayers[0]);
					$dispName = $tournamentObj->objPlayer->get_info_filtered("displayname");
				}
				
				$dispTeamOrPlayer = "Player";
			
			}
			else {
				$dispName = $tournamentObj->objTeam->get_info_filtered("name");
				$dispTeamOrPlayer = "Team";
			}
			
			echo "
				<p class='main'>Use the form below to change the selected ".strtolower($dispTeamOrPlayer)."'s seed.</p>
				<table class='formTable' style='width: 350px'>
					<tr>
						<td class='formLabel'>".$dispTeamOrPlayer.":</td>
						<td class='main'>".$dispName."</td>
					</tr>
					<tr>
						<td class='formLabel'>Seed:</td>
						<td class='main'><select id='newSeedSelect' class='textBox'>".$seedOptions."</option></td>
					</tr>
				</table>
			";
			
			
		}
		elseif(isset($_POST['newSeed']) && $_POST['newSeed'] > 0 && $_POST['newSeed'] <= $tournamentInfo['maxteams'] && $tournamentObj->getTeamIDBySeed($_POST['newSeed']) !== false) {
			
			if($tournamentInfo['playersperteam'] == 1) {
				$strPlayerTeam = "Player";
			}
			else {
				$strPlayerTeam = "Team";
			}
			
			$swappingTeamInfo = $tournamentObj->objTeam->get_info();
			
			$teamMatchID = $tournamentObj->getMatches(1, $teamInfo['tournamentteam_id']);
			$swappingTeamMatchID = $tournamentObj->getMatches(1, $swappingTeamInfo['tournamentteam_id']);
			
			$blnCheck1 = $tournamentObj->objTeam->update(array("seed"), array($teamInfo['seed']));
			$tournamentObj->objTeam->select($teamInfo['tournamentteam_id']);
			$blnCheck2 = $tournamentObj->objTeam->update(array("seed"), array($swappingTeamInfo['seed']));
			
			$tournamentObj->objMatch->select($teamMatchID[0]);
			$teamMatchInfo = $tournamentObj->objMatch->get_info();
			if($teamMatchInfo['team1_id'] == $teamInfo['tournamentteam_id']) {
				$arrUpdateColumn = array("team1_id");
			}
			else {
				$arrUpdateColumn = array("team2_id");
			}
			
			$blnCheck3 = $tournamentObj->objMatch->update($arrUpdateColumn, array($swappingTeamInfo['tournamentteam_id']));
			
			$tournamentObj->objMatch->select($swappingTeamMatchID[0]);
			$swappingTeamMatchInfo = $tournamentObj->objMatch->get_info();
			if($swappingTeamMatchInfo['team1_id'] == $swappingTeamInfo['tournamentteam_id']) {
				$arrUpdateColumn = array("team1_id");
			}
			else {
				$arrUpdateColumn = array("team2_id");
			}
			
			$blnCheck4 = $tournamentObj->objMatch->update($arrUpdateColumn, array($teamInfo['tournamentteam_id']));
			
			
			
			if($blnCheck1 && $blnCheck2 && $blnCheck3 & $blnCheck4) {
				
				echo "
					<p class='main' align='center'>
						".$strPlayerTeam." Seed Changed Successfully!
					</p>
				";
				
			}
			else {
				
				echo "
				<p class='main' align='center'>
					Unable to change ".$strPlayerTeam." Seed!
				</p>
				";
				
			}
			
			
		}
		
		
	}
	
	
}


?>