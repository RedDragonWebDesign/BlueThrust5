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


function dispIAMessages($iaID) {
	global $MAIN_ROOT, $dbprefix, $mysqli;
	
	$iaMember = new Member($mysqli);
	$counter = 0;
	$iaMessages = "";
	$iaMessagesQuery = $mysqli->query("SELECT * FROM ".$dbprefix."iarequest_messages WHERE iarequest_id = '".$iaID."' ORDER BY messagedate DESC");
	while($iaMessageRow = $iaMessagesQuery->fetch_assoc()) {
		if($counter == 1) {
			$addCSS = "";
			$counter = 0;
		}
		else {
			$addCSS = " alternateBGColor";		
			$counter = 1;
		}
		
		$iaMember->select($iaMessageRow['member_id']);
		$iaMessages .= "
			<div class='dottedLine".$addCSS."' style='padding: 10px 5px; margin-left: auto; margin-right: auto; width: 80%;'>
				".$iaMember->getMemberLink()." - ".getPreciseTime($iaMessageRow['messagedate'])."<br><br>
				<div style='padding-left: 5px'>".nl2br(filterText($iaMessageRow['message']))."</div>
			</div>
		";
	}

	if($iaMessagesQuery->num_rows == 0) {
		$iaMessages = "<i>No Messages</i>";	
	}
	
	return $iaMessages;
}



if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {

	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");

	$consoleObj = new ConsoleOption($mysqli);
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$cID = $consoleObj->findConsoleIDByName("View Inactive Requests");
	$consoleObj->select($cID);


	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {

		exit();

	}

	$memberInfo = $member->get_info_filtered();
	$iaRequestObj = new Basic($mysqli, "iarequest", "iarequest_id");
	$checkRequestID = $iaRequestObj->select($_POST['iaRequestID']);
	if($_POST['action'] == "postmessage" && trim($_POST['message']) != "" && $checkRequestID) {
		
		$iaRequestMessageObj = new Basic($mysqli, "iarequest_messages", "iamessage_id");
		
		$arrColumns = array("iarequest_id", "member_id", "messagedate", "message");
		$arrValues = array($iaRequestObj->get_info("iarequest_id"), $memberInfo['member_id'], time(), $_POST['message']);
		
		$iaRequestMessageObj->addNew($arrColumns, $arrValues);
		
		echo dispIAMessages($iaRequestObj->get_info("iarequest_id"));
		
		$requestIACID = $consoleObj->findConsoleIDByName("Inactive Request");
		$member->select($iaRequestObj->get_info("member_id"));
		$member->postNotification("A new message was posted on your inactive request!<br><br><a href='".$MAIN_ROOT."members/console.php?cID=".$requestIACID."'>View Messages</a>");
		
		exit();
	}
	elseif(($_POST['action'] == "approve" || $_POST['action'] == "deny")  && $checkRequestID) {

		$requestStatus = ($_POST['action'] == "approve") ? 1 : 2;
		
		$iaRequestObj->update(array("reviewer_id", "reviewdate", "requeststatus"), array($memberInfo['member_id'], time(), $requestStatus));
		
		if($requestStatus == 1) {
			$member->select($iaRequestObj->get_info("member_id"));
			$member->update(array("onia", "inactivedate"), array(1, time()));
			$member->postNotification("Your inactive request was approved!");
		}
		else {
			$member->select($iaRequestObj->get_info("member_id"));
			$member->update(array("onia", "inactivedate"), array(0, 0));
			$member->postNotification("Your inactive request was denied!");
		}
		
		$member->select($memberInfo['member_id']);
	}
	elseif($_POST['action'] == "delete" && $checkRequestID) {
		
		$member->select($iaRequestObj->get_info("member_id"));
		$dispIAMemberName = $member->getMemberLink();
		$iaRequestObj->delete();
		$member->postNotification("Your inactive request was deleted!");
		
		$member->select($memberInfo['member_id']);
		
		$member->logAction("Deleted ".$dispIAMemberName."'s IA Request.");
		
	}
	
	
}

$iaMember = new Member($mysqli);

