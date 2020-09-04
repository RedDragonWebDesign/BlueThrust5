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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$boardObj = new ForumBoard($mysqli);

if(!$boardObj->objTopic->select($_GET['topicID'])) {

	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members'
		</script>
	";
	
	exit();
}

$topicInfo = $boardObj->objTopic->get_info_filtered();
$boardObj->select($boardObj->objTopic->get_info("forumboard_id"));
$boardInfo = $boardObj->get_info_filtered();

$forumCatObj = new Basic($mysqli, "forum_category", "forumcategory_id");
$boardObj->objPost->select($topicInfo['forumpost_id']);
$postInfo = $boardObj->objPost->get_info_filtered();

$boardIDs = $boardObj->getAllBoards();
$catName = "";
$nonSelectableItems = array();
foreach($boardIDs as $id) {
	$boardObj->select($id);
	$forumCatID = $boardObj->get_info("forumcategory_id");
	$forumCatObj->select($forumCatID);
	if($forumCatObj->get_info_filtered("name") != $catName) {
		$catName = $forumCatObj->get_info_filtered("name");
		$catKey = "category_".$forumCatID;
		$forumBoardOptions[$catKey] = "<b>".$catName."</b>";
		$nonSelectableItems[] = $catKey;
	}
	
	if(($member->hasAccess($consoleObj) || $boardObj->memberIsMod($memberInfo['member_id'])) && $id != $topicInfo['forumboard_id']) {
		$spacing = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $boardObj->calcBoardDepth());
		$forumBoardOptions[$id] = $spacing.$boardObj->get_info_filtered("name");
	}
}


$i = 0;
$arrComponents = array(
	"selecteditem" => array(
		"type" => "custom",
		"display_name" => "Selected Topic",
		"sortorder" => $i++,
		"html" => "<div class='formInput'><b>".$postInfo['title']."</b></div>"
	),
	"moveto" => array(
		"type" => "select",
		"attributes" => array("class" => "formInput textBox"),
		"display_name" => "Move To",
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"options" => $forumBoardOptions,
		"sortorder" => $i++,
		"db_name" => "forumboard_id",
		"non_selectable_items" => $nonSelectableItems
	),
	"postredirect" => array(
		"type" => "checkbox",
		"attributes" => array("id" => "postRedirect", "class" => "formInput", "checked" => "checked"),
		"value" => 1,
		"display_name" => "Post a Redirect",
		"sortorder" => $i++
	),
	"postredirect_top" => array(
		"type" => "custom",
		"sortorder" => $i++,
		"html" => "<div id='postRedirectSection'>"
	),
	"postredirect_desc" => array(
		"type" => "textarea",
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox", "rows" => "5", "cols" => "40"),
		"display_name" => "Redirect Description",
		"tooltip" => "Let users know why the topic is being moved.",
		"value" => "This topic has been moved to [BOARD].\n\n[TOPIC_LINK]"
	),
	"postredirect_bottom" => array(
		"type" => "custom",
		"sortorder" => $i++,
		"html" => "</div>"
	),
	"submit" => array(
		"type" => "submit",
		"value" => "Move Topic",
		"sortorder" => $i++,
		"attributes" => array("class" => "submitButton formSubmitButton")
	
	)
);


$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to move the selected topic.",
	"saveObject" => $boardObj->objTopic,
	"saveMessage" => "Successfully Moved Topic!",
	"saveType" => "update",
	"saveLink" => $MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id'],
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID."&topicID=".$topicInfo['forumtopic_id'], "method" => "post"),
	"afterSave" => array("post_topic_redirect")
);


function post_topic_redirect() {
	global $mysqli, $boardObj, $postInfo, $MAIN_ROOT, $topicInfo, $member;
		
	if($_POST['postredirect'] == 1) {
		$boardObj->select($_POST['moveto']);
		
		$arrColumns = array("forumboard_id", "lockstatus");
		$arrValues = array($topicInfo['forumboard_id'], 1);
		$boardObj->objTopic->addNew($arrColumns, $arrValues);
		
		$message = str_replace("[BOARD]", "<a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$_POST['moveto']."'>".$boardObj->get_info_filtered("name")."</a>", $_POST['postredirect_desc']);
		$message = str_replace("[TOPIC_LINK]", "<a href='".$MAIN_ROOT."forum/viewtopic.php?tID=".$_GET['topicID']."'>".$postInfo['title']."</a>", $message);
		
		$message .= "\n\n\n<p class='tinyFont'><i>Moved by ".$member->getMemberLink()." on ".getPreciseTime(time(), "", true)."</i></p>";
		
		$arrColumns = array("member_id", "dateposted", "title", "message", "forumtopic_id");
		$arrValues = array($postInfo['member_id'], time(), "MOVED - ".$postInfo['title'], $message, $boardObj->objTopic->get_info("forumtopic_id"));
		$boardObj->objPost->addNew($arrColumns, $arrValues);
		$boardObj->objTopic->update(array("forumpost_id", "lastpost_id"), array($boardObj->objPost->get_info("forumpost_id"), $boardObj->objPost->get_info("forumpost_id")));
	}
	
	
	$member->logAction("Moved forum topic, <a href='".$MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id']."'>".$postInfo['title']."</a>, to <a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$_POST['moveto']."'>".$boardObj->get_info_filtered("name")."</a>");
	
}

$breadcrumbObj->clearBreadcrumb();
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", $MAIN_ROOT."forum");
$breadcrumbObj->addCrumb($boardInfo['name'], $MAIN_ROOT."forum/viewboard.php?bID=".$topicInfo['forumboard_id']);
$breadcrumbObj->addCrumb($postInfo['title'], $MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id']);
$breadcrumbObj->addCrumb("Move Topic");
$breadcrumb = $breadcrumbObj->getBreadcrumb();
?>

<script type='text/javascript'>

	$(document).ready(function() {
		var redirectClicked = 1;
		$('#postRedirect').click(function() {

			if(redirectClicked == 1) {
				$('#postRedirectSection').hide();
				redirectClicked = 0;
			}
			else {
				$('#postRedirectSection').show();
				redirectClicked = 1;
			}

		});


		$('#breadCrumb').html("<?php echo $breadcrumbObj->getBreadcrumb(); ?>");
		$('#consoleTopBackButton').attr('href', '<?php echo $MAIN_ROOT; ?>forum/viewtopic.php?tID=<?php echo $_GET['topicID']; ?>');
		$('#consoleBottomBackButton').attr('href', '<?php echo $MAIN_ROOT; ?>forum/viewtopic.php?tID=<?php echo $_GET['topicID']; ?>');
		
	});

</script>