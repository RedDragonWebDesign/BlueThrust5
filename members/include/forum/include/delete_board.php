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
include_once("../../../../classes/forumboard.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$boardObj = new ForumBoard($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Boards");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $boardObj->select($_POST['bID'])) {
	$boardInfo = $boardObj->get_info_filtered();
	
	if(isset($_POST['confirm'])) {
		
		$boardObj->delete();
		$member->logAction("Deleted Forum Board: ".$boardInfo['name']);
		
		include("main_manageboards.php");
		
	}
	else {
		$addMessage = "";
		if(count($boardObj->getSubForums()) > 0) {
			$addMessage = "<br><br>All sub-forums will be moved to the parent category/sub-forum.";	
		}
		
		echo "
		
			<p class='main' align='center'>
				Are you sure you want to delete the board, <b>".$boardInfo['name']."</b>?<br><br>All posts in this board will also be deleted.".$addMessage."
			</p>
		
		";
		
		
	}	

	
}


?>