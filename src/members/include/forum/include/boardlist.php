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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/basicorder.php");
include_once("../../../../classes/basicsort.php");
include_once("../../../../classes/forumboard.php");

// List all subforums function


function listSubForums($forumID, $filterOut, $indent=1) {
	global $mysqli;

	
	$boardObj = new ForumBoard($mysqli);
	
	$boardObj->select($forumID);
	$arrSubForums = $boardObj->getSubForums();

	foreach($arrSubForums as $value) {
		
		if($filterOut != $value) {
			$boardObj->select($value);
			$boardInfo = $boardObj->get_info_filtered();
			echo "<option value='".$boardInfo['forumboard_id']."'>".str_repeat("&nbsp;&nbsp;&nbsp;", $indent)."&middot; ".$boardInfo['name']."</option>";
			
			$moreSubForums = $boardObj->getSubForums();
			if(count($moreSubForums) > 0) {
				listSubForums($value, $filterOut, ($indent+1));		
			}
		}
		
	}
	
}



// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Add Board");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


$boardObj = new ForumBoard($mysqli);

// Check Login
$LOGIN_FAIL = true;

$arrSelectBoard = "";
if(isset($_POST['bID']) && $boardObj->select($_POST['bID'])) {
	$arrSelectBoard = $boardObj->findBeforeAfter();	

	if($boardObj->get_info("subforum_id") != 0) {
		$arrSelectBoard[0] = $boardObj->get_info("subforum_id");
	}

}
else {
	$_POST['bID'] = "";	
}



if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	

	if($categoryObj->select($_POST['catID'])) {

		$arrBoards = $categoryObj->getAssociateIDs("AND subforum_id = '0' ORDER BY sortnum", true);
		
		foreach($arrBoards as $value) {

			if($_POST['bID'] != $value) {
			
				$boardObj->select($value);
				$boardInfo = $boardObj->get_info_filtered();
				
				$selectBoard = "";
				if($_POST['bID'] != "" && $arrSelectBoard[0] == $boardInfo['forumboard_id']) {
					$selectBoard = " selected";
				}
				
				echo "<option value='".$boardInfo['forumboard_id']."'".$selectBoard.">".$boardInfo['name']."</option>";
				
				listSubForums($boardInfo['forumboard_id'], $_POST['bID']);
			}
			
		}
		
		if(count($arrBoards) == 0 || ($_POST['bID'] != "" && count($arrBoards) == 1)) {
			echo "<option value='first'>(first board)</option>";	
		}
	
	}

	
}

?>