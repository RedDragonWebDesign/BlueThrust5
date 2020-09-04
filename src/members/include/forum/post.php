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



include_once("../classes/forumboard.php");

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

$cID = $_GET['cID'];
$forumAttachmentsCID = $consoleObj->findConsoleIDByName("Post Forum Attachments");

$consoleObj->select($forumAttachmentsCID);

$blnCheckForumAttachments = $member->hasAccess($consoleObj);
$consoleObj->select($cID);

if($blnCheckForumAttachments) {
	include_once($prevFolder."classes/download.php");
	include_once($prevFolder."classes/downloadcategory.php");
	$attachmentObj = new Download($mysqli);
	$downloadCatObj = new DownloadCategory($mysqli);
	$downloadCatObj->selectBySpecialKey("forumattachments");
	$forumAttachmentCatID = $downloadCatObj->get_info("downloadcategory_id");
}

$boardObj = new ForumBoard($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


if(!$boardObj->select($_GET['bID']) || ($boardObj->select($_GET['bID']) && !$boardObj->memberHasAccess($memberInfo))) {
	echo "<script type='text/javascript'>window.location = '".$MAIN_ROOT."members'</script>";
	exit();
}


$boardInfo = $boardObj->get_info_filtered();
$blnPostReply = false;
$addToForm = "";
if(isset($_GET['tID']) && $boardObj->objTopic->select($_GET['tID'])) {
	$blnPostReply = true;
	$topicInfo = $boardObj->objTopic->get_info();		
	
	// Check if topic is actually in the selected board
	if($topicInfo['forumboard_id'] != $boardInfo['forumboard_id']) {
		echo "<script type='text/javascript'>window.location = '".$MAIN_ROOT."members'</script>";
		exit();
	}
	elseif($topicInfo['lockstatus'] == 1) {

		echo "
			<div id='lockedMessage' style='display: none'>
				<p class='main' align='center'>
					This topic is locked!
				</p>
			</div>
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#lockedMessage').dialog({
						title: 'Post Reply - Locked!',
						show: 'scale',
						modal: true,
						width: 400,
						zIndex: 999999,
						resizable: false,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}
						},
						close: function(event, ui) {
							window.location = '".$MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id']."'						
						}
					
					});

				});
			</script>
		";
		
		exit();
		
	}
	
	
	$boardObj->objPost->select($topicInfo['forumpost_id']);
	$postInfo = $boardObj->objPost->get_info_filtered();
	
	$dispTopicName = "<tr><td colspan='2' class='largeFont'><b>".$postInfo['title']."</b><input type='hidden' id='postSubject' value='".$postInfo['title']."'><br><br></td></tr>";
	$arrTopicName = array(
		"type" => "custom",
		"sortorder" => 1,
		"html" => "<span class='largeFont'><b>".$postInfo['title']."</b></span><input type='hidden' id='postSubject' value='".$postInfo['title']."'>"	
	);
	
	
	$addToForm = "&tID=".$_GET['tID'];
	echo "
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#breadCrumbTitle').html(\"Post Reply\");
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."forum'>Forum</a> > <a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$_GET['bID']."'>".$boardInfo['name']."</a> > Post Reply\");
			$('#consoleTopBackButton').attr('href', '".$MAIN_ROOT."forum/viewtopic.php?bID=".$_GET['bID']."&tID=".$_GET['tID']."');
			$('#consoleBottomBackButton').attr('href', '".$MAIN_ROOT."forum/viewtopic.php?bID=".$_GET['bID']."&tID=".$_GET['tID']."');
			$('title').html(\"Post Reply - ".filterText($websiteInfo['clanname'])."\");
		});
	</script>
	";
	$postActionWord = "reply";
	
}
else {
	
	echo "
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."forum'>Forum</a> > <a href='".$MAIN_ROOT."forum/viewboard.php?bID=".$_GET['bID']."'>".$boardInfo['name']."</a> > Post Topic\");
			$('#consoleTopBackButton').attr('href', '".$MAIN_ROOT."forum/viewboard.php?bID=".$_GET['bID']."');
			$('#consoleBottomBackButton').attr('href', '".$MAIN_ROOT."forum/viewboard.php?bID=".$_GET['bID']."');
		});
	</script>
	";
	

	$arrTopicName = array(
		"type" => "text",
		"sortorder" => 1,
		"attributes" => array("class" => "formInput textBox"),
		"display_name" => "Topic",
		"db_name" => "title",
		"validate" => array("NOT_BLANK")
	);
	
	$postActionWord = "topic";
}


