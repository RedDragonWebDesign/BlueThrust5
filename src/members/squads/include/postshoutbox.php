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
include_once("../../../classes/shoutbox.php");


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


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();

	if($squadObj->select($_GET['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], "postshoutbox")) {
		
		$squadInfo = $squadObj->get_info();
		
		$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");
		$arrColumns = array("member_id", "squad_id", "dateposted", "newspost", "newstype");
		$arrValues = array($memberInfo['member_id'], $squadInfo['squad_id'], time(), $_POST['message'], 3);
		
		$squadNewsObj->addNew($arrColumns, $arrValues);
		
		if($squadObj->memberHasAccess($memberInfo['member_id'], "manageshoutbox")) {
			$blnManageShoutbox = true;
		}
				
	}
}


$squadMemberList = $squadObj->getMemberList();
$blnShowShoutBox = false;
if(in_array($memberInfo['member_id'], $squadMemberList) && $squadInfo['privateshoutbox'] == 1) {
	$blnShowShoutBox = true;
}
elseif($squadInfo['privateshoutbox'] == 0) {
	$blnShowShoutBox = true;
}

if($blnShowShoutBox) {

	$shoutboxObj = new Shoutbox($mysqli, "squadnews", "squadnews_id");
	
	$shoutboxObj->strDivID = "squadsShoutbox";
	$shoutboxObj->intDispWidth = 205;
	$shoutboxObj->intDispHeight = 400;
	$shoutboxObj->blnUpdateShoutbox = true;
	$shoutboxObj->strSQLSort = " AND squad_id ='".$squadInfo['squad_id']."'";
	
	if($blnManageShoutbox) {
		$shoutboxObj->strEditLink = $MAIN_ROOT."members/squads/managesquad.php?&pID=ManageShoutbox&sID=".$squadInfo['squad_id']."&nID=";
		$shoutboxObj->strDeleteLink = $MAIN_ROOT."members/squads/include/deleteshoutpost.php?sID=".$squadInfo['squad_id'];
	}
	
	
	echo $shoutboxObj->dispShoutbox();

}


?>