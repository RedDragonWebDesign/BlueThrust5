<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("../../../../_setup.php");
require_once("../../../../classes/member.php");
require_once("../../../../classes/rank.php");
require_once("../../../../classes/consoleoption.php");
require_once("../../../../classes/game.php");


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
		require_once("main.php");
	}
	
	
}