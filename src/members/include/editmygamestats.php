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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


include_once("../classes/game.php");

$cID = $_GET['cID'];

$intEditProfileCID = $consoleObj->findConsoleIDByName("Edit Profile");

$dispError = "";
$countErrors = 0;

$gameObj = new Game($mysqli);
$arrGames = $gameObj->getGameList();
$gameStatsObj = new Basic($mysqli, "gamestats", "gamestats_id");
$memberGameStatObj = new Basic($mysqli, "gamestats_members", "gamestatmember_id");

function saveGameStats() {
	global $mysqli, $dbprefix, $memberInfo, $member, $arrGames, $gameObj, $memberGameStatObj, $gameStatsObj;
	$mysqli->query("DELETE FROM ".$dbprefix."gamestats_members WHERE member_id = '".$memberInfo['member_id']."'");
	foreach($arrGames as $gameID) {
		
		if($member->playsGame($gameID)) {
		
			$gameObj->select($gameID);
			
			$arrGameStats = $gameObj->getAssociateIDs("ORDER BY ordernum");
			foreach($arrGameStats as $gameStatsID) {

				$gameStatsObj->select($gameStatsID);
				
				if($gameStatsObj->get_info("stattype") == "input") {
					
					$statType = "statvalue";
					if($gameStatsObj->get_info("textinput") == 1) {
						$statType = "stattext";	
					}
					
					$postVal = "stat_".$gameStatsID;
					
					$memberGameStatObj->addNew(array("gamestats_id", "member_id", $statType, "dateupdated"), array($gameStatsID, $memberInfo['member_id'], $_POST[$postVal], time()));

				}

			}
		
		}
		
	}
	
}


// Setup Form
$i = 1;
$arrComponents = array();

foreach($arrGames as $gameID) {
	$gameObj->select($gameID);
	$arrGameStats = $gameObj->getAssociateIDs("ORDER BY ordernum");

	if($member->playsGame($gameID) && count($arrGameStats) > 0) {
		$arrComponents['customsection_'.$gameID] = array(
			"type" => "section",
			"options" => array("section_title" => $gameObj->get_info_filtered("name")),
			"sortorder" => $i++
		);
		
		foreach($arrGameStats as $gameStatsID) {
			$gameStatsObj->select($gameStatsID);	
			$gameStatsInfo = $gameStatsObj->get_info_filtered();
			if($gameStatsInfo['stattype'] == "input") {
				$textBoxWidth = array("style" => "width: 5%");
				$blnText = false;
				if($gameStatsInfo['textinput'] == 1) {
					$textBoxWidth = array();	
					$blnText = true;
				}
				$gameStatValue = $member->getGameStatValue($gameStatsID, $blnText);
				
				$arrComponents['stat_'.$gameStatsID] = array(
					"display_name" => $gameStatsInfo['name'],
					"attributes" => array_merge(array("class" => "formInput textBox"), $textBoxWidth),
					"value" => $gameStatValue,
					"sortorder" => $i++
				);
			}
		}
		
	}
	
}


$additionalNote = "";
if($i == 1) {
	$customHTML = "
		<div class='shadedBox' style='margin-top: 40px; margin-left: auto; margin-right: auto; width: 45%'>
			<p align='center'>
				You need to set which games you play in your <a href='".$MAIN_ROOT."members/console.php?cID=".$intEditProfileCID."'>profile</a>!
			</p>
		</div>
	";
	
	$arrComponents['nogamesmessage'] = array(
		"type" => "custom",
		"html" => $customHTML,
		"sortorder" => $i++
	);

	$additionalNote = "<br><br><b><u>NOTE:</u></b> If you have selected which games you play in your profile, there might not be any stats associated with them.";
}
else {

	$arrComponents['submit'] = array(
		"type" => "submit",
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"value" => "Save Stats",
		"sortorder" => $i++
	);
	
}

$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"afterSave" => array("saveGameStats"),
	"saveMessage" => "Successfully saved game stats!",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
	"description" => "Use the form below to edit your game stats.".$additionalNote
);


?>