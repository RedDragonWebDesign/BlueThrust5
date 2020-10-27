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
include_once("../../../../classes/basicorder.php");



$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Member Application");
$consoleObj->select($cID);

$appComponentObj = new BasicOrder($mysqli, "app_components", "appcomponent_id");


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info_filtered();

	if($appComponentObj->select($_POST['acID'])) {
		
		$member->logAction("Modified the member application component order.");
		
		$appComponentObj->move($_POST['acDir']);
		
		include("appcomponentlist.php");
		
		
		
	}
	
	
}


?>