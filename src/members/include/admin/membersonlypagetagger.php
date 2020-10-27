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

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Member's Only Pages");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	
	if($_POST['setTaggerStatus'] == 1) {
	
		if(isset($_SESSION['btMembersOnlyTagger']) && $_SESSION['btMembersOnlyTagger'] == 1) {
			$_SESSION['btMembersOnlyTagger'] = 0;
			
			echo "
			
				The member's only page tagger is currently <b>off</b>.<br><br>
			
				<a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn On Member's Only Page Tagger</a>
			
			";
			
		}
		else {
			$_SESSION['btMembersOnlyTagger'] = 1;
			
			echo "
				
				The member's only page tagger is currently <b>on</b>.<br><br>
			
				<a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn Off Member's Only Page Tagger</a>
			
			";
			
		}
	
	}
	elseif($_POST['setPageStatus'] == 1 && !isset($_POST['pageID']) && $_SESSION['btMembersOnlyTagger'] == 1) {
		
		
		$taggerObj = new Basic($mysqli, "membersonlypage", "pageurl");
		
		if(!$taggerObj->select($_POST['tagURL'], false)) {

			$taggerObj->addNew(array("pagename", "pageurl", "dateadded"), array($_POST['pageName'], $_POST['tagURL'], time()));

			echo "
			
				<p align='center' style='margin: 0px; margin-bottom: 15px'><b>Members Only Tagger: ".$_POST['pageName']."</b></p>
				
				<p align='center'>Current Status: <span class='pendingFont'>Member's Only</span><br>Return to <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Member's Only Pages</a></p>
			
				
				<div class='taggerBottomLeft'><a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn Off</a></div>
				<div class='taggerBottomRight'><a href='javascript:void(0)' onclick='setMembersOnlyPageStatus()'>Untag Page</a></div>
				
		
			";
			
			
			
		}
		else {
			$taggerObj->delete();
			echo "
			
				<p align='center' style='margin: 0px; margin-bottom: 15px'><b>Members Only Tagger: ".$_POST['pageName']."</b></p>
				
				<p align='center'>Current Status: <span class='publicNewsColor'>Public</span><br>Return to <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Member's Only Pages</a></p>
			
				
				<div class='taggerBottomLeft'><a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn Off</a></div>
				<div class='taggerBottomRight'><a href='javascript:void(0)' onclick='setMembersOnlyPageStatus()'>Tag Page</a></div>
				
			
			";
		}
		
		
		
		
	}
	elseif($_POST['setPageStatus'] == 1 && isset($_POST['pageID'])) {
		
		$taggerObj = new Basic($mysqli, "membersonlypage", "page_id");
		if($taggerObj->select($_POST['pageID'])) {
			
			$taggerObj->delete();	
			include("membersonlypageslist.php");
			
		}		
	}
	elseif($_POST['setSectionStatus'] == 1 && ($_POST['pageID'] == "profile" || $_POST['pageID'] == "forum") && ($_POST['pageStatusValue'] == 1 || $_POST['pageStatusValue'] == 0)) {
		
		$settingName = "private".$_POST['pageID'];
		$arrColumn = array("value");
		$arrValue = array($_POST['pageStatusValue']);
		$webInfoObj->select($webInfoObj->get_key($settingName));
		if($webInfoObj->update($arrColumn, $arrValue)) {
			echo "<span class='successFont'><i>section privacy updated!</i></span>";	
		}
		else {
			echo "<span class='failedFont'><i>unable to update privacy settings!</i></span>";
		}
		
	}
	
	
}


?>