$result = $mysqli->query("SELECT * FROM ".$dbprefix."iarequest ORDER BY requestdate DESC");
	while($row = $result->fetch_assoc()) {
		
		
		$iaMessages = dispIAMessages($row['iarequest_id']);
		
		$iaMember->select($row['member_id']);
		
		if(trim($row['reason']) == "") {
			$row['reason'] = "None";	
		}
		
		$dispActions = "";
		if($row['requeststatus'] == 0) {
			$dispActions = "<a href='javascript:void(0)' id='iaRequestAction' data-iarequest='".$row['iarequest_id']."' data-action='approve'>Approve</a> - <a href='javascript:void(0)' id='iaRequestAction' data-iarequest='".$row['iarequest_id']."' data-action='deny'>Deny</a> - ";
		}
		
		$dispActions .= "<a href='javascript:void(0)' id='iaRequestAction' data-iarequest='".$row['iarequest_id']."' data-action='delete'>Delete</a>";
		
		
		$dispRequestStatus = "<span class='pendingFont'>Pending</span>";
		if($row['requeststatus'] == 1) {
			$member->select($row['reviewer_id']);
			$dispRequestStatus = "<span class='allowText'>Approved</span> by ".$member->getMemberLink()." - ".getPreciseTime($row['reviewdate']);
			$member->select($memberInfo['member_id']);
		}
		elseif($row['requeststatus'] == 2) {
			$member->select($row['reviewer_id']);
			$dispRequestStatus = "<span class='denyText'>Denied</span> by ".$member->getMemberLink()." - ".getPreciseTime($row['reviewdate']);
			$member->select($memberInfo['member_id']);
		}
		
		echo "

			<div class='dottedBox' style='margin: 20px auto; width: 90%'>
				<table class='formTable' style='width: 95%'>
					<tr>
						<td class='formLabel'>Request Date:</td>
						<td class='main'>".getPreciseTime($row['requestdate'])."</td>
					</tr>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'>".$iaMember->getMemberLink()."</td>
					</tr>
					<tr>
						<td class='formLabel'>Status:</td>
						<td class='main'>".$dispRequestStatus."</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Reason:</td>
						<td class='main' valign='top'>".filterText($row['reason'])."</td>
					</tr>
					<tr>
						<td class='main' colspan='2'><br><div class='dottedLine' style='padding-bottom: 3px'><b>Messages:</b></div></td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
							<div id='messagesloadingSpiral_".$row['iarequest_id']."' style='display: none'>
								<p align='center' class='main'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>
						
							<div id='iaMessagesDiv_".$row['iarequest_id']."'>".$iaMessages."</div><br>
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Leave Message:</td>
						<td class='main' valign='top'><textarea style='width: 50%; height: 90px' id='iaMessageText_".$row['iarequest_id']."' class='textBox'></textarea><br><input style='margin-top: 10px' id='postMessage' data-iarequest='".$row['iarequest_id']."' type='button' class='submitButton' value='Post'></td>
					</tr>
					<tr>
						<td colspan='2' align='center' class='main'><br><br>
							<b>".$dispActions."</b>
						</td>
				</table>
			</div>
			<br>
		";
		
	}
	
	if($result->num_rows == 0) {

		echo "
			<div class='shadedBox' style='width: 50%; margin: 20px auto;'>
				<p class='main' align='center'>
					No Inactive Requests!
				</p>
			</div>
		";
	}
	else {
		
		echo "

			<script type='text/javascript'>
				$(document).ready(function() {
					
					$(\"input[id='postMessage']\").click(function() {

						var txtMessageID = '#iaMessageText_'+$(this).attr('data-iarequest');
						var txtLoadingID = '#messagesloadingSpiral_'+$(this).attr('data-iarequest');
						var messageDivID = '#iaMessagesDiv_'+$(this).attr('data-iarequest');
						
						$(txtLoadingID).show();
						$(messageDivID).fadeOut(250);
						
						$.post('".$MAIN_ROOT."members/include/membermanagement/include/inactiverequestlist.php', { action: 'postmessage', iaRequestID: $(this).attr('data-iarequest'), message: $(txtMessageID).val() }, function(data) {
						
							$(messageDivID).html(data);
							$(txtLoadingID).hide();
							$(messageDivID).fadeIn(250);
							$(txtMessageID).val('');
						
						});
						
					});
					
					
				
					$(\"a[id='iaRequestAction']\").click(function() {

					$('#loadingSpiral').show();
					$('#iaRequestList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/membermanagement/include/inactiverequestlist.php', { action: $(this).attr('data-action'), iaRequestID: $(this).attr('data-iarequest') }, function(data) {
					
						$('#iaRequestList').html(data);
						$('#loadingSpiral').hide();
						
						$('#iaRequestList').fadeIn(250);
					
					});
				
			});
				
				});
			</script>
		
		";
		
	}
	
?>