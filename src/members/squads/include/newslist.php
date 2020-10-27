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

$pID = strtolower($_POST['pID']);
$counter = 0;


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	
	if($squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], $pID)) {
		
		if(!$_POST['filterShoutbox']) {
			$filterNewsType = "(newstype = '1' OR newstype = '2')";	
		}
		else {
			$filterNewsType = "newstype = '3'";	
		}
		
		
		$squadInfo = $squadObj->get_info_filtered();
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."squadnews WHERE squad_id = '".$squadInfo['squad_id']."' AND ".$filterNewsType." ORDER BY dateposted DESC");
		
		while($row = $result->fetch_assoc()) {
			$member->select($row['member_id']);
			$squadMemberInfo = $member->get_info_filtered();
			
			if($squadMemberInfo['avatar'] == "") {
				$squadMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
			}
			
			if($row['newstype'] == 1) {
				$dispNewsType = " - <span class='publicNewsColor' style='font-style: italic'>public</span>";
			}
			elseif($row['newstype'] == 2) {
				$dispNewsType = " - <span class='privateNewsColor' style='font-style: italic'>private</span>";
			}
			elseif($row['newstype'] == 3) {
				$dispNewsType = "";
			}
			
			$dispLastEdit = "";
			if($member->select($row['lasteditmember_id'])) {

				$dispLastEditTime = getPreciseTime($row['lasteditdate']);
				$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";
			}
			
			$member->select($row['member_id']);
			echo "
				<div class='newsDiv' id='newsDiv_".$row['squadnews_id']."'>
					<img src='".$squadMemberInfo['avatar']."' class='avatarImg'>
					<div class='postInfo'>
						posted by ".$member->getMemberLink()." - ".getPreciseTime($row['dateposted']).$dispNewsType."<br>
						<span class='subjectText'>".filterText($row['postsubject'])."</span>
					</div>
					<br>
					<div class='dottedLine' style='margin-top: 5px'></div>
					<div class='postMessage'>
						".nl2br(parseBBCode(filterText($row['newspost'])))."
					</div>
					<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
					<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
					<p style='padding: 0px; margin: 0px' align='right'><b><a href='javascript:void(0)' onclick=\"editNews('".$row['squad_id']."', '".$row['squadnews_id']."')\">EDIT</a> | <a href='javascript:void(0)' onclick=\"deleteNews('".$row['squad_id']."', '".$row['squadnews_id']."')\">DELETE</a></b></p>
				</div>
			";
			
			
			$counter++;
			
		}
		
		if($counter == 0) {
			echo "
				<div class='shadedBox' style='width: 300px; font-style: italic; margin-left: auto; margin-right: auto; margin-bottom: 20px'>
					<p class='main' align='center'>
						There are currently no news posts!
					</p>
				</div>
			";
		}
		

		
	}

	
	
}