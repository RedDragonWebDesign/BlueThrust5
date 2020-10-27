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

$cID = $consoleObj->findConsoleIDByName("Manage News");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();

$commentObj = new Basic($mysqli, "comments", "comment_id");
$newsObj = new News($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $commentObj->select($_POST['commentID'])) {

	$commentInfo = $commentObj->get_info_filtered();
	
	$newsObj->select($commentInfo['news_id']);
	$newsInfo = $newsObj->get_info_filtered();
	$member->select($commentInfo['member_id']);
	
	$posterInfo = $member->get_info_filtered();
	
	$logMessage = "Deleted comment by ".$member->getMemberLink()." on news post: <b><a href='".$MAIN_ROOT."news/viewpost.php?nID=".$newsInfo['news_id']."'>".$newsInfo['postsubject']."</a></b>";
	
	$member->select($memberInfo['member_id']);
	$member->logAction($logMessage);
	
	$commentObj->delete();	
	
	$arrComments = $newsObj->getComments();
	$commentCount = $newsObj->countComments();
	
}



include("../../../news/comments.php");

echo "
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#commentCount').html('".$commentCount."');
		});
	</script>
";

?>