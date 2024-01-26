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

if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $consoleObj->select($_POST['cID'])) {
		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$consoleInfo = $consoleObj->get_info();

		$consoleCatObj->select($consoleInfo['consolecategory_id']);
		$arrAssociates = $consoleCatObj->getAssociateIDs("ORDER BY sortnum");
		array_unshift($arrAssociates, "");
		unset($arrAssociates[0]);

		$intSortNum = $consoleInfo['sortnum'];
		$moveUp = $intSortNum-1;
		$moveDown = $intSortNum+1;
		$makeMove = "";

		if ($_POST['cDir'] == "up" and $consoleObj->select($arrAssociates[$moveUp])) {
			$makeMove = "before";
		} elseif ($_POST['cDir'] == "down" and $consoleObj->select($arrAssociates[$moveDown])) {
			$makeMove = "after";
		}


		if ($makeMove != "") {
			$newSpot = $consoleObj->makeRoom($makeMove);

			if (is_numeric($newSpot)) {
				$consoleObj->select($_POST['cID']);
				$consoleObj->update(["sortnum"], [$newSpot]);
			}

			$consoleObj->resortOrder();
		}

		$_GET['cID'] = $cID;
		require_once("main.php");
	}
}
