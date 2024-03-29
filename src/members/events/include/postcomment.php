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
require_once("../../../classes/consoleoption.php");
require_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$objMember = new Member($mysqli);

$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if ($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventMessage->select($_POST['messageID'])) {
	$eventID = $eventObj->objEventMessage->get_info("event_id");
	$eventObj->select($eventID);

	$eventInfo = $eventObj->get_info_filtered();

	$memberInfo = $member->get_info_filtered();

	if (trim($_POST['commentMessage']) != "" && $member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "postmessages") || $memberInfo['rank_id'] == 1)) {
		$eventObj->objEventMessageComment->addNew(["eventmessage_id", "member_id", "dateposted", "comment"], [$_POST['messageID'], $memberInfo['member_id'], time(), $_POST['commentMessage']]);
	}


	if (in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true)) || $memberInfo['member_id'] == $eventInfo['member_id'] || $memberInfo['rank_id'] == 1) {
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."eventmessage_comment WHERE eventmessage_id = '".$_POST['messageID']."' ORDER BY dateposted ASC");
		while ($row = $result->fetch_assoc()) {
			if ($objMember->select($row['member_id'])) {
				$memInfo = $objMember->get_info_filtered();

				if ($memInfo['profilepic'] == "") {
					$dispProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
				} else {
					$dispProfilePic = $MAIN_ROOT.$memInfo['profilepic'];
				}


				$dispDeleteMessage = "";
				if ($eventObj->memberHasAccess($memberInfo['member_id'], "managemessages")) {
					$dispDeleteMessage = " - <a href='javascript:void(0)' onclick=\"deleteMessage('".$row['comment_id']."', 'c')\">Delete</a>";
				}

				echo "
					<li class='dottedLine'>
						<div class='profilePic'><img src='".$dispProfilePic."'></div>
						<div class='main messageDiv'><b>".$objMember->getMemberLink()."</b><br>
							".nl2br(parseBBCode($row['comment']))."<br>
							<div class='tinyFont' style='margin-top: 5px'>".getPreciseTime($row['dateposted']).$dispDeleteMessage."</div>
						</div>
						<div style='clear: both'></div>
					</li>
				";
			}
		}
	}
}
