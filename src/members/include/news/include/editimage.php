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
$imageSliderInfo = $imageSliderObj->get_info_filtered();

$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb($consoleObj->get_info_filtered("pagetitle"), $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb(parseBBCode($imageSliderInfo['name']));

$breadcrumbObj->updateBreadcrumb();

define("HPIMAGE_FORM", true);
include(BASE_DIRECTORY."members/include/news/hpimage_form.php");

$imageOrder = $imageSliderObj->findBeforeAfter();

$imageSliderObj->select($imageSliderInfo['imageslider_id']);
$arrComponents['displayorder']['before_after_value'] = $imageOrder[0];
$arrComponents['displayorder']['after_selected'] = $imageOrder[1];
$arrComponents['submit']['value'] = "Save";



$setupFormArgs['components'] = $arrComponents;
$setupFormArgs['saveType'] = "update";
$setupFormArgs['prefill'] = true;
$setupFormArgs['attributes']['action'] .= "&imgID=".$imageSliderInfo['imageslider_id']."&action=edit";
$setupFormArgs['saveMessage'] = "Successfully saved home page image!";