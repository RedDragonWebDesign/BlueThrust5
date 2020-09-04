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

include_once("../../classes/btupload.php");
if(!isset($member) || !isset($tournamentObj) || substr($_SERVER['PHP_SELF'], -strlen("managetournament.php")) != "managetournament.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$tournamentObj->select($tID);


	if(!$member->hasAccess($consoleObj)) {

		exit();
	}
}




if(!$tournamentObj->objMatch->select($_GET['match']) || ($tournamentObj->objMatch->get_info("team1_id") == 0 && $tournamentObj->objMatch->get_info("team2_id") == 0)) {
	
	echo "
	<div id='errorMessage' style='display: none'>
		<p class='main' align='center'>
			Unable to manage match! At least one team/player must be involved in a match to manage it!
		</p>
	</div>
	
	<script type='text/javascript'>
		popupDialog('Update Match - Error!', '".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches', 'errorMessage');
	</script>
	";
	
	
	
	exit();
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Match\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches'><b>".$tournamentInfo['name'].":</b> Manage Matches</a> > Manage Match\");
});
</script>
";


$dispError = "";
$countErrors = 0;

$matchInfo = $tournamentObj->objMatch->get_info();

if($_POST['submit']) {
	
	
	// Check Player 2
	
	if($_POST['playertwo'] != 0 && (!$tournamentObj->objTeam->select($_POST['playertwo']) || $tournamentObj->objTeam->get_info("tournament_id") != $tournamentInfo['tournament_id'] || $tournamentObj->objTeam->get_info("tournamentteam_id") == $matchInfo['team1_id'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid team in the match up.<br>";
	}
	
	
	
	// Check Scores
	
	if(($_POST['team1score'] != "" && !is_numeric($_POST['team1score'])) || ($_POST['team2score'] != "" && !is_numeric($_POST['team2score']))) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Scores must be a numeric value.<br>";
	}
	
	// Check Outcome
	
	if($_POST['outcome'] != 0 && $_POST['outcome'] != 1 && $_POST['outcome'] != 2) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid outcome.<br>";
	}
	
	
	// Check Replay Upload
	
	if($_FILES['uploadreplay']['name'] != "") {
		
		$uploadReplayObj = new BTUpload($_FILES['uploadreplay'], "replay_", "../../downloads/replays/", array(".zip"));
		
		if(!$uploadReplayObj->uploadFile()) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload the replay. Please make sure the file extension is .zip and that the file size is not too big.<br>";
		}
		else {
			$matchReplayURL = $MAIN_ROOT."downloads/replays/".$uploadReplayObj->getUploadedFileName();
		}
		
		
	}
	else {
		$matchReplayURL = $_POST['replayurl'];	
	}
	
	
	
	
	if($countErrors == 0) {
		
		
		// Swap players if team 2 is changing
		if($matchInfo['team2_id'] != $_POST['playertwo'] && $_POST['playertwo'] != 0) {
			
			$playerTwoMatch = $tournamentObj->getMatches($matchInfo['round'], $_POST['playertwo']);
			$tournamentObj->objMatch->select($playerTwoMatch[0]);
			$playerTwoMatchInfo = $tournamentObj->objMatch->get_info();
			
			if($_POST['playertwo'] == $playerTwoMatchInfo['team1_id']) {
				$arrColumns = array("team1_id");
			}
			else {
				$arrColumns = array("team2_id");
			}
			
			$tournamentObj->objMatch->update($arrColumns, array($matchInfo['team2_id']));

			
		}
		
		
		if($_POST['outcome'] == 1) {
			$matchWinner = $matchInfo['team1_id'];
		}
		elseif($_POST['outcome'] == 2) {
			$matchWinner = $_POST['playertwo'];	
		}
		
		
		
		$arrColumns = array("team2_id", "team1score", "team2score", "outcome", "adminreplayurl");
		$arrValues = array($_POST['playertwo'], $_POST['team1score'], $_POST['team2score'], $_POST['outcome'], $matchReplayURL);
		$tournamentObj->objMatch->select($matchInfo['tournamentmatch_id']);
		
		if($tournamentObj->objMatch->update($arrColumns, $arrValues)) {
			
			if($_POST['outcome'] != 0 && $matchInfo['nextmatch_id'] != 0) {
			
				$nextMatchSpot = $tournamentObj->getNextMatchTeamSpot($matchWinner);
				
				$tournamentObj->objMatch->select($matchInfo['nextmatch_id']);
				
				
				$tournamentObj->objMatch->update(array($nextMatchSpot), array($matchWinner));
			}
			
			
			echo "
			
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Updated Match</b>!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Update Match', '".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches', 'successBox');
			</script>
			
			";
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";			
		}
		
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}
	
	
	
}


