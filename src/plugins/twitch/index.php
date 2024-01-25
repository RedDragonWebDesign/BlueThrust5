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


// Config File
$prevFolder = "../../";

require_once($prevFolder."_setup.php");
require_once(BASE_DIRECTORY."plugins/twitch/twitch.php");

if (isset($_GET['user']) && setupStreamPage()) {
	$webInfoObj->setPage("plugins/twitch/include/stream.php");
} else {
	$webInfoObj->setPage("plugins/twitch/include/main.php");
}

$pluginObj = new btPlugin($mysqli);

$pluginObj->verifyPlugin("Twitch", array("twitchsocial_id"));

// Start Page
$PAGE_NAME = "Twitch Streams - ";
require_once(BASE_DIRECTORY."themes/".$THEME."/_header.php");


$breadcrumbObj->setTitle("Twitch Streams");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Twitch Streams");
require_once(BASE_DIRECTORY."include/breadcrumb.php");

$webInfoObj->displayPage();

require_once(BASE_DIRECTORY."themes/".$THEME."/_footer.php");
