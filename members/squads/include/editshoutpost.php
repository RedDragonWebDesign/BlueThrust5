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

$pID = "manageshoutbox";



// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");
	if($squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], "manageshoutbox") && $squadNewsObj->select($_POST['nID'])) {

		
		
		
		
		if($_POST['submit']) {
			
			// Check Message
			
			if(trim($_POST['message']) == "") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not make a blank shoutbox post.<br>";
			}
			
			if($countErrors == 0) {
				$time = time();
				$arrColumns = array("newspost", "lasteditmember_id", "lasteditdate");
				$arrValues = array($_POST['message'], $memberInfo['member_id'], $time);
			
				
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
				<strong>Unable to edit shoutbox post because the following errors occurred:</strong><br><br>
				$dispError
				</div>
				";
			}
			
			
			
			echo "
			
					<table class='formTable'>
						<tr>
							<td class='formLabel' valign='top'>Message:</td>
							<td class='main'>
								<textarea rows='10' cols='55' class='textBox' id='message_".$squadNewsInfo['squadnews_id']."'>".$squadNewsInfo['newspost']."</textarea>
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
			
			";
		}
		
		
		
		if($_POST['cancel']) {
			$squadNewsInfo = $squadNewsObj->get_info_filtered();
		
			
			$member->select($squadNewsInfo['member_id']);
			$squadMemberInfo = $member->get_info_filtered();
		
			if($squadMemberInfo['avatar'] == "") {
				$squadMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
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
			posted by ".$member->getMemberLink()." - ".getPreciseTime($squadNewsInfo['dateposted'])."
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
