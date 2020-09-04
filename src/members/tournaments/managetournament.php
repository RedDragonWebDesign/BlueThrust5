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

include_once("../../_setup.php");
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/tournament.php");


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
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);

$pID = strtolower($_GET['pID']);


$tID = $_GET['tID'];

$prevFolder = "../../";

$PAGE_NAME = "Tournaments - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > ".$consoleTitle;
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

include("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>$consoleTitle</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";


if(isset($_GET['match'])) {
	echo "
	<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches'>Go Back</a></p>
	";
}
elseif($_GET['pID'] == "ManagePools" && isset($_GET['poolID'])) {
	echo "
	<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManagePools'>Go Back</a></p>
	";
}
else {
	echo "
	<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tID."'>Go Back</a></p>
	";
}


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($tID)) {
	
	$tournamentInfo = $tournamentObj->get_info_filtered();
	$memberInfo = $member->get_info_filtered();
	
	if($memberInfo['member_id'] == $tournamentInfo['member_id'] || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
	
	
		switch($pID) {
			case "manageplayers":
				include("manageplayers.php");
				break;
			case "manageteams":
				include("manageteams.php");
				break;
			case "managepools":
				include("managepools.php");
				break;
			case "deletetournament":
				if($memberInfo['member_id'] == $tournamentInfo['member_id']) {
					include("deletetournament.php");
				}
				else {
					echo "
						<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';</script>
					";
				}
				
				break;
			case "startmatches";
				include("startmatches.php");
				break;
			case "managematches":
				
				
				if(!isset($_GET['match'])) {
					include("managematches.php");
				}
				else {
					include("managematch.php");
				}
				
				break;
			case "edittournamentinfo":
				include("editinfo.php");
				break;
			case "setmanagers":
				if($memberInfo['member_id'] == $tournamentInfo['member_id']) {
					include("setmanagers.php");
				}
				else {
					echo "
						<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';</script>
					";
				}
				break;
			default:
				echo "
					<script type='text/javascript'>window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';</script>
				";
		}
	
		
		if(isset($_GET['match'])) {
			echo "
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'><br><br>&laquo; <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManageMatches'>Go Back</a></p></div>
			";			
		}
		elseif($_GET['pID'] == "ManagePools" && isset($_GET['poolID'])) {
			echo "
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'><br><br>&laquo; <a href='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=ManagePools'>Go Back</a></p></div>
			";
		}
		else {
			echo "
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'><br><br>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tID."'>Go Back</a></p></div>
			";
		}
		
		
	}
	else {
		echo "
			<div class='shadedBox' style='width: 300px; margin-top: 50px; margin-left: auto; margin-right: auto;'>
				<p class='main' align='center'>
					You don't have access to this page!<br><br>
					<a href='".$MAIN_ROOT."members'>Go Back</a>
				</p>
			</div>
		";
	}

}
else {

	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
		
}



include("../../themes/".$THEME."/_footer.php");

?>