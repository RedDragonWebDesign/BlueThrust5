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



if(!isset($member)|| substr($_SERVER['PHP_SELF'], -strlen("console.php")) != "console.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	if(!$member->hasAccess($consoleObj)) {

		exit();
	}
}
include_once("../classes/tournament.php");

$tMemberObj = new Member($mysqli);

$countErrors = 0;
$dispError = "";

$tournamentObj = new Tournament($mysqli);

$arrTournaments = $member->getTournamentList();

if($_POST['submit']) {
	
	if(!$tournamentObj->select($_POST['tournament'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid tournament.<br>";
	}
	else {
	
		$tournamentInfo = $tournamentObj->get_info_filtered();
		// Check Password
		if($tournamentInfo['password'] != "" && $tournamentInfo['password'] != md5($_POST['tournamentpassword'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You entered an incorrect password for the tournament.<br>";
		}
	
		// Check Spots left
		
		$arrPlayers = $tournamentObj->getPlayers();
		$maxPlayers = $tournamentInfo['playersperteam']*$tournamentInfo['maxteams'];
		if($maxPlayers == count($arrPlayers)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> This tournament is currently full.<br>";
		}
		
		// Check if already in tournament
		
		if(in_array($memberInfo['member_id'], $arrTournaments)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You are already in this tournament.<br>";
		}
	
	
	}

	
	if($countErrors == 0) {
		
		if($tournamentObj->objPlayer->addNew(array("member_id", "tournament_id"), array($memberInfo['member_id'], $tournamentInfo['tournament_id']))) {
			
			if($tournamentInfo['playersperteam'] == 1) {
				$arrUnfilledTeams = $tournamentObj->getUnfilledTeams();
				if(count($arrUnfilledTeams) > 0) {
					$newTeam = $arrUnfilledTeams[0];
					$tournamentObj->objPlayer->update(array("team_id"), array($newTeam));
				}				
			}
			
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Joined the Tournament!
					</p>
				</div>
			
			<script type='text/javascript'>
				popupDialog('Join a Tournament', '".$MAIN_ROOT."members', 'successBox');
			</script>
			
			";
			
			
			$tMemberObj->select($tournamentInfo['member_id']);
			$tMemberObj->postNotification($member->getMemberLink()." has joined your tournament: <a href='".$MAIN_ROOT."tournaments/view.php?tID=".$tournamentInfo['tournament_id']."'>".$tournamentInfo['name']."</a>");
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
	}

	
	if($countErrors > 0) {
		$_POST['submit'] = false;	
	}
	
	
}

if(!$_POST['submit']) {
	
	$tournamentSQL = "('".implode("','", $arrTournaments)."')";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournaments WHERE tournament_id NOT IN ".$tournamentSQL." ORDER BY name");
	while($row = $result->fetch_assoc()) {
		$dispSelected = "";
		if(isset($_GET['tID']) && $row['tournament_id'] == $_GET['tID']) {
			$dispSelected = " selected";
		}
		
		$tournamentOptions .= "<option value='".$row['tournament_id']."'".$dispSelected.">".filterText($row['name'])."</option>";
	}
	
	
	
	if($result->num_rows > 0) {
	echo "
	
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			";
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to join tournament because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
			
	echo "		
				Use the form below to join a tournament.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Tournament:</td>
						<td class='main'><select id='tournamentID' name='tournament' class='textBox'><option value=''>Select</option>".$tournamentOptions."</select></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							
							<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center' class='main'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>
						
						
							<input type='button' id='btnFakeSubmit' class='submitButton' style='width: 150px' value='Join Tournament'>
						
							<input type='submit' name='submit' style='display: none' id='btnSubmit'>
							
						</td>
					</tr>
				</table>
			
			</div>
			<input type='hidden' id='tournamentPassword' name='tournamentpassword' value=''>
		</form>
		
		<div id='checkPasswordDump'></div>
		
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#btnFakeSubmit').click(function() {
					
					$('#loadingSpiral').show();
					$.post('".$MAIN_ROOT."members/include/tournaments/include/checkpassword.php', { tID: $('#tournamentID').val() }, function(data) {
					
						$('#checkPasswordDump').html(data);
						
					
					});
				
				});
			
			});
		
		</script>
		
	";
	
	}
	else {

		echo "
			<div class='shadedBox' style='width: 40%; margin: 25px auto'>
				<p class='main' align='center'>
					<i>There are no tournaments for you to join!</i>
				</p>
			</div>
		";
	}
	
	
}



?>