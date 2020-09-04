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

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->objTeam->select($_POST['teamID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();

	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	$tournamentObj->select($teamInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$tmemberID = $tournamentInfo['member_id'];
	
	
	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {

		if($_POST['getWhat'] == "name") {
		
			echo $teamInfo['name'];
		
		}
		elseif($_POST['getWhat'] == "playerlist") {
			
			$arrPlayers = $tournamentObj->getTeamPlayers($teamInfo['tournamentteam_id'], true);
			
			$playerCount = 1;
			foreach($arrPlayers as $playerID) {
				
				$tournamentObj->objPlayer->select($playerID);
				$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
				
				if(is_numeric($playerInfo['member_id']) && $member->select($playerInfo['member_id'])) {
					$dispPlayerName = $member->get_info_filtered("username");
				}
				else {
					$dispPlayerName = $playerInfo['displayname'];
				}
				
				
				echo "
					<div class='mttPlayerSlot main'>
						".$playerCount.". <a href='javascript:void(0)' onclick=\"setPlayerTeam('".$playerID."')\">".$dispPlayerName."</a>
						<div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"deletePlayer('".$playerInfo['tournamentplayer_id']."')\">X</a></div>
					</div>
				";
				
				$playerCount++;
				
			}

			for($i=$playerCount; $i<=$tournamentInfo['playersperteam']; $i++) {
				echo "<div class='mttPlayerSlot main'>".$i.". <span style='font-style: italic'>Empty Player Slot</span></div>";
			}
			
			
		}
		
	}
	
	
}





?>