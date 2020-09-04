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
$posterRankObj = new Rank($mysqli);

$intPostTopicCID = $consoleObj->findConsoleIDByName("Post Topic");
$intManagePostsCID = $consoleObj->findConsoleIDByName("Manage Forum Posts");

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");

$downloadCatObj = new DownloadCategory($mysqli);
$attachmentObj = new Download($mysqli);

$downloadCatObj->selectBySpecialKey("forumattachments");

$moveTopicCID = $consoleObj->findConsoleIDByName("Move Topic");

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


if(!$boardObj->objTopic->select($_GET['tID'])) {
	echo "
	<script type='text/javascript'>window.location = 'index.php';</script>
	";
	exit();
}

$topicInfo = $boardObj->objTopic->get_info();
$boardObj->select($topicInfo['forumboard_id']);
$boardObj->objPost->select($topicInfo['forumpost_id']);
$boardInfo = $boardObj->get_info_filtered();

$postInfo = $boardObj->objPost->get_info_filtered();

$boardObj->objPost->select($topicInfo['lastpost_id']);
$lastPostInfo = $boardObj->objPost->get_info_filtered();

$EXTERNAL_JAVASCRIPT .= "<script type='text/javascript' src='".$MAIN_ROOT."js/ace/src-min-noconflict/ace.js' charset='utf-8'></script>";

define("RESIZE_FORUM_IMAGES", true);
include("forum_image_resize.php");


// Start Page
$PAGE_NAME = $postInfo['title']." - ".$boardInfo['name']." - ";


// Quick Reply

$quickReplyForm = new Form();
$btThemeObj->addHeadItem("richtext-js", $quickReplyForm->getRichtextboxJSFile());

include($prevFolder."themes/".$THEME."/_header.php");

// Check Private Forum

