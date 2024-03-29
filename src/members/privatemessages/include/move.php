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
require_once("../../../classes/pmfolder.php");
require_once("../../../classes/privatemessage.php");

// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();

$pmFolderObj = new PMFolder($mysqli);
$checkFolder = $pmFolderObj->select($_POST['newFolder']);

$pmFolderObj->setFolder($_POST['newFolder']);
$pmFolderObj->intMemberID = $memberInfo['member_id'];


// Check Login
$LOGIN_FAIL = true;
if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && ($checkFolder || $pmFolderObj->isMemberFolder())) {
	$pmObj = new PrivateMessage($mysqli);

	$arrPMIDS = json_decode($_POST['movePMs']);

	foreach ($arrPMIDS as $pmID) {
		$pmMID = "";
		if (strpos($pmID, "_") !== false) {
			$tempPMID = substr($pmID, 0, strpos($pmID, "_"));
			$pmMID = str_replace($tempPMID."_", "", $pmID);
			$pmID = $tempPMID;
		}

		if ($pmObj->select($pmID)) {
			$tempPMInfo = $pmObj->get_info_filtered();
			$arrRecipients = $pmObj->getRecipients();



			if ($tempPMInfo['sender_id']  == $memberInfo['member_id'] && $pmMID == "") {
				// Sender
				echo "hi";
				$pmObj->update(["senderfolder_id"], [$_POST['newFolder']]);
			} elseif ($tempPMInfo['receiver_id'] == $memberInfo['member_id']) {
				// Receiver
				$pmObj->update(["receiverfolder_id"], [$_POST['newFolder']]);
			} elseif (in_array($memberInfo['member_id'], $arrRecipients)) {
				// Receiver - Multi Member PM

				$tempKey = array_search($memberInfo['member_id'], $arrRecipients);

				$pmObj->multiMemPMObj->select($tempKey);

				$pmObj->multiMemPMObj->update(["pmfolder_id"], [$_POST['newFolder']]);
			}
		}
	}
}
