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
	
	
	// Config File
	$prevFolder = "../";
	
	include($prevFolder."_setup.php");
	
	include_once($prevFolder."classes/member.php");
	include_once($prevFolder."classes/poll.php");

	
	$consoleObj = new ConsoleOption($mysqli);
	$pollObj = new Poll($mysqli);
	$member = new Member($mysqli);
	
	$arrReturn = array("result" => "fail");
	$pollOptionSelector = "poll_".$_POST['pollID'];
	if($pollObj->select($_POST['pollID'])) {
	
		$pollInfo = $pollObj->get_info_filtered();
		$pollObj->objAccess->arrAccessFor = array("keyName" => "poll_id", "keyValue" => $pollInfo['poll_id']);
		
		$blnVote = false;
		$member->select($_SESSION['btUsername']);
		$memberID = "";
		if($pollInfo['accesstype'] == "members" && $member->authorizeLogin($_SESSION['btPassword'])) {
			$memberID = $member->get_info("member_id");
			$blnVote = true;
		}
		elseif($pollInfo['accesstype'] == "memberslimited" && $member->authorizeLogin($_SESSION['btPassword']) && $pollObj->hasAccess($member)) {
			$memberID = $member->get_info("member_id");
			$blnVote = true;
		}
		elseif($pollInfo['accesstype'] == "public") {
			$memberID = ($member->authorizeLogin($_SESSION['btPassword'])) ? $member->get_info("member_id") : "";
			$blnVote = true;	
		}
		
		
		
		if($blnVote) {
			foreach(json_decode($_POST['pollOptionID'],true) as $pollOptionID) {
				$pollObj->objPollOption->select($pollOptionID);
				$pollOptionInfo = $pollObj->objPollOption->get_info_filtered();
				$arrReturn = $pollObj->vote($memberID, $pollOptionInfo);
			}
		}
		else {
			$arrReturn['errors'] = "You do not have permission to vote on this poll.";	
		}
				
	}

	echo json_encode($arrReturn);

?>