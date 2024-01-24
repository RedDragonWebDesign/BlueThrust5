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

require_once("../../../../../_setup.php");
require_once("../../../../../classes/member.php");
require_once("../../../../../classes/rank.php");
require_once("../../../../../classes/consoleoption.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);

$intAddConsoleCID = $consoleObj->findConsoleIDByName("Add Console Option");
$consoleObj->select($intAddConsoleCID);
$checkAccess1 = $member->hasAccess($consoleObj);


$intManageConsoleCID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($intManageConsoleCID);
$checkAccess2 = $member->hasAccess($consoleObj);


$blnSuccess = false;
if ($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if (($checkAccess1 || $checkAccess2) && is_numeric($_POST['kID']) && isset($_SESSION['btAccessRules'][$_POST['kID']])) {


		unset($_SESSION['btAccessRules'][$_POST['kID']]);


	}


	require_once("view.php");

}