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

if($tournamentInfo['playersperteam'] == 1) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManagePlayers';</script>");
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Teams\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Manage Teams\");
});
</script>
";


$dispError = "";
$countErrors = 0;

$arrPlayers = $tournamentObj->getPlayers();
$arrPlayerPIDs = $tournamentObj->getPlayers(true);
$arrTeams = $tournamentObj->getTeams(true);

$maxPlayers = $tournamentInfo['playersperteam'];

if($tournamentInfo['seedtype'] == 2) {
	$dispRandomSeedMessage = "Teams were given random seeds when the tournament was created.  To change a team's seed, simply click the change team seed link.";
}

$dispClanOnly = "";
if($tournamentInfo['access'] == 1) {
	$dispClanOnly = "This is a clan only tournament.  You may only add clan members to this tournament.";
}


$teamCounter = 1;
foreach($arrTeams as $teamID) {

	$tournamentObj->objTeam->select($teamID);
	$teamInfo = $tournamentObj->objTeam->get_info_filtered();


	$dispTeamName = $teamInfo['name'];
	if($teamInfo['name'] == "") {
		$dispTeamName = "Team ".$teamCounter;
	}

	$teamoptions .= "<option value='".$teamID."'>".$dispTeamName."</option>";

	$teamCounter++;
}


// Get Squads
$result = $mysqli->query("SELECT * FROM ".$dbprefix."squads ORDER BY name");
while($row = $result->fetch_assoc()) {
	$squadoptions .= "<option value='".$row['squad_id']."'>".filterText($row['name'])."</option>";	
}

echo "
<div class='formDiv main' style='border: 0px; background: none'>
	Use this page to manage tournament players.<br><br>
	<span style='text-decoration: underline; font-weight: bold'>NOTE:</span> The maximum players per team for this tournament is <b>".$maxPlayers."</b>.  You can change the maximum amount of players by going to the <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo'>Edit Tournament Info</a> page. To move a player to a different team, click on their name in the player list.  ".$dispRandomSeedMessage.$dispClanOnly."


<p style='padding-top: 5px'>
<table class='formTable' style='margin-left: 0px'>
	<tr>
		<td class='main' style='width: 100px; padding-left: 0px'><b>Select Team:</b></td>
		<td class='main'><select id='selectteam' class='textBox'>".$teamoptions."</select></td>
	</tr>
	<tr>
		<td class='main' style='width: 100px; padding-left: 0px'><b>Change Name:</b></td>
		<td class='main'><input type='text' id='changename' value='' class='textBox' style='width: 150px'> <input type='button' class='submitButton' id='changenameButton' style='width: 60px' value='Save'></td>
	</tr>
	<tr>
		<td class='main' colspan='2'>
			<div id='saveNameMessageDiv'></div>
		</td>
	</tr>
	<tr>
		<td class='main' colspan='2' align='center'>

			<div class='loadingSpiral' id='loadingSpiralChange'>
				<p align='center'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
				</p>
			</div>
		
		</td>
	</tr>
</table>
</p>

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

	
	echo "
	</div>
	</div>

	<div class='mttRightColumn main'>
		<b>Add Players: <a href='javascript:void(0)' onmouseover=\"showToolTip('If adding multiple players, separate each with a line break')\" onmouseout='hideToolTip()'>(?)</a> - <a href='javascript:void(0)' id='selectFromSquadsLink'>Select From Squads</a></b>
		<div class='dottedLine' style='padding-top: 3px'></div>
		<textarea class='textBox' id='newplayers' rows='4' cols='35' style='margin-top: 8px; margin-left: 0px'></textarea><br>
		
		<input type='button' id='addPlayersButton' class='submitButton' value='Add Players' style='width: 100px; margin-top: 3px'>	
		
	</div>
	<div style='clear: both'></div>
</div>

