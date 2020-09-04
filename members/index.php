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
include("../_setup.php");


// Classes needed for index.php
include_once("../classes/member.php");
include_once("../classes/rank.php");
include_once("../classes/consoleoption.php");
include_once("../classes/consolecategory.php");


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
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > My Account";
$EXTERNAL_JAVASCRIPT = "<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>";
$PAGE_NAME = "My Account - ";
include("../themes/".$THEME."/_header.php");

$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

$LOGIN_FAIL = true;

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {
		$LOGIN_FAIL = false;
		
		$memberInfo = $member->get_info();
		$memberRankID = $memberInfo['rank_id'];
		
		$memberRank = new Rank($mysqli);
		$memberRank->select($memberRankID);
		$rankPrivileges = $member->get_privileges();
		$clickCategory = "";
		
		
		$strPrivileges = implode(",", $rankPrivileges);
		
		$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."consolecategory ORDER BY ordernum DESC");
		
		while($row = $result->fetch_assoc()) {
			$arrConsoleCats[] = $row['consolecategory_id'];
		}
		
		$result->free();
		
		$arrFullySortedConsole = array();
		$consoleObj = new ConsoleOption($mysqli);
		foreach($rankPrivileges as $consoleoption) {
		
			
			$consoleObj->select($consoleoption);
			$consoleInfo = $consoleObj->get_info();
			if($member->hasAccess($consoleObj) && $consoleInfo['hide'] == 0) {
				$sortNum = array_search($consoleInfo['consolecategory_id'], $arrConsoleCats);
				
				$arrFullySortedConsole[$sortNum][] = $consoleoption;
			}
		
		}
		$consoleCatObj = new ConsoleCategory($mysqli);
		
		$dispConsoleOptions = "";
		$dispConsoleCategories = "";
		$counter = 0;
		$totalConsoleCats = count($arrConsoleCats);
		
		foreach($arrConsoleCats as $key => $categoryID) {
			
			$consoleCatObj->select($categoryID);
			$consoleCatInfo = $consoleCatObj->get_info_filtered();
			
			$arrConsoleOptions = $arrFullySortedConsole[$key];
			$categoryCSS = "consoleCategory_clicked";
			if(count($arrConsoleOptions)) {
				
				
				$blnShowCategoryList = false;
				$hideoptions = "";
				if($counter > 0) {
					$hideoptions = "style='display: none'";
					$categoryCSS = "consoleCategory";
				}
				$counter++;
				
				if(isset($_GET['select']) && $categoryID == $_GET['select']) {
					$clickCategory = "
						<script type='text/javascript'>
							selectCategory('".$counter."');
						</script>
					";
					
				}
				elseif(isset($_SESSION['lastConsoleCategory']) && $_SESSION['lastConsoleCategory']['catID'] == $categoryID && $_SESSION['lastConsoleCategory']['exptime'] > time()) {
					$clickCategory = "
						<script type='text/javascript'>
							selectCategory('".$counter."');
						</script>
					";
				}
				elseif($memberInfo['defaultconsole'] == $categoryID && $clickCategory == "") {
					$clickCategory = "
					<script type='text/javascript'>
						selectCategory('".$counter."');
					</script>
					";
					$blnShowCategoryList = true;
				}
				
				
				
				$divIDName = str_replace(" ", "", $consoleCatInfo['name']).$categoryID;
				$dispConsoleCategories .= "<div class='".$categoryCSS."' id='categoryName".$counter."' onmouseover=\"moverCategory('".$counter."')\" onmouseout=\"moutCategory('".$counter."')\" onclick=\"selectCategory('".$counter."')\">".$consoleCatInfo['name']."</div>";		
				$dispConsoleOptions .= "<div id='categoryOption".$counter."' ".$hideoptions.">";
				$dispConsoleOptions .= "
				<div class='dottedLine' style='padding-bottom: 3px; margin-bottom: 5px'>
					<b>Menu Options - ".$consoleCatInfo['name']."</b>
				</div>
				<div style='padding-left: 5px; padding-bottom: 15px'>
				<ul style='padding: 0px; padding-left: 15px'>
				";
				foreach($arrConsoleOptions as $consoleOptionID) {
			
					$consoleObj->select($consoleOptionID);
					$consoleInfo = $consoleObj->get_info_filtered();
					$dispPageTitle = $consoleInfo['pagetitle'];
					if($consoleInfo['sep'] == "1") {
						$dispPageTitle = "<div class='dashedLine' style='width: 80%; margin: 6px 1px; padding: 0px'></div>";
						$dispConsoleOptions .= $dispPageTitle;
					}
					elseif($consoleInfo['hide'] == 0) {
						
						$memberAppCID = $consoleObj->findConsoleIDByName("View Member Applications");
						$diplomacyRequestsCID = $consoleObj->findConsoleIDByName("View Diplomacy Requests");
						$viewEventInvitationsCID = $consoleObj->findConsoleIDByName("View Event Invitations");
						$viewInactiveRequestsCID = $consoleObj->findConsoleIDByName("View Inactive Requests");
						$privateMessagesCID = $consoleObj->findConsoleIDByName("Private Messages");
						
						if($consoleInfo['console_id'] == $memberAppCID) {
							$getUnseenApps = $mysqli->query("SELECT memberapp_id FROM ".$dbprefix."memberapps WHERE seenstatus = '0'");
							$unseenApps = $getUnseenApps->num_rows;
							$getAllApps = $mysqli->query("SELECT memberapp_id FROM ".$dbprefix."memberapps");
							$totalApps = $getAllApps->num_rows;
							
							if($unseenApps > 0) {
								$dispPageTitle .= " <b>(".$unseenApps.")</b>";
							}
							else {
								$dispPageTitle .= " (".$totalApps.")";
							}
							
							
						}
						
						
						switch($consoleInfo['console_id']) {
							case $diplomacyRequestsCID:
								$diplomacyRequestsSQL = $mysqli->query("SELECT diplomacyrequest_id FROM ".$dbprefix."diplomacy_request WHERE confirmemail = '1'");
								$countDiplomacyRequests = $diplomacyRequestsSQL->num_rows;
								$dispPageTitle .= " (".$countDiplomacyRequests.")";
								break;
							case $viewEventInvitationsCID:
								$eventInvitationsSQL = $mysqli->query("SELECT em.eventmember_id FROM ".$dbprefix."events_members em, ".$dbprefix."events e WHERE status = '0' AND em.event_id = e.event_id AND e.startdate > '".time()."' AND em.member_id = '".$memberInfo['member_id']."'");
								$countEventInvitations = $eventInvitationsSQL->num_rows;
								$dispPageTitle .= " (".$countEventInvitations.")";
								break;
							case $viewInactiveRequestsCID:
								$iaRequestSQL = $mysqli->query("SELECT iarequest_id FROM ".$dbprefix."iarequest WHERE requeststatus = '0'");
								$countIARequests = $iaRequestSQL->num_rows;
								$dispPageTitle .= " (".$countIARequests.")";
								break;
							case $privateMessagesCID:
								$dispPageTitle .= "</a></li>";
								$dispPageTitle .= "<li><a href='".$MAIN_ROOT."members/privatemessages/compose.php'>Compose Message";
								break;
						}
						
						
						$dispConsoleOptions .= "<li><a href='console.php?cID=".$consoleInfo['console_id']."'>".$dispPageTitle."</a></li>";
					}
				}
				$dispConsoleOptions .= "</ul></div></div>";
				
			}
					
		
		}
		
		
		
		echo "
		<div class='breadCrumbTitle'>My Account</div>
		<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
			$dispBreadCrumb
		</div>
			<div id='myAccountPageCategories' style='float: left; text-align: left; width: 35%; padding: 10px 0px 0px 40px'>
				".$dispConsoleCategories."
			</div>
			<div id='myAccountPageOptions' style='float: right; text-align: left; width: 40%; padding: 10px 40px 0px 10px'>
				<div id='myAccountPageReturn' style='display: none' data-goback='yes'>
					Return to Category List
				</div>
				
				".$dispConsoleOptions."
				
				<div id='myAccountPageReturn' style='display: none' data-goback='yes'>
					Return to Category List
				</div>
			</div>
		";
		
		
		echo $clickCategory;
	}

}



if($LOGIN_FAIL) {
die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}

if($blnShowCategoryList || true) {

	echo "
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#myAccountPageCategories').removeClass('hideConsoleCategories').addClass('showConsoleCategories');
				$('#myAccountPageOptions').removeClass('showConsoleOptions').addClass('hideConsoleOptions');
			});
		</script>
	";
	
}

include("../themes/".$THEME."/_footer.php");


?>