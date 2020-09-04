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

include_once("../../_setup.php");
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/rankcategory.php");
include_once("../../classes/squad.php");
include_once("../../classes/tournament.php");
include_once("../../classes/privatemessage.php");
include_once("../../classes/pmfolder.php");


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
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$prevFolder = "../../";
$PAGE_NAME = "View Message - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > View Message";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

include("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>View Message</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";

$pmObj = new PrivateMessage($mysqli);
$multiMemPMObj = $pmObj->multiMemPMObj;


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $pmObj->select($_GET['pmID'])) {

	$memberInfo = $member->get_info_filtered();
	
	$pmInfo = $pmObj->get_info_filtered();
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."privatemessage_members WHERE pm_id = '".$pmInfo['pm_id']."' AND member_id = '".$memberInfo['member_id']."'");
	
	$senderResult = $mysqli->query("SELECT * FROM ".$dbprefix."privatemessage_members WHERE pm_id = '".$pmInfo['pm_id']."'");
	
	$blnMultiPM = false;
	
	
	if($pmInfo['receiver_id'] == $memberInfo['member_id'] || $pmInfo['sender_id'] == $memberInfo['member_id'] || $result->num_rows > 0) {
		$member->select($pmInfo['sender_id']);
		$dispFromMember = $member->getMemberLink();
		
		if(($memberInfo['member_id'] == $pmInfo['receiver_id']) || ($memberInfo['member_id'] == $pmInfo['sender_id'] && $senderResult->num_rows == 0)) {
			$member->select($pmInfo['receiver_id']);
			$dispToMember = $member->getMemberLink();
			$pmObj->update(array("status"), array(1));	
		}
		elseif($result->num_rows > 0) {
			
			$row = $result->fetch_assoc();
			$pmMemberID = $row['pmmember_id'];
			$multiMemPMObj->select($pmMemberID);
			$multiMemPMObj->update(array("seenstatus"), array(1));
			$blnMultiPM = true;
			$dispToMember = $pmObj->getRecipients(true);
			
		}
		elseif($memberInfo['member_id'] == $pmInfo['sender_id'] && $senderResult->num_rows > 0) {
			// Member is the sender			
			$blnMultiPM = true;
			$dispToMember = $pmObj->getRecipients(true);
		}
		
		$dispPreviousMessages = "";
		
		
		
		// Folder Info
		$multiPM = isset($_GET['pmMID']);

		$pmFolderID = $pmObj->getFolder($memberInfo['member_id'], $multiPM);
		$pmFolderObj = new PMFolder($mysqli);
		$pmFolderObj->select($pmFolderID);
		$pmFolderInfo = $pmFolderObj->get_info_filtered();
		
		
		if($pmInfo['originalpm_id'] != 0) {
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."privatemessages WHERE originalpm_id = '".$pmInfo['originalpm_id']."' AND pm_id != '".$pmInfo['pm_id']."' ORDER BY datesent DESC");
			$oldPMObj = new PrivateMessage($mysqli);
	
				
			$dispPreviousMessages .= "
				<tr>
					<td class='main' colspan='2'><br><br>
						<b>Previous Messages:</b>
						<div class='dottedLine' style='width: 90%; padding-top: 5px'></div><br>
					</td>
				</tr>
			";
			
			
			
			
			
			while($row = $result->fetch_assoc()) {
				$oldPMObj->select($row['pm_id']);
				
				if($row['receiver_id'] != 0) {
				
					$member->select($row['receiver_id']);
					$dispToPrevMember = $member->getMemberLink();
				}
				else {
					
					$dispToPrevMember = $oldPMObj->getRecipients(true);
					$pmObj->select($row['pm_id']);
					$arrReceivers = $pmObj->getAssociateIDs();
					
					
				}
				
				$member->select($row['sender_id']);
				$dispFromPrevMember = $member->getMemberLink();
				
				
				$dispPreviousMessages .= "
				
					<tr>
						<td class='formLabel'>To:</td>
						<td class='main'>
							".$dispToPrevMember."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Date:</td>
						<td class='main'>
							".getPreciseTime($row['datesent'])."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>From:</td>
						<td class='main'>
							".$dispFromPrevMember."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Subject:</td>
						<td class='main'>".$row['subject']."</td>
					</tr>
					<tr>
						<td colspan='2'><br></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Message:</td>
						<td class='main'>
							<div style='position: relative; word-wrap:break-word'>
								".nl2br(parseBBCode($row['message']))."
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'>
							<div class='dottedLine' style='width: 90%; margin-top: 30px; margin-bottom: 30px'></div>
						</td>
					</tr>
				";
				
			}
		
		}
		
		
		
		if($pmInfo['originalpm_id'] == 0) {
			$replyID = $pmInfo['pm_id'];
			$threadID = $pmInfo['pm_id'];
		}
		else {
			$replyID = $pmInfo['pm_id'];
			$threadID = $pmInfo['originalpm_id'];
			
			$pmObj->select($threadID);
			
			$originalPMInfo = $pmObj->get_info_filtered();
			$member->select($originalPMInfo['receiver_id']);
			$oldPMObj->select($originalPMInfo['pm_id']);
			$dispToPrevMember = ($originalPMInfo['receiver_id'] != 0) ? $member->getMemberLink() : $oldPMObj->getRecipients(true);
			
			$member->select($originalPMInfo['sender_id']);
			$dispFromPrevMember = $member->getMemberLink();
			
			$dispPreviousMessages .= "
			
			
				<tr>
					<td class='formLabel'>To:</td>
					<td class='main'>
						".$dispToPrevMember."
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Date:</td>
					<td class='main'>
						".getPreciseTime($originalPMInfo['datesent'])."
					</td>
				</tr>
				<tr>
					<td class='formLabel'>From:</td>
					<td class='main'>
						".$dispFromPrevMember."
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Subject:</td>
					<td class='main'>".$originalPMInfo['subject']."</td>
				</tr>
				<tr>
					<td colspan='2'><br></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Message:</td>
					<td class='main'>
					<div style='position: relative; word-wrap:break-word'>
						".nl2br(parseBBCode($originalPMInfo['message']))."
					</div>
					</td>
				</tr>
				<tr>
					<td colspan='2' class='main'>
						<div class='dottedLine' style='width: 90%; margin-top: 30px; margin-bottom: 30px'></div>
					</td>
				</tr>
			
			
			";
			
			
			
			
		}
		
		
		echo "
		
			<div class='formDiv'>
				<p style='padding: 0px; margin: 0px; padding-right: 20px; padding-top: 10px' class='main' align='right'>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&folder=".$pmFolderID."'>Return to ".$pmFolderInfo['name']."</a>
				</p>
			
				<table class='formTable' style='margin-top: 0px; table-layout: fixed'>
					<tr>
						<td class='formLabel'>To:</td>
						<td class='main'>
							".$dispToMember."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Date:</td>
						<td class='main'>
							".getPreciseTime($pmInfo['datesent'])."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>From:</td>
						<td class='main'>
							".$dispFromMember."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Subject:</td>
						<td class='main'>".$pmInfo['subject']."</td>
					</tr>
					<tr>
						<td colspan='2'><br></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Message:</td>
						<td class='main'>
						<div style='position: relative; word-wrap:break-word'>
							".nl2br(parseBBCode($pmInfo['message']))."
						</div>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<div class='dottedLine' style='width: 75%'></div><br>
							<input type='button' id='replyButton' class='submitButton' value='Reply'>
							";
		
						if($blnMultiPM) {
							
							echo "<input type='button' id='replyAllButton' class='submitButton' style='margin-left: 20px' value='Reply All'>";
							
						}
		
		echo "
						</td>
					</tr>
					".$dispPreviousMessages."					
				</table>
				
			</div>
		
			";
		
		$member->select($memberInfo['member_id']);
		$totalPMs = $member->countPMs();
		$totalNewPMs = $member->countPMs(true);
		

		if($totalNewPMs > 0) {
			$dispPMCount = "PM Inbox <b>(".$totalNewPMs.")</b> <img src='".$MAIN_ROOT."themes/".$THEME."/images/pmalert.gif'>";
			$intPMCount = $totalNewPMs;
		}
		else {
			$dispPMCount = "PM Inbox (".$totalPMs.")";
			$intPMCount = $totalPMs;
		}
		
		echo "
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#replyButton').click(function() {
						window.location = '".$MAIN_ROOT."members/privatemessages/compose.php?replyID=".$replyID."&threadID=".$threadID."';
					});
					
					
					$('#replyAllButton').click(function() {
						window.location = '".$MAIN_ROOT."members/privatemessages/compose.php?replyID=".$replyID."&threadID=".$threadID."&replyall=1';
					});
					
					
					$('#pmLoggedInLink').html(\"".$dispPMCount."\");
					
				});
			
			</script>
			
		";
		
	
	
	}
	else {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
	}
	
}
else {

	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");

}



include("../../themes/".$THEME."/_footer.php");

?>