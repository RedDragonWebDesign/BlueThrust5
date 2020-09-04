<?php 

if(!defined("MAIN_ROOT")) { exit(); }

function setThankYouPageBreadcrumb() {
	global $breadcrumbObj, $campaignInfo;
	
	$breadcrumbObj->setTitle("Thank You!");
	$breadcrumbObj->popCrumb();
	$breadcrumbObj->addCrumb($campaignInfo['title'], MAIN_ROOT."plugins/donations/?campaign_id=".$campaignInfo['donationcampaign_id']);
	$breadcrumbObj->addCrumb("Thank You!");
}

?>