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

include_once("../../_setup.php");
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/tournament.php");
include_once("../../classes/consoleoption.php");

if(!isset($tournamentObj)) {
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$tournamentObj = new Tournament($mysqli);
}



if(!isset($tID)) {
	$tID = $_POST['tID'];
}


if(!$tournamentObj->select($tID)) {
	exit();
}

$tournamentInfo = $tournamentObj->get_info_filtered();



		
if(!isset($_POST['roundSelected']) || !is_numeric($_POST['roundSelected'])) {
	$_POST['roundSelected'] = 1;	
}
$arrMatches = $tournamentObj->getMatches($_POST['roundSelected']);

$matchCount = 0;
foreach($arrMatches as $matchID) {
	$matchCount++;
	$tournamentObj->objMatch->select($matchID);
	$matchInfo = $tournamentObj->objMatch->get_info();
	
	$teamScore[1] = $matchInfo['team1score'];
	$teamScore[2] = $matchInfo['team2score'];
	
	$addStyle[1] = "";
	$addStyle[2] = "";
	if($matchInfo['outcome'] == 1) {
		$addStyle[1] = " class='successFont' style='font-weight: bold'";
		$addStyle[2] = " class='failedFont'";		
	}
	elseif($matchInfo['outcome'] == 2) {
		$addStyle[2] = " class='successFont' style='font-weight: bold'";
		$addStyle[1] = " class='failedFont'";
	}
	
	echo "
		<div class='main dottedBox' style='float: left; width: 95%'>
	";
	
		
		for($i=1; $i<=2; $i++) {
			
			$teamColumn = "team".$i."_id";
			$dispName = "Empty Spot";
			$dispSeed = "";
			if($tournamentObj->objTeam->select($matchInfo[$teamColumn])) {
				$teamInfo = $tournamentObj->objTeam->get_info_filtered();
				$dispSeed = "#".$teamInfo['seed'];
				//$arrPlayers = $tournamentObj->getTeamPlayers($matchInfo[$teamColumn], true);
				$dispName = $tournamentObj->getPlayerName();
			
				if($dispName == "") {
					$dispName = "Bye";	
				}
				
			}
			
			
			
			echo "
			<div class='shadedBox' style='position: relative; border: 0px; margin-bottom: 2px'>".$dispName."
			
				<div style='position: absolute; width: 40px; right: 30px; top: 5px; z-index: 9999'><a href='javascript:void(0)' onmouseover=\"showToolTip('Score')\" onmouseout='hideToolTip()'".$addStyle[$i].">".$teamScore[$i]."</a></div>
				<div style='position: absolute; width: 25px; right: 3px; top: 5px; z-index: 9999'><a href='javascript:void(0)' onmouseover=\"showToolTip('Seed')\" onmouseout='hideToolTip()'>".$dispSeed."</a></div>
			</div>					
			";
	
		}

		
		

	
	
	
	
	echo "</div><div style='clear:both'></div>";	
	
}
		
	
	
	
	




?>