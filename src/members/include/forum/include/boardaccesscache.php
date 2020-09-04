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


include("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/forumboard.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$accessMemberObj = new Member($mysqli);
$rankObj = new Rank($mysqli);

$boardObj = new ForumBoard($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	/*
	 * 0 - No Access
	 * 1 - Full Access
	 * 2 - Read Only
	 */
	
	
	if(isset($_POST['action']) &&  $accessMemberObj->select($_POST['mID'])) {
		if($_POST['action'] == "add" && ($_POST['accessRule'] == "1" || $_POST['accessRule'] == "0" || $_POST['accessRule'] == "2")) {
			$_SESSION['btMemberAccessCache'][$_POST['mID']] = $_POST['accessRule'];
		}
		elseif($_POST['action'] == "delete") {
			$_SESSION['btMemberAccessCache'][$_POST['mID']] = "";
		}
	}
	
	
	// Display Cache List
	
	echo "
	
		<table align='left' border='0' cellspacing='2' cellpadding='2' width=\"90%\">
			<tr>
				<td class='formTitle' width=\"60%\">Member:</td>
				<td class='formTitle' width=\"20%\">Access:</td>
				<td class='formTitle' width=\"20%\">Actions:</td>
			</tr>
			
			";
	
	$countRules = 0;
	foreach($_SESSION['btMemberAccessCache'] as $memID => $accessRule) {
		if($accessRule != "" &&  $accessMemberObj->select($memID)) {
			$tempMemInfo = $accessMemberObj->get_info_filtered();
			$rankObj->select($tempMemInfo['rank_id']);
			
			$dispRankName = $rankObj->get_info_filtered("name");
			
			$dispAccess = "<span class='denyText'>Deny</span>";
			if($accessRule == 1) {
				$dispAccess = "<span class='pendingFont'>Full</span>";
			}
			elseif($accessRule == 2) {
				$dispAccess = "<span class='allowText'>Read-Only</span>";
			}

			
			echo "
				<tr>
					<td class='main'><a href='".$MAIN_ROOT."profile.php?mID=".$tempMemInfo['username']."'>".$dispRankName." ".$tempMemInfo['username']."</a></td>
					<td class='main' align='center'>".$dispAccess."</td>
					<td class='main' align='center'><a href='javascript:void(0)' onclick=\"deleteAccessRule('".$memID."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete'></a></td>
				</tr>			
			";
			
			
			$countRules++;
		}
	}
	
	
	if($countRules == 0) {

		echo "
			<tr>
				<td class='main' colspan='3'>
					<p align='center' style='padding-top: 10px'><i>No special member access rules set!</i></p>
				</td>
			</tr>		
		";
	}
	
	echo "
			
		</table>
	
	
	";
	

}

?>