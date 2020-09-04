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


if(!isset($member) || !isset($tournamentObj) || !$tournamentObj->poolsComplete() || substr($_SERVER['PHP_SELF'], -strlen("managetournament.php")) != "managetournament.php") {

	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
		</script>
	";
	
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

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Start Matches\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Start Matches\");
});
</script>
";


$dispError = "";
$countErrors = 0;

if($tournamentInfo['playersperteam'] == 1) {
	$dispTeamOrPlayer = "Player";	
}
else {
	$dispTeamOrPlayer = "Team";	
}


if($_POST['submit']) {
	
	$mysqli->query("DELETE FROM ".$dbprefix."tournamentmatches WHERE tournament_id = '".$tournamentInfo['tournament_id']."'");	
	$tournamentObj->update(array("seedtype"), array(1));
	
	$tournamentObj->resetMatches();
	
	echo "
	
		<div style='display: none' id='successBox'>
			<p align='center'>
				Successfully Started Tournament Matches!
			</p>
		</div>

			<script type='text/javascript'>
				popupDialog('Start Tournament Matches', '".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tID."', 'successBox');
			</script>
	
	";
	
	
	
}
elseif(!$_POST['submit']) {

	$arrPools = $tournamentObj->getPoolList();
	$arrPoolTeams = array();
	$arrTeams = $tournamentObj->getTeams(true);
	
	
	$totalPoolCount = 0;
	$totalPoolsFinished = 0;
	foreach($arrPools as $poolID) {
		$tournamentObj->objTournamentPool->select($poolID);
		$totalPoolCount++;
		$poolInfo = $tournamentObj->objTournamentPool->get_info();
		if($poolInfo['finished'] == 0) {
			$tournamentObj->objTournamentPool->update(array("finished"), array(1));	
		}
		else {
			$totalPoolsFinished++;
		}
		
	}
	
	
	if($totalPoolCount != $totalPoolsFinished) {
		
		foreach($arrTeams as $teamID) {
			$arrWinCount[$teamID] = 0;
		}
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentpools_teams WHERE tournament_id = '".$tID."'");
		while($row = $result->fetch_assoc()) {
		
			if($row['winner'] == 1) {
				$winningTeam = $row['team1_id'];
			}
			else {
				$winningTeam = $row['team2_id'];
			}
	
			$arrWinCount[$winningTeam] += 1;
		
		}
		
		arsort($arrWinCount);
		
		$seedCount = 1;
		foreach($arrWinCount as $teamID => $wins) {
			
			$tournamentObj->objTeam->select($teamID);
			$tournamentObj->objTeam->update(array("seed"), array($seedCount));
			
			$seedCount++;
		}
		
	}
	
	
	
	
	
	echo "
	
		<div class='formDiv' style='border: 0px; background: none'>
			Below shows how each ".strtolower($dispTeamOrPlayer)." will be seeded based on their pool records. Click on a ".strtolower($dispTeamOrPlayer)."s name to change their seed.<br><br> 
			<b><u>NOTE:</u></b> Once you start the tournament matches, you will not be able to go back and modify the pools.  You can however, still view the outcomes in the tournament profile.
			<br><br>
			
			
			<div class='loadingSpiral' id='loadingSpiral'>
				<p align='center'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
				</p>
			</div>
			<div id='teamListDiv'>
			<table class='formTable' align='center' style='margin-left: auto; margin-right: auto; width: 250px'>
				<tr>
					<td class='formTitle' style='width: 40px'>Seed:</td>
					<td class='formTitle' style='width: 210px'>".$dispTeamOrPlayer.":</td>
				</tr>
			
				";
	
			
			$arrTeams = $tournamentObj->getTeams(true, "ORDER BY seed");
			foreach($arrTeams as $teamID) {	
				$dispName = $tournamentObj->getPlayerName($teamID);
				$tournamentObj->objTeam->select($teamID);
				$dispSeed = $tournamentObj->objTeam->get_info("seed");
				$teamPoolID = $tournamentObj->getTeamPoolID($teamID);
				$tournamentObj->objTournamentPool->select($teamPoolID);
				$tournamentObj->objTournamentPool->getTeamRecord($teamID);
				
				echo "
					<tr>
						<td class='main' align='center'>".$dispSeed.".</td>
						<td class='main' style='padding-left: 5px'><a href='javascript:void(0)' onclick=\"setSeed('".$teamID."')\">".$dispName."</a> (".$tournamentObj->objTournamentPool->getTeamRecord($teamID).")</td>
					</tr>
				";
				
				
				$seedCount++;
				
			}
			
			echo "
				
			</table>
			
			<br>
			<form action='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=StartMatches' method='post'>
				<p align='center'>
					<input type='submit' name='submit' value='Start Matches!' class='submitButton' style='width: 125px'>
				</p>
			</form>
			
			</div>
			
			
		</div>
		<div id='changeSeedDiv' style='display: none'></div>
		
		<script type='text/javascript'>
		
			function setSeed(intTeamID) {
					
					$(document).ready(function() {
						
						$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', { tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID },
						function(data) {
							
							$('#changeSeedDiv').html(data);
							$('#changeSeedDiv').dialog({
							
								title: 'Manage ".$dispTeamOrPlayer."s - Change Seed',
								modal: true,
								width: 400,
								show: 'scale',
								resizable: false,
								zIndex: 9999,
								buttons: {
	
									'Save': function() {
										
										$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', { tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID, newSeed: $('#newSeedSelect').val() },
										function(data1) {
											
											$('#teamListDiv').fadeOut(250);
											$('#loadingSpiral').show();
											$.post('".$MAIN_ROOT."members/tournaments/include/poolseedlist.php', { tID: '".$tournamentInfo['tournament_id']."' }, function(data2) {
												$('#teamListDiv').html(data2);
												$('#loadingSpiral').hide();
												$('#teamListDiv').fadeIn(data2);									
											});
										
										
											
											$('#successBox').html(data1);
											$('#successBox').dialog({
											
											
												title: 'Manage ".$dispTeamOrPlayer."s - Change Seed',
												modal: true,
												width: 400,
												show: 'scale',
												resizable: false,
												zIndex: 9999,
												buttons: {
													'OK': function() {
													
														$(this).dialog('close');
													
													}
												}
											});
										
										});
										
										$(this).dialog('close');
									
									},
									'Cancel': function() {
									
										$(this).dialog('close');
									
									}
	
								}
	
							});
					
						});
					});
				
				}
		
		
		</script>
		
	";


}

?>