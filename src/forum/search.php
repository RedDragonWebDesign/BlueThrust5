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
$prevFolder = "../";

include($prevFolder."_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$boardObj = new ForumBoard($mysqli);
$subForumObj = new ForumBoard($mysqli);
$member = new Member($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");

define("RESIZE_FORUM_IMAGES", true);
include("forum_image_resize.php");

// Start Page
$PAGE_NAME = "Search Forum - ";
include($prevFolder."themes/".$THEME."/_header.php");

// Check Private Forum

if($websiteInfo['privateforum'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}


$LOGGED_IN = false;
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
}


$breadcrumbObj->setTitle("Search Forum");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", $MAIN_ROOT."forum");
$breadcrumbObj->addCrumb("Search Forum");

if(count($_GET) > 0) {
	$_POST['fakesearchuser'] = $_GET['searchuser'];
	$_POST['checkCSRF'] = $_SESSION['csrfKey'];
	$_POST['submit'] = true;
	$_POST['filtertopics_replies'] = 0;
	$_POST['filtertopics'] = 0;
	$_POST['filterposts'] = 0;
	$_POST['filterposts_newold'] = 0;
	$_POST['sortresults'] = 0;
	$_POST['sortresults_ascdesc'] = 0;
	
	if(count($_GET['filterboards']) == 0) {
		$_POST['filterboards'][] = 0;	
	}
	
	foreach($_GET as $key => $value) {
		$_POST[$key] = $_GET[$key];
	}
	
}

if(count($_POST) > 0) {
	$breadcrumbObj->popCrumb();
	$breadcrumbObj->addCrumb("Search Forum", $MAIN_ROOT."forum/search.php");
	$breadcrumbObj->addCrumb("Search Results");	
}


include($prevFolder."include/breadcrumb.php");


$arrMemberList = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE disabled = '0' AND rank_id != '1' ORDER BY username");
while($row = $result->fetch_assoc()) {
	$arrMemberList[] = array("id" => $row['member_id'], "value" => filterText($row['username']));	
}

$memberList = json_encode($arrMemberList);

$filterBoardOptions[0] = "Search All Boards";
$result = $mysqli->query("SELECT ".$dbprefix."forum_board.forumboard_id FROM ".$dbprefix."forum_board, ".$dbprefix."forum_category WHERE ".$dbprefix."forum_board.forumcategory_id = ".$dbprefix."forum_category.forumcategory_id AND ".$dbprefix."forum_board.subforum_id = '0' ORDER BY ".$dbprefix."forum_category.ordernum DESC, ".$dbprefix."forum_board.sortnum");
while($row = $result->fetch_assoc()) {
	$boardObj->select($row['forumboard_id']);
	if($boardObj->memberHasAccess($memberInfo)) {
		
		$filterBoardOptions[$row['forumboard_id']] = $boardObj->get_info_filtered("name");	
		
		if(count($boardObj->getSubForums()) > 0) {
			
			recurseSubForums("&nbsp;&nbsp;&nbsp;&nbsp;");			
			
		}		
	}
}

function recurseSubForums($spacing) {
	global $filterBoardOptions, $boardObj, $memberInfo;	
	
	$arrSubforums = $boardObj->getSubForums();
	foreach($arrSubforums as $boardID) {
		$boardObj->select($boardID);
		if($boardObj->memberHasAccess($memberInfo)) {
			$filterBoardOptions[$boardObj->get_info("forumboard_id")] = $spacing.$boardObj->get_info_filtered("name");
			if(count($boardObj->getSubForums()) > 0) {			
				recurseSubForums("&nbsp;&nbsp;&nbsp;&nbsp;".$spacing);
			}
		}
	}
	
}

function check_filter_boards() {
	global $boardObj, $formObj;
	
	$countErrors = 0;
	foreach($_POST['filterboards'] as $value) {
		if(!$boardObj->select($value) && $value != 0) {
			$countErrors++;	
		}
	}
	
	if($countErrors > 0) {
		$formObj->errors[] = "You selected an invalid board filter.";	
	}
}


$filterBoardSize = floor(count($filterBoardOptions)*.85);

$i=1;
$formComponents = array(
	"searchbykeyword" => array(
		"options" => array("section_title" => "Search by Keyword"),
		"sortorder" => $i++,
		"type" => "section",
		"validate" => array("search_checks")
	),
	"keyword" => array(
		"type" => "text",
		"attributes" => array("class" => "formInput textBox"),
		"display_name" => "Keyword",
		"sortorder" => $i++
	),
	"filterkeyword" => array(
		"type" => "select",
		"options" => array("Search Entire Posts", "Search Titles Only"),
		"display_name" => "Filter Keyword",
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"searchbyuser" => array(
		"options" => array("section_title" => "Search by Username"),
		"sortorder" => $i++,
		"type" => "section"
	),
	"searchuser" => array(
		"type" => "autocomplete",
		"attributes" => array("class" => "formInput textBox"),
		"display_name" => "Username",
		"sortorder" => $i++,
		"options" => array("real_id" => "searchUser", "fake_id" => "fakeSearchUser", "list" => $memberList)
	),
	"filterusername" => array(
		"type" => "select",
		"options" => array("Find Posts by User", "Find Topics Started by User"),
		"display_name" => "Filter Username",
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"searchoptions" => array(
		"options" => array("section_title" => "Search Options"),
		"sortorder" => $i++,
		"type" => "section"
	),
	"filtertopics" => array(
		"type" => "select",
		"options" => array("At Least", "At Most"),
		"display_name" => "Find Topics with",
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"html" => "<div class='formInput main' style='padding-left: 10px'><input type='text' style='width: 15%' value='0' name='filtertopics_replies' class='textBox'> Replies</div>",
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"filterposts" => array(
		"type" => "select",
		"options" => array("Any Date", "Your Last Login", "Yesterday", "A week ago", "2 weeks ago", "1 month ago", "3 months ago" , "6 months ago", "A year ago"),
		"display_name" => "Find Posts from",
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"html" => "<div class='formInput main' style='padding-left: 10px'><select name='filterposts_newold' class='textBox'><option value='0'>and Newer</option><option value='1'>and Older</option></select></div>",
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"sortresults" => array(
		"type" => "select",
		"options" => array("Last post date", "Topic Title", "Number of Replies", "Number of Views", "Topic Start Date", "Forum", "Username", "Member Rank"),
		"display_name" => "Sort Results by",
		"attributes" => array("class" => "formInput textBox"),
		"sortorder" => $i++,
		"html" => "<div class='formInput main' style='padding-left: 10px'><select name='sortresults_ascdesc' class='textBox'><option value='0'>in Descending Order</option><option value='1'>in Ascending Order</option></select></div>",
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"filterboardsection" => array(
		"type" => "section",
		"options" => array("section_title" => "Filter Boards"),
		"sortorder" => $i++
	),
	"filterboards[]" => array(
		"type" => "select",
		"display_name" => "Select Boards",
		"attributes" => array("multiple" => "multiple", "class" => "formInput textBox", "size" => $filterBoardSize, "style" => "width: 40%"),
		"options" => $filterBoardOptions,
		"sortorder" => $i++,
		"validate" => array("check_filter_boards")
	),
	"include_subforums" => array(
		"type" => "checkbox",
		"value" => 1,
		"display_name" => "Include Sub-Forums",
		"attributes" => array("class" => "formInput", "checked" => "checked"),
		"sortorder" => $i++
		
	),
	"submit" => array(
		"type" => "submit",
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"sortorder" => $i++,
		"value" => "Search"
	)
);


$setupFormArgs = array(
	"name" => "search_form",
	"components" => $formComponents,
	"description" => "Use the form below to search the forum.",
	"attributes" => array("method" => "post", "action" => $MAIN_ROOT."forum/search.php")

);

$formObj = new Form($setupFormArgs);


if($_POST['submit'] && $formObj->validate()) {
	$_SESSION['btLastSearch'] = time();
	
	define("SHOW_SEARCHRESULTS", true);
	include("search_results.php");

	
}
else {

	$formObj->show();

}


function search_checks() {
	
	global $formObj;
	
	if(trim($_POST['keyword']) == "" && trim($_POST['fakesearchuser']) == "") {
		$formObj->errors[] = "You must enter at least a search keyword or username.";
	}
	
	if(isset($_SESSION['btLastSearch']) && time()-$_SESSION['btLastSearch'] < 15) {
		//$formObj->errors[] = "Please wait 15 seconds before searching again.";	
	}
	
	if(!is_numeric($_POST['filtertopics_replies'])) {
		$formObj->errors[] = "The number of topic replies must be a positive numeric value.";	
	}

}


include($prevFolder."themes/".$THEME."/_footer.php");
?>