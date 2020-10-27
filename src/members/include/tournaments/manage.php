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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

include_once($prevFolder."classes/tournament.php");
$cID = $_GET['cID'];


$tournamentObj = new Tournament($mysqli);

$counter = 0;
$dispTournamentNames = "";
$arrTournaments = $member->getTournamentList(true);
$createTournamentCID = $consoleObj->findConsoleIDByName("Create a Tournament");
echo "
<p align='right' class='main' style='padding-right: 20px'>
	&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$createTournamentCID."'>Create a Tournament</a> &laquo;
</p>
";

$clickCounter = 0;
if(count($arrTournaments) > 0) {

	foreach($arrTournaments as $tournamentID) {
		
		if($tournamentObj->select($tournamentID)) {

			$categoryCSS = "consoleCategory_clicked";
			$hideoptions = "";
			if($counter > 0) {
				$hideoptions = "style='display: none'";
				$categoryCSS = "consoleCategory";
			}
			$counter++;
			$tournamentInfo = $tournamentObj->get_info_filtered();
			
			if($_GET['select'] == $tournamentInfo['tournament_id']) {
				$clickCounter = $counter;
			}
			
			$dispTournamentNames .= "<div class='".$categoryCSS."' style='width: 200px; margin: 3px' id='categoryName".$counter."' onmouseover=\"moverCategory('".$counter."')\" onmouseout=\"moutCategory('".$counter."')\" onclick=\"selectCategory('".$counter."')\">".$tournamentInfo['name']."</div>";
			$dispTournamentOptions .= "<div id='categoryOption".$counter."' ".$hideoptions.">";
			$dispTournamentOptions .= "
			<div class='dottedLine' style='padding-bottom: 3px; margin-bottom: 5px'>
			<b>Manage Tournament - ".$tournamentInfo['name']."</b>
			</div>
			<div style='padding-left: 5px'><ul style='padding: 0px; padding-left: 15px'>
			";
			
			
			$arrTournamentOptionsPageID = array("ManageMatches", "ManageTeams", "EditTournamentInfo");
			$arrTournamentOptionsDispName = array("Manage Matches", "Manage Teams/Players", "Edit Tournament Info");
			
			if($tournamentInfo['seedtype'] != 3) {
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=ManageMatches'>Manage Matches</a></li>";
			}
			else {
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=ManagePools'>Manage Pools</a></li>";
			}
			
			if($tournamentInfo['playersperteam'] > 1) { 
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=ManageTeams'>Manage Teams</a></li>"; 
			}
			else {
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=ManagePlayers'>Manage Players</a></li>";
			}
			
			
			if($tournamentInfo['member_id'] == $memberInfo['member_id']) {
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=SetManagers'>Set Tournament Managers</a></li>";	
				$dispTournamentOptions .= "<li><a href='javascript:void(0)' onclick=\"deleteTournament('".$tournamentInfo['tournament_id']."')\">Delete Tournament</a></li>";
			}
			
			$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo'>Edit Tournament Info</a></li>";
			$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."tournaments/view.php?tID=".$tournamentInfo['tournament_id']."'>View Tournament Page</a></li>";		
			$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."tournaments/bracket.php?tID=".$tournamentInfo['tournament_id']."' target='_blank'>View Bracket</a></li>";
			if($tournamentInfo['seedtype'] == 3 && $tournamentObj->poolsComplete()) {
				$dispTournamentOptions .= "<li><a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=StartMatches'>Start Tournament Matches</li>";
			}
			$dispTournamentOptions .= "</ul></div></div>";
			
		}
		
	}
	
	echo "
	
		<div style='float: left; text-align: left; width: 225px; padding: 10px 0px 0px 40px'>
			$dispTournamentNames
		</div>
		<div style='float: right; text-align: left; width: 300px; padding: 10px 40px 0px 10px'>
			$dispTournamentOptions
		</div>
	
		<div style='clear:both; height: 30px; margin-top: 20px'></div>

		
		<div style='display: none' id='deleteMessage' class='main'><p align='center'>Are you sure you want to delete this tournament?</p></div>
		
		<script type='text/javascript'>
		
			function deleteTournament(intTournamentID) {
			
				$(document).ready(function() {
				
				
					$('#deleteMessage').dialog({
					
						title: 'Delete Tournament',
						modal: true,
						zIndex: 99999,
						width: 400,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {

								$.post('".$MAIN_ROOT."members/tournaments/deletetournament.php', { tID: intTournamentID }, function(data) {
									window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
								});
							
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
						

					});
									
				
				});
			
			}
		
		</script>
		
		
		
	";
	
	if($clickCounter != 0) {
		
		echo "
			<script type='text/javascript'>
				selectCategory('".$clickCounter."');
			</script>
		";
		
	}
	
}
else {
	$intCreateATournamentCID = $consoleObj->findConsoleIDByName("Create A Tournament");
	echo "
	<div class='shadedBox' style='width: 400px; margin-top: 50px; margin-bottom: 50px; margin-left: auto; margin-right: auto;'>
		<p align='center' class='main'>
			<i>You have not created any tournaments!<br><br>You can create a tournament by clicking <a href='console.php?cID=".$intCreateATournamentCID."'>HERE</a>!</i>
		</p>
	</div>
	";
	
	
}