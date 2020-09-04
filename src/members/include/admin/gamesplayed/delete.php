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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");
include_once("../../../../classes/game.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$gameObj = new Game($mysqli);

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Games Played");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $gameObj->select($_POST['gID'])) {
		
		define("MEMBERRANK_ID", $memberInfo['rank_id']);
		
		
		if($_POST['confirm'] == 1) {
			$gameObj->delete();
			
			//$gameObj->resortOrder();
			
			include("main.php");
		}
		else {
			$gameName = $gameObj->get_info_filtered("name");
			echo "<p align='center'>Are you sure you want to delete the game <b>".$gameName."</b>?</p>";
		}
		
	}
	elseif(!$gameObj->select($_POST['gID'])) {
		
		echo "<p align='center'>Unable find the selected game.  Please try again or contact the website administrator.</p>";
		
	}
	
}



?>