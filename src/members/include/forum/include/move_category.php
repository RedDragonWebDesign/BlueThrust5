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


include("../../../../_setup.php");
include_once("../../../../classes/member.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Forum Categories");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if(($memberInfo['rank_id'] == 1 || $member->hasAccess($consoleObj)) && $categoryObj->select($_POST['catID'])) {

		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$categoryObj->move($_POST['cDir']);

		include("main_managecategory.php");

	}

}

?>