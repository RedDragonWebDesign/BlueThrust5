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
require_once("../../../../classes/rankcategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$rankCatObj = new RankCategory($mysqli);

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Rank Categories");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $rankCatObj->select($_POST['rID'])) {
		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$rankCatObj->move($_POST['cDir']);

		require_once("main.php");
	}
}
