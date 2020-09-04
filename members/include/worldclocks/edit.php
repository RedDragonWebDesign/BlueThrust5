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

	if(!defined("LOGGED_IN") || !LOGGED_IN) { die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."'</script>"); }
	
	
	$breadcrumbObj->popCrumb();
	$breadcrumbObj->addCrumb("Manage World Clocks", MAIN_ROOT."members/console.php?cID=".$_GET['cID']);
	$breadcrumbObj->addCrumb($clockInfo['name']);
	$breadcrumbObj->updateBreadcrumb();
	
	
	include(BASE_DIRECTORY."members/include/worldclocks/clock_form.php");
	
	$clockOrder = $clockObj->findBeforeAfter();
	$clockObj->select($clockInfo['clock_id']);
	
	$arrComponents['displayorder']['before_after_value'] = $clockOrder[0];
	$arrComponents['displayorder']['after_selected'] = $clockOrder[1];
	$arrComponents['displayorder']['value'] = $clockInfo['clock_id'];
	$arrComponents['submit']['value'] = "Save";
	
	
	$setupFormArgs['description'] = "Use the form below to edit the <b>".$clockInfo['name']."</b> world clock.";
	$setupFormArgs['saveType'] = "update";
	$setupFormArgs['components'] = $arrComponents;
	$setupFormArgs['prefill'] = true;
	$setupFormArgs['skipPrefill'] = array("ordernum");
	$setupFormArgs['attributes']['action'] .= "&clockID=".$clockInfo['clock_id']."&action=edit";
	$setupFormArgs['saveMessage'] = "Successfully saved world clock!";
	$setupFormArgs['saveLink'] = MAIN_ROOT."members/console.php?cID=".$_GET['cID'];
	
?>