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

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->objTeam->select($_POST['teamID']) && $tournamentObj->objTournamentPool->select($_POST['poolID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();

	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	$tournamentObj->select($teamInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$poolInfo = $tournamentObj->objTournamentPool->get_info();
	
	$dispTeamName = $tournamentObj->getPlayerName();
	
	$tmemberID = $tournamentInfo['member_id'];
	
	
	if(($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) && $poolInfo['tournament_id'] == $teamInfo['tournament_id']) {
		
		$selectedTeam = "";
		$dispNoWinnerSelected = "";
		$dispPlayerOneWinnerSelected = "";
		$dispPlayerTwoWinnerSelected = "";
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentpools_teams WHERE pool_id = '".$_POST['poolID']."' AND (team1_id = '".$_POST['teamID']."' OR team2_id = '".$_POST['teamID']."') ORDER BY poolteam_id");
		while($row = $result->fetch_assoc()) {
			
			if($_POST['poolTeamID'] == "") {
				$_POST['poolTeamID'] = $row['poolteam_id'];	
			}
			
			if($row['team1_id'] != $_POST['teamID']) {
				$selectedTeam = $row['team1_id'];
			}
			elseif($row['team2_id'] != $_POST['teamID']) {
				$selectedTeam = $row['team2_id'];
			}
			
			
			if($_POST['poolTeamID'] == $row['poolteam_id']) {				
				
				if($row['team1_id'] == $_POST['teamID']) {
					$playerOneValue = 1;
					$playerTwoValue = 2;
					
			
					if($row['winner'] == 1) {
						$dispPlayerOneWinnerSelected = " selected";	
					}
					elseif($row['winner'] == 2) {
						$dispPlayerTwoWinnerSelected = " selected";	
					}
					
					
				}
				elseif($row['team2_id'] == $_POST['teamID']) {
					$playerOneValue = 2;
					$playerTwoValue = 1;
					
					if($row['winner'] == 1) {
						$dispPlayerTwoWinnerSelected = " selected";
					}
					elseif($row['winner'] == 2) {
						$dispPlayerOneWinnerSelected = " selected";
					}
					
				}
				
				if($row['winner'] == 0) {
					$dispNoWinnerSelected = " selected";
				}
				
				if($_POST['teamID'] == $row['team1_id']) {
					$dispTeamScore = $row['team1score'];
					$dispOpponentScore = $row['team2score'];
				}
				elseif($_POST['teamID'] == $row['team2_id']) {
					$dispTeamScore = $row['team2score'];
					$dispOpponentScore = $row['team1score'];
				}
			
			
			}
		
			if($selectedTeam != "") {
		
				$tournamentObj->objTeam->select($selectedTeam);
				$dispOpponentName = $tournamentObj->getPlayerName();
		
				if($dispOpponentName == "") {
					$dispOpponentName = "Empty Spot";
				}
		
				
				if($row['winner'] == 0) {
					$dispOpponentName .= " (no winner selected)";
				}

		
				$dispSelected = "";
				if($_POST['poolTeamID'] == $row['poolteam_id']) {
					$dispSelected  = " selected";	
				}
				
		
				$opponentoptions .= "<option value='".$row['poolteam_id']."'".$dispSelected.">".$dispOpponentName."</option>";
			}
			
			
			if($_POST['poolTeamID'] == $row['poolteam_id']) {
			
				
				
				
			}
			
			
		}
		
		
		
		echo "
		
		<table class='formTable'>
			<tr>
				<td class='formLabel'>Opponent:</td>
				<td class='main'><select id='opponent' name='opponent' class='textBox'>".$opponentoptions."</select></td>
			</tr>
			<tr>
				<td class='formLabel'>".$dispTeamName."'s Score:</td>
				<td class='main'><input type='text' id='teamscore' class='textBox' value='".$dispTeamScore."' style='width: 30px'></td>
			</tr>
			<tr>
				<td class='formLabel'>Opponents's Score:</td>
				<td class='main'><input type='text' id='opponentscore' value='".$dispOpponentScore."' class='textBox' style='width: 30px'></td>
			</tr>
			<tr>
				<td class='formLabel'>Winner:</td>
				<td class='main'><select id='winner' class='textBox'><option value='0'".$dispNoWinnerSelected.">None Yet</option><option value='".$playerOneValue."'".$dispPlayerOneWinnerSelected.">".$dispTeamName."</option><option value='".$playerTwoValue."'".$dispPlayerTwoWinnerSelected.">Opponent</option></select></td>
			</tr>
			<tr>
				<td class='main' colspan='2' align='center'><br>
					<input type='button' id='saveButton' class='submitButton' style='width: 100px' value='Save'>
				</td>
			</tr>
		</table>
		
		
		
		<script type='text/javascript'>
				
			$(document).ready(function() {
			
				$('#opponent').change(function() {

				
					$('#loadingSpiral').show();
					$('#poolDiv').hide();
				
					$.post('".$MAIN_ROOT."members/tournaments/include/loadpoolform.php', { teamID: '".$_POST['teamID']."', poolID: '".$_POST['poolID']."', poolTeamID: $('#opponent').val() }, function(data) {
					
						$('#poolDiv').html(data);
						$('#loadingSpiral').hide();
						$('#poolDiv').fadeIn(250);
					
					});
				
				
				});
				
				
				$('#saveButton').click(function() {
				
				
					$('#loadingSpiral').show();
					$('#poolDiv').hide();
					
					$.post('".$MAIN_ROOT."members/tournaments/include/savepoolmatch.php', { teamID: '".$_POST['teamID']."', poolID: '".$_POST['poolID']."', poolTeamID: $('#opponent').val(), teamScore: $('#teamscore').val(), opponentScore: $('#opponentscore').val(), matchWinner: $('#winner').val() }, function(data) {
					
						$('#poolDiv').html(data);
						$('#loadingSpiral').hide();
						$('#poolDiv').fadeIn(250);
					
					});
				
				
				});
				
				
				
			
			});
		
		</script>
		
		";
		
		
	}
	
	
}


?>


