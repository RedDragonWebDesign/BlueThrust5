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
$prevFolder = "../../";

include_once($prevFolder."_setup.php");
include_once("classes/campaign.php");
include("breadcrumb_functions.php");

switch($_GET['p']) {
	case "history":

		break;
	case "thankyou":
		$webInfoObj->setPage("plugins/donations/include/thankyou.php");
		$hooksObj->addHook("breadcrumb", "setThankYouPageBreadcrumb");
		break;
	default:
		if(isset($_GET['custom']) && isset($_GET['payment_status'])) {
			$customVars = json_decode($_GET['custom'], true);
			header("Location: ".FULL_SITE_URL."plugins/donations/?campaign_id=".$customVars['campaign_id']."&p=thankyou");	
		} 
		else {
			$webInfoObj->setPage("plugins/donations/include/main.php");
		}
}


$campaignObj = new DonationCampaign($mysqli);
$donationPlugin = new btPlugin($mysqli);

if(!$donationPlugin->selectByName("Donations") || !$campaignObj->select($_GET['campaign_id'])) {
	echo "<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>";
	exit();
}
elseif($donationPlugin->selectByName("Donations") && $donationPlugin->getConfigInfo("email") == "") {
	echo "
		<script type='text/javascript'>
			alert('Please complete the plugin configuration before continuing!');
			window.location = '".$MAIN_ROOT."';
		</script>
	";
	exit();
}

$campaignObj->updateCurrentPeriod();


$campaignInfo = $campaignObj->get_info_filtered();

// Start Page
$PAGE_NAME = $campaignInfo['title']." - ";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);

$breadcrumbObj->setTitle($campaignInfo['title']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Donation Campaign: ".$campaignInfo['title']);
include($prevFolder."include/breadcrumb.php");

$webInfoObj->displayPage();

include($prevFolder."themes/".$THEME."/_footer.php"); 

?>