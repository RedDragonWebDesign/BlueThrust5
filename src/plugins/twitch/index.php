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
include_once(BASE_DIRECTORY."plugins/twitch/twitch.php");

if(isset($_GET['user']) && setupStreamPage()) {
	$webInfoObj->setPage("plugins/twitch/include/stream.php");
}
else {
	$webInfoObj->setPage("plugins/twitch/include/main.php");
}

$pluginObj = new btPlugin($mysqli);

$pluginObj->verifyPlugin("Twitch", array("twitchsocial_id"));

// Start Page
$PAGE_NAME = "Twitch Streams - ";
include(BASE_DIRECTORY."themes/".$THEME."/_header.php");


$breadcrumbObj->setTitle("Twitch Streams");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Twitch Streams");
include(BASE_DIRECTORY."include/breadcrumb.php");

$webInfoObj->displayPage();

include(BASE_DIRECTORY."themes/".$THEME."/_footer.php"); 


?>