<div class='formDiv' style='margin-top: 30px'>
	<div class='dottedLine main' style='padding-bottom: 3px'><b>Unassigned Players:</b></div>
	<p style='padding: 5px; margin: 0px'>
		Below is a list of players who have joined the tournament but have not yet been added to a team.<br><br>
		With selected: <select id='moveUnassignedPlayers' class='textBox'><option value='add'>Add to Selected Team</option><option value='delete'>Remove from Tournament</option></select> <input type='button' id='btnUnassignedPlayers' class='submitButton' value='GO'>
	</p>
	
	<table class='formTable' style='border-spacing: 0px'>
		<tr>
			<td class='main' style='width: 5%' align='center'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='18' height='18' id='checkAllX' style='cursor: pointer'></td>
			<td class='formTitle'>Player Name:</td>
		</tr>
	</table>
	<div class='loadingSpiral' id='loadingSpiralUnassignedList'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='manageTournamentTeamsUnassignedPlayers'>
	";
	define("SHOW_UNASSIGNEDPLAYERS", true);
	include("include/unassignedplayers.php");
	echo "
	</div>
	
</div>

<div id='errorMessage' style='display: none' class='main'><p align='center'>You can not add more players than the maximum players of <b>".$maxPlayers."</b>!<br><br>You can increase this amount from the <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo'>Edit Tournament Info</a> page.</p></div>


<div id='selectFromSquadsDiv' style='display: none'>

	<table class='formTable' style='width: 95%'>
		<tr>
			<td class='formLabel'>Select Squad:</td>
			<td class='main'><select class='textBox' id='selectSquadID'><option value=''>Select</option>".$squadoptions."</select></td>
		</tr>
		<tr>
			<td class='formLabel'>Set Team Name: <a href='javascript:void(0)' onmouseover=\"showToolTip('Mark the checkbox to set the tournament team\'s name to the squad name.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
			<td class='main'><input type='checkbox' id='setTeamName' value='1'></td>
		</tr>
	</table>
	<div class='loadingSpiral' id='loadingSpiralSquadList'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='squadListDiv' style='display: none'></div>
	

