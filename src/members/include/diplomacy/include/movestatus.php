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

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Diplomacy Statuses");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if(($memberInfo['rank_id'] == 1 || $member->hasAccess($consoleObj)) && $diplomacyStatusObj->select($_POST['sID'])) {

		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$diplomacyStatusObj->move($_POST['sDir']);

		require_once("main_managestatuses.php");

	}



}