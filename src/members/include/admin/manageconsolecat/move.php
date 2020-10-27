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


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);

$consoleCatObj = new ConsoleCategory($mysqli);


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $consoleCatObj->select($_POST['catID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		
		$consoleCatObj->move($_POST['cDir']);
		
		$_GET['cID'] = $cID;
		include("main.php");
		
		
		
	}
	
	
}


?>