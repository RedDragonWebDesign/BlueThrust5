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
		
		
		$arrAllPlayers = $tournamentObj->getPlayers();
		$playerList = urlencode($_POST['players']);
		$arrNewPlayers = explode("%0A", $playerList);
		$arrTeamPlayers = $tournamentObj->getTeamPlayers($_POST['teamID']);
		$teamPlayerCount = count($arrTeamPlayers);
		$blnErrorDuplicatePlayer = false;
		$blnErrorFullTeam = false;
		foreach($arrNewPlayers as $newPlayer) {
			
			$newPlayer = urldecode($newPlayer);
			if($teamPlayerCount < $tournamentInfo['playersperteam']) {
				if($member->select($newPlayer)) {
					$checkMemberID = $member->get_info("member_id");
	
					if(!in_array($checkMemberID, $arrAllPlayers)) {
						
						if($tournamentObj->objPlayer->addNew(array("tournament_id", "team_id", "member_id"), array($tournamentInfo['tournament_id'], $_POST['teamID'], $checkMemberID))) {
						
							$teamPlayerCount++;
						
						}
						
						
					}
					else {
						
						$dispErrorMembers .= "<b>&middot;</b> ".$member->getMemberLink()."<br>";
						$blnErrorDuplicatePlayer = true;
						
					}
					
					
				}
				elseif(!$member->select($newPlayer) && $tournamentInfo['access'] != 1) {
					
					if($tournamentObj->objPlayer->addNew(array("tournament_id", "team_id", "displayname"), array($tournamentInfo['tournament_id'], $_POST['teamID'], $newPlayer))) {
						
						$teamPlayerCount++;
						
					}
					
				}
				
			}
			else {
				$blnErrorFullTeam = true;
				break;
			}
			
		}
		
		
	}
	
	
}

$_POST['getWhat'] = "playerlist";
include("getteaminfo.php");

echo "
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#newplayers').val('');
		});
	</script>
";

if($blnErrorDuplicatePlayer) {

	echo "
	
	<div id='errorMessage1' class='main' style='display: none'>
		<p>
			The following players are already on a team in this tournament:
			<p>
				".$dispErrorMembers."
			</p>
		</p>
	</div>
	
	<script type='text/javascript'>
	
		$('#errorMessage1').dialog({
					
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
				
	
	</script>
	
	
	
	";
	
}

if($blnErrorFullTeam) {

	echo "

	<div id='errorMessage2' class='main' style='display: none'>
		<p align='center'>
			This team is currently full!
		</p>
	</div>
	
	<script type='text/javascript'>
	
		$('#errorMessage2').dialog({
					
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
				
	
	</script>


";

}

?>