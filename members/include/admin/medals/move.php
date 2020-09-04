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

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$medalObj = new Medal($mysqli);
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Medals");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $medalObj->select($_POST['itemID'])) {
		
		define("LOGGED_IN", true);
		
		$medalObj->move($_POST['moveDir']);
		
		include("main.php");
		include("../../../console.managelist.list.php");
		
	}
	
	
}



?>