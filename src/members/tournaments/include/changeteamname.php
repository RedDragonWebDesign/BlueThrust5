<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


require_once("../../../_setup.php");
require_once("../../../classes/member.php");
require_once("../../../classes/rank.php");
require_once("../../../classes/tournament.php");


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);
$dispMessage = "";
if ($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->objTeam->select($_POST['teamID']) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info();

	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	$tournamentObj->select($teamInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$tmemberID = $tournamentInfo['member_id'];


	if (($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1")  || $tournamentObj->isManager($memberInfo['member_id']) && trim($_POST['newName']) != "") {
		if ($tournamentObj->objTeam->update(["name"], [$_POST['newName']])) {
			$dispMessage = "<p class='successFont'><b>Team Name Saved!</b></p>";

			$teamCounter = 1;
			$arrTeams = $tournamentObj->getTeams(true);
			foreach ($arrTeams as $teamID) {
				$tournamentObj->objTeam->select($teamID);
				$teamInfo = $tournamentObj->objTeam->get_info_filtered();


				$dispTeamName = $teamInfo['name'];
				if ($teamInfo['name'] == "") {
					$dispTeamName = "Team ".$teamCounter;
				}

				$dispSelected = "";
				if ($teamID == $_POST['teamID']) {
					$dispSelected = " selected";
				}

				$teamoptions .= "<option value='".$teamID."'".$dispSelected.">".$dispTeamName."</option>";

				$teamCounter++;
			}

			$dispMessage .= "
				
				<script type='text/javascript'>
					$('#selectteam').html(\"".$teamoptions."\");
				</script>
			
			";
		} else {
			$dispMessage = "<p class='failedFont'><b>Unable to save team name!</b></p>";
		}
	} else {
		$dispMessage = "<p class='failedFont'><b>Unable to save team name.  Make sure your team name is not blank!</b></p>";
	}
}

echo $dispMessage;
