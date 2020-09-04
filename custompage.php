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


if(!defined("CUSTOM_PAGE")) {
	// Config File
	$prevFolder = "";
	
	include_once($prevFolder."_setup.php");
	
	
	// Classes needed for index.php
	
	
	$customPageObj = new Basic($mysqli, "custompages", "custompage_id");
	
	if(!$customPageObj->select($_GET['pID'])) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
	}
	
	
	$customPageInfo = $customPageObj->get_info();
}

// Start Page
$PAGE_NAME = $customPageInfo['pagename']." - ";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($customPageInfo['pagename']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb($customPageInfo['pagename']);
include($prevFolder."include/breadcrumb.php");


echo $customPageInfo['pageinfo'];

include($prevFolder."themes/".$THEME."/_footer.php"); ?>