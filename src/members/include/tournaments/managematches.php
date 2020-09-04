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



if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/tournament.php");


$tMemberObj = new Member($mysqli);

$tournamentObj = new Tournament($mysqli);

$arrTournaments = $member->getTournamentList();

$dispError = "";
$countErrors = 0;


if(isset($_GET['mID']) && $tournamentObj->objMatch->select($_GET['mID'])) {
	
	include("include/managematch.php");
	
	
}
elseif(isset($_GET['pID']) && $tournamentObj->objTournamentPool->objTournamentPoolMatch->select($_GET['pID'])) {

	include("include/managepoolmatch.php");
	
}
else {

	echo "
		<table class='formTable' id='tournamentListTable'>
			<tr>
				<td class='formTitle' style='width: 45%'>Tournament:</td>
				<td class='formTitle' style='width: 40%'>Opponent:</td>
				<td class='formTitle' style='width: 15%'>Round:</td>
			</tr>
	";
	$counter = 0;
	foreach($arrTournaments as $tournamentID) {
		
		$tournamentObj->select($tournamentID);
		$tournamentName = $tournamentObj->get_info_filtered("name");
		$playerID = $tournamentObj->getTournamentPlayerID($memberInfo['member_id']);
		
		$tournamentObj->objPlayer->select($playerID);
		
		$teamID = $tournamentObj->objPlayer->get_info("team_id");
		
		// Get matches
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentpools_teams WHERE tournament_id='".$tournamentID."' AND ((team1_id != '0' AND team2_id = '".$teamID."') OR (team2_id != '0' AND team1_id = '".$teamID."')) AND (team1approve = '0' OR team2approve = '0')");
		while($row = $result->fetch_assoc()) {
			$counter++;
			
			if($row['team1_id'] != $teamID) {
				$dispOpponent = $tournamentObj->getPlayerName($row['team1_id']);
			}
			else {
				$dispOpponent = $tournamentObj->getPlayerName($row['team2_id']);	
			}
			$tournamentObj->objPlayer->select($playerID);
			
			if($tMemberObj->select($dispOpponent)) {
				$dispOpponent = $tMemberObj->getMemberLink();	
			}
			
			$dispPools .= "
				<tr>
					<td class='main' style='height: 20px'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&pID=".$row['poolteam_id']."'>".$tournamentName."</a></td>
					<td class='main' style='height: 20px; padding-left: 10px'>".$dispOpponent."</td>
					<td class='main' style='height: 20px' align='center'>Pool</td>
				</tr>
			";
		}
		
		
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentmatch WHERE tournament_id='".$tournamentID."' AND ((team1_id != '0' AND team2_id = '".$teamID."') OR (team2_id != '0' AND team1_id = '".$teamID."')) AND (team1approve = '0' OR team2approve = '0') ORDER BY round");
		
		
		while($row = $result->fetch_assoc()) {
			$counter++;
		
			
			if($row['team1_id'] != $teamID) {
				$dispOpponent = $tournamentObj->getPlayerName($row['team1_id']);
			}
			else {
				$dispOpponent = $tournamentObj->getPlayerName($row['team2_id']);
			}
			$tournamentObj->objPlayer->select($playerID);
			
			if($tMemberObj->select($dispOpponent)) {
				$dispOpponent = $tMemberObj->getMemberLink();
			}

			
			$arrDispMatches[$row['round']] .= "
				<tr>
					<td class='main' style='height: 25px'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&mID=".$row['tournamentmatch_id']."'>".$tournamentName."</a></td>
					<td class='main' style='height: 25px; padding-left: 10px'>".$dispOpponent."</td>
					<td class='main' style='height: 25px' align='center'>".$row['round']."</td>
				</tr>
				";		
		}
		
		
		
	}
	
	echo $dispPools;
	
	foreach($arrDispMatches as $value) {
		echo $value;	
	}
	
	
	
	echo "</table>";
	
	
	if($counter == 0) {
	
		echo "
			<div class='shadedBox' style='width: 40%; margin: 25px auto'>
				<p class='main' align='center'>
					<i>You don't have any pending matches!</i>
				</p>
			</div>
		";
	
	}
	else {
		
	echo "	
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#tournamentListTable tr:even').addClass('alternateBGColor');
			
			
			});
		
		</script>
		
	";
	
	}


}


?>