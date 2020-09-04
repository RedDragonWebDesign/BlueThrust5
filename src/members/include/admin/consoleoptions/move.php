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

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $consoleObj->select($_POST['cID'])) {
		
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
		
		if($_POST['cDir'] == "up" AND $consoleObj->select($arrAssociates[$moveUp])) {
			$makeMove = "before";
		}
		elseif($_POST['cDir'] == "down" AND $consoleObj->select($arrAssociates[$moveDown])) {
			$makeMove = "after";
		}

		
		if($makeMove != "") {
			$newSpot = $consoleObj->makeRoom($makeMove);
			
			if(is_numeric($newSpot)) {
				$consoleObj->select($_POST['cID']);
				$consoleObj->update(array("sortnum"), array($newSpot));
			}
			
			$consoleObj->resortOrder();
		}
		
		$_GET['cID'] = $cID;
		include("main.php");
	}
	
	
}



?>