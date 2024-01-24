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


if (!defined("MAIN_ROOT")) {
exit();
}

$consoleObj = new ConsoleOption($mysqli);
$boardObj = new ForumBoard($mysqli);
$subForumObj = new ForumBoard($mysqli);
$member = new Member($mysqli);
$postMemberObj = new Member($mysqli);
// Start Page

require_once(BASE_DIRECTORY."themes/".$THEME."/_header.php");

$memberInfo = array();

$LOGGED_IN = false;
if ($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
}


require_once($prevFolder."include/breadcrumb.php");
