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


$consoleCatID = $consoleObj->get_info("consolecategory_id");
$socialInfo = $socialObj->get_info_filtered();

$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb($consoleObj->get_info_filtered("pagetitle"), $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb($socialInfo['name']);

$breadcrumbObj->updateBreadcrumb();

define("SOCIALMEDIA_FORM", true);
include(BASE_DIRECTORY."members/include/social/socialmedia_form.php");

$socialOrder = $socialObj->findBeforeAfter();

$socialObj->select($socialInfo['social_id']);
$arrComponents['displayorder']['before_after_value'] = $socialOrder[0];
$arrComponents['displayorder']['after_selected'] = $socialOrder[1];
$arrComponents['displayorder']['value'] = $socialInfo['social_id'];
$arrComponents['submit']['value'] = "Save";


$setupFormArgs['components'] = $arrComponents;
$setupFormArgs['saveType'] = "update";
$setupFormArgs['prefill'] = true;
$setupFormArgs['attributes']['action'] .= "&sID=".$socialInfo['social_id']."&action=edit";
$setupFormArgs['saveMessage'] = "Successfully saved social media icon!";
$setupFormArgs['skipPrefill'] = array("ordernum");


?>