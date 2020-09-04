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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/pmfolder.php");
include_once("../../../classes/privatemessage.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pmObj = new PrivateMessage($mysqli);
$multiMemPMObj = $pmObj->multiMemPMObj;

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {	

$memberInfo = $member->get_info_filtered();
$arrPM = array();
$arrPMMID = array();


$pmFolderObj = new PMFolder($mysqli);
$pmFolderObj->intMemberID = $memberInfo['member_id'];


// Stick Folder Conditions in variables

$isFolderSet = isset($_POST['folder']);
$selectedFolder = $pmFolderObj->select($_POST['folder']);
$condition1 = (!$selectedFolder || ($selectedFolder && !$pmFolderObj->isMemberFolder()));
$arrStandardFolders = array(0, -1, -2);

// Folder Checks
if(!$isFolderSet) {
	$_POST['folder'] = 0;
	$pmFolderObj->setFolder($_POST['folder']);
}
elseif($isFolderSet && $condition1 && !in_array($_POST['folder'], $arrStandardFolders)) {
	exit();
}
elseif(in_array($_POST['folder'], $arrStandardFolders)) {
	$pmFolderObj->setFolder($_POST['folder']);	
}



$pmFolderObj->setFolder($_POST['folder']);
$arrFolderContents = $pmFolderObj->getFolderContents();
$arrPM = $arrFolderContents[0];
$arrPMMID = $arrFolderContents[1];


echo "<table class='formTable' style='border-spacing: 0px; table-layout: fixed'>";
foreach($arrPM as $key => $value) {

	$pmObj->select($key);
	$pmInfo = $pmObj->get_info_filtered();

	$useAltBG = " alternateBGColor";
	
	if(isset($arrPMMID[$key]) && $multiMemPMObj->select($arrPMMID[$key]) && $multiMemPMObj->get_info("seenstatus") == 1) {
		$useAltBG = "";	
	}
	elseif(!isset($arrPMMID[$key]) && $pmInfo['status'] == 1) {
		$useAltBG = "";
	}
	
	$addToPMValue = "";
	$addToPMURL = "";
	if(isset($arrPMMID[$key])) {
		$addToPMValue = "_".$arrPMMID[$key];
		$addToPMURL = "&pmMID=".$arrPMMID[$key];	
	}
	
	$member->select($pmInfo['sender_id']);
	
	if($_POST['folder'] == "-1" && $pmInfo['receiver_id'] != 0) {
		$member->select($pmInfo['receiver_id']);
		$dispSender = $member->getMemberLink();
		$member->select($memberInfo['member_id']);
	}
	elseif($_POST['folder'] == "-1" && $pmInfo['receiver_id'] == 0) {
		$dispSender = $pmObj->getRecipients(true);
	}
	else {
		$dispSender = $member->getMemberLink();
	}
	

	echo "
	<tr>
		<td class='pmInbox main solidLine".$useAltBG."' style='padding-left: 0px' width=\"5%\"><input type='checkbox' value='".$pmInfo['pm_id'].$addToPMValue."' class='textBox'></td>
		<td class='pmInbox main solidLine".$useAltBG."' style='overflow: hidden' width=\"30%\"><div style='width: 85%; white-space:nowrap; overflow: hidden; text-overflow: ellipsis'>".$dispSender."</a></div></td>
		<td class='pmInbox main solidLine".$useAltBG."' style='overflow: hidden' width=\"35%\"><div style='width: 85%; white-space:nowrap; overflow: hidden; text-overflow: ellipsis'><a href='".$MAIN_ROOT."members/privatemessages/view.php?pmID=".$pmInfo['pm_id'].$addToPMURL."'>".filterText($pmInfo['subject'])."</a></div></td>
		<td class='pmInbox main solidLine".$useAltBG."' width=\"30%\">".getPreciseTime($pmInfo['datesent'])."</td>
	</tr>
	";

}

if(count($arrPM) == 0) {

	echo "
	<tr>
		<td class='main' colspan='4'>
			<p align='center' style='font-style: italic'>
				This folder is empty!
			</p>
		</td>
	</tr>

	";

}

echo "</table>";

}

?>