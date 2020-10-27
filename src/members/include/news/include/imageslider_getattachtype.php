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
include_once("../../../../classes/news.php");
include_once("../../../../classes/shoutbox.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);

$cID = $consoleObj->findConsoleIDByName("Add Home Page Image");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);

$consoleInfo = $consoleObj->get_info_filtered();

$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {
	
	echo "<option value=''>Select</option>";
	
	$arrTypes = array("news", "tournament", "event");
	if(in_array($_POST['attachtype'], $arrTypes)) {
		
		switch($_POST['attachtype']) {
			case "news":
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."news WHERE newstype != '3' ORDER BY dateposted DESC");
				while($row = $result->fetch_assoc()) {
					echo "<option value='".$row['news_id']."'>".$row['postsubject']."</option>";	
				}
				break;
			case "tournament":
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournaments ORDER BY startdate DESC");
				while($row = $result->fetch_assoc()) {
					echo "<option value='".$row['tournament_id']."'>".$row['name']."</option>";
				}
				break;
			case "event":
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."events ORDER BY startdate DESC");
				while($row = $result->fetch_assoc()) {
					echo "<option value='".$row['event_id']."'>".$row['title']."</option>";
				}
				break;	
		}
		
	}	
	
	
}
else {
	echo "no";	
}


?>