// Check Full Access
	
	$topicOrReply = (isset($_GET['tID'])) ? "Reply" : "Topic";

	if(!$boardObj->memberHasAccess($memberInfo, true)) {
		echo "
			<div id='lockedMessage' style='display: none'>
				<p class='main' align='center'>
					You don't have posting privileges on this board!
				</p>
			</div>
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#lockedMessage').dialog({
						title: 'Post ".$topicOrReply." - Error!',
						show: 'scale',
						modal: true,
						width: 400,
						zIndex: 999999,
						resizable: false,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}
						},
						close: function(event, ui) {
							window.location = '".$MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id']."'						
						}
					
					});
	
				});
			</script>
		";
		exit();
	}



$dispQuote = "";
if(isset($_GET['quote']) && $boardObj->objPost->select($_GET['quote'])) {
	$quotedInfo = $boardObj->objPost->get_info_filtered();
	$quotedMember = new Member($mysqli);
	$quotedMember->select($quotedInfo['member_id']);

	$dispQuote = "
	[quote]<a href='".$MAIN_ROOT."forum/viewtopic.php?tID=".$quotedInfo['forumtopic_id']."#".$quotedInfo['forumpost_id']."'>Originally posted by ".$quotedMember->get_info_filtered("username").":</a><br>".$boardObj->objPost->get_info("message")."<br>[/quote]";
}

$i=2;
$arrComponents = array(
	"topicname" => $arrTopicName,
	"message" => array(
		"type" => "richtextbox",
		"sortorder" => $i++,
		"display_name" => "Message",
		"attributes" => array("id" => "richTextarea", "style" => "width: 90%", "rows" => "10"),
		"value" => $dispQuote,
		"db_name" => "message",
		"validate" => array("NOT_BLANK")
	)
		
);


if($blnCheckForumAttachments) {
	
	$arrAttachmentComponents = array(
		"attachments" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => "Attachments",
			"html" => "<div class='formInput'><div id='attachmentsDiv' style='margin-bottom: 10px'>
							<input type='file' name='forumattachment_1' class='textBox' style='border: 0px'>
						</div>
						<a href='javascript:void(0)' id='addMoreAttachments'>Add More Attachments</a></div>
						<input type='hidden' id='numOfAttachments' value='1' name='numofattachments'>"
				
		)
			
	);
	
	
	
	$arrComponents = array_merge($arrComponents, $arrAttachmentComponents);
	
}


$arrPostButtons = array(
	"submit" => array(
		"type" => "submit",
		"sortorder" => $i++,
		"value" => "Post",
		"attributes" => array("class" => "formSubmitButton submitButton")		
	),
	"preview" => array(
		"type" => "button",
		"sortorder" => $i++,
		"attributes" => array("class" => "formSubmitButton submitButton", "id" => "btnPreview"),
		"value" => "Preview"
	),
	"preview_section" => array(
		"type" => "custom",
		"sortorder" => $i++,
		"html" => "<div id='loadingSpiral' class='loadingSpiral'>
						<p align='center'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
						</p>
					</div>
					<div id='previewPost'></div>"			
	)
);

$arrComponents = array_merge($arrComponents, $arrPostButtons);

$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"description" => "",
		"saveObject" => $boardObj->objPost,
		"saveMessage" => "Successfully posted new ".$postActionWord."!",
		"saveType" => "add",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID."&bID=".$_GET['bID'].$addToForm, "method" => "post", "enctype" => "multipart/form-data"),
		"afterSave" => array("saveAdditionalPostData"),
		"saveAdditional" => array("member_id" => $memberInfo['member_id'], "dateposted" => time())
);



