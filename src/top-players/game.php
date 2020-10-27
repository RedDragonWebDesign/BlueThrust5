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
include($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/game.php");


// Classes needed for index.php


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


$gameObj = new Game($mysqli);

if($gameObj->select($_GET['gID'])) {
	$gameObj->refreshImageSize();
	$gameInfo = $gameObj->get_info_filtered();
}
else {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
}


// Start Page
$PAGE_NAME = $gameInfo['name']." - Top Players - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);

$breadcrumbObj->setTitle("Top Players: ".$gameInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Top Players: ".$gameInfo['name']);

include($prevFolder."include/breadcrumb.php");

	$gameStatObj = new Basic($mysqli, "gamestats", "gamestats_id");
	$arrGameStats = $gameObj->getAssociateIDs("ORDER BY ordernum");
	
	
	if(count($arrGameStats) > 0) {
		
		
		if(isset($_GET['sID']) && in_array($_GET['sID'], $arrGameStats) && $gameStatObj->select($_GET['sID'])) {
			$gameStatObj->select($_GET['sID']);
		}
		else {
			$gameStatObj->select($arrGameStats[0]);
			$_GET['sID'] = $arrGameStats[0];
		}
		
		$gameStatInfo = $gameStatObj->get_info_filtered();

		$arrMemberList = $gameObj->getMembersWhoPlayThisGame();
		$arrTopPlayers = array();
		foreach($arrMemberList as $memberID) {
			$member->select($memberID);
			
			
			if($gameStatInfo['stattype'] == "calculate") {
				$arrTopPlayers[$memberID] = $gameObj->calcStat($_GET['sID'], $member);
				
			}
			else {
				$arrTopPlayers[$memberID] = $member->getGameStatValue($_GET['sID']);
			}
				
			
		}
		
		
		
		if($_GET['sort'] != "up") {
			$dispSort = "<a href='".$MAIN_ROOT."top-players/game.php?gID=".$_GET['gID']."&sID=".$_GET['sID']."&sort=up'><img src='".$MAIN_ROOT."themes/".$THEME."/images/downarrow.png'></a>";
			$_GET['sort'] = "down";
			arsort($arrTopPlayers);
		}
		else {
			$dispSort = "<a href='".$MAIN_ROOT."top-players/game.php?gID=".$_GET['gID']."&sID=".$_GET['sID']."&sort=down'><img src='".$MAIN_ROOT."themes/".$THEME."/images/uparrow.png'></a>";
			$_GET['sort'] = "up";
			asort($arrTopPlayers);
		}
		
		
		foreach($arrGameStats as $gameStatID) {
			$gameStatObj->select($gameStatID);
			$dispSelected = "";
			if($gameStatID == $_GET['sID']) {
				$dispSelected = " selected";
			}
			
			$statoptions .= "<option value='".$gameStatID."'".$dispSelected.">".$gameStatObj->get_info_filtered("name")."</option>";
		}
		
	
		
		echo "
			<div style='padding-top: 20px; padding-bottom: 10px'>
				<p class='main' align='right' style='padding-right: 20px'>
					<b>Select Stat:</b> <select id='statSelect' class='textBox'>".$statoptions."</option></select> <input type='button' class='submitButton' id='selectStatButton' style='width: 40px' value='GO'>
				</p>
			</div>
			<p align='center'>
				<img src='".$gameInfo['imageurl']."' width='".$gameInfo['imagewidth']."' height='".$gameInfo['imageheight']."'>
			</p>
			<table class='formTable'>
				<tr>
					<td class='formTitle' align='center' style='width: 5%; height: 14px'>#</td>
					<td class='formTitle' style='width: 60%'>Member</td>
					<td class='formTitle' align='center' style='width: 35%'>".$gameStatInfo['name']." - ".$dispSort."</td>
				</tr>
				";
		
		
		$counter = 0;
		foreach($arrTopPlayers as $memberID => $statValue) {
			$counter++;
		
			$addCSS = "";
			if($counter%2 == 0) {
				$addCSS = " alternateBGColor";
			}
			
			$member->select($memberID);
			echo "
				<tr>
					<td class='main".$addCSS."' style='height: 30px'>".$counter.".</td>
					<td class='main".$addCSS."' style='height: 30px; padding-left: 20px'>".$member->getMemberLink()."</td>
					<td class='main".$addCSS."' align='center' style='height: 30px'>".$statValue."</td>
				</tr>
			
			";
			
			
			if($counter >= 10) {
				break;
			}
		}
		
		if($counter < 10) {
			for($i=($counter+1); $i<=10; $i++) {
				$addCSS = "";
				if($i%2 == 0) {
					$addCSS = " alternateBGColor";
				}
				
				
				echo "
					<tr>
						<td class='main".$addCSS."' style='height: 30px'>".$i.".</td>
						<td class='main".$addCSS."' style='height: 30px; padding-left: 20px'><i>Empty</i></td>
						<td class='main".$addCSS."' align='center' style='height: 30px'>-</td>
					</tr>
				";
			}
		}
		
		
		echo "
			</table>

			
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#selectStatButton').click(function() {
						window.location = \"".$MAIN_ROOT."top-players/game.php?gID=".$_GET['gID']."&sID=\"+$('#statSelect').val()+\"&sort=".$_GET['sort']."\";
					});
				});
			</script>
		";
		
	}
	else {
		
		echo "
		
			<div class='shadedBox' style='width: 300px; margin-top: 50px; margin-left: auto; margin-right: auto'>
				<p class='main' align='center'>
					<i>There are currently no stats added for this game!</i>
				</p>
			</div>
		
		";
		
	}
	
	





include($prevFolder."themes/".$THEME."/_footer.php");


?>