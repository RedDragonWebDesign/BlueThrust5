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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/basicorder.php");
	
	
	
	$consoleObj = new ConsoleOption($mysqli);
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$cID = $consoleObj->findConsoleIDByName("View Member Applications");
	$consoleObj->select($cID);
	
	
	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {
	
		exit();
	
	}
	
}

$diplomacyStatusObj = new Basic($mysqli, "diplomacy_status", "diplomacystatus_id");
$addClanCID = $consoleObj->findConsoleIDByName("Diplomacy: Add a Clan");

$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_request WHERE confirmemail = '1'");
while($row = $result->fetch_assoc()) {
	$row = filterArray($row);
	
	foreach($row as $key=>$value) {
		if($value == "") {
			$row[$key] = "Not Set";
		}
	}
	
	$diplomacyStatusObj->select($row['diplomacystatus_id']);
	$dispRequestStatus = $diplomacyStatusObj->get_info_filtered("name");
	echo "
		<div class='dottedBox' style='margin-top: 20px; width: 90%; margin-left: auto; margin-right: auto;'>
			<table class='formTable' style='width: 95%'>
				<tr>
					<td class='formLabel' valign='top'>Submitted By:</td>
					<td class='main' valign='top'>".$row['name']." (<a href='mailto:".$row['email']."'>".$row['email']."</a>)</td>
				</tr>
				<tr>
					<td class='formLabel'>Date Submitted:</td>
					<td class='main'>".getPreciseTime($row['dateadded'])."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Clan Name:</td>
					<td class='main' valign='top'>".$row['clanname']."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Clan Leaders:</td>
					<td class='main' valign='top'>".$row['leaders']."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Requested Status:</td>
					<td class='main' valign='top'>".$dispRequestStatus."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Clan Tag:</td>
					<td class='main' valign='top'>".$row['clantag']."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Games Played:</td>
					<td class='main' valign='top'>".$row['gamesplayed']."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Website:</td>
					<td class='main' valign='top'><a href='".$row['website']."' target='_blank'>".$row['website']."</a></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Clan Size:</td>
					<td class='main' valign='top'>".ucfirst($row['clansize'])."</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Message:</td>
					<td class='main' valign='top'>".nl2br($row['message'])."</td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'>
						<br>
						<b><a href='".$MAIN_ROOT."members/console.php?cID=".$addClanCID."&reqID=".$row['diplomacyrequest_id']."'>Add Clan</a></b> - <b><a href='javascript:void(0)' onclick=\"declineRequest('".$row['diplomacyrequest_id']."')\">Decline</a></b>
					</td>
				</tr>
			</table>
		</div>
	";
	
}


if($result->num_rows == 0) {
	echo "
		<div class='shadedBox' style='width: 400px; margin-top: 50px; margin-left: auto; margin-right: auto'>
			<p class='main' align='center'>
				<i>There are currently no diplomacy requests.</i>
			</p>
		</div>
	";
}

?>