</div>
<script type='text/javascript'>

	$(document).ready(function() {
	
		var intCheckAll = 0;
		
		$('#checkAllX').click(function() {


			if(intCheckAll == 0) {
				$('input[data-unassignedplayer]').attr('checked', true);
				intCheckAll = 1;
			}
			else {
				$('input[data-unassignedplayer]').attr('checked', false);
				intCheckAll = 0;
			}
			

		});
	
		
		$('#btnUnassignedPlayers').click(function() {
		
			var arrPlayerID = [];
			
			$('input[data-unassignedplayer]:checked').each(function() {
				arrPlayerID.push(this.value);			
				
			});
			
			arrPlayerID = JSON.stringify(arrPlayerID);
			var postAction;
			
			if($('#moveUnassignedPlayers').val() == \"add\") {
				postAction = \"add\";
			}
			else if($('#moveUnassignedPlayers').val() == \"delete\") {
				postAction = \"remove\";
			}

			$('#loadingSpiralUnassignedList').show();
			$('#manageTournamentTeamsUnassignedPlayers').hide();
			$.post('".$MAIN_ROOT."members/tournaments/include/unassignedplayers.php', { playerList: arrPlayerID, action: postAction, teamID: $('#selectteam').val(), tournamentID: '".$tID."' }, function(data) {

				$('#manageTournamentTeamsUnassignedPlayers').html(data);
			
				if(postAction == \"add\") {
					$('#selectteam').change();
				}
				
				$('#manageTournamentTeamsUnassignedPlayers').fadeIn(250);
				$('#loadingSpiralUnassignedList').hide();
			});
		
			
		});
	
		$('#addPlayersButton').click(function() {
	
			
			$('#loadingSpiral').show();
			$('#playerListDiv').hide();
			var intTeamID = $('#selectteam').val();
			$.post('".$MAIN_ROOT."members/tournaments/include/addteamplayers.php', { teamID: intTeamID, players: $('#newplayers').val() }, 
			function(data) {
			
				$('#loadingSpiral').hide();
				$('#playerListDiv').html(data);
				$('#playerListDiv').fadeIn(250);
				
			});
				
		
		});
		
		$('#changenameButton').click(function() {
			
			var intTeamID = $('#selectteam').val();
			var strNewTeamName = $('#changename').val();
		
			$('#saveNameMessageDiv').hide();
			$.post('".$MAIN_ROOT."members/tournaments/include/changeteamname.php', { teamID: intTeamID, newName: strNewTeamName }, function(data) {
				$('#saveNameMessageDiv').html(data);
				$('#saveNameMessageDiv').fadeIn(250).delay(5000).fadeOut(250);
				
			});
			
			
		});
		
		$('#selectteam').change(function() {
		
			var intTeamID = $('#selectteam').val();
		
			$('#loadingSpiralChange').show();
			$.post('".$MAIN_ROOT."members/tournaments/include/getteaminfo.php', { teamID: intTeamID, getWhat: 'name' }, 
			function(data) {
				$('#changename').val(data);
			});
			
			$.post('".$MAIN_ROOT."members/tournaments/include/getteaminfo.php', { teamID: intTeamID, getWhat: 'playerlist' }, 
			function(data) {
				$('#playerListDiv').html(data);
				$('#loadingSpiralChange').hide();
			});
			
			
			

		});
		
		
		$('#selectFromSquadsLink').click(function() {
		
			$('#selectFromSquadsDiv').dialog({
			
				title: 'Manage Teams - Select From Squads',
				modal: true,
				zIndex: 99999,
				show: 'scale',
				resizable: false,
				width: 400,
				buttons: {
					'Add': function() {
						
						$('#squadMemberList input[type=checkbox]').each(function() {
							
							if($(this).is(':checked')) {
								
								$('#newplayers').val($('#newplayers').val()+'\\n'+$(this).val());
							}
							
							
						
						});
						
						
						if($('#setTeamName').attr('checked') == 'checked') {
						
							$('#changename').val($('#squadName').val());
							$('#changenameButton').click();
						
						}
						
						
						$('#addPlayersButton').click();
						
						resetSquadList();
						$(this).dialog('close');
					
					},
					'Cancel': function() {
					
						resetSquadList();
					
						$(this).dialog('close');
					}
				}
			
			});
			
		});
		
		
		$('#selectSquadID').change(function() {
		
			$('#loadingSpiralSquadList').show();
			$('#squadListDiv').hide();
			$.post('".$MAIN_ROOT."members/tournaments/include/squadmemberlist.php', { squadID: $('#selectSquadID').val(), teamID: $('#selectteam').val() }, function(data) {
			
				$('#squadListDiv').html(data);
				$('#loadingSpiralSquadList').hide();
				$('#squadListDiv').fadeIn(250);
			
			});
		
		});
		
		
		
		
		$('#selectteam').change();
		
	});
	
	
	function resetSquadList() {
		
		$(document).ready(function() {
			$('#selectSquadID').val('');
			$('#setTeamName').attr('checked', false);
			$('#squadListDiv').hide();
		});
	
	}
	
	function deletePlayer(intPlayerID) {
	
		$(document).ready(function() {
		
			$('#loadingSpiral').show();
			$('#playerListDiv').hide();
			
			$.post('".$MAIN_ROOT."members/tournaments/include/deleteteamplayer.php', { playerID: intPlayerID }, 
			function(data) {
			
				$('#loadingSpiral').hide();
				$('#playerListDiv').html(data);
				$('#playerListDiv').fadeIn(250);
				
				
				$('#loadingSpiralUnassignedList').show();
				$('#manageTournamentTeamsUnassignedPlayers').hide();
				
				arrPlayerID = JSON.stringify(intPlayerID);
				
				$.post('".$MAIN_ROOT."members/tournaments/include/unassignedplayers.php', { playerList: arrPlayerID, teamID: $('#selectteam').val(), tournamentID: '".$tID."' }, function(data) {
					$('#manageTournamentTeamsUnassignedPlayers').html(data);
			
					
					$('#manageTournamentTeamsUnassignedPlayers').fadeIn(250);
					$('#loadingSpiralUnassignedList').hide();
				});
				
			});
		
		
		
		});
	
	}
	
</script>

";

?>