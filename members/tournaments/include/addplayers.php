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

	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
		
		$arrPlayers = $tournamentObj->getPlayers();
		$playerList = urlencode($_POST['players']);
		$arrNewPlayers = explode("%0A", $playerList);
	
		$maxPlayers = $tournamentInfo['playersperteam']*$tournamentInfo['maxteams'];
		
		
		
		if((count($arrNewPlayers)+count($arrPlayers)) <= $maxPlayers) {
		
		
			foreach($arrNewPlayers as $newPlayer) {
				$newPlayer = urldecode($newPlayer);
				
				$arrPlayers = $tournamentObj->getPlayers();

					if($member->select($newPlayer)) {
						$newPlayerID = $member->get_info("member_id");
						
						if(!in_array($newPlayerID, $arrPlayers)) { // Prevent multiple entries of same person
						
							$tournamentObj->objPlayer->addNew(array("member_id", "tournament_id"), array($newPlayerID, $tID));
					
						}
					
					}
					elseif($tournamentInfo['access'] != 1) {
						
						if(!in_array($newPlayer, $arrPlayers)) { // Prevent multiple entries of same person
						
							$tournamentObj->objPlayer->addNew(array("displayname", "tournament_id"), array($newPlayer, $tID));
						
						}
					}
					
					
					if($tournamentInfo['playersperteam'] == 1) {
						
						$arrUnfilledTeams = $tournamentObj->getUnfilledTeams();
						if(count($arrUnfilledTeams) > 0) {
							
							
							$newTeam = $arrUnfilledTeams[0];
							$tournamentObj->objPlayer->update(array("team_id"), array($newTeam));
														
						}
					}

			}
		
		
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#newplayers').val('');
					});
				</script>
			";
			
	}
	else {
		
		$filterPlayers = filterText($_POST['players']);
		echo "
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
					$('#newplayers').val('".$filterPlayers."');
					
					$('#errorMessage').dialog({
					
						title: 'Add Players - Error!',
						zIndex: 99999,
						modal: true,
						show: 'scale',
						width: 400,
						resizable: false,
						buttons: {
						
							'OK': function() {
								$(this).dialog('close');
							}
						
						}
					
					});
				
	
					
				});
			</script>
		
		";
		
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