if(!$_POST['submit']) {
	
	if($tournamentInfo['playersperteam'] == 1) {
		$strPlayerTeam = "Player";
		
		if($tournamentObj->objTeam->select($matchInfo['team1_id'])) {
			
			$teamInfo = $tournamentObj->objTeam->get_info_filtered();
			
			$arrPlayers = $tournamentObj->getTeamPlayers($teamInfo['tournamentteam_id'], true);
			$dispPlayer1 = "Empty Spot";
			if($tournamentObj->objPlayer->select($arrPlayers[0])) {
				
				$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
				
				if($member->select($playerInfo['member_id'])) {
					$dispPlayer1 = $member->getMemberLink();
				}
				else {
					$dispPlayer1 = $playerInfo['displayname'];
				}
				
			}
			
			
		}
		
		
		$arrPlayers = $tournamentObj->getPlayers(true);
		
		$selectOutcomePlayer1 = "";
		$selectOutcomePlayer2 = "";
		if($matchInfo['outcome'] == 1) {
			$selectOutcomePlayer1 = " selected";	
		}
		elseif($matchInfo['outcome'] != 0) {
			$selectOutcomePlayer2 = " selected";
		}
		
		
		foreach($arrPlayers as $playerID) {
			
			if($playerID != $playerInfo['tournamentplayer_id']) {
				
				
				
				$tournamentObj->objPlayer->select($playerID);
				$player2TeamID = $tournamentObj->objPlayer->get_info("team_id");
				if($member->select($tournamentObj->objPlayer->get_info("member_id"))) {
					$dispOptionName = $member->get_info_filtered("username");
				}
				else {
					$dispOptionName = $tournamentObj->objPlayer->get_info_filtered("displayname");
				}
				
				$dispSelected = "";
				if($player2TeamID == $matchInfo['team2_id']) {
					$dispSelected = " selected";
				}
				
				$teamoptions .= "<option value='".$player2TeamID."'".$dispSelected.">".$dispOptionName."</option>";
			}
			
		}
		
		
	}
	else {
		$strPlayerTeam = "Team";
		
		$arrTeams = $tournamentObj->getTeams(true);
		foreach($arrTeams as $teamID) {	
			
			$dispSelected = "";
			if($teamID == $matchInfo['team2_id']) {
				$dispSelected = " selected";
			}
			
			$tournamentObj->objTeam->select($teamID);
			$teamoptions .= "<option value='".$teamID."'".$dispSelected.">".$tournamentObj->objTeam->get_info_filtered("name")."</option>";
			
		}
		
		$tournamentObj->objTeam->select($matchInfo['team1_id']);
		$dispPlayer1 = $tournamentObj->objTeam->get_info_filtered("name");
		
	}
	
	
	
	
	echo "
	
		<form action='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches&match=".$_GET['match']."' method='post' enctype='multipart/form-data'>
		<div class='formDiv'>
		
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit match because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
		
	echo "
		Use the form below to edit the match details.<br>
		<p class='main'>
			<span style='font-weight: bold; text-decoration: underline'>NOTE:</span> If you change a match up, it will not affect player/team seeds.
		</p>
			<table class='formTable'>
				<tr>
					<td colspan='2' class='main'>
						<b>Match Up</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>".$strPlayerTeam." 1:</td>
					<td class='main'>".$dispPlayer1."</td>
				</tr>
				<tr>
					<td class='formLabel'>".$strPlayerTeam." 2:</td>
					<td class='main'><select name='playertwo' class='textBox'><option value='0'>Empty Spot</option>".$teamoptions."</select></td>
				</tr>
				<tr>
					<td colspan='2' class='main'><br>
						<b>Match Outcome</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>".$strPlayerTeam." 1 Score:</td>
					<td class='main'><input type='text' name='team1score' class='textBox' value='".$matchInfo['team1score']."' style='width: 40px'></td>
				</tr>
				<tr>
					<td class='formLabel'>".$strPlayerTeam." 2 Score:</td>
					<td class='main'><input type='text' name='team2score' class='textBox' value='".$matchInfo['team2score']."' style='width: 40px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Match Winner:</td>
					<td class='main'><select name='outcome' class='textBox'><option value='0'>None Yet</option><option value='1'".$selectOutcomePlayer1.">".$strPlayerTeam." 1</option><option value='2'".$selectOutcomePlayer2.">".$strPlayerTeam." 2</option></select></td>
				</tr>
				<tr>
					<td colspan='2' class='main'><br>
						<b>Upload Replay</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Match Replay:</td>
					<td class='main'>File:<br>
						<input type='file' class='textBox' name='uploadreplay' style='width: 250px; border: 0px'><br>
						<span style='font-size: 10px'>&nbsp;&nbsp;&nbsp;<b>&middot;</b> File Type: .zip<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						<p><br><b><i>OR</i></b><br></p>
						URL:<br>
						<input type='text' class='textBox' name='replayurl' value='".$matchInfo['adminreplayurl']."' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td colspan='2' class='main' align='center'><br>
						<input type='submit' name='submit' value='Update Match' class='submitButton' style='width: 135px'>
					</td>
				</tr>
				
			</table>
		
		</div>
		</form>
	
	";
	
}



?>