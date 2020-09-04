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
$member = new Member($mysqli);

$postMemberObj = new Member($mysqli);

$intPostTopicCID = $consoleObj->findConsoleIDByName("Post Topic");

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


if(!$boardObj->select($_GET['bID'])) {
	echo "
		<script type='text/javascript'>window.location = 'index.php';</script>
	";
	exit();
}

$boardInfo = $boardObj->get_info_filtered();


// Start Page
$PAGE_NAME = $boardInfo['name']." - Forum - ";
include($prevFolder."themes/".$THEME."/_header.php");

// Check Private Forum

if($websiteInfo['privateforum'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}

$memberInfo = array();


$LOGGED_IN = false;
$NUM_PER_PAGE = 25;
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
	$NUM_PER_PAGE = $memberInfo['topicsperpage'];
}

if($NUM_PER_PAGE == 0) {
	$NUM_PER_PAGE = 25;
}

if(!$boardObj->memberHasAccess($memberInfo)) {
	echo "
	<script type='text/javascript'>window.location = 'index.php';</script>
	";
	exit();
}

$arrTopics = $boardObj->getForumTopics();

if(!isset($_GET['pID']) || !is_numeric($_GET['pID'])) {
	$intOffset = 0;
	$_GET['pID'] = 1;
}
else {
	$intOffset = $NUM_PER_PAGE*($_GET['pID']-1);
}

$blnPageSelect = false;

// Count Pages
$NUM_OF_PAGES = ceil(count($arrTopics)/$NUM_PER_PAGE);

if($NUM_OF_PAGES == 0) {
	$NUM_OF_PAGES = 1;	
}

if($_GET['pID'] > $NUM_OF_PAGES) {

	echo "
	<script type='text/javascript'>window.location = 'viewboard.php?bID=".$_GET['bID']."';</script>
	";
	exit();

}

// Check for Next button
$dispNextPage = "";
if($_GET['pID'] < $NUM_OF_PAGES) {
	$dispNextPage = "<span style='padding-left: 10px'><b><a href='viewboard.php?bID=".$_GET['bID']."&pID=".($_GET['pID']+1)."'>Next</a> &raquo;</b></span>";
	$blnPageSelect = true;
}

// Check for Previous button
$dispPreviousPage = "";
if(($_GET['pID']-1) > 0) {
	$dispPreviousPage = "<b>&laquo; <a href='viewboard.php?bID=".$_GET['bID']."&pID=".($_GET['pID']-1)."'>Previous</a></b>";
	$blnPageSelect = true;
}


for($i=1; $i<=$NUM_OF_PAGES; $i++) {
	$selectPage = "";
	if($i == $_GET['pID']) {
		$selectPage = " selected";	
	}
	$pageoptions .= "<option value='".$i."'".$selectPage.">".$i."</option>";
}

$dispPageSelectTop = "";
$dispPageSelectBottom = "";
if($blnPageSelect) {
	$dispPageSelectTop = "
	<p style='margin-top: 0px'><b>Page:</b> <select id='pageSelectTop' class='textBox'>".$pageoptions."</select> <input type='button' id='btnPageSelectTop' class='submitButton' value='GO' style='width: 40px'></p>
	<p style='margin-top: 0px'>".$dispPreviousPage.$dispNextPage."</p>
	";
	
	$dispPageSelectBottom = "
	<p style='margin-top: 0px'><b>Page:</b> <select id='pageSelectBottom' class='textBox'>".$pageoptions."</select> <input type='button' id='btnPageSelectBottom' class='submitButton' value='GO' style='width: 40px'></p>
	<p style='margin-top: 0px'>".$dispPreviousPage.$dispNextPage."</p>
	";
}


// Subforums

