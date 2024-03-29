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

require_once("../../../_setup.php");
require_once("../../../classes/member.php");
require_once("../../../classes/rank.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


// Check Login
$LOGIN_FAIL = true;
if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info_filtered();
	$pmObj = new Basic($mysqli, "privatemessages", "pm_id");

	$arrPMIDS = json_decode($_POST['deletePMs'], true);


	foreach ($arrPMIDS as $pmID) {
		if (!is_numeric($pmID)) {
			$arrMultiMemPM = explode("_", $pmID);

			$pmID = $arrMultiMemPM[0];
			$pmMID = $arrMultiMemPM[1];
		}


		$pmObj->select($pmID);
		$pmInfo = $pmObj->get_info();

		if ($pmInfo['receiver_id'] == 0) {
			$multiMemPMObj = new Basic($mysqli, "privatemessage_members", "pmmember_id");

			$multiMemPMObj->select($pmMID);


			$multiMemPMInfo = $multiMemPMObj->get_info();

			if ($multiMemPMInfo['member_id'] == $memberInfo['member_id'] && $multiMemPMInfo['pmfolder_id'] == -2) {
				$multiMemPMObj->update(["deletestatus"], [1]);
			} elseif ($multiMemPMInfo['member_id'] == $memberInfo['member_id']) {
				$multiMemPMObj->update(["pmfolder_id"], [-2]);
			}
		} elseif ($pmInfo['receiver_id'] == $memberInfo['member_id'] && $pmInfo['receiverfolder_id'] == -2) {
			$pmObj->update(["deletereceiver"], [1]);
		} elseif ($pmInfo['receiver_id'] == $memberInfo['member_id']) {
			$pmObj->update(["receiverfolder_id"], [-2]);
		} elseif ($pmInfo['sender_id'] == $memberInfo['member_id'] && $pmInfo['senderfolder_id'] == -2) {
			$pmObj->update(["deletesender"], [1]);
		} elseif ($pmInfo['sender_id'] == $memberInfo['member_id']) {
			$pmObj->update(["senderfolder_id"], [-2]);
		}
	}
}
