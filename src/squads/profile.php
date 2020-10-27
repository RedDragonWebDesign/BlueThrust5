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

// Classes needed for profile.php

include_once($prevFolder."classes/squad.php");
include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/shoutbox.php");
include_once($prevFolder."classes/consoleoption.php");


$squadObj = new Squad($mysqli);
$consoleObj = new ConsoleOption($mysqli);

if(!isset($member)) {
	$member = new Member($mysqli);
	
	if(isset($_SESSION['btUsername']) AND isset($_SESSION['btPassword']) && $member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {

		$memberInfo = $member->get_info_filtered();
			
	}
}

if(!$squadObj->select($_GET['sID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
}
else {
	$squadInfo = $squadObj->get_info_filtered();	
}

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
$PAGE_NAME = $squadInfo['name']." - ";
include($prevFolder."themes/".$THEME."/_header.php");

$member->select($squadInfo['member_id']);
$dispFounderLink = $member->getMemberLink();


$dispRecruitingStatus = "<span class='successFont'>Open</span>";
if($squadInfo['recruitingstatus'] == 0) {
	$dispRecruitingStatus = "<span class='failedFont'>Closed</span>";
}



$squadMemberList = $squadObj->getMemberList();

$arrPublicNews = $squadObj->getNewsPostList(1);
$arrPrivateNews = array();

if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {
	
	$member->select($_SESSION['btUsername']);
	if($member->authorizeLogin($_SESSION['btPassword'])) {
		$memberInfo = $member->get_info_filtered();
		
		if(in_array($memberInfo['member_id'], $squadMemberList)) {
			
			$arrPrivateNews = $squadObj->getNewsPostList(2);
			
		}
		
	}
		
}

$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");
$arrAllNews = array_merge($arrPublicNews, $arrPrivateNews);
rsort($arrAllNews);
$dispSquadNews = "";
foreach($arrAllNews as $newsPostID) {
	
	$squadNewsObj->select($newsPostID);
	$squadNewsInfo = $squadNewsObj->get_info_filtered();
	
	$member->select($squadNewsInfo['member_id']);
	$newsMemberInfo = $member->get_info_filtered();
	$dispMemberLink = $member->getMemberLink();
	
	
	if($newsMemberInfo['avatar'] == "") {
		$newsMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
	}
	else {
		$newsMemberInfo['avatar'] = $MAIN_ROOT.$newsMemberInfo['avatar'];	
	}
	
	$dispNewsType = "<span class='publicNewsColor' style='font-style: italic'>Public News</span>";
	
	if($squadNewsInfo['newstype'] == 2) {
		$dispNewsType = "<span class='privateNewsColor' style='font-style: italic'>Private News</span>";
	}
	
	$dispSquadNews .= "
		
		<div class='squadNewsPost'>
			<img src='".$newsMemberInfo['avatar']."' class='avatarImg'>
			<div class='squadNewsInfo'>
				posted by: ".$dispMemberLink." - ".getPreciseTime($squadNewsInfo['dateposted'])."<br>
				<span class='squadNewsSubject'>".$squadNewsInfo['postsubject']."</span><br>
				".$dispNewsType."
			</div>
			<div class='dottedLine' style='margin: 10px 0px; clear: both'></div>
			<div style='padding-left: 5px'>
				".nl2br(parseBBCode($squadNewsInfo['newspost']))."
			</div>
			<div class='dottedLine' style='margin: 5px 0px'></div>
		</div>
	
	";
	
}

if($dispSquadNews == "") {
	$dispSquadNews = "<p align='center' class='main'><i>No Squad News Posted!</i></p>";	
}

$squadRankList = $squadObj->getRankList();
$dispSquadRanks = "";
$countRanks = 1;
foreach($squadRankList as $squadRankID) {
	$squadObj->objSquadRank->select($squadRankID);
	$dispSquadRanks .= $countRanks.". ".$squadObj->objSquadRank->get_info_filtered("name")."<br>";
	$countRanks++;
}


$arrSquadMembers = array();
foreach($squadMemberList as $realMemberID) {
	$squadMemberID = $squadObj->getSquadMemberID($realMemberID);
	
	$squadObj->objSquadMember->select($squadMemberID);
	$squadObj->objSquadRank->select($squadObj->objSquadMember->get_info("squadrank_id"));
	$squadRankInfo = $squadObj->objSquadRank->get_info();
	$arrSquadMembers[$realMemberID] = $squadRankInfo['sortnum'];	
}

asort($arrSquadMembers);
$counter = 0;
$blnPost = false;
$blnManageShoutbox = false;
$blnManageNewsPost = false;
foreach($arrSquadMembers as $key => $sortnum) {
	// $key = member_id
	
	$squadMemberID = $squadObj->getSquadMemberID($key);
	$squadObj->objSquadMember->select($squadMemberID);
	$squadMemberInfo = $squadObj->objSquadMember->get_info();
	$squadObj->objSquadRank->select($squadMemberInfo['squadrank_id']);
	$squadRankInfo = $squadObj->objSquadRank->get_info_filtered();

	$member->select($key);
	$dispMemberLink = $member->getMemberLink();
	
	
	// Check if squad member is looking at the profile page.
	// See if squad member has any squad privileges
	
	if($memberInfo['member_id'] == $squadMemberInfo['member_id'] && $squadObj->memberHasAccess($memberInfo['member_id'], "postshoutbox")) {
		$blnPost = true;
		
		if($squadObj->memberHasAccess($memberInfo['member_id'], "managenews")) {
			$blnManageNewsPost = true;	
		}
		
		if($squadObj->memberHasAccess($memberInfo['member_id'], "manageshoutbox")) {
			$blnManageShoutbox = true;	
		}
		
	}
	
	if(substr($member->get_info_filtered("profilepic"), 0, 4) == "http") {
		$squadMemberProfilePic = $member->get_info_filtered("profilepic");
	}
	else {
		$squadMemberProfilePic = $MAIN_ROOT.$member->get_info_filtered("profilepic");
	}
	
	
	
	if($squadMemberProfilePic == $MAIN_ROOT) {
		$squadMemberProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
	}
	
	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}
	
	$dispLastPromotion = "<span style='font-style: italic'>Never</span>";
	$dispLastDemotion = "<span style='font-style: italic'>Never</span>";
	
	if($squadMemberInfo['lastpromotion'] != 0) {
		$dispLastPromotion = getPreciseTime($squadMemberInfo['lastpromotion']);
	}
	
	if($squadMemberInfo['lastdemotion'] != 0) {
		$dispLastDemotion = getPreciseTime($squadMemberInfo['lastdemotion']);
	}
	
	$dispMemberList .= "
		<tr>
			<td class='main profilePic".$addCSS."' valign='top'><img src='".$squadMemberProfilePic."'></td>
			<td class='main".$addCSS."' valign='top'>
				<span class='largeFont'>".$dispMemberLink."</span><br>
				<b>Rank:</b> ".$squadRankInfo['name']."<br>
				<b>Date Joined:</b> ".getPreciseTime($squadMemberInfo['datejoined'])."<br>
				<b>Last Promotion:</b> ".$dispLastPromotion."<br>
				<b>Last Demotion:</b> ".$dispLastDemotion."
			</td>
		</tr>
	
	";
	
}


// Shoutbox

$shoutboxObj = new Shoutbox($mysqli, "squadnews", "squadnews_id");

$shoutboxObj->strDivID = "squadsShoutbox";
$shoutboxObj->intDispWidth = 95;
$shoutboxObj->intDispHeight = 400;
$shoutboxObj->strSQLSort = " AND squad_id ='".$squadInfo['squad_id']."'";

if($blnPost) { $shoutboxObj->strPostLink = $MAIN_ROOT."members/squads/include/postshoutbox.php?sID=".$squadInfo['squad_id']; }
if($blnManageShoutbox) { 
	$shoutboxObj->strEditLink = $MAIN_ROOT."members/squads/managesquad.php?pID=ManageShoutbox&sID=".$squadInfo['squad_id']."&nID=";
	$shoutboxObj->strDeleteLink = $MAIN_ROOT."members/squads/include/deleteshoutpost.php?sID=".$squadInfo['squad_id'];
}


$breadcrumbObj->setTitle($squadInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Squads", $MAIN_ROOT."squads");
$breadcrumbObj->addCrumb($squadInfo['name']);
include($prevFolder."include/breadcrumb.php");

echo "	
		<div class='squadContainer'>
		
			<div class='squadLeftColumn'>
				<img src='".$squadInfo['logourl']."' class='squadLogo'>
				
				<div class='squadInfoTitle'>SQUAD NEWS</div>
				<div class='squadInfoBox'>
					<div class='squadNews'>
						".$dispSquadNews."
					</div>
				</div>
				<div class='squadInfoTitle'>MEMBERS</div>
				<div class='squadInfoBox'>
					
					<table class='formTable' style='border-spacing: 0px'>
						".$dispMemberList."
					</table>
					
				</div>
				
			</div>
		
			<div class='squadRightColumn'>
				<div class='squadInfoTitle' style='margin-top: 0px'>SQUAD INFORMATION</div>
				<div class='squadInfoBox'>
					<b>About the Squad:</b><br>
					".nl2br($squadInfo['description'])."
					<div class='dottedLine' style='margin: 5px 0px'></div>
					<b>Squad Founder:</b><br>
					".$dispFounderLink."<br><br>
					
					<b>Date Founded:</b><br>".getPreciseTime($squadInfo['datecreated'])."<br><br>
					
					<b>Recruiting:</b> ".$dispRecruitingStatus."<br><br>
					
					<b>Total Members:</b> ".$squadObj->countMembers()."<br><br>
					
					";
					
					if($squadInfo['website'] != "") {
						echo "
							<b>Website:</b> <a href='".$squadInfo['website']."' target='_blank'>View Site</a><br><br>
						";
					}

				if(in_array($memberInfo['member_id'], $squadMemberList)) {
					echo "

						<div class='dottedLine' style='margin: 5px 0px'></div>
						<p align='center' class='largeFont main'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$consoleObj->findConsoleIDByName("View Your Squads")."&select=".$squadInfo['squad_id']."'>Manage Squad</a></b></p>
					";
					
				}
				else {
					
					echo "
					
						<div class='dottedLine' style='margin: 5px 0px'></div>
						<p align='center' class='largeFont main'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$consoleObj->findConsoleIDByName("Apply to a Squad")."&select=".$squadInfo['squad_id']."'>Apply to this Squad</a></b></p>
					";
					
				}
			
				
				echo "
					
				</div>
				";



			$dispShoutbox = "
				<div class='squadInfoTitle'>SHOUTBOX</div>
				<div class='squadInfoBox'>
					".$shoutboxObj->dispShoutbox("","",true)."
				</div>
			";

			if($squadInfo['privateshoutbox'] == 0 && in_array($memberInfo['member_id'], $squadMemberList)) {
				echo $dispShoutbox;
			}
			elseif($squadInfo['privateshoutbox'] == 1) {
				echo $dispShoutbox;	
			}
				
				
				echo "
				<div class='squadInfoTitle'>RANKINGS</div>
				<div class='squadInfoBox'>
					
					".$dispSquadRanks."

				</div>
			</div>
		</div>
	
		<script type='text/javascript'>
			$(document).ready(function() {
			
				$('#squadsShoutbox').animate({
				scrollTop:$('#squadsShoutbox')[0].scrollHeight
			}, 1000);
			
			
			
			});
			
			function reloadSquadsShoutbox() {
				$(document).ready(function() {

					$.post('".$MAIN_ROOT."members/squads/include/reloadshoutbox.php', { sID: '".$squadInfo['squad_id']."' }, function(data) {

						$('#squadsShoutbox').html(data);
	
					});
						
						
					
				});

				
				setTimeout('reloadSquadsShoutbox()', 20000);
			}


			setTimeout('reloadSquadsShoutbox()', 20000);
		
		</script>
";

include($prevFolder."themes/".$THEME."/_footer.php");


?>