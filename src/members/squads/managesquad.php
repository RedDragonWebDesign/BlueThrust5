<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("../../_setup.php");
require_once("../../classes/member.php");
require_once("../../classes/rank.php");
require_once("../../classes/squad.php");


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

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);

$manageAllSquadsCID = $consoleObj->findConsoleIDByName("Manage All Squads");
$consoleObj->select($manageAllSquadsCID);

$blnManageAllSquads = $member->hasAccess($consoleObj);


$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];


$squadObj = new Squad($mysqli);
$arrSquadPrivileges = $squadObj->arrSquadPrivileges;

$pID = strtolower($_GET['pID']);

if($pID == "viewapps") { $pID = "acceptapps"; }

$sID = $_GET['sID'];


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword'])) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	$blnShowPage = false;
	// Check Squad ID
	
	if(($squadObj->select($_GET['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], $pID)) || $blnManageAllSquads) {
		$blnShowPage = true;		
	}
	elseif($squadObj->select($_GET['sID']) && !$squadObj->memberHasAccess($memberInfo['member_id'], $pID)) {
		$blnShowPage = false;
	}
	else {
		echo "
			<script type='text/javascript'>
				window.location = '".$MAIN_ROOT."'
			</script>
		";
		exit();
	}
	
	if($pID == "closesquad") {
		if($memberInfo['member_id'] == $squadObj->get_info("member_id") || $blnManageAllSquads) {
			$blnShowPage = true;	
		}
	}
	elseif($pID == "leavesquad") {
		if($memberInfo['member_id'] != $squadObj->get_info("member_id")) {
			$blnShowPage = true;
		}		
	}
		
}


if($LOGIN_FAIL) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}


$prevFolder = "../../";
$PAGE_NAME = "Manage Squad - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > ".$consoleTitle;
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

require_once("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>$consoleTitle</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";
if($blnShowPage) {
	
	if($_GET['nID'] != "") {
		echo "
		<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageNews'>Go Back</a></p>
		";
	}
	elseif($_GET['rID'] != "") {
		echo "
		<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks'>Go Back</a></p>
		";
	}
	else {
		echo "
		<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$sID."'>Go Back</a></p>
		";
	}
	
	
	$squadInfo = $squadObj->get_info_filtered();
	switch($pID) {
		case "postnews":
			require_once("postnews.php");
			break;
		case "managenews":
			require_once("managenews.php");
			break;
		case "addrank":
			require_once("addrank.php");
			break;
		case "manageranks":
			require_once("manageranks.php");
			break;
		case "sendinvites":
			require_once("sendinvites.php");
			break;
		case "acceptapps":
			require_once("viewapps.php");
			break;
		case "manageshoutbox":
			require_once("manageshoutbox.php");
			break;
		case "setrank":
			require_once("setrank.php");
			break;
		case "removemember":
			require_once("removemember.php");
			break;
		case "closesquad":
			require_once("closesquad.php");
			break;
		case "editprofile":
			require_once("editprofile.php");
			break;
		case "leavesquad":
			require_once("leavesquad.php");
			break;
		default:
			echo "
				<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."'</script>
			";
			break;		
	}
	
	
	if($_GET['nID'] != "") {
		echo "
		<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageNews'>Go Back</a></p></div>
		";
	}
	elseif($_GET['rID'] != "") {
		echo "
		<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks'>Go Back</a></p></div>
		";
	}
	else {
		echo "
		<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$sID."'>Go Back</a></p></div>
		";
	}
	
}
else {

	echo "

	<div class='shadedBox' style='width: 400px; margin-top: 50px; margin-bottom: 50px; margin-left: auto; margin-right: auto;'>
		<p align='center' class='main'>
			<i>You do not have access to this squad privilege!</i><br><br><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a>
		</p>
	</div>

	
	";
		
}

require_once("../../themes/".$THEME."/_footer.php");