$subForumObj = new ForumBoard($mysqli);
$arrSubForums = $boardObj->getSubForums();
$dispSubForums = "";
foreach($arrSubForums as $boardID) {
	
	$subForumObj->select($boardID);
	
	if($subForumObj->memberHasAccess($memberInfo)) {
		$subForumInfo = $subForumObj->get_info_filtered();
		$arrForumTopics = $subForumObj->getForumTopics();
		
		$newTopicBG = "";
		$dispNewTopicIMG = "";
		
		if($LOGGED_IN && $subForumObj->hasNewTopics($memberInfo['member_id'])) {
			$dispNewTopicIMG = " <img style='margin-left: 5px' src='".$MAIN_ROOT."themes/".$THEME."/images/forum-new.png' title='New Posts!'>";
			$newTopicBG = " boardNewPostBG";
		}
		
		// Get Last Post Display Info
		if(count($arrForumTopics) > 0) {
			$subForumObj->objPost->select($arrForumTopics[0]);
			$firstPostInfo = $subForumObj->objPost->get_info_filtered();
			
			$subForumObj->objTopic->select($firstPostInfo['forumtopic_id']);
			$lastPostID = $subForumObj->objTopic->get_info("lastpost_id");
			
			$subForumObj->objPost->select($lastPostID);
			$lastPostInfo = $subForumObj->objPost->get_info_filtered();
			
			$postMemberObj->select($lastPostInfo['member_id']);
			
			$dispLastPost = "<div class='boardLastPostTitle'><a href='viewtopic.php?tID=".$firstPostInfo['forumtopic_id']."#".$lastPostID."' title='".$firstPostInfo['title']."'>".$firstPostInfo['title']."</a></div>by ".$postMemberObj->getMemberLink()."<br>".getPreciseTime($lastPostInfo['dateposted']);
		}
		else {
			$dispLastPost = "<div style='text-align: center'>No Posts</div>";	
		}
		
		$dispTopicCount = $subForumObj->countTopics();
		$dispPostCount = $subForumObj->countPosts();
		
		$arrDispMoreSubForums = array();
		$arrMoreSubForums = $subForumObj->getSubForums();
	
		foreach($arrMoreSubForums as $value) {
			$subForumObj->select($value);
			$subForumInfo = $subForumObj->get_info_filtered();
			
			$arrDispMoreSubForums[] = "<a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$value."'>".$subForumInfo['name']."</a>";
		}
		
		
		$dispMoreSubForums = "";
		if(count($arrDispMoreSubForums) > 0) {
			$dispMoreSubForums = "<br><br><b>Sub-Forums:</b><br>&nbsp;&nbsp;".implode("&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;", $arrDispMoreSubForums);	
		}
		
		$subForumObj->select($boardID);
		$subForumInfo = $subForumObj->get_info_filtered();
		$dispSubForums .= "
			<tr class='boardRows".$newTopicBG."'>
				<td class='boardName dottedLine".$newTopicBG."'><a href='viewboard.php?bID=".$subForumInfo['forumboard_id']."'>".$subForumInfo['name']."</a>".$dispNewTopicIMG."<br><span class='boardDescription'>".$subForumInfo['description'].$dispMoreSubForums."</span></td>
				<td class='dottedLine boardLastPost".$newTopicBG."'>".$dispLastPost."</td>
				<td class='dottedLine boardTopicCount".$newTopicBG."' align='center'>".$dispTopicCount."<span id='forumPageTopicCount' style='display: none'> Topics</span></td>
				<td class='dottedLine boardTopicCount".$newTopicBG."' align='center'>".$dispPostCount."<span id='forumPagePostCount' style='display: none'> Posts</span></td>
			
			</tr>
		";
		
	}

}

