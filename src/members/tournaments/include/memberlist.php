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

require_once("../../../_setup.php");
require_once("../../../classes/member.php");
require_once("../../../classes/rank.php");
require_once("../../../classes/tournament.php");

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$tournamentObj = new Tournament($mysqli);
$tID = $_GET['tID'];
$arrMembers = [];

if ($member->authorizeLogin($_SESSION['btPassword']) && $tournamentObj->select($tID) && $member->hasAccess($consoleObj) && strlen($_GET['term']) >= 3) {
	$memberInfo = $member->get_info();
	$tmemberID = $tournamentObj->get_info("member_id");

	if ($memberInfo['member_id'] == $tmemberID || $memberInfo['rank_id'] == "1" || $tournamentObj->isManager($memberInfo['member_id'])) {
		$tournamentPlayers = $tournamentObj->getPlayers();
		$memberSQL = "('".implode("','", $tournamentPlayers)."')";


		$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE username LIKE '".$_GET['term']."%' ORDER BY username");
		while ($row = $result->fetch_assoc()) {
			$arrMembers[] = ["id" => $row['member_id'], "value" => $row['username']];
		}

		echo json_encode($arrMembers);
	}
}
