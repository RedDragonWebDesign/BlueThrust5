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
require_once("../../../../classes/profilecategory.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$profileCatObj = new ProfileCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Categories");
$consoleObj->select($cID);
$_GET['cID'] = $cID;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $profileCatObj->select($_POST['catID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		
		$profileCatObj->move($_POST['cDir']);
		
		require_once("main.php");
		
		
		
	}
	
	
}