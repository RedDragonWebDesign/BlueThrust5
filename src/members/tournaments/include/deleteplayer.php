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
	$tmemberID = $tournamentObj->get_info("member_id");
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$maxPlayers = $tournamentInfo['playersperteam']*$tournamentInfo['maxteams'];
	
	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
		
		if($tournamentObj->objPlayer->select($_POST['playerID']) && $tournamentObj->objPlayer->get_info("tournament_id") == $tID) {
			
			$tournamentObj->objPlayer->delete();
			
		}

		
		$arrPlayers = $tournamentObj->getPlayers();
		$counter = 1;
		foreach($arrPlayers as $playerID) {
		
			$tPlayerID = $tournamentObj->getTournamentPlayerID($playerID);
			
			$tournamentObj->objPlayer->select($tPlayerID);
			$playerInfo = $tournamentObj->objPlayer->get_info();
			
			if($member->select($playerID)) {
		
				$dispPlayer = $member->get_info_filtered("username");
				
				
			}
			else {
				
				$dispPlayer = $playerID;
				
			}
			
			$teamID = $playerInfo['team_id'];
			$arrSortPlayers[$teamID] = strtolower($dispPlayer);
			
			$arrDispPlayer[$teamID] = "
				<a href='javascript:void(0)' onclick=\"setPlayerSeed('".$playerInfo['team_id']."')\">".$dispPlayer."</a><div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"deletePlayer('".$playerInfo['tournamentplayer_id']."')\">X</a></div>
			";
			
			
		
			//$counter++;
		}
		
		asort($arrSortPlayers);
		foreach($arrSortPlayers as $key=>$value) {
			echo "<div class='mttPlayerSlot main'>".$counter.". ".$arrDispPlayer[$key]."</div>";
			$counter++;
		}
		
		if(count($arrPlayers) < $maxPlayers) {
			
			for($i=$counter; $i<=$maxPlayers; $i++) {
				echo "
					<div class='mttPlayerSlot main'>".$i.". <span style='font-style: italic'>Empty Player Slot</span></div>
				";
			}
			
		}
		
		
	}
	
	
}


?>