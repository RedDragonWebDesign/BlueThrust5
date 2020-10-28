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


if(!defined("CUSTOM_PAGE")) {
	// Config File
	$prevFolder = "";
	
	require_once($prevFolder."_setup.php");
	
	
	// Classes needed for index.php
	
	
	$customPageObj = new Basic($mysqli, "custompages", "custompage_id");
	
	if(!$customPageObj->select($_GET['pID'])) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
	}
	
	
	$customPageInfo = $customPageObj->get_info();
}

// Start Page
$PAGE_NAME = $customPageInfo['pagename']." - ";
require_once($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($customPageInfo['pagename']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb($customPageInfo['pagename']);
require_once($prevFolder."include/breadcrumb.php");


echo $customPageInfo['pageinfo'];

require_once($prevFolder."themes/".$THEME."/_footer.php");