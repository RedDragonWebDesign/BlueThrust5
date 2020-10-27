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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/news.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Post Comment");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $newsObj->select($_POST['nID'])) {
	$memberInfo = $member->get_info();
	$newsInfo = $newsObj->get_info();
	$blnPostComment = false;
	if($newsInfo['newstype'] == 2) {
		$privateNewsCID = $consoleObj->findConsoleIDByName("View Private News");
		$consoleObj->select($privateNewsCID);
		
		if($member->hasAccess($consoleObj)) {
			$blnPostComment	= true;
		}

	}
	else {
		$blnPostComment = true;	
	}
	
	
	if($blnPostComment) {
		$newsObj->postComment($memberInfo['member_id'], $_POST['comment']);
	}

	$arrComments = $newsObj->getComments();
	$commentCount = $newsObj->countComments();
	
	include_once("../../../news/comments.php");
	
	echo "
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#commentCount').html('".$commentCount."');
			});
		</script>
	";
	
}



?>