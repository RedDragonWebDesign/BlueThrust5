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

if($tournamentInfo['playersperteam'] > 1) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageTeams';</script>");	
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Players\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Manage Players\");
});
</script>
";


$dispError = "";
$countErrors = 0;

$arrPlayers = $tournamentObj->getPlayers();
$arrPlayerPIDs = $tournamentObj->getPlayers(true);
$arrTeams = $tournamentObj->getTeams(true);

$maxPlayers = $tournamentInfo['playersperteam']*$tournamentInfo['maxteams'];

if($tournamentInfo['playersperteam'] == 1 && $tournamentInfo['seedtype'] == 2) {
	$dispRandomSeedMessage = "As you add new players, they will be assigned a random seed.  Click on a player to change and/or view their seed.  ";
}

$dispClanOnly = "";
if($tournamentInfo['access'] == 1) {
	$dispClanOnly = "This is a clan only tournament.  You may only add clan members to this tournament.";
}


echo "
<div class='formDiv main' style='border: 0px; background: none'>
	Use this page to manage tournament players.<br><br>
	<span style='text-decoration: underline; font-weight: bold'>NOTE:</span> The maximum players for this tournament is <b>".$maxPlayers."</b>.  You can change the maximum amount of players by going to the <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo'>Edit Tournament Info</a> page. ".$dispRandomSeedMessage.$dispClanOnly."
</div>
<div class='manageTournamentTeams' id='manageTournamentTeamsDiv'>
	<div class='mttLeftColumn main'>
	<b>Player List:</b>
	<div class='dottedLine' style='padding-top: 3px'></div>
	<div class='loadingSpiral' id='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='playerListDiv'>
	";
	

	$dispSeedChangeDiv = "";
	if($tournamentInfo['playersperteam'] == 1) {
		$dispSeedChangeDiv = "<div id='changeSeedDiv' class='main' style='display: none'></div><div id='successBox' class='main' style='display: none'></div>";
		$i = 1;
		$arrSortPlayers = array();
		$arrDispPlayers = array();
		$arrEmptySpots = array();
		foreach($arrTeams as $teamID) {
		
			$teamPlayer = $tournamentObj->getTeamPlayers($teamID, true);
			$tournamentObj->objTeam->select($teamID);
			$teamInfo = $tournamentObj->objTeam->get_info_filtered();
			
			
			if(count($teamPlayer) > 0) {
				
				$tournamentObj->objPlayer->select($teamPlayer[0]);
				$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
				
				if($member->select($playerInfo['member_id'])) {
					$dispPlayer = $member->get_info_filtered("username");
					
				}
				else {
					$dispPlayer = $playerInfo['displayname'];
				}
				
				$arrSortPlayers[$teamID] = strtolower($dispPlayer);
				
				$arrDispPlayers[$teamID] = "
					<a href='javascript:void(0)' onclick=\"setPlayerSeed('".$teamInfo['tournamentteam_id']."')\">".$dispPlayer."</a><div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"deletePlayer('".$playerInfo['tournamentplayer_id']."')\">X</a></div>
				";
				
				
			}
			else {
				
				$arrEmptySpots[$teamID] = "";
				$arrDispPlayers[$teamID] = "
					<span style='font-style: italic'>Empty Player Slot</span>
				";
			}
			
			$i++;
		}
		
		
		asort($arrSortPlayers);
		$arrCombinedPlayersAndEmpty = $arrSortPlayers+$arrEmptySpots;
		$i=1;
		foreach($arrCombinedPlayersAndEmpty as $key=>$value) {
			
			echo "<div class='mttPlayerSlot main'>".$i.". ".$arrDispPlayers[$key]."</div>";
			$i++;
			
		}
		
		
	}
	else {
		
		
	}
	

	echo "
	</div>
	</div>

	<div class='mttRightColumn main'>
		<b>Add Players: <a href='javascript:void(0)' onmouseover=\"showToolTip('If adding multiple players, separate each with a line break')\" onmouseout='hideToolTip()'>(?)</a></b>
		<div class='dottedLine' style='padding-top: 3px'></div>
		<textarea class='textBox' id='newplayers' rows='4' cols='35' style='margin-top: 8px; margin-left: 0px'></textarea><br>
		
		<input type='button' id='addPlayersButton' class='submitButton' value='Add Players' style='width: 100px; margin-top: 3px'>	
	
	</div>
</div>
<div id='errorMessage' style='display: none' class='main'><p align='center'>You can not add more players than the maximum players of <b>".$maxPlayers."</b>!<br><br>You can increase this amount from the <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo'>Edit Tournament Info</a> page.</p></div>
".$dispSeedChangeDiv."
<script type='text/javascript'>

	$(document).ready(function() {
	
		$('#addPlayersButton').click(function() {
	
			
			$('#loadingSpiral').show();
			$('#playerListDiv').hide();
			$.post('".$MAIN_ROOT."members/tournaments/include/addplayers.php', { tID: '".$tournamentInfo['tournament_id']."', players: $('#newplayers').val() }, 
			function(data) {
			
				$('#loadingSpiral').hide();
				$('#playerListDiv').html(data);
				$('#playerListDiv').fadeIn(250);
				
			});
				
		
		});
		
		
	});
	
	
	function deletePlayer(intPlayerID) {
	
		$(document).ready(function() {
		
			$('#loadingSpiral').show();
			$('#playerListDiv').hide();
			
			$.post('".$MAIN_ROOT."members/tournaments/include/deleteplayer.php', { tID: '".$tournamentInfo['tournament_id']."', playerID: intPlayerID }, 
			function(data) {
			
				$('#loadingSpiral').hide();
				$('#playerListDiv').html(data);
				$('#playerListDiv').fadeIn(250);
				
				
			});
		
		
		
		});
	
	}
	
	";
	if($tournamentInfo['playersperteam'] == 1) {
		
		echo "
			function setPlayerSeed(intTeamID) {
				
				$(document).ready(function() {
					
					$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', { tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID },
					function(data) {
						
						$('#changeSeedDiv').html(data);
						$('#changeSeedDiv').dialog({
						
							title: 'Manage Players - Change Seed',
							modal: true,
							width: 400,
							show: 'scale',
							resizable: false,
							zIndex: 9999,
							buttons: {

								'Save': function() {
									
									$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', { tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID, newSeed: $('#newSeedSelect').val() },
									function(data1) {
									
										
										$('#successBox').html(data1);
										$('#successBox').dialog({
										
										
											title: 'Manage Players - Change Seed',
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
		";
		
	}
	
echo "
</script>

";

?>