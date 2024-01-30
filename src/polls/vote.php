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
// Config File
$prevFolder = "../";

require_once($prevFolder."_setup.php");
require_once($prevFolder."classes/member.php");
require_once($prevFolder."classes/poll.php");

$consoleObj = new ConsoleOption($mysqli);
$pollObj = new Poll($mysqli);
$member = new Member($mysqli);

$arrReturn = ["result" => "fail"];
$pollOptionSelector = "poll_".$_POST['pollID'];
if ($pollObj->select($_POST['pollID'])) {
	$pollInfo = $pollObj->get_info_filtered();
	$pollObj->objAccess->arrAccessFor = ["keyName" => "poll_id", "keyValue" => $pollInfo['poll_id']];

	$blnVote = false;
	$member->select($_SESSION['btUsername']);
	$memberID = "";

	if ($pollInfo['accesstype'] == "members" && $member->authorizeLogin($_SESSION['btPassword'])) {
		$memberID = $member->get_info("member_id");
		$blnVote = true;
	} elseif ($pollInfo['accesstype'] == "memberslimited" && $member->authorizeLogin($_SESSION['btPassword']) && $pollObj->hasAccess($member)) {
		$memberID = $member->get_info("member_id");
		$blnVote = true;
	} elseif ($pollInfo['accesstype'] == "public") {
		$memberID = ($member->authorizeLogin($_SESSION['btPassword'])) ? $member->get_info("member_id") : "";
		$blnVote = true;
	}

	if ($blnVote) {
		if ($pollInfo['multivote'] == 1 || !$pollObj->hasVoted($memberID, $_POST['pollID'])) {
			$voteSuccess = false;
			foreach (json_decode($_POST['pollOptionID'], true) as $pollOptionID) {
				$pollObj->objPollOption->select($pollOptionID);
				$pollOptionInfo = $pollObj->objPollOption->get_info_filtered();
				$voteResult = $pollObj->vote($memberID, $pollOptionInfo);
				if ($voteResult['result'] == 'success') {
					$voteSuccess = true;
				}
			}
			if ($voteSuccess) {
				$arrReturn = ["result" => "success", "message" => "Your vote has been recorded."];
			} else {
				$arrReturn['errors'] = "There was an error recording your vote.";
			}
		} else {
			$arrReturn['errors'] = "You have already voted in this poll.";
		}
	} else {
		$arrReturn['errors'] = "You do not have permission to vote on this poll.";
	}
} else {
	$arrReturn['errors'] = "Poll not found.";
}

echo json_encode($arrReturn);
