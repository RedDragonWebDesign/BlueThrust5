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
	
	$countErrors = 0;
	$arrErrors = array();
	// Check IP
	
	if(trim($_POST['ipaddress']) == "") {
		$countErrors++;
		$arrErrors[] = "IP address may not be blank.";
	}
	
	if(isset($_POST['customExp'])) {

		switch($_POST['banLengthUnit']) {
			case "minute":	
				$_POST['expTime'] = $_POST['banLength'];
				break;
			case "hour":
				$_POST['expTime'] = $_POST['banLength']*60;
				break;
			case "day":
				$_POST['expTime'] = $_POST['banLength']*60*24;
				break;
			case "week":
				$_POST['expTime'] = $_POST['banLength']*60*24*7;
				break;
			case "month":
				$_POST['expTime'] = $_POST['banLength']*60*24*30;
				break;
			case "year":
				$_POST['expTime'] = $_POST['banLength']*60*24*365;
				break;
			default:
				$_POST['expTime'] = "error";
		}
	}
	
	
	
	if(!is_numeric($_POST['expTime']) || (is_numeric($_POST['expTime']) && $_POST['expTime'] < 0)) {
		$countErrors++;
		$arrErrors[] = "You entered an invalid expire time.";	
	}

	
	
	if($countErrors == 0) {
		
		$setExpTime = ($_POST['expTime'] == 0) ? $setExpTime = 0 : ($_POST['expTime']*60)+time();
		
		if($ipbanObj->addNew(array("ipaddress", "exptime", "dateadded"), array($_POST['ipaddress'], $setExpTime, time()))) {
			$arrReturn = array("result"=>"success", "settime"=>$setExpTime);
		}
		else {
			$countErrors++;
			$arrErrors[] = "Unable to save information to database! Please contact the website administrator.";	
		}
		
	}
		
	
	if($countErrors > 0) {
		$arrReturn = array("result"=>"fail", "errors"=>$arrErrors);		
	}
	
	echo json_encode($arrReturn);	
	
?>