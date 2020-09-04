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

include("../../../../_setup.php");
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

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $gameObj->select($_POST['gID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		
		$gameObj->move($_POST['gDir']);
		
		$_GET['cID'] = $cID;
		include("main.php");
	}
	
	
}



?>