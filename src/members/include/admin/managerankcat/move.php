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
include_once("../../../../classes/rankcategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$rankCatObj = new RankCategory($mysqli);

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Rank Categories");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $rankCatObj->select($_POST['rID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		
		$rankCatObj->move($_POST['cDir']);
		
		include("main.php");
		
		
		
	}
	
	
}


?>