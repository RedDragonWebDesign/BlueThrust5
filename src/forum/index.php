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
$postMemberObj = new Member($mysqli);

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


// Start Page
$PAGE_NAME = "Forum - ";
include($prevFolder."themes/".$THEME."/_header.php");

// Check Private Forum

if($websiteInfo['privateforum'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}

$memberInfo = array();




$LOGGED_IN = false;
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
}

$breadcrumbObj->setTitle("Forum");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum");
include($prevFolder."include/breadcrumb.php");

$boardObj->showSearchForm();
echo "	
	<table class='forumTable'>
";


$result = $mysqli->query("SELECT forumcategory_id FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrForumCats[] = $row['forumcategory_id'];
	
	$categoryObj->select($row['forumcategory_id']);
	$catInfo = $categoryObj->get_info_filtered();
	$arrBoards = $categoryObj->getAssociateIDs("AND subforum_id = '0' ORDER BY sortnum", true);
	$dispBoards = "";
	foreach($arrBoards as $boardID) {
		
		$boardObj->select($boardID);
		
		if($boardObj->memberHasAccess($memberInfo)) {
			$boardInfo = $boardObj->get_info_filtered();
			$arrForumTopics = $boardObj->getForumTopics();
			
			$newTopicBG = "";
			$dispNewTopicIMG = "";
			
			if($LOGGED_IN && $boardObj->hasNewTopics($memberInfo['member_id'])) {
				$dispNewTopicIMG = " <img style='margin-left: 5px' src='".$MAIN_ROOT."themes/".$THEME."/images/forum-new.png' title='New Posts!'>";
				$newTopicBG = " boardNewPostBG";
			}
			
			// Get Last Post Display Info
			if(count($arrForumTopics) > 0) {
				$boardObj->objPost->select($arrForumTopics[0]);
				$firstPostInfo = $boardObj->objPost->get_info_filtered();
				
				$boardObj->objTopic->select($firstPostInfo['forumtopic_id']);
				$lastPostID = $boardObj->objTopic->get_info("lastpost_id");
				
				$boardObj->objPost->select($lastPostID);
				$lastPostInfo = $boardObj->objPost->get_info_filtered();
				
				$postMemberObj->select($lastPostInfo['member_id']);
				
				$dispLastPost = "<div class='boardLastPostTitle'><a href='viewtopic.php?tID=".$firstPostInfo['forumtopic_id']."#".$lastPostID."' title='".$firstPostInfo['title']."'>".$firstPostInfo['title']."</a></div>by ".$postMemberObj->getMemberLink()."<br>".getPreciseTime($lastPostInfo['dateposted']);
			}
			else {
				$dispLastPost = "<div style='text-align: center'>No Posts</div>";	
			}
			
			$dispTopicCount = $boardObj->countTopics();
			$dispPostCount = $boardObj->countPosts();
			
			$arrDispSubForums = array();
			$arrSubForums = $boardObj->getSubForums();
		
			foreach($arrSubForums as $value) {
				$subForumObj->select($value);
				$subForumInfo = $subForumObj->get_info_filtered();
				
				$arrDispSubForums[] = "<a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$value."'>".$subForumInfo['name']."</a>";
			}
			
			
			$dispSubForums = "";
			if(count($arrDispSubForums) > 0) {
				$dispSubForums = "<br><br><b>Sub-Forums:</b><br>&nbsp;&nbsp;".implode("&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;", $arrDispSubForums);	
			}
			
			
			$dispBoards .= "
				<tr class='boardRows".$newTopicBG."'>
					<td class='boardName dottedLine".$newTopicBG."'><a href='viewboard.php?bID=".$boardInfo['forumboard_id']."'>".$boardInfo['name']."</a>".$dispNewTopicIMG."<br><span class='boardDescription'>".$boardInfo['description'].$dispSubForums."</span></td>
					<td class='dottedLine boardLastPost".$newTopicBG."'>".$dispLastPost."</td>
					<td class='dottedLine boardTopicCount".$newTopicBG."' align='center'>".$dispTopicCount."<span id='forumPageTopicCount' style='display: none'> Topics</span></td>
					<td class='dottedLine boardTopicCount boardPostCount".$newTopicBG."' align='center'>".$dispPostCount."<span id='forumPagePostCount' style='display: none'> Posts</span></td>
				
				</tr>
			";
			
		}

	}
	
	
	if($dispBoards != "") {
	
		echo "
			<tr>
				<td colspan='4' class='boardCategory'>
					".$catInfo['name']."
				</td>
			</tr>
			<tr>
				<td class='boardTitles'>Forum:</td>
				<td class='boardTitles forumLastPost'>Last Post:</td>
				<td class='boardTitles forumTopicCount'>Topics:</td>
				<td class='boardTitles forumTopicCount'>Posts:</td>
			</tr>
		";
		echo $dispBoards;
		
		echo "<tr><td colspan='4'><br></td></tr>";
	
	}
	
	
}

if($result->num_rows == 0) {

	echo "
		
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
			<p class='main' align='center'>
				No boards have been made yet!
			</p>
		</div>
	
	";
	
}


echo "</table>";



include($prevFolder."themes/".$THEME."/_footer.php");
?>