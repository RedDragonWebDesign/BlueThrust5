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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage News");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."news WHERE newstype != '3' ORDER BY dateposted DESC");
	$checkHTMLConsoleObj = new ConsoleOption($mysqli);
	$htmlNewsCID = $checkHTMLConsoleObj->findConsoleIDByName("HTML in News Posts");
	$checkHTMLConsoleObj->select($htmlNewsCID);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
		
			$member->select($row['member_id']);
			$posterInfo = $member->get_info_filtered();
			
			if($posterInfo['avatar'] == "") {
				$posterInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
			}
			else {
				$posterInfo['avatar'] = $MAIN_ROOT.$posterInfo['avatar'];
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
				$checkHTMLAccess = $member->hasAccess($checkHTMLConsoleObj);
				$dispLastEditTime = getPreciseTime($row['lasteditdate']);
				$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";
			}
			
			$member->select($row['member_id']);
			
			if(!isset($checkHTMLAccess)) { $checkHTMLAccess = $member->hasAccess($checkHTMLConsoleObj); }
		
			
			$dispNews = ($checkHTMLAccess) ? parseBBCode($row['newspost']) : nl2br(parseBBCode(filterText($row['newspost'])));
		
			echo "
			
				<div class='newsDiv' id='newsDiv_".$row['news_id']."'>
					<div class='postInfo'>
						<div style='float: left'><img src='".$posterInfo['avatar']."' class='avatarImg'></div>
						<div style='float: left; margin-left: 15px'>posted by ".$member->getMemberLink()." - ".getPreciseTime($row['dateposted']).$dispNewsType."<br>
						<span class='subjectText'>".filterText($row['postsubject'])."</span></div>
						<div style='clear: both'></div>
					</div>
					<br>
					<div class='dottedLine' style='margin-top: 5px'></div>
					<div class='postMessage'>
						".utf8_decode($dispNews)."
					</div>
					<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
					<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
					<p style='padding: 0px; margin: 0px' align='right'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&newsID=".$row['news_id']."&action=edit'>EDIT</a> | <a href='javascript:void(0)' onclick=\"deleteNews('".$row['news_id']."')\">DELETE</a></b></p>
				</div>
			
			
			";
			
		}		
	}
	else {
		
		
		echo "
			<div class='shadedBox' style='width: 300px; margin-left: auto; margin-right: auto'>
				<p class='main' align='center'>
					<i>There are currently no news posts!</i>
				</p>
			</div>
		";
		
		
	}
	
	
	
	
}


?>