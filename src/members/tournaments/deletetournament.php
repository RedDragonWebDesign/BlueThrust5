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
require_once("../../classes/tournament.php");

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);
$tID = $_POST['tID'];
$arrMembers = [];
echo $tID;
if ($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($tID) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info();
	$tmemberID = $tournamentObj->get_info("member_id");

	if ($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1") {
		$tournamentObj->delete();

		echo "deleted";
	}
} else {
	echo "no";
}
