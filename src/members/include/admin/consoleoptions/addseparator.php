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
require_once("../../../../classes/consolecategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$consoleCatObj = new ConsoleCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $consoleCatObj->select($_POST['cID'])) {
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$consoleCatInfo = $consoleCatObj->get_info();

		$arrAssociates = $consoleCatObj->getAssociateIDs("ORDER BY sortnum");

		$resortOrder = false;
		if (count($arrAssociates) > 0) {
			$consoleObj->select($arrAssociates[0]);
			$intSpot = $consoleObj->makeRoom("before");
			$resortOrder = true;
		} else {
			$intSpot = 1;
		}


		$consoleObj->addNew(["consolecategory_id", "pagetitle", "sep", "sortnum"], [$_POST['cID'], "-separator-", "1", $intSpot]);
		$newSepID = $consoleObj->get_info("console_id");
		$rankPrivObj = new Basic($mysqli, "rank_privileges", "privilege_id");
		$rankPrivObj->addNew(["console_id", "rank_id"], [$newSepID, 1]);


		require_once("main.php");
	}
}
