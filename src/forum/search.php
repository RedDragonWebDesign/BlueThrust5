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

function recurseSubForums($spacing) {
	global $filterBoardOptions, $boardObj, $memberInfo;

	$arrSubforums = $boardObj->getSubForums();
	foreach ($arrSubforums as $boardID) {
		$boardObj->select($boardID);
		if ($boardObj->memberHasAccess($memberInfo)) {
			$filterBoardOptions[$boardObj->get_info("forumboard_id")] = $spacing.$boardObj->get_info_filtered("name");
			if (count($boardObj->getSubForums()) > 0) {
				recurseSubForums("&nbsp;&nbsp;&nbsp;&nbsp;".$spacing);
			}
		}
	}
}

/**
 * Validation function used by the form processor
 */
function check_filter_boards() {
	global $boardObj, $formObj;

	$countErrors = 0;
	foreach ($_POST['filterboards'] as $value) {
		if (!$boardObj->select($value) && $value != 0) {
			$countErrors++;
		}
	}

	if ($countErrors > 0) {
		$formObj->errors[] = "You selected an invalid board filter.";
	}
}

// Config File
$prevFolder = "../";

require_once($prevFolder."_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$boardObj = new ForumBoard($mysqli);
$subForumObj = new ForumBoard($mysqli);
$member = new Member($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");

define("RESIZE_FORUM_IMAGES", true);
require_once("forum_image_resize.php");

// Start Page
$PAGE_NAME = "Search Forum - ";
require_once($prevFolder."themes/".$THEME."/_header.php");

// Check Private Forum

if ($websiteInfo['privateforum'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}


$LOGGED_IN = false;
if ($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
}


$breadcrumbObj->setTitle("Search Forum");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", $MAIN_ROOT."forum");
$breadcrumbObj->addCrumb("Search Forum");

// $_POST is the method for doing advanced searches. But there is also a limited $_GET search for searching all of a user's posts. If using $_GET search, set the $_POST variables here.
if (count($_GET) > 0) {
	$_POST['fakesearchuser'] = $_GET['searchuser'];
	$_POST['checkCSRF'] = $_SESSION['csrfKey'];
	$_POST['submit'] = true;
	$_POST['filtertopics_replies'] = 0;
	$_POST['filtertopics'] = 0;
	$_POST['filterposts'] = 0;
	$_POST['filterposts_newold'] = 0;
	$_POST['sortresults'] = 0;
	$_POST['sortresults_ascdesc'] = 0;

	if (count($_GET['filterboards']) == 0) {
		$_POST['filterboards'][] = 0;
	}

	foreach ($_GET as $key => $value) {
		$_POST[$key] = $_GET[$key];
	}
}

if (count($_POST) > 0) {
	$breadcrumbObj->popCrumb();
	$breadcrumbObj->addCrumb("Search Forum", $MAIN_ROOT."forum/search.php");
	$breadcrumbObj->addCrumb("Search Results");
}


require_once($prevFolder."include/breadcrumb.php");


$arrMemberList = [];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE disabled = '0' AND rank_id != '1' ORDER BY username");
while ($row = $result->fetch_assoc()) {
	$arrMemberList[] = ["id" => $row['member_id'], "value" => filterText($row['username'])];
}

$memberList = json_encode($arrMemberList);

// Populate the $filterBoardOptions variable, which contains the list of boards to include in the Filter Boards <select> element
$filterBoardOptions[0] = "Search All Boards";
$result = $mysqli->query("SELECT ".$dbprefix."forum_board.forumboard_id FROM ".$dbprefix."forum_board, ".$dbprefix."forum_category WHERE ".$dbprefix."forum_board.forumcategory_id = ".$dbprefix."forum_category.forumcategory_id AND ".$dbprefix."forum_board.subforum_id = '0' ORDER BY ".$dbprefix."forum_category.ordernum DESC, ".$dbprefix."forum_board.sortnum");
while ($row = $result->fetch_assoc()) {
	$boardObj->select($row['forumboard_id']);
	if ($boardObj->memberHasAccess($memberInfo)) {
		$filterBoardOptions[$row['forumboard_id']] = $boardObj->get_info_filtered("name");

		if (count($boardObj->getSubForums()) > 0) {
			recurseSubForums("&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	}
}

$filterBoardSize = floor(count($filterBoardOptions)*.85);

$i=1;
$formComponents = [
	"searchbykeyword" => [
		"options" => ["section_title" => "Search by Keyword"],
		"sortorder" => $i++,
		"type" => "section",
		"validate" => ["search_checks"]
	],
	"keyword" => [
		"type" => "text",
		"attributes" => ["class" => "formInput textBox"],
		"display_name" => "Keyword",
		"sortorder" => $i++
	],
	"filterkeyword" => [
		"type" => "select",
		"options" => ["Search Entire Posts", "Search Titles Only"],
		"display_name" => "Filter Keyword",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"searchbyuser" => [
		"options" => ["section_title" => "Search by Username"],
		"sortorder" => $i++,
		"type" => "section"
	],
	"searchuser" => [
		"type" => "autocomplete",
		"attributes" => ["class" => "formInput textBox"],
		"display_name" => "Username",
		"sortorder" => $i++,
		"options" => ["real_id" => "searchUser", "fake_id" => "fakeSearchUser", "list" => $memberList]
	],
	"filterusername" => [
		"type" => "select",
		"options" => ["Find Posts by User", "Find Topics Started by User"],
		"display_name" => "Filter Username",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"searchoptions" => [
		"options" => ["section_title" => "Search Options"],
		"sortorder" => $i++,
		"type" => "section"
	],
	"filtertopics" => [
		"type" => "select",
		"options" => ["At Least", "At Most"],
		"display_name" => "Find Topics with",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"html" => "<div class='formInput main' style='padding-left: 10px'><input type='text' style='width: 15%' value='0' name='filtertopics_replies' class='textBox'> Replies</div>",
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"filterposts" => [
		"type" => "select",
		"options" => ["Any Date", "Your Last Login", "Yesterday", "A week ago", "2 weeks ago", "1 month ago", "3 months ago" , "6 months ago", "A year ago"],
		"display_name" => "Find Posts from",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"html" => "<div class='formInput main' style='padding-left: 10px'><select name='filterposts_newold' class='textBox'><option value='0'>and Newer</option><option value='1'>and Older</option></select></div>",
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"sortresults" => [
		"type" => "select",
		"options" => ["Last post date", "Topic Title", "Number of Replies", "Number of Views", "Topic Start Date", "Forum", "Username", "Member Rank"],
		"display_name" => "Sort Results by",
		"attributes" => ["class" => "formInput textBox"],
		"sortorder" => $i++,
		"html" => "<div class='formInput main' style='padding-left: 10px'><select name='sortresults_ascdesc' class='textBox'><option value='0'>in Descending Order</option><option value='1'>in Ascending Order</option></select></div>",
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"filterboardsection" => [
		"type" => "section",
		"options" => ["section_title" => "Filter Boards"],
		"sortorder" => $i++
	],
	"filterboards[]" => [
		"type" => "select",
		"display_name" => "Select Boards",
		"attributes" => ["multiple" => "multiple", "class" => "formInput textBox", "size" => $filterBoardSize, "style" => "width: 40%"],
		"options" => $filterBoardOptions,
		"sortorder" => $i++,
		"validate" => ["check_filter_boards"]
	],
	"include_subforums" => [
		"type" => "checkbox",
		"value" => 1,
		"display_name" => "Include Sub-Forums",
		"attributes" => ["class" => "formInput", "checked" => "checked"],
		"sortorder" => $i++

	],
	"submit" => [
		"type" => "submit",
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"sortorder" => $i++,
		"value" => "Search"
	]
];


$setupFormArgs = [
	"name" => "search_form",
	"components" => $formComponents,
	"description" => "Use the form below to search the forum.",
	"attributes" => ["method" => "post", "action" => $MAIN_ROOT."forum/search.php"]

];

$formObj = new Form($setupFormArgs);


if (isset($_POST['submit']) && $formObj->validate()) {
	$_SESSION['btLastSearch'] = time();

	define("SHOW_SEARCHRESULTS", true);
	require_once("search_results.php");
} else {
	$formObj->show();
}


function search_checks() {

	global $formObj;

	if (trim($_POST['keyword']) == "" && trim($_POST['fakesearchuser']) == "") {
		$formObj->errors[] = "You must enter at least a search keyword or username.";
	}

	if (isset($_SESSION['btLastSearch']) && time()-$_SESSION['btLastSearch'] < 15) {
		//$formObj->errors[] = "Please wait 15 seconds before searching again.";
	}

	if (!is_numeric($_POST['filtertopics_replies'])) {
		$formObj->errors[] = "The number of topic replies must be a positive numeric value.";
	}
}


require_once($prevFolder."themes/".$THEME."/_footer.php");
