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

include("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/pmfolder.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pmFolderObj = new PMFolder($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage PM Folders");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {

	$memberInfo = $member->get_info_filtered();
	$arrSpecialFolders = array(0, -1, -2);
	$pmFolderObj->intMemberID = $memberInfo['member_id'];
	if($member->hasAccess($consoleObj) && $pmFolderObj->select($_POST['folder']) && $pmFolderObj->isMemberFolder() && !in_array($_POST['folder'], $arrSpecialFolders)) {
		
		define('SHOW_FOLDERLIST', true);
		$pmFolderObj->setCategoryKeyValue($memberInfo['member_id']);
		$pmFolderObj->move($_POST['folderDir']);
		$pmFolderObj->resortOrder();
		$_GET['cID'] = $cID;
		include("folderlist.php");
	}

}



?>