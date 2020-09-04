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
include_once("../../../classes/squad.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$squadObj = new Squad($mysqli);
$arrSquadPrivileges = $squadObj->arrSquadPrivileges;

$pID = "managenews";



// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");
	if($squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], "managenews") && $squadNewsObj->select($_POST['nID'])) {

		
		
		
		
		if($_POST['submit']) {
		
			
			// Check News Type
			//	1 - Public
			// 	2 - Private
			
			if($_POST['newstype'] != 1 && $_POST['newstype'] != 2) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid news type.<br>";
			}
			
			
			// Check Subject
			
			if(trim($_POST['subject']) == "") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a news subject.<br>";
			}
			
			// Check Message
			
			if(trim($_POST['message']) == "") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not make a blank news post.<br>";
			}
			
			if($countErrors == 0) {
				$time = time();
				$arrColumns = array("newstype", "postsubject", "newspost", "lasteditmember_id", "lasteditdate");
				$arrValues = array($_POST['newstype'], $_POST['subject'], $_POST['message'], $memberInfo['member_id'], $time);
			
				
				if($squadNewsObj->update($arrColumns, $arrValues)) {
			
					$_POST['cancel'] = true;
			
				}
				else {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
				}
			
			
			}
			
			if($countErrors > 0) {
				$_POST = filterArray($_POST);
				$_POST['submit'] = false;
			}
			
			
			
		
		}
		
		
		
		
		
		
		if(!$_POST['submit'] && !$_POST['cancel']) {
			$squadNewsInfo = $squadNewsObj->get_info_filtered();
			
			if($dispError != "") {
				echo "
				<div class='errorDiv'>
				<strong>Unable to edit squad news because the following errors occurred:</strong><br><br>
				$dispError
				</div>
				";
			}
			
			
			
			echo "
			
					<table class='formTable'>
						<tr>
							<td class='formLabel'>News Type:</td>
							<td class='main'><select class='textBox' id='newsType_".$squadNewsInfo['squadnews_id']."' onchange='updateTypeDesc".$squadNewsInfo['squadnews_id']."()'><option value='1'>Public</option><option value='2' ".$privateSelected.">Private</option></select><span class='tinyFont' style='padding-left: 10px' id='typeDesc_".$squadNewsInfo['squadnews_id']."'></span></td>
						</tr>
						<tr>
							<td class='formLabel'>Subject:</td>
							<td class='main'><input type='text' id='subject_".$squadNewsInfo['squadnews_id']."' value='".$squadNewsInfo['postsubject']."' class='textBox' style='width: 250px'></td>
						</tr>
						<tr>
							<td class='formLabel' valign='top'>Message:</td>
							<td class='main'>
								<textarea rows='10' cols='50' class='textBox' id='message_".$squadNewsInfo['squadnews_id']."'>".$squadNewsInfo['newspost']."</textarea>
							</td>
						</tr>
						<tr>
							<td class='main' align='center' colspan='2'><br><br>
								<input type='button' onclick=\"saveNewsPost('".$squadNewsInfo['squad_id']."', '".$squadNewsInfo['squadnews_id']."')\" value='Save' class='submitButton' style='width: 90px'><br><br>
								<a href='javascript:void(0)' onclick=\"cancelEdit('".$squadNewsInfo['squad_id']."', '".$squadNewsInfo['squadnews_id']."')\">Cancel</a>
							</td>
						</tr>
					</table>
			</form>
			
			<script type='text/javascript'>
				function updateTypeDesc".$squadNewsInfo['squadnews_id']."() {
					$(document).ready(function() {
						$('#typeDesc').hide();
						if($('#newsType_".$squadNewsInfo['squadnews_id']."').val() == \"1\") {
							$('#typeDesc_".$squadNewsInfo['squadnews_id']."').html('<i>Share this news for the world to see!</i>');
						}
						else {
							$('#typeDesc_".$squadNewsInfo['squadnews_id']."').html('<i>Only show this post to squad members!</i>');
						}
						$('#typeDesc_".$squadNewsInfo['squadnews_id']."').fadeIn(250);
					
					});
				}
				
				
				
				updateTypeDesc".$squadNewsInfo['squadnews_id']."();
			</script>
			
			";
		}
		
		
		
		if($_POST['cancel']) {
			$squadNewsInfo = $squadNewsObj->get_info_filtered();
		
			
			$member->select($squadNewsInfo['member_id']);
			$squadMemberInfo = $member->get_info_filtered();
		
			if($squadMemberInfo['avatar'] == "") {
				$squadMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
			}
		
			if($squadNewsInfo['newstype'] == 1) {
				$dispNewsType = "<span class='publicNewsColor' style='font-style: italic'>public</span>";
			}
			else {
				$dispNewsType = "<span class='privateNewsColor' style='font-style: italic'>private</span>";
			}
			
			$dispLastEdit = "";
			if($member->select($squadNewsInfo['lasteditmember_id'])) {
				$dispLastEditTime = getPreciseTime($squadNewsInfo['lasteditdate']);
				$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";	
			}
			
			$member->select($squadNewsInfo['member_id']);
			
			echo "
			<img src='".$squadMemberInfo['avatar']."' class='avatarImg'>
			<div class='postInfo'>
			posted by ".$member->getMemberLink()." - ".getPreciseTime($squadNewsInfo['dateposted'])." - ".$dispNewsType."<br>
			<span class='subjectText'>".filterText($squadNewsInfo['postsubject'])."</span>
			</div>
			<br>
			<div class='dottedLine' style='margin-top: 5px'></div>
			<div class='postMessage'>
			".nl2br(parseBBCode(filterText($squadNewsInfo['newspost'])))."
			</div>
			<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
			<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
			<p style='padding: 0px; margin: 0px' align='right'><b><a href='javascript:void(0)' onclick=\"editNews('".$squadNewsInfo['squad_id']."', '".$squadNewsInfo['squadnews_id']."')\">EDIT</a> | <a href='javascript:void(0)' onclick=\"deleteNews('".$squadNewsInfo['squad_id']."', '".$squadNewsInfo['squadnews_id']."')\">DELETE</a></b></p>
			";
		
		
		}
		
		
		
		
		
		

	}
	
	
}

?>