echo "
	<script type='text/javascript'>
	
	
		$(document).ready(function() {
		
			var numOfAttachments = 1;
			$('#addMoreAttachments').click(function() {
				numOfAttachments++;
				
				if(numOfAttachments <= ".ini_get("max_file_uploads").") {
	
					$('#attachmentsDiv').append(\"<br><input type='file' name='forumattachment_\"+numOfAttachments+\"' class='textBox' style='border: 0px'>\");
					$('#numOfAttachments').val(numOfAttachments);
				
				}
				else {
					$('#addMoreAttachments').html('Maximum number of attachments reached!');		
				}
				
				$('#testattachments').html($('#attachmentsDiv').html());
				
			});
		
		
			$('#btnPreview').click(function() {
					
				$('#loadingSpiral').show();
				$.post('".$MAIN_ROOT."members/include/forum/include/previewpost.php', { wysiwygHTML: $('#richTextarea').val(), previewSubject: $('#postSubject').val() }, function(data) {
					$('#previewPost').hide();
					$('#previewPost').html(data);
					$('#loadingSpiral').hide();
					$('#previewPost').fadeIn(250);
				
					$('html, body').animate({
						scrollTop:$('#previewPost').offset().top
					}, 1000);
					
				});
			
			});
			
		});
	</script>
";


function saveAdditionalPostData() {
	global $formObj, $blnPostReply, $boardObj, $mysqli, $topicInfo;
	
	if(!$blnPostReply) {
		// New Topic
		$postInfo = $boardObj->objPost->get_info();
		$arrColumns = array("forumboard_id", "forumpost_id", "lastpost_id");
		$arrValues = array($_GET['bID'], $postInfo['forumpost_id'], $postInfo['forumpost_id']);
		$boardObj->objTopic->addNew($arrColumns, $arrValues);
		
		$boardObj->objPost->update(array("forumtopic_id"), array($boardObj->objTopic->get_info("forumtopic_id")));		
	}
	else {
		$boardObj->objPost->update(array("forumtopic_id"), array($topicInfo['forumtopic_id']));
		$newReplies = $topicInfo['replies']+1;
		$boardObj->objTopic->update(array("replies", "lastpost_id"), array($newReplies, $boardObj->objPost->get_info("forumpost_id")));
		
	}
	
	
	$formObj->saveLink = $boardObj->objPost->getLink();
	
	$arrDownloadID = checkForAttachments();
	if(is_array($arrDownloadID)) {
		$forumAttachmentObj = new Basic($mysqli, "forum_attachments", "forumattachment_id");
		foreach($arrDownloadID as $downloadID) {
			$forumAttachmentObj->addNew(array("download_id", "forumpost_id"), array($downloadID, $boardObj->objPost->get_info("forumpost_id")));
		}	
		
	}
	
	
}

function checkForAttachments() {
	global $formObj, $mysqli, $blnCheckForumAttachments, $prevFolder;
	
	$returnVal = false;
	if($blnCheckForumAttachments) {
		$attachmentObj = new Download($mysqli);
		$downloadCatObj = new DownloadCategory($mysqli);
		$downloadCatObj->selectBySpecialKey("forumattachments");
		$forumAttachmentCatID = $downloadCatObj->get_info("downloadcategory_id");
		

		$arrDownloadID = array();
		$arrDLColumns = array("downloadcategory_id", "member_id", "dateuploaded", "filename", "mimetype", "filesize", "splitfile1", "splitfile2");
		for($i=1;$i<=$_POST['numofattachments'];$i++) {
			
			$tempPostName = "forumattachment_".$i;
			if($_FILES[$tempPostName]['name'] != "" && $attachmentObj->uploadFile($_FILES[$tempPostName], $prevFolder."downloads/files/forumattachment/", $forumAttachmentCatID)) {

				$splitFiles = $attachmentObj->getSplitNames();
				$fileSize = $attachmentObj->getFileSize();
				$mimeType = $attachmentObj->getMIMEType();
				
				$arrDLValues = array($forumAttachmentCatID, $memberInfo['member_id'], time(), $_FILES[$tempPostName]['name'], $mimeType, $fileSize, "downloads/files/forumattachment/".$splitFiles[0], "downloads/files/forumattachment/".$splitFiles[1]);
				
				if($attachmentObj->addNew($arrDLColumns, $arrDLValues)) {
					$arrDownloadID[] = $attachmentObj->get_info("download_id");
				}	
			}
			elseif($_FILES[$tempPostName]['name'] != "") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload attachment #".$i.": ".$_FILES[$tempPostName]['name'].".<br>";
			}	
			
		}
		$returnVal = $arrDownloadID;

	}

	return $returnVal;
}

?>