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
require_once("../../../../classes/imageslider.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);

if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $imageSliderObj->select($_POST['imgID'])) {
		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$imageSliderObj->move($_POST['iDir']);

		$_GET['cID'] = $cID;
		require_once("imagelist.php");
	} else {
		echo $_POST['imgID'];
	}
}
