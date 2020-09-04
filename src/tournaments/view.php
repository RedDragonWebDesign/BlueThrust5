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

// Config File
$prevFolder = "../";

include($prevFolder."_setup.php");

// Classes needed for view.php

include_once($prevFolder."classes/tournament.php");
include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/consoleoption.php");
include_once($prevFolder."classes/game.php");


$tournamentObj = new Tournament($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$gameObj = new Game($mysqli);

if(!isset($member)) {
	$member = new Member($mysqli);

	if(isset($_SESSION['btUsername']) AND isset($_SESSION['btPassword']) && $member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {

		$memberInfo = $member->get_info_filtered();

	}
}

if(!$tournamentObj->select($_GET['tID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
}
else {
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$tID = $_GET['tID'];
}

$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


// Start Page
$PAGE_NAME = $tournamentInfo['name']." - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

echo "
<script type='text/javascript'>
	$('#toolTip').css('width', '30px');
</script>
";

// Get Rounds with Matches that are settable

$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentmatch WHERE tournament_id='".$tournamentInfo['tournament_id']."' AND (team1_id != '0' OR team2_id != '0') ORDER BY round");
while($row = $result->fetch_assoc()) {
	$arrRounds[] = $row['round'];
}

$arrRounds = array_unique($arrRounds);

foreach($arrRounds as $roundNum) {
	$roundoptions .= "<option value='".$roundNum."'>Round ".$roundNum."</option>";
}

$member->select($tournamentInfo['member_id']);
$dispManager = $member->getMemberLink();
$pluralManagers = "";
$arrManagers = $tournamentObj->getManagers();
foreach($arrManagers as $tMemberID) {
	if($member->select($tMemberID)) {
		$dispManager .= "<br>".$member->getMemberLink();
		$pluralManagers = "s";
	}
}

$dateTimeObj = new DateTime();
$dateTimeObj->setTimestamp($tournamentInfo['startdate']);
$includeTimezone = "";
$dispTimezone = "";

if($tournamentInfo['timezone'] != "") { 
	$timeZoneObj = new DateTimeZone($tournamentInfo['timezone']);
	$dateTimeObj->setTimezone($timeZoneObj);
	$includeTimezone = " T";
	$dispOffset = ((($timeZoneObj->getOffset($dateTimeObj))/60)/60);
	$dispSign = ($dispOffset < 0) ? "" : "+";
	
	$dispTimezone = $dateTimeObj->format(" T")."<br>".str_replace("_", " ", $tournamentInfo['timezone'])." (UTC".$dispSign.$dispOffset.")";
}

$dateTimeObj->setTimezone(new DateTimeZone("UTC"));

$dispStartDate = $dateTimeObj->format("M j, Y g:i A").$dispTimezone;

if($tournamentInfo['startdate'] < time() && $tournamentObj->getTournamentWinner() == 0) {
	$dispStatus = "<span class='successFont'>Started</span>";	
}
elseif($tournamentInfo['startdate'] > time()) {
	$dispStatus = "<span class='pendingFont'>Forming</span>";
}
elseif($tournamentInfo['startdate'] < time() && $tournamentObj->getTournamentWinner() != 0) {
	$dispStatus = "<span class='failedFont'>Finished</span>";	
}


if($tournamentInfo['access'] == 1) {
	$dispAccess = "Clan Only";
}
elseif($tournamentInfo['access'] == 2) {
	$dispAccess = "Multi-Clan";
}
else {
	$dispAccess = "Everyone";
}

if($tournamentInfo['description'] == "") {
	$tournamentInfo['description'] = "None";	
}

if($tournamentInfo['seedtype'] == 1 && !$tournamentObj->checkForPools()) {
	$dispSeedType = "Manual";
}
elseif($tournamentInfo['seedtype'] == 2) {
	$dispSeedType = "Random";
}
else {
	$dispSeedType = "Pools";	
}


if($tournamentInfo['eliminations'] == 1) {
	$dispEliminations = "Single Elimination";	
}
else {
	$dispEliminations = "Double Elimination";
}


$arrPlayers = $tournamentObj->getPlayers();

$totalEntrants = count($arrPlayers);

$breadcrumbObj->setTitle($tournamentInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Tournaments", $MAIN_ROOT."tournaments");
$breadcrumbObj->addCrumb($tournamentInfo['name']);

include($prevFolder."include/breadcrumb.php");

echo "
	
	<div class='tournamentProfileContainer'>
	
	
		<div class='tournamentProfileLeft'>
			<span class='tournamentProfileTitle'>TOURNAMENT INFORMATION</span>
			<div class='dashedBox main' style='padding: 5px; margin-right: 10px'>
				<b>General Information:</b>
				<div class='dottedLine' style='margin-top: 2px'></div>
				<p class='tinyFont'>
					<b>Tournament Name:</b><br>
					".$tournamentInfo['name']."<br><br>
					<b>Manager".$pluralManagers.":</b><br>
					".$dispManager."<br><br>
					<b>Start Date:</b><br>
					".$dispStartDate."<br><br>
					<b>Status:</b> 
					".$dispStatus."<br><br>
					<b>Access:</b> 
					".$dispAccess."<br><br>
					<b>Total Entrants:</b> 
					".$totalEntrants."<br><br>
					<b>Extra Info:</b><br>
					".nl2br($tournamentInfo['description'])."<br>
				</p><br>
				<b>Tournament Structure:</b>
				<div class='dottedLine' style='margin-top: 2px'></div>
				<p class='tinyFont'>
					<b>Max Teams:</b> ".$tournamentInfo['maxteams']."<br><br>
					<b>Players per Team:</b> ".$tournamentInfo['playersperteam']."<br><br>
					<b>Seeding:</b> ".$dispSeedType."<br><br>
					<b>Eliminations:</b> ".$dispEliminations."<br><br>
					
				</p>
				
			</div>
		</div>
	
		<div class='tournamentProfileRight'>
	
		";

		
		
		if($tournamentInfo['playersperteam'] == 1) {
			$dispPlayerOrTeam = "Player";	
		}
		else {
			$dispPlayerOrTeam = "Team";	
		}
		
		if($tournamentInfo['seedtype'] == 3) {
			$arrPools = $tournamentObj->getPoolList();
			$dispPools = "";
			$dispPoolLetter = "A";
			foreach($arrPools as $poolID) {
				$tournamentObj->objTournamentPool->select($poolID);
				
				
				
				$arrPoolTeams = $tournamentObj->objTournamentPool->getTeamsInPool();
				$dispPools .= "<tr><td colspan='2' class='main'><b><u>Pool ".$dispPoolLetter.":</u></b></td></tr>";
				$dispPools .= "<tr><td class='tinyFont' style='width: 65%'><b>".$dispPlayerOrTeam.":</b></td><td class='tinyFont' style='width: 35%'><b>Record:</b></td></tr>";
				$counter = 0;
				foreach($arrPoolTeams as $teamID) {
					$addCSS = " alternateBGColor";
					if($counter%2 == 0) {
						$addCSS = "";
					}
					$counter++;
					$tournamentObj->objTeam->select($teamID);
					$dispTeamName = $tournamentObj->getPlayerName();
					if($dispTeamName == "") {
						$dispTeamName = "<i>Empty Spot</i>";	
					}
					
					$dispTeamRecord = $tournamentObj->objTournamentPool->getTeamRecord($teamID);
					
					$dispPools .= "<tr><td class='tinyFont".$addCSS."' valign='top' style='width: 65%'>".$counter.". ".$dispTeamName."</td><td class='tinyFont".$addCSS."' style='width: 35%' valign='top'>".$dispTeamRecord."</td></tr>";						
					
				}
				
				
				
				$dispPoolLetter++;
				$dispPools .= "<tr><td colspan='2' class='tinyFont'><br></td></tr>";
			}
			
			
				
			echo "
				
				<span class='tournamentProfileTitle'>POOLS</span>
				<div class='dashedBox main' style='height: 400px; overflow-y: auto; padding: 5px'>
					<table class='formTable' style='margin-left: 3px; margin-top: 3px; width: 95%; border-spacing: 0px'>
						".$dispPools."
					</table>
				</div>
				<p class='tournamentProfileTitle' align='center'>
					<a href='".$MAIN_ROOT."tournaments/poolmatches.php?tID=".$tID."' target='_blank'>View Matches</a>
				</p>
				
			";
				
			
			
			
		}
		
		
		if($tournamentInfo['seedtype'] != 3) {
			echo "
			<span class='tournamentProfileTitle'>MATCHES</span>
			<div class='dashedBox main' style='height: 400px; overflow-y: auto; padding: 5px'>
			
				<b>Select Round:</b> <select id='roundSelect' class='textBox'>".$roundoptions."</select>
				<div class='loadingSpiral' id='loadingSpiral'>
					<p align='center'>
						<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
					</p>
				</div>
				<div id='matchDiv'>
				
					";

			include("include/listmatches.php");
			echo "
				</div>
			</div>
			<p class='tournamentProfileTitle' align='center'>
				<a href='".$MAIN_ROOT."tournaments/bracket.php?tID=".$tID."' target='_blank'>View Bracket</a>
			</p>
			
			";
			
			if($tournamentObj->checkForPools()) {
				echo "
					<p class='tournamentProfileTitle' align='center'>
						<a href='".$MAIN_ROOT."tournaments/poolmatches.php?tID=".$tID."' target='_blank'>View Pool Matches</a>
					</p>
				";
			}
			
			if($tournamentObj->memberCanJoin($memberInfo['member_id'])) {
				
				$tConsoleObj = new ConsoleOption($mysqli);
				$joinTournamentLink = $tConsoleObj->getConsoleLinkByName("Join a Tournament", false);
				
				echo "
					<p class='tournamentProfileTitle' align='center'>
						<a href='".$joinTournamentLink."&tID=".$tID."'>Join Tournament</a>
					</p>
				";
			}
			
		}
		
		echo "	
		</div>
	
	</div>
	
	<script type='text/javascript'>
	
		$(document).ready(function() {
	
			$('#roundSelect').change(function() {
	
				$('#roundNumSpan').html($('#roundSelect').val());
	
				$('#matchDiv').hide();
				$('#loadingSpiral').show();
				$.post('".$MAIN_ROOT."tournaments/include/listmatches.php', { tID: ".$tID.", roundSelected: $('#roundSelect').val() }, function(data) {
					$('#matchDiv').html(data);
					
					$('#loadingSpiral').hide();
					$('#matchDiv').fadeIn(250);
				});
	
			});
			
		});
	
	
	</script>
	
";




include($prevFolder."themes/".$THEME."/_footer.php");

?>
