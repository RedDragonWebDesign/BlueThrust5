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

// Check for valid Console Option

$consoleObj = new ConsoleOption($mysqli);

$checkConsole = $mysqli->query("SELECT console_id FROM ".$dbprefix."console ORDER BY console_id");

while($row = $checkConsole->fetch_assoc()) {
	$arrConsoleOptions[] = $row['console_id'];
}

if(!$consoleObj->select($_GET['cID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}
$cID = $_GET['cID'];


// Load any plugins
$consolePluginObj = new btPlugin($mysqli);
$arrPlugins = $consolePluginObj->getPluginPage("console");

foreach($arrPlugins as $pluginPageInfo) {
	include_once($pluginPageInfo['pagepath']);
}


// Start Page

$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];
$PAGE_NAME = $consoleTitle." - ";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."js/colorpicker/jquery.miniColors.js'></script>
<link rel='stylesheet' media='screen' type='text/css' href='".$MAIN_ROOT."js/colorpicker/jquery.miniColors.css'>
";


$arrTinyMCEPages = array("Manage News", "Post News", "Add Custom Page", "Manage Custom Pages", "Add Custom Form Page", "Manage Custom Form Pages", "Post Topic", "Manage Forum Posts", "Add Menu Item", "Add Menu Category", "Manage Menu Categories", "Manage Menu Items", "Edit Profile");
$arrAceEditorPages = array("Modify Current Theme", "Add Menu Category", "Add Menu Item", "Manage Menu Categories", "Manage Menu Items");


if(in_array($consoleInfo['pagetitle'], $arrTinyMCEPages)) {
	$btThemeObj->addHeadItem("richtexteditor", "<script type='text/javascript' src='".$MAIN_ROOT."js/tiny_mce/jquery.tinymce.js'></script>");
}

if(in_array($consoleInfo['pagetitle'], $arrAceEditorPages)) {
	$btThemeObj->addHeadItem("codeedtior", "<script type='text/javascript' src='".$MAIN_ROOT."js/ace/src-min-noconflict/ace.js' charset='utf-8'></script>");	
}

$hooksObj->run("init_console");

include("../themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($consoleTitle);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("My Account", $MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']);
$breadcrumbObj->addCrumb($consoleTitle);
$hooksObj->addHook("breadcrumb", "resetConsoleBreadcrumb");

$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

$LOGIN_FAIL = true;

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {
		$LOGIN_FAIL = false;
		
		$memberInfo = $member->get_info();
		
		$_SESSION['lastConsoleCategory'] = array("catID" => $consoleInfo['consolecategory_id'], "exptime" => time()+300);
		
		// Check for IA
		
		if($memberInfo['onia'] == 1 && $cID != $consoleObj->findConsoleIDByName("Cancel IA")) {
			$cancelIACID = $consoleObj->findConsoleIDByName("Cancel IA");
			echo "
			
				<div id='iaMessage' style='display: none'>
					<p class='main' align='center'>You are currently Inactive!<br><br>While inactive, you do not have access to console options.<br><br><a href='".$MAIN_ROOT."members/console.php?cID=".$cancelIACID."'><b>Click Here</b></a> to become active again!</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Inactive Member', '".$MAIN_ROOT."members', 'iaMessage');
				</script>
			";
				
			exit();
		}		
		
		$memberRankID = $memberInfo['rank_id'];
		define("MEMBERRANK_ID", $memberRankID);
		
		$memberRank = new Rank($mysqli);
		$memberRank->select($memberRankID);
		$rankPrivileges = $memberRank->get_privileges();
		
		
		
		
		if($member->hasAccess($consoleObj) || ($consoleInfo['pagetitle'] == "Manage Forum Posts" && !isset($_GET['noaccess']))) {
			//$getClanInfo = $mysqli->query("SELECT * FROM ".$dbprefix."websiteinfo WHERE websiteinfo_id = '1'");
			$arrClanInfo = $websiteInfo;//$getClanInfo->fetch_assoc();
			// Console Security

			define("PREVENT_HACK", $arrClanInfo['preventhack']);
			
			include($prevFolder."include/breadcrumb.php");
			
			if(isset($_GET['action']) && $_GET['action'] == "edit") {
				echo "
				<p align='right' style='margin-bottom: 10px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a></p>
				";
			}
			elseif(!isset($_GET['action'])) {
				echo "
				<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."' id='consoleTopBackButton'>Go Back</a></p>
				";
			}
			
			
			if(substr($consoleInfo['filename'], 0, strlen("../")) != "../") {
				$include_file = "include/".$consoleInfo['filename'];
			}
			else {
				$include_file = $consoleInfo['filename'];	
			}
						
			$formObj = new Form();
			require($include_file);
			if(isset($setupFormArgs)) {
				include("console.form.php");
			}
			elseif(isset($setupManageListArgs)) {
				include("console.managelist.php");	
			}
			
			
			if(isset($_GET['action']) && $_GET['action'] == "edit") {
				echo "
					<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a></p>				
				";
			}
			elseif(!isset($_GET['action'])) {
				echo "
					<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."' id='consoleBottomBackButton'>Go Back</a></p></div>
				";
			}
			
		}
		else {
			echo "<div class='formDiv' style='width: 300px; padding: 5px; margin-top: 50px; margin-left: auto; margin-right: auto'><p align='center'><i>You don't have access to this console option!</i><br><br><a href='console.php'>Return to My Account</a></p></div>";	
		}
		
	}

}


if($LOGIN_FAIL) {
die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}


include("../themes/".$THEME."/_footer.php");


?>