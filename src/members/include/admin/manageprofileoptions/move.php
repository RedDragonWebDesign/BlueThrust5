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
require_once("../../../../classes/profileoption.php");
require_once("../../../../classes/profilecategory.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$profileOptionObj = new ProfileOption($mysqli);
$profileCatObj = new ProfileCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
$consoleObj->select($cID);
$_GET['cID'] = $cID;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $profileOptionObj->select($_POST['oID'])) {

		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$profileOptionInfo = $profileOptionObj->get_info();

		$profileCatObj->select($profileOptionInfo['profilecategory_id']);
		$arrAssociates = $profileCatObj->getAssociateIDs("ORDER BY sortnum");
		array_unshift($arrAssociates, "");
		unset($arrAssociates[0]);

		$intSortNum = $profileOptionInfo['sortnum'];
		$moveUp = $intSortNum-1;
		$moveDown = $intSortNum+1;
		$makeMove = "";

		if($_POST['oDir'] == "up" AND $profileOptionObj->select($arrAssociates[$moveUp])) {
			$makeMove = "before";
		}
		elseif($_POST['oDir'] == "down" AND $profileOptionObj->select($arrAssociates[$moveDown])) {
			$makeMove = "after";
		}


		if($makeMove != "") {
			$newSpot = $profileOptionObj->makeRoom($makeMove);

			if(is_numeric($newSpot)) {
				$profileOptionObj->select($_POST['oID']);
				$profileOptionObj->update(array("sortnum"), array($newSpot));
			}

			$profileOptionObj->resortOrder();
		}

		require_once("main.php");
	}


}