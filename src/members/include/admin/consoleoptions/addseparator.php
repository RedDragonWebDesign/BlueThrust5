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
include_once("../../../../classes/consolecategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$consoleCatObj = new ConsoleCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $consoleCatObj->select($_POST['cID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$consoleCatInfo = $consoleCatObj->get_info();		
		
		$arrAssociates = $consoleCatObj->getAssociateIDs("ORDER BY sortnum");
		
		$resortOrder = false;
		if(count($arrAssociates) > 0) {
			$consoleObj->select($arrAssociates[0]);
			$intSpot = $consoleObj->makeRoom("before");
			$resortOrder = true;
		}
		else {
			$intSpot = 1;
		}
		
		
		$consoleObj->addNew(array("consolecategory_id", "pagetitle", "sep", "sortnum"), array($_POST['cID'], "-separator-", "1", $intSpot));
		$newSepID = $consoleObj->get_info("console_id");
		$rankPrivObj = new Basic($mysqli, "rank_privileges", "privilege_id");
		$rankPrivObj->addNew(array("console_id", "rank_id"), array($newSepID, 1));
		
		
		include("main.php");
		
	}
	
}

?>