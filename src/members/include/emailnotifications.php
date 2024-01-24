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
}
else {
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

$timeBefore = array(0 => "Never");
for ($i=1; $i<=60; $i++) {
	$timeBefore[$i] = $i;
}


//<select class='formInput textBox' name='tournament_unitbefore'><option value='minutes'>Minutes</option><option value='hour'>Hours</option><option value='day'>Days</option></select>

$timeUnits = array("minutes" => "Minutes", "hour" => "Hours", "days" => "Days");
$timeAttributes = array("class" => "formInput textBox");

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
$arrComponents = array(
	"privatemessages" => array(
		"type" => "section",
		"options" => array("section_title" => "Private Messages:"),
		"sortorder" => $i++
	),
	"pm" => array(
		"type" => "select",
		"display_name" => "Receive a PM",
		"options" => array(1 => "Yes", "0" => "No"),
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"value" => $emailNotificationInfo['privatemessage'],
		"db_name" => "privatemessage"
	),
	"forceemail" => array(
		"type" => "select",
		"display_name" => "Block E-mailed PMs",
		"options" => array(1 => "Yes", "0" => "No"),
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"value" => $emailNotificationInfo['email_privatemessage'],
		"tooltip" => "There is an option to force an e-mail to be sent when sending a private message.  Mark this as yes to not receive e-mailed PMs.",
		"db_name" => "email_privatemessage"
	),
	"tournaments" => array(
		"type" => "section",
		"options" => array("section_title" => "Tournament Reminder:"),
		"sortorder" => $i++
	),
	"tournament_time" => array(
		"type" => "select",
		"display_name" => "E-mail me",
		"options" => $timeBefore,
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"html" => "<div class='formInput formInputSideComponent'>".$tournamentUnitSelectBox."</div><div class='formInput formInputSideComponent'>before</div>",
		"value" => $emailNotificationInfo['tournament_time'],
		"db_name" => "tournament_time"
	),
	"events" => array(
		"type" => "section",
		"options" => array("section_title" => "Event Reminder:"),
		"sortorder" => $i++
	),
	"event_time" => array(
		"type" => "select",
		"display_name" => "E-mail me",
		"options" => $timeBefore,
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"html" => "<div class='formInput formInputSideComponent'>".$eventUnitSelectBox."</div><div class='formInput formInputSideComponent'>before</div>",
		"value" => $emailNotificationInfo['event_time'],
		"db_name" => "event_time"
	),
	"forum" => array(
		"type" => "section",
		"options" => array("section_title" => "Forum Notifications:", "section_description" => "E-mail me when there is a new post in:"),
		"sortorder" => $i++
	),
	"forumtopics" => array(
		"type" => "select",
		"display_name" => "Topics I Started",
		"options" => array(1 => "Yes", "0" => "No"),
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"value" => $emailNotificationInfo['forum_topic'],
		"db_name" => "forum_topic"
	),
	"forumposts" => array(
		"type" => "select",
		"display_name" => "Topics I Posted In",
		"options" => array(1 => "Yes", "0" => "No"),
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox"),
		"value" => $emailNotificationInfo['forum_post'],
		"db_name" => "forum_post"
	),
	"submit" => array(
		"type" => "submit",
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => array("class" => "submitButton formSubmitButton")
	)
);


$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveObject" => $emailNotification,
	"saveType" => "update",
	"saveMessage" => "Successfully saved e-mail notification settings!",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
	"description" => "Use the form below to set your e-mail notification settings.",
	"saveAdditional" => array("tournament_unit" => $_POST['tournament_unitbefore'], "event_unit" => "event_unitbefore")
);
