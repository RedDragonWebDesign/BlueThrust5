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


if(!defined("SHOW_MANAGERLIST")) {
	include_once("../../../_setup.php");
	include_once("../../../classes/member.php");
	include_once("../../../classes/rank.php");
	include_once("../../../classes/tournament.php");
	
	
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
	$consoleObj->select($cID);
	
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$tournamentObj = new Tournament($mysqli);
	
	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$tournamentObj->select($_POST['tID']) || $tournamentObj->get_info("member_id") != $memberInfo['member_id'] || !$member->hasAccess($consoleObj)) {
		exit();	
	}
}


$arrManagers = $tournamentObj->getManagers();

foreach($arrManagers as $tManagerID => $tMemberID) {
	$member->select($tMemberID);
	echo "<div class='mttPlayerSlot main'>".$member->getMemberLink()."<div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"deleteManager('".$tManagerID."')\">X</a></div></div>";
	
}

if(count($arrManagers) == 0) {
	echo "
		<div class='shadedBox' style='width: 75%; margin-top: 10px; margin-left: auto; margin-right: auto'>
			<p class='main' align='center'>
				<i>No managers assigned to this tournament.</i>
			</p>
		</div>
	";
}

$member->select($memberInfo['member_id']);

?>