$breadcrumbObj->setTitle($boardInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", $MAIN_ROOT."forum");
$dispBreadCrumbChain = "";
if($boardInfo['subforum_id'] != 0) {
	$subForumID = $boardInfo['subforum_id'];
	$submForumBC = array();
	while($subForumID != 0) {
		$subForumObj->select($subForumID);
		$subForumInfo = $subForumObj->get_info_filtered();
		$subForumID = $subForumInfo['subforum_id'];
		//$dispBreadCrumbChain = "<a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$subForumInfo['forumboard_id']."'>".$subForumInfo['name']."</a> > ".$dispBreadCrumbChain;
		$subForumBC[] = array("link" => $MAIN_ROOT."forum/viewboard.php?bID=".$subForumInfo['forumboard_id'], "value" => $subForumInfo['name']);
	}

	krsort($subForumBC);
	foreach($subForumBC as $bcInfo) {
		$breadcrumbObj->addCrumb($bcInfo['value'], $bcInfo['link']);
	}

}
$breadcrumbObj->addCrumb($boardInfo['name']);
include($prevFolder."include/breadcrumb.php");

$boardObj->showSearchForm();
echo "
<table class='forumTable'>
";

if($dispSubForums != "") {

	echo "	
	
		<tr>
			<td colspan='4' class='boardCategory'>Sub-Forums</td>
		</tr>
		<tr>
			<td class='boardTitles'>Forum:</td>
			<td class='boardTitles forumLastPost'>Last Post:</td>
			<td class='boardTitles forumTopicCount'>Topics:</td>
			<td class='boardTitles forumTopicCount'>Posts:</td>
		</tr>
	";
	
	echo $dispSubForums;
	echo "<tr><td colspan='4'><br><br></td></tr>";
}

$pageSelector = new PageSelector();
$pageSelector->setPages($NUM_OF_PAGES);
$pageSelector->setCurrentPage($_GET['pID']);
$pageSelector->setLink(MAIN_ROOT."forum/viewboard.php?bID=".$_GET['bID']."&pID=");


echo "
	<tr>
		<td colspan='2' class='main' valign='bottom'>
			"; 
			if(LOGGED_IN && $boardObj->memberHasAccess($memberInfo, true)) { 
				echo "<p style='margin-top: 0px'><b>&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intPostTopicCID."&bID=".$boardInfo['forumboard_id']."'>NEW TOPIC</a> &laquo;</b></p>"; 
			}
		echo "
		</td>
		<td colspan='2' align='right' class='main'>
			";
		
		$pageSelector->show();
		
echo "
		</td>
	</tr>
	<tr>
		<td class='boardTitles'>Topic:</td>
		<td class='boardTitles forumTopicCount'>Replies:</td>
		<td class='boardTitles forumTopicCount'>Views:</td>
		<td class='boardTitles forumLastPost'>Last Post:</td>
	</tr>
	<tr>
		<td class='dottedLine' style='padding-top: 5px' colspan='4'></td>
	</tr>
";

$arrPageTopics = $boardObj->getForumTopics(" ft.stickystatus DESC, fp.dateposted DESC", " LIMIT ".$intOffset.", ".$NUM_PER_PAGE);

foreach($arrPageTopics as $postID) {
	
	$boardObj->objPost->select($postID);
	$postInfo = $boardObj->objPost->get_info_filtered();

	$boardObj->objTopic->select($postInfo['forumtopic_id']);
	$topicInfo = $boardObj->objTopic->get_info();
	
	$postMemberObj->select($postInfo['member_id']);
	$dispTopicPoster = $postMemberObj->getMemberLink();
	
	$boardObj->objPost->select($topicInfo['lastpost_id']);
	$lastPostInfo = $boardObj->objPost->get_info_filtered();
	
	$postMemberObj->select($lastPostInfo['member_id']);
	$dispLastPoster = $postMemberObj->getMemberLink();
	
	$dispTopicIconsIMG = "";
	$newTopicBG = "";
	if($LOGGED_IN && !$member->hasSeenTopic($topicInfo['forumtopic_id']) && ($lastPostInfo['dateposted']+(60*60*24*7)) > time()) {
		$newTopicBG = " boardNewPostBG";
		$dispTopicIconsIMG = " <img style='margin-left: 5px' src='".$MAIN_ROOT."themes/".$THEME."/images/forum-new.png' title='New Posts!'>";
	}
	
	if($topicInfo['stickystatus'] == 1) {
		$newTopicBG = " boardNewPostBG";
		$dispTopicIconsIMG .= " <img src='".$MAIN_ROOT."themes/".$THEME."/images/forum-sticky.png' title='Sticky' style='margin-left: 5px'>";
	}
	
	if($topicInfo['lockstatus'] == 1) {
		$newTopicBG = " boardNewPostBG";
		$dispTopicIconsIMG .= " <img src='".$MAIN_ROOT."themes/".$THEME."/images/forum-locked.png' title='Locked' style='margin-left: 5px'>";
	}
	
	
	echo "
		<tr class='boardRows".$newTopicBG."'>
			<td class='boardName dottedLine".$newTopicBG."'><a href='viewtopic.php?tID=".$postInfo['forumtopic_id']."'>".$postInfo['title']."</a>".$dispTopicIconsIMG."<br><span class='boardDescription'>by ".$dispTopicPoster." - ".getPreciseTime($postInfo['dateposted'])."</span></td>
			<td class='boardTopicCount dottedLine".$newTopicBG."' align='center'>".$topicInfo['replies']."<span id='forumPagePostCount' style='display: none'> Replies</span></td>
			<td class='boardTopicCount dottedLine".$newTopicBG."' align='center'>".$topicInfo['views']."<span id='forumPagePostCount' style='display: none'> Views</span></td>
			<td class='boardLastPost dottedLine".$newTopicBG."'>by ".$dispLastPoster."<br>".getPreciseTime($lastPostInfo['dateposted'])."</td>
		</tr>
	";
	
}

echo "
	<tr>
		<td colspan='2' style='padding-top: 15px' class='main' valign='top'>
		";

		if(LOGGED_IN) {
			echo "
				<p style='margin-top: 0px'><b>&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intPostTopicCID."&bID=".$boardInfo['forumboard_id']."'>NEW TOPIC</a> &laquo;</b></p>
			";
		}
	echo "
		
		</td>
		<td colspan='2' style='padding-top: 15px' align='right' class='main'>
			";
	$pageSelector->show();
	echo "
		</td>
	</tr>
</table>
";

if(count($arrTopics) == 0) {
	
	echo "
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
			<p class='main' align='center'>
				<i>No Posts Yet!</i><br>
				<a href='".$MAIN_ROOT."members/console.php?cID=".$intPostTopicCID."&bID=".$_GET['bID']."'>Be the first!</a>
			</p>
		</div>
	";	
	
}

if($blnPageSelect) {
	echo "
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#btnPageSelectTop, #btnPageSelectBottom').click(function() {
					
					var jqPageSelect = \"#pageSelectBottom\";
					var intNewPage = 0;
					
					if($(this).attr('id') == \"btnPageSelectTop\") {
						jqPageSelect = \"#pageSelectTop\";
					}
					
					intNewPage = $(jqPageSelect).val();
					
					window.location = 'viewboard.php?bID=".$_GET['bID']."&pID='+intNewPage;
					
				});
			});
		</script>
	";
}

include($prevFolder."themes/".$THEME."/_footer.php");


?>