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

$countErrors = 0;
$dispError = "";

$tournamentObj = new Tournament($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->objTeam->select($_POST['teamID']) && $tournamentObj->objTournamentPool->select($_POST['poolID']) && $tournamentObj->objTournamentPool->objTournamentPoolMatch->select($_POST['poolTeamID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();

	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	$tournamentObj->select($teamInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$poolInfo = $tournamentObj->objTournamentPool->get_info();
	$poolTeamInfo = $tournamentObj->objTournamentPool->objTournamentPoolMatch->get_info();

	$dispTeamName = $tournamentObj->getPlayerName();

	$tmemberID = $tournamentInfo['member_id'];


	if(($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) && $poolInfo['tournament_id'] == $teamInfo['tournament_id'] && $poolTeamInfo['tournament_id'] == $teamInfo['tournament_id']) {
		
		
		// Check Match Score
		if(!is_numeric($_POST['teamScore']) || !is_numeric($_POST['opponentScore'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Scores must be a numeric value.";
		}
		
		
		// Check Winner
		$arrMatchWinners = array(0,1,2);
		if(!in_array($_POST['matchWinner'], $arrMatchWinners)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid match winner.";
		}
		
		// Check Match Details
		
		if($teamInfo['tournamentteam_id'] != $poolTeamInfo['team1_id'] && $teamInfo['tournamentteam_id'] != $poolTeamInfo['team2_id']) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected teams do not face each other.";
		}
		
		// Check if correct pools
		
		if($poolInfo['tournamentpool_id'] != $poolTeamInfo['pool_id']) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected match is not in the selected pool.";
		}
		
		
		
	}
	else {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to verify tournament information.";	
	}
	
	
	
}
else {
	$countErrors++;
	$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to verify tournament information.";
}



if($countErrors == 0) {
	
	$arrUpdateColumns = array("team1score", "team2score", "winner");
	
	if($poolTeamInfo['team1_id'] == $teamInfo['tournamentteam_id']) {
		$arrUpdateValues = array($_POST['teamScore'], $_POST['opponentScore'], $_POST['matchWinner']);
	}
	else {
		$arrUpdateValues = array($_POST['opponentScore'], $_POST['teamScore'], $_POST['matchWinner']);
	}
	
	$checkSave = $tournamentObj->objTournamentPool->objTournamentPoolMatch->update($arrUpdateColumns, $arrUpdateValues);
	
	if($checkSave) {
		echo "
	
			<div style='display: none' id='successBox'>
				<p align='center'>
					Pool Match Information Saved!
				</p>
			</div>
	
			<script type='text/javascript'>
			
			$('#successBox').dialog({
				title: 'Tournament Pool Match - Save',
				modal: true,
				zIndex: 99999,
				width: 400,
				resizable: false,
				show: 'scale',
				buttons: {
					'Ok': function() {
						$(this).dialog('close');
					}
				}
			
			});
			$('.ui-dialog :button').blur();
			
			
			</script>
		";
	}
	else {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
	}

}


 
if($countErrors > 0) {
	echo "
		<div class='errorDiv'>
			<strong>Unable to edit tournament info because the following errors occurred:</strong><br><br>
			$dispError
		</div>
	";
}


include("loadpoolform.php");

?>
