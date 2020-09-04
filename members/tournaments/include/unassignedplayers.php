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


if(!defined("SHOW_UNASSIGNEDPLAYERS")) {
	
	
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
		$tID = $tournamentInfo['tournament_id'];
		
		if($memberInfo['member_id'] != $tmemberID && $memberInfo['rank_id'] != "1" && !$tournamentObj->isManager($memberInfo['member_id'])) {	
			exit();
		}
		
	}
	

	$arrTeams = $tournamentObj->getTeams();
	
	if(isset($_POST['action']) && $_POST['action'] == "remove") {
		
		$arrRemovePlayers = json_decode($_POST['playerList'], true);
		
		foreach($arrRemovePlayers as $playerID) {
		
			if($tournamentObj->objPlayer->select($playerID)) {
				$tournamentObj->objPlayer->delete();
			}			
		}

	}
	elseif(isset($_POST['action']) && $_POST['action'] == "add" && in_array($_POST['teamID'], $arrTeams)) {
		$arrUnableToAddPlayer = array();
		$arrAddPlayers = json_decode($_POST['playerList'], true);
		foreach($arrAddPlayers as $playerID) {
			
			$arrUnfilledTeams = $tournamentObj->getUnfilledTeams();
			$blnBasicChecks = $tournamentObj->objPlayer->select($playerID) && $tournamentObj->objPlayer->get_info("tournament_id") == $_POST['tournamentID'];
			if($blnBasicChecks && in_array($_POST['teamID'], $arrUnfilledTeams)) {
				$tournamentObj->objPlayer->update(array("team_id"), array($_POST['teamID']));
			}
			elseif($blnBasicChecks && !in_array($_POST['teamID'], $arrUnfilledTeams)) {
				$arrUnableToAddPlayer[] = $playerID;
			}
		}
		
	}
	
	
}


echo "
	<table class='formTable' style='border-spacing: 0px'>
			";
	
	$arrUnassignedPlayers = array();
	$result = $mysqli->query("SELECT tournamentplayer_id FROM ".$dbprefix."tournamentplayers WHERE tournament_id = '".$tID."' AND team_id = '0'");
	while($row = $result->fetch_assoc()) {
		$tournamentObj->objPlayer->select($row['tournamentplayer_id']);
		$playerInfo = $tournamentObj->objPlayer->get_info_filtered();	
		
		if($member->select($playerInfo['member_id']) && $playerInfo['member_id'] != 0) {
			$arrUnassignedPlayers[$row['tournamentplayer_id']] = $member->getMemberLink();	
		}
		else {
			$arrUnassignedPlayers[$row['tournamentplayer_id']] = $playerInfo['displayname'];
		}
			
		
	}
	
	
	asort($arrUnassignedPlayers);
	
	$counter = 0;
	foreach($arrUnassignedPlayers as $playerID => $playerName) {
		
		$tournamentObj->objPlayer->select($playerID);
		$plainTextUsername = "";
		if($member->select($tournamentObj->objPlayer->get_info("member_id"))) {
			$plainTextUsername = $member->get_info_filtered("username");	
		}
		
		if($counter == 1) {
			$addCSS = " alternateBGColor";
			$counter = 0;
		}
		else {
			$addCSS = "";
			$counter = 1;
		}
		
		echo "
				<tr>
					<td class='main manageList".$addCSS."' style='text-align: center; width: 5%'><input type='checkbox' value='".$playerID."' data-unassignedplayer='1' data-username='".$plainTextUsername."'></td>
					<td class='main manageList".$addCSS."' style='padding-left: 10px'>".$playerName."</td>
				</tr>			
			";
		
	}
	
	echo "
		</table>
		";
	
	
	if($result->num_rows == 0) {

		echo "
		
			<div class='shadedBox main' style='width: 45%; margin-left: auto; margin-right: auto'>
				<p align='center'><i>There are no unassigned players!</i></p>
			</div>
		
		";
		
	}
	
	$member->select($memberInfo['member_id']);

	if(isset($arrUnableToAddPlayer) && count($arrUnableToAddPlayer) > 0) {
		echo "
			<div id='unassignedPlayersMessage' class='main' style='display: none'>
				<p align='center' class='main'>
					This team is full! The following players were not added:<br>
				</p>
				<ul>
					";
				foreach($arrUnableToAddPlayer as $playerID) {
					if($tournamentObj->objPlayer->select($playerID) && $member->select($tournamentObj->objPlayer->get_info("member_id"))) {
						echo "
							<li>".$member->getMemberLink()."</li>
						";
					}
				}
		echo "
				</ul>
			</div>
			<script type='text/javascript'>
				$(document).ready(function() {
				
					$('#unassignedPlayersMessage').dialog({
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
		$member->select($memberInfo['member_id']);
	}
		
?><br>