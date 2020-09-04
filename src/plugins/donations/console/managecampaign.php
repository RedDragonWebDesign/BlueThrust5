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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {	
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
	
}

$cID = $_GET['cID'];

include_once(BASE_DIRECTORY."plugins/donations/classes/campaign.php");

$campaignObj = new DonationCampaign($mysqli);
$objManageList = new btOrderManageList($campaignObj);
$objManageList->strMainListLink = BASE_DIRECTORY."plugins/donations/console/managecampaign_main.php";

if($_GET['campaignID'] != "" && $campaignObj->select($_GET['campaignID']) && $_GET['action'] == "edit") {
	include("managecampaign_edit.php");
}
elseif($_GET['action'] == "delete" && $campaignObj->select($_POST['itemID'])) {
	$info = $campaignObj->get_info_filtered();
	$objManageList->strDeleteName = $info['title'];
	$objManageList->strDeletePostVarID = "campaignID";	
}
elseif($_GET['p'] == "log" && $campaignObj->select($_GET['campaignID'])) {
	include(BASE_DIRECTORY."plugins/donations/console/donationlog.php");	
}
elseif(isset($_GET['donationID']) && $campaignObj->donationObj->select($_GET['donationID'])) {
	include(BASE_DIRECTORY."plugins/donations/console/donationdetails.php");	
}
else {
	include($objManageList->strMainListLink);	
}



?>