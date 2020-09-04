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

if(!isset($consoleObj)) {
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



if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($tID) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info();
	$tmemberID = $tournamentObj->get_info("member_id");
	$tournamentInfo = $tournamentObj->get_info_filtered();

	
	if($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
		
		
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
				<div class='main dottedBox' style='float: left; width: 45%'>
			";
			
			
			if($tournamentInfo['playersperteam'] == 1) {
				
				
				for($i=1; $i<=2; $i++) {
					
					$teamColumn = "team".$i."_id";
					$dispName = "Empty Spot";
					$dispSeed = "";
					if($tournamentObj->objTeam->select($matchInfo[$teamColumn])) {
						$teamInfo = $tournamentObj->objTeam->get_info_filtered();
						$dispSeed = "#".$teamInfo['seed'];
						$arrPlayers = $tournamentObj->getTeamPlayers($matchInfo[$teamColumn], true);
						
						if($tournamentObj->objPlayer->select($arrPlayers[0])) {
							
							$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
							
							$dispName = "<a href='javascript:void(0)' onclick='setPlayerSeed(".$teamInfo['tournamentteam_id'].")'>";
							
							if($member->select($playerInfo['member_id'])) {
								
								$dispName .= $member->get_info_filtered("username");
								
							}
							else {
								
								$dispName .= $playerInfo['displayname'];
								
							}
							
							$dispName .= "</a>";
							
						}
						else {
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
				echo "
					<div class='main' style='position: relative; margin-top:5px; padding: 3px; text-align: right'>
						<a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches&match=".$matchID."'>Manage Match</a>
					</div>
				";
				
				
				
			}
			else {
				// Multi-player Team Tournament
				
				for($i=1; $i<=2; $i++) {
				
					$teamColumn = "team".$i."_id";
					$dispName = "Empty Spot";
					$dispSeed = "";
					if($tournamentObj->objTeam->select($matchInfo[$teamColumn])) {
						$teamInfo = $tournamentObj->objTeam->get_info_filtered();
						$dispSeed = "#".$teamInfo['seed'];

						$dispPlayerList = "";
						$arrTeamPlayers = $tournamentObj->getTeamPlayers($teamInfo['tournamentteam_id'], true);
						foreach($arrTeamPlayers as $playerID) {
							$tournamentObj->objPlayer->select($playerID);
						
							$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
							if(is_numeric($playerInfo['member_id']) && $member->select($playerInfo['member_id'])) {
						
								$dispPlayerList .= "<b>&middot;</b> ".$member->getMemberLink()."<br>";
						
							}
							else {
						
								$dispPlayerList .= "<b>&middot;</b> ".$playerInfo['displayname']."<br>";
						
							}
						
						
						}
						
						if($dispPlayerList == "") {
							$dispPlayerList = "No Players on Team";
						}
						
						
						$dispName = "<a href='javascript:void(0)' onmouseover=\"showToolTip('".addslashes($dispPlayerList)."')\" onmouseout='hideToolTip()' onclick='setPlayerSeed(".$teamInfo['tournamentteam_id'].")'>".$teamInfo['name']."</a>";
						
				
					}
				
				
				
					echo "
					<div class='shadedBox' style='position: relative; border: 0px; margin-bottom: 2px'>".$dispName."
				
					<div style='position: absolute; width: 40px; right: 30px; top: 5px; z-index: 9999'><a href='javascript:void(0)' onmouseover=\"showToolTip('Score')\" onmouseout='hideToolTip()'".$addStyle[$i].">".$teamScore[$i]."</a></div>
					<div style='position: absolute; width: 25px; right: 3px; top: 5px; z-index: 9999'><a href='javascript:void(0)' onmouseover=\"showToolTip('Seed')\" onmouseout='hideToolTip()'>".$dispSeed."</a></div>
					</div>
					";
				
				}
				echo "
				<div class='main' style='position: relative; margin-top:5px; padding: 3px; text-align: right'>
				<a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches&match=".$matchID."'>Manage Match</a>
				</div>
				";
				
				
				
				
				
			}
			
			
			
			
			echo "</div>";
			
			if(($matchCount%2) == 0) {
				echo "
					<div style='clear:both'></div>
				";
			}
			
			
		}
		
	}
	
	
	
}



?>