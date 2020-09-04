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

include("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/imageslider.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $imageSliderObj->select($_POST['imgID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		
		$imageSliderObj->move($_POST['iDir']);
		
		$_GET['cID'] = $cID;
		include("imagelist.php");
	}
	else {
		echo $_POST['imgID'];
	}
	
	
}




?>