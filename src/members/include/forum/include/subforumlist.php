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
include_once("../../../../classes/basicsort.php");
include_once("../../../../classes/forumboard.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Add Board");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$boardObj = new ForumBoard($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	


	if(isset($_POST['subforum']) && $boardObj->select($_POST['subforum'])) {
		$arrSubForums = $boardObj->getSubForums();
		
		
		$arrSelectBoard = "";
		if(isset($_POST['bID']) && $boardObj->select($_POST['bID'])) {
			$arrSelectBoard = $boardObj->findBeforeAfter();	
		}
		else {
			$_POST['bID'] = "";	
		}
		
		foreach($arrSubForums as $forumID) {
			if($forumID != $_POST['bID']) {
				$boardObj->select($forumID);
				$boardInfo = $boardObj->get_info_filtered();
	
				$selectBoard = "";
				if($_POST['bID'] != "" && $arrSelectBoard[0] == $boardInfo['forumboard_id']) {
					$selectBoard = " selected";
				}
				
				echo "<option value='".$boardInfo['forumboard_id']."'".$selectBoard.">".$boardInfo['name']."</option>";
			}
		}
		
		if(count($arrSubForums) == 0 || ($_POST['bID'] != "" && count($arrSubForums) == 1)) {
			echo "<option value='first'>(first board)</option>";	
		}
		
	}


}

?>
