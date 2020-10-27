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

	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("IP Banning");
	$consoleObj->select($cID);
	
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		$memberInfo = $member->get_info_filtered();		
	}
	else {
		exit();	
	}
	
	
	if($ipbanObj->select($_POST['ipaddress'])) {

		$ipbanObj->delete();
		$arrReturn = array("result" => "success");
		
	}
	else {
		$arrReturn = array("result" => "fail");
	}
	
	echo json_encode($arrReturn);
	
?>