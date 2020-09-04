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
include_once("../../../../classes/news.php");
include_once("../../../../classes/tournament.php");
include_once("../../../../classes/event.php");

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
$tournamentObj = new Tournament($mysqli);
$eventObj = new Event($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {
	
	
	$arrTypes = array("news", "tournament", "event");
	
	$arrTypeObj['news']['obj'] = $newsObj;
	$arrTypeObj['tournament']['obj'] = $tournamentObj;
	$arrTypeObj['event']['obj'] = $eventObj;
	
	$arrTypeObj['news']['title'] = "postsubject";
	$arrTypeObj['tournament']['title'] = "name";
	$arrTypeObj['event']['title'] = "title";
	
	$arrTypeObj['news']['message'] = "newspost";
	$arrTypeObj['tournament']['message'] = "description";
	$arrTypeObj['event']['message'] = "description";
	
	
	
	if(in_array($_POST['attachtype'], $arrTypes)) {
		$checkInfo = false;
		switch($_POST['attachtype']) {
			case "news":
				$checkInfo = $newsObj->select($_POST['attachID']);
				$linkURL = $MAIN_ROOT."news/viewpost.php?nID=".$_POST['attachID'];
				break;
			case "tournament":
				$checkInfo = $tournamentObj->select($_POST['attachID']);
				$linkURL = $MAIN_ROOT."tournaments/view.php?tID=".$_POST['attachID'];
				break;
			case "event":
				$checkInfo = $eventObj->select($_POST['attachID']);
				$linkURL = $MAIN_ROOT."events/info.php?eID=".$_POST['attachID'];
				break;	
		}

		
		if($checkInfo) {
			
			$attachObj = $arrTypeObj[$_POST['attachtype']]['obj'];
			$attachTitle = $arrTypeObj[$_POST['attachtype']]['title'];
			$attachMessage = $arrTypeObj[$_POST['attachtype']]['message'];
			
			$attachInfo = $attachObj->get_info_filtered();
			
			$attachMessage = str_replace(array("\r", "\n"), "\\n", $attachInfo[$attachMessage]);
			echo "
			
				<script type='text/javascript'>
			
					$(document).ready(function() {
					
						$('#imageTitle').val('".$attachInfo[$attachTitle]."');
						$('#imageMessage').val('".$attachMessage."');
						
						$('#linkURL').val('".$linkURL."');
					
					});
				
					
				</script>

				
			";
			
		}
		
		
		
	}	
	
} else { echo "no"; }


?>