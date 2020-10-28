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

require_once(BASE_DIRECTORY."plugins/donations/classes/campaign.php");

$cID = $_GET['cID'];
$campaignObj = new DonationCampaign($mysqli);

define("CAMPAIGN_FORM", true);

// Default End Date

$endDate = new DateTime();
$endDate->setTimestamp(time());
$endDate->setTimezone(new DateTimeZone("UTC"));

$defaultEndDate = $endDate->format("M j, Y");

$setRecurringBox = 0;

require_once(BASE_DIRECTORY."plugins/donations/console/campaign_form.php");

$setupFormArgs['components']['enddate']['options']['defaultDate'] = $defaultEndDate;