if($websiteInfo['privateforum'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}


$blnShowAttachments = false;
if((constant('LOGGED_IN') == true && $downloadCatObj->get_info("accesstype") == 1) || $downloadCatObj->get_info("accesstype") == 0) {
	$blnShowAttachments = true;
}

$memberInfo = array();


$LOGGED_IN = false;
$NUM_PER_PAGE = $websiteInfo['forum_postsperpage'];
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
	$NUM_PER_PAGE = $memberInfo['postsperpage'];
	
	if(!$member->hasSeenTopic($topicInfo['forumtopic_id']) && ($lastPostInfo['dateposted']+(60*60*24*7)) > time()) {
		$mysqli->query("INSERT INTO ".$dbprefix."forum_topicseen (member_id, forumtopic_id) VALUES ('".$memberInfo['member_id']."', '".$topicInfo['forumtopic_id']."')");
	}

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

$arrUpdateViewsColumn = array("views");
$newViewCount = $topicInfo['views']+1;
$arrUpdateViewsValue = array($newViewCount);
$boardObj->objTopic->update($arrUpdateViewsColumn, $arrUpdateViewsValue);

$totalPostsSQL = $mysqli->query("SELECT forumpost_id FROM ".$dbprefix."forum_post WHERE forumtopic_id = '".$topicInfo['forumtopic_id']."' ORDER BY dateposted");

$totalPosts = $totalPostsSQL->num_rows;


if(!isset($_GET['pID']) || !is_numeric($_GET['pID'])) {
	$intOffset = 0;
	$_GET['pID'] = 1;
}
else {
	$intOffset = $NUM_PER_PAGE*($_GET['pID']-1);
}

$blnPageSelect = false;

// Count Pages
$NUM_OF_PAGES = ceil($totalPosts/$NUM_PER_PAGE);

if($NUM_OF_PAGES == 0) {
	$NUM_OF_PAGES = 1;	
}

if($_GET['pID'] > $NUM_OF_PAGES) {

	echo "
	<script type='text/javascript'>window.location = 'viewtopic.php?tID=".$_GET['tID']."';</script>
	";
	exit();

}

$breadcrumbObj->setTitle($postInfo['title']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", $MAIN_ROOT."forum");
if($boardInfo['subforum_id'] != 0) {
	$subForumObj = new ForumBoard($mysqli);
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
$breadcrumbObj->addCrumb($boardInfo['name'], $MAIN_ROOT."forum/viewboard.php?bID=".$boardInfo['forumboard_id']);
$breadcrumbObj->addCrumb($postInfo['title']);
include($prevFolder."include/breadcrumb.php");


$blnManagePosts = false;
$dispManagePosts = "";
if($LOGGED_IN) {
	if($topicInfo['lockstatus'] == 0) {
		$dispPostReply = "<b>&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intPostTopicCID."&bID=".$topicInfo['forumboard_id']."&tID=".$topicInfo['forumtopic_id']."'>POST REPLY</a> &laquo;</b>";
	}
	else {
		$dispPostReply = "<b>&raquo; LOCKED &laquo;</b>";	
	}
	
	$consoleObj->select($intManagePostsCID);
	if($boardObj->memberIsMod($memberInfo['member_id']) || $member->hasAccess($consoleObj)) {
		$blnManagePosts = true;
		
		if($topicInfo['stickystatus'] == 0) {
			$dispManagePosts .= "<b>&raquo <a href='".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&tID=".$topicInfo['forumtopic_id']."&action=sticky'>STICKY TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
		}
		else {
			$dispManagePosts .= "<b>&raquo <a href='".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&tID=".$topicInfo['forumtopic_id']."&action=sticky'>UNSTICKY TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
		}
		
		
		if($topicInfo['lockstatus'] == 0) {
			$dispManagePosts .= "<b>&raquo <a href='".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&tID=".$topicInfo['forumtopic_id']."&action=lock'>LOCK TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
		}
		else {
			$dispManagePosts .= "<b>&raquo <a href='".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&tID=".$topicInfo['forumtopic_id']."&action=lock'>UNLOCK TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
		}
		
		$dispManagePosts .= "<b>&raquo <a href='javascript:void(0)' onclick='deleteTopic()'>DELETE TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
		$dispManagePosts .= "<b>&raquo <a href='".$MAIN_ROOT."members/console.php?cID=".$moveTopicCID."&topicID=".$_GET['tID']."'>MOVE TOPIC</a> &laquo;</b>&nbsp;&nbsp;&nbsp;";
	}


}



$boardObj->showSearchForm();
echo "
<div class='formDiv' style='background: none; border: 0px; overflow: auto'>
	<div style='float: right'>".$dispManagePosts.$dispPostReply."</div>
</div>
";

$pageSelector = new PageSelector();
$pageSelector->setPages($NUM_OF_PAGES);
$pageSelector->setCurrentPage($_GET['pID']);
$pageSelector->setLink(MAIN_ROOT."forum/viewtopic.php?tID=".$_GET['tID']."&pID=");
$pageSelector->show();

$countManagablePosts = 0;
define("SHOW_FORUMPOST", true);
$result = $mysqli->query("SELECT forumpost_id FROM ".$dbprefix."forum_post WHERE forumtopic_id = '".$topicInfo['forumtopic_id']."' ORDER BY dateposted LIMIT ".$intOffset.", ".$NUM_PER_PAGE);
while($row = $result->fetch_assoc()) {
	$boardObj->objPost->select($row['forumpost_id']);
	$boardObj->objPost->blnManageable = $blnManagePosts;
	
	if($boardObj->objPost->get_info("member_id") == $memberInfo['member_id'] || $blnManagePosts) {
		$countManagablePosts++;
		$boardObj->objPost->blnManageable = true;
	}
	
	$boardObj->objPost->show();
}

$pageSelector->show();

echo "
<div class='formDiv' style='background: none; border: 0px; overflow: auto'>
	<div style='float: right'>".$dispManagePosts.$dispPostReply."</div>
</div>

";

if(LOGGED_IN && $topicInfo['lockstatus'] == 0) {
	
	$forumConsoleObj = new ConsoleOption($mysqli);
	$postCID = $forumConsoleObj->findConsoleIDByName("Post Topic");
	$forumConsoleObj->select($postCID);
	$postReplyLink = $forumConsoleObj->getLink();
	
	$i = 1;
	$arrComponents = array(
		"message" => array(
			"type" => "richtextbox",
			"sortorder" => $i++,
			"display_name" => "Message",
			"attributes" => array("id" => "richTextarea", "style" => "width: 90%", "rows" => "10"),
			"validate" => array("NOT_BLANK")
		),
		"submit" => array(
			"type" => "submit",
			"sortorder" => $i++,
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"value" => "Post"
		)
	);
	
	$arrSetupReplyForm = array(
		"name" => "forum-quick-reply",
		"components" => $arrComponents,
		"wrapper" => array(),
		"attributes" => array("method" => "post", "action" => $postReplyLink."&bID=".$boardInfo['forumboard_id']."&tID=".$topicInfo['forumtopic_id'])
	);
	
	$quickReplyForm->buildForm($arrSetupReplyForm);
	echo "

		<div class='formDiv'>
			<b>Quick Reply:</b>

			";
		
		$quickReplyForm->show();
	
	echo "
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
					
					window.location = 'viewtopic.php?tID=".$_GET['tID']."&pID='+intNewPage;
					
				});
			});
		</script>
	";
}

if($blnManagePosts) {
	echo "
		<div id='confirmDeleteTopicDiv' style='display: none'>
			<p align='center' class='main'>
				Are you sure you want to delete this topic?<br><br>
				All posts will be deleted within the topic as well.
			</p>
		</div>
		<script type='text/javascript'>
			function deleteTopic() {
			
				$(document).ready(function() {
	
					$('#confirmDeleteTopicDiv').dialog({
						title: 'Delete Topic - Confirm Delete',
						show: 'scale',
						zIndex: 99999,
						width: 400,
						resizable: false,
						modal: true,
						buttons: {
							'Yes': function() {
								$(this).dialog('close');
								window.location = '".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&tID=".$topicInfo['forumtopic_id']."&action=delete'
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
					
					});
				
				});
	
			}
		</script>
	";
}


if($countManagablePosts > 0) {
	echo "
	
	<div id='confirmDeleteDiv' style='display: none'>
			<p align='center' class='main'>
				Are you sure you want to delete this post?<br><br>
			</p>
		</div>
		<script type='text/javascript'>
			function deletePost(intPostID) {
			
				$(document).ready(function() {
	
					$('#confirmDeleteDiv').dialog({
						title: 'Delete Post - Confirm Delete',
						show: 'scale',
						zIndex: 99999,
						width: 400,
						resizable: false,
						modal: true,
						buttons: {
							'Yes': function() {
								$(this).dialog('close');
								window.location = '".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&pID='+intPostID+'&action=delete'
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
					
					});
				
				});
	
			}
		</script>
	";
	
}

include($prevFolder."themes/".$THEME."/_footer.php");
?>