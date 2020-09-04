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

if($tournamentInfo['seedtype'] != 3) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManagePlayers';</script>");
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Pools\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Manage Pools\");
});
</script>
";


$dispError = "";
$countErrors = 0;
$blnShowPoolList = true;

if(isset($_GET['poolID']) && isset($_GET['teamID']) && $tournamentObj->objTournamentPool->select($_GET['poolID']) && $tournamentObj->objTeam->select($_GET['teamID'])) {
	
	$poolInfo = $tournamentObj->objTournamentPool->get_info();
	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	
	$dispTeamName = $tournamentObj->getPlayerName();
	
	if(trim($dispTeamName) != "" && $tournamentInfo['tournament_id'] == $poolInfo['tournament_id'] && $tournamentInfo['tournament_id'] == $teamInfo['tournament_id']) {
		$blnShowPoolList = false;		
		
		echo "
		
			<div class='formDiv'>
				Use the form below to manage each of ".$dispTeamName."'s pool matches.

				<div id='loadingSpiral'>
					<p align='center' class='main'>
						<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
					</p>
				</div>
				
				<div id='poolDiv'>

					<script type='text/javascript'>
				
						$(document).ready(function() {
							
							$('#loadingSpiral').show();
							$('#poolDiv').hide();
						
							$.post('".$MAIN_ROOT."members/tournaments/include/loadpoolform.php', { teamID: '".$_GET['teamID']."', poolID: '".$_GET['poolID']."' }, function(data) {
							
								$('#poolDiv').html(data);
								$('#loadingSpiral').hide();
								$('#poolDiv').fadeIn(250);
							
							});
	
						});
				
					</script>

				</div>
				
				
		";
		
		
	}
	
	echo "
			</div>
	";

	
}


if($blnShowPoolList) {

	if($tournamentInfo['playersperteam'] == 1) {
		$dispTeamOrPlayer = "Player";
	}
	else {
		$dispTeamOrPlayer = "Team";
	}
	
	
	echo "
	
	<div class='formDiv main' style='border: 0px; background: none'>
	
		<p class='main'>
			Below is a listing of the pools setup for this tournament.  Click on one of the ".strtolower($dispTeamOrPlayer)."s to view and manage all their matches.  Once all pool matches have a winner, you will be able to seed ".strtolower($dispTeamOrPlayer)."s.
		</p>
	
		";
	
			
			$arrPools = $tournamentObj->getPoolList();
			$arrPoolTeams = array();
			$startingPoolLetter = "A";
			
			foreach($arrPools as $poolID) {
				$tournamentObj->objTournamentPool->select($poolID);
				
				$arrPoolTeams = $tournamentObj->objTournamentPool->getTeamsInPool();
				
				
				echo "
					<table class='formTable' style='margin-left: 0px'>
						<tr>
							<td class='main dottedLine'>
								<b>Pool ".$startingPoolLetter."</b>
							</td>
						</tr>
					</table>
					<table class='formTable' style='margin-left: 0px; border-spacing: 0px'>
						<tr>
							<td class='main' style='width: 65%; text-decoration: underline; font-weight: bold'>".$dispTeamOrPlayer.":</td>
							<td class='main' style='width: 35%; text-decoration: underline; font-weight: bold'>Record:</td>
						</tr>
					";
				
				$counter = 0;
				foreach($arrPoolTeams as $teamID) {
					$addCSS = " alternateBGColor";
					if($counter%2 == 0) {
						$addCSS = "";
					}
					
					$dispTeamName = "";
					$tournamentObj->objTeam->select($teamID);
					$teamInfo = $tournamentObj->objTeam->get_info_filtered();
					
					if($tournamentInfo['playersperteam'] == 1) {
						
						$dispTeamName = $tournamentObj->getPlayerName($teamID);
						
						if($dispTeamName == "") {
							$dispTeamName = "<i>Empty Spot</i>";	
						}
						else {
							$dispTeamName = "<b><a href='managetournament.php?tID=".$tID."&pID=ManagePools&poolID=".$poolID."&teamID=".$teamID."'>".$dispTeamName."</a></b>";
						}
						
						
					}
					else {
						$dispTeamName = "<b><a href='managetournament.php?tID=".$tID."&pID=ManagePools&poolID=".$poolID."&teamID=".$teamID."'>".$teamInfo['name']."</a></b>";
					}
					
					$teamRecord = $tournamentObj->objTournamentPool->getTeamRecord($teamInfo['tournamentteam_id']);
					
					echo "
						<tr>
							<td class='main".$addCSS."' style='width: 65%; height: 20px'>".$dispTeamName."</td>
							<td class='main".$addCSS."' style='width: 35%; height: 20px'>".$teamRecord."</td>
						</tr>
					";
					$counter++;
				}
				
				
				echo "
						
						
					</table>
				";
				
				$startingPoolLetter++;
			}
		
	
	echo "
	</div>
	";


}
?>