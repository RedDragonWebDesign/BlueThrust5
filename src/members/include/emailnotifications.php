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

if (!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
} else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$emailNotification = new EmailNotificationSetting($mysqli);
$emailNotification->setMemberID($memberInfo['member_id']);

$emailNotificationInfo = $emailNotification->get_info_filtered();

//print_r($emailNotification->getNotificationItems("tournaments", "tournament_id", "Tournaments", "startdate"));


$cID = $_GET['cID'];

$timeBefore = [0 => "Never"];
for ($i=1; $i<=60; $i++) {
	$timeBefore[$i] = $i;
}


//<select class='formInput textBox' name='tournament_unitbefore'><option value='minutes'>Minutes</option><option value='hour'>Hours</option><option value='day'>Days</option></select>

$timeUnits = ["minutes" => "Minutes", "hour" => "Hours", "days" => "Days"];
$timeAttributes = ["class" => "formInput textBox"];

$timeUnitSelectbox = new SelectBox();
$timeUnitSelectbox->setOptions($timeUnits);
$timeUnitSelectbox->setAttributes($timeAttributes);
$timeUnitSelectbox->setComponentName("tournament_unitbefore");
$timeUnitSelectbox->setComponentValue($emailNotificationInfo['tournament_unit']);

$tournamentUnitSelectBox = $timeUnitSelectbox->getHTML();

$timeUnitSelectbox->setComponentName("event_unitbefore");
$timeUnitSelectbox->setComponentValue($emailNotificationInfo['event_unit']);

$eventUnitSelectBox = $timeUnitSelectbox->getHTML();

$i = 0;
$arrComponents = [
	"privatemessages" => [
		"type" => "section",
		"options" => ["section_title" => "Private Messages:"],
		"sortorder" => $i++
	],
	"pm" => [
		"type" => "select",
		"display_name" => "Receive a PM",
		"options" => [1 => "Yes", "0" => "No"],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"value" => $emailNotificationInfo['privatemessage'],
		"db_name" => "privatemessage"
	],
	"forceemail" => [
		"type" => "select",
		"display_name" => "Block E-mailed PMs",
		"options" => [1 => "Yes", "0" => "No"],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"value" => $emailNotificationInfo['email_privatemessage'],
		"tooltip" => "There is an option to force an e-mail to be sent when sending a private message.  Mark this as yes to not receive e-mailed PMs.",
		"db_name" => "email_privatemessage"
	],
	"tournaments" => [
		"type" => "section",
		"options" => ["section_title" => "Tournament Reminder:"],
		"sortorder" => $i++
	],
	"tournament_time" => [
		"type" => "select",
		"display_name" => "E-mail me",
		"options" => $timeBefore,
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"html" => "<div class='formInput formInputSideComponent'>".$tournamentUnitSelectBox."</div><div class='formInput formInputSideComponent'>before</div>",
		"value" => $emailNotificationInfo['tournament_time'],
		"db_name" => "tournament_time"
	],
	"events" => [
		"type" => "section",
		"options" => ["section_title" => "Event Reminder:"],
		"sortorder" => $i++
	],
	"event_time" => [
		"type" => "select",
		"display_name" => "E-mail me",
		"options" => $timeBefore,
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"html" => "<div class='formInput formInputSideComponent'>".$eventUnitSelectBox."</div><div class='formInput formInputSideComponent'>before</div>",
		"value" => $emailNotificationInfo['event_time'],
		"db_name" => "event_time"
	],
	"forum" => [
		"type" => "section",
		"options" => ["section_title" => "Forum Notifications:", "section_description" => "E-mail me when there is a new post in:"],
		"sortorder" => $i++
	],
	"forumtopics" => [
		"type" => "select",
		"display_name" => "Topics I Started",
		"options" => [1 => "Yes", "0" => "No"],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"value" => $emailNotificationInfo['forum_topic'],
		"db_name" => "forum_topic"
	],
	"forumposts" => [
		"type" => "select",
		"display_name" => "Topics I Posted In",
		"options" => [1 => "Yes", "0" => "No"],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox"],
		"value" => $emailNotificationInfo['forum_post'],
		"db_name" => "forum_post"
	],
	"submit" => [
		"type" => "submit",
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => ["class" => "submitButton formSubmitButton"]
	]
];


$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveObject" => $emailNotification,
	"saveType" => "update",
	"saveMessage" => "Successfully saved e-mail notification settings!",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"description" => "Use the form below to set your e-mail notification settings.",
	"saveAdditional" => ["tournament_unit" => $_POST['tournament_unitbefore'], "event_unit" => "event_unitbefore"]
];
