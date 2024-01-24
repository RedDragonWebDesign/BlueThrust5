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


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);

$consoleCatObj = new ConsoleCategory($mysqli);


if ($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $consoleCatObj->select($_POST['catID'])) {

		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$consoleCatObj->move($_POST['cDir']);

		$_GET['cID'] = $cID;
		require_once("main.php");



	}


}