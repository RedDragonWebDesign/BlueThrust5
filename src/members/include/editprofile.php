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


$cID = $_GET['cID'];

$profileCategoryObj = new ProfileCategory($mysqli);
$profileOptionObj = new ProfileOption($mysqli);
$gameObj = new Game($mysqli);
$arrGames = $gameObj->getGameList();
$consoleCatSettingObj = new Basic($mysqli, "consolecategory", "consolecategory_id");

$arrSocialMediaInfo = $member->objSocial->get_entries([], "ordernum DESC");


// Setup Default Console Category Select Options

$arrPrivileges = $memberRank->get_privileges();
$arrConsoleCats = [];
$consoleSettingObj = new ConsoleOption($mysqli);

foreach ($arrPrivileges as $consoleOptionID) {
	$consoleSettingObj->select($consoleOptionID);
	$consoleCatID = $consoleSettingObj->get_info("consolecategory_id");
	if (!in_array($consoleCatID, $arrConsoleCats)) {
		$consoleCatSettingObj->select($consoleCatID);
		$consoleCatOrder = $consoleCatSettingObj->get_info("ordernum");
		$arrConsoleCats[$consoleCatOrder] = $consoleCatID;
	}
}


krsort($arrConsoleCats);

foreach ($arrConsoleCats as $value) {
	$consoleCatSettingObj->select($value);
	$defaultConsoleOptions[$value] = $consoleCatSettingObj->get_info_filtered("name");
}


// Setup Notification Settings Options
$notificationOptions = ["Show notification with sound", "Show notification without sound", "Don't show notifications"];


// Setup Forum Settings Options
$forumPostsPerPage = [10=>10, 25=>25, 50=>50, 75=>75, 100=>100];


// Setup Birthday
$maxBirthdayYear = date("Y")-8;
$maxDate = mktime(0, 0, 0, 12, 31, $maxBirthdayYear);
$maxBirthdayDate = "new Date(".date("Y", $maxDate).",12,31)";
$defaultBirthdayDate = "";

if ($memberInfo['birthday'] != 0) {
	$bdayDate = new DateTime();
	$bdayDate->setTimestamp($memberInfo['birthday']);
	$bdayDate->setTimezone(new DateTimeZone("UTC"));

	$dispBirthdayDate = $bdayDate->format("M j, Y");//date("M j, Y", $memberInfo['birthday']);
	$defaultBirthdayDate = $dispBirthdayDate;
}



// Signature Filter

function filterSignature() {

	$_POST['wysiwygHTML'] = str_replace("<?", "&lt;?", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("?>", "?&gt;", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("<script", "&lt;script", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("</script>", "&lt;/script&gt;", $_POST['wysiwygHTML']);
}


// Save Custom Values

function saveCustomValues() {
	global $mysqli, $member, $arrGames, $gameMemberObj, $dbprefix, $memberInfo, $arrSocialMediaInfo;

	// Save Custom Profile Options
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."profileoptions ORDER BY sortnum");
	while ($row = $result->fetch_assoc()) {
		$postVal = "custom_".$row['profileoption_id'];
		$member->setProfileValue($row['profileoption_id'], $_POST[$postVal]);
	}

	// Save Social Media Info

	foreach ($arrSocialMediaInfo as $socialMediaInfo) {
		$postVal = "socialmedia_".$socialMediaInfo['social_id'];
		if ($member->objSocial->objSocialMember->selectByMulti(["member_id" => $memberInfo['member_id'], "social_id" => $socialMediaInfo['social_id']])) {
			$arrColumns = ["value"];
			$arrValues = [$_POST[$postVal]];
			$member->objSocial->objSocialMember->update($arrColumns, $arrValues);
		} else {
			$arrColumns = ["social_id", "member_id", "value"];
			$arrValues = [$socialMediaInfo['social_id'], $memberInfo['member_id'], $_POST[$postVal]];
			$member->objSocial->objSocialMember->addNew($arrColumns, $arrValues);
		}
	}

	// Save Games Played

	$mysqli->query("DELETE FROM ".$dbprefix."gamesplayed_members WHERE member_id = '".$memberInfo['member_id']."'");
	$gameMemberObj = new Basic($mysqli, "gamesplayed_members", "gamemember_id");
	foreach ($arrGames as $gameID) {
		$postVal = "game_".$gameID;
		if ($_POST[$postVal] == 1) {
			$gameMemberObj->addNew(["member_id", "gamesplayed_id"], [$memberInfo['member_id'], $gameID]);
		}
	}

	if (!$member->playsGame($_POST['maingame'])) {
		$gameMemberObj->addNew(["member_id", "gamesplayed_id"], [$memberInfo['member_id'], $_POST['maingame']]);
	}
}


$i = 1;
$arrComponents = [
	"submit" => [
		"type" => "submit",
		"sortorder" => 99,
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"value" => "Save"
	],
	"imageinfo" => [
		"type" => "section",
		"options" => ["section_title" => "Image Information"],
		"sortorder" => $i++
	],
	"profilepic" => [
		"display_name" => "Profile Picture",
		"tooltip" => "Appears in your profile and squad profile",
		"type" => "file",
		"sortorder" => $i++,
		"attributes" => ["class" => "textBox", "style" => "width: 100%"],
		"db_name" => "profilepic",
		"options" => ["file_types" => [".gif", ".png", ".jpg", ".bmp"], "default_dimensions" => "150x200 pixels", "file_prefix" => "profile_", "save_loc" => "../images/profile/", "ext_length" => 4, "append_db_value" => "images/profile/"],
		"value" => $memberInfo['profilepic']
	],
	"avatar" => [
		"display_name" => "Avatar",
		"tooltip" => "Appears in your news and forum posts",
		"type" => "file",
		"sortorder" => $i++,
		"attributes" => ["class" => "textBox", "style" => "width: 100%"],
		"db_name" => "avatar",
		"options" => ["file_types" => [".gif", ".png", ".jpg", ".bmp"], "default_dimensions" => "50x50 pixels", "file_prefix" => "avatar_", "save_loc" => "../images/avatar/", "ext_length" => 4, "append_db_value" => "images/avatar/"],
		"value" => $memberInfo['avatar']
	],
	"consolesettings" => [
		"type" => "section",
		"options" => ["section_title" => "Console Settings:"],
		"sortorder" => $i++,
	],
	"defaultconsole" => [
		"type" => "select",
		"display_name" => "Default Console",
		"tooltip" => "Pick the console category that you want automatically selected when viewing the My Account page.",
		"sortorder" => $i++,
		"db_name" => "defaultconsole",
		"attributes" => ["class" => "textBox formInput"],
		"value" => $memberInfo['defaultconsole'],
		"options" => $defaultConsoleOptions,
		"validate" => ["RESTRICT_TO_OPTIONS"]

	],
	"notificationsettings" => [
		"type" => "section",
		"options" => ["section_title" => "Notification Settings:"],
		"sortorder" => $i++,
	],
	"notifications" => [
		"type" => "select",
		"display_name" => "Select",
		"tooltip" => "Notifications will show when you are promoted or awarded a medal etc.  Choose how you want to see theme here.",
		"sortorder" => $i++,
		"db_name" => "notifications",
		"attributes" => ["class" => "textBox formInput"],
		"value" => $memberInfo['notifications'],
		"options" => $notificationOptions,
		"validate" => ["RESTRICT_TO_OPTIONS"]

	],
	"forumsettings" => [
		"type" => "section",
		"options" => ["section_title" => "Forum Settings:"],
		"sortorder" => $i++,
	],
	"topicsperpage" => [
		"type" => "select",
		"display_name" => "Topics Per Page",
		"sortorder" => $i++,
		"db_name" => "topicsperpage",
		"attributes" => ["class" => "textBox formInput"],
		"value" => $memberInfo['topicsperpage'],
		"options" => $forumPostsPerPage,
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"postsperpage" => [
		"type" => "select",
		"display_name" => "Posts Per Page",
		"sortorder" => $i++,
		"db_name" => "postsperpage",
		"attributes" => ["class" => "textBox formInput"],
		"value" => $memberInfo['postsperpage'],
		"options" => $forumPostsPerPage,
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"wysiwygHTML" => [
		"type" => "richtextbox",
		"display_name" => "Signature",
		"attributes" => ["id" => "richTextarea", "style" => "width: 90%", "rows" => "10"],
		"value" => $memberInfo['forumsignature'],
		"sortorder" => $i++,
		"db_name" => "forumsignature",
		"validate" => ["filterSignature"]
	],
	"contactsettings" => [
		"type" => "section",
		"options" => ["section_title" => "Contact/Social Media Information:"],
		"sortorder" => $i++
	],
	"email" => [
		"type" => "text",
		"display_name" => "E-mail",
		"attributes" => ["class" => "textBox formInput"],
		"value" => $memberInfo['email'],
		"sortorder" => $i++,
		"db_name" => "email"
	]

];


// Social Media Info

$arrSocialMediaComponents = [];
$memberSocialInfo = $member->objSocial->getMemberSocialInfo();
foreach ($arrSocialMediaInfo as $socialMediaInfo) {
	$dispSocialMediaValue = (isset($memberSocialInfo[$socialMediaInfo['social_id']])) ? $memberSocialInfo[$socialMediaInfo['social_id']] : "";

	$tempComponentName = "socialmedia_".$socialMediaInfo['social_id'];

	$arrSocialMediaComponents[$tempComponentName] = [
		"type" => "text",
		"display_name" => $socialMediaInfo['name'],
		"tooltip" => $socialMediaInfo['tooltip'],
		"attributes" => ["class" => "textBox formInput"],
		"sortorder" => $i++,
		"value" => $dispSocialMediaValue
	];
}

$arrBirthdayComponents = [
	"birthdaysection" => [
		"type" => "section",
		"options" => ["section_title" => "Birthday:"],
		"sortorder" => $i++
	],
	"birthday" => [
		"type" => "datepicker",
		"sortorder" => $i++,
		"display_name" => "Select Date",
		"attributes" => ["style" => "cursor: pointer", "id" => "jsBirthday", "class" => "textBox formInput"],
		"db_name" => "birthday",
		"value" => ($memberInfo['birthday']*1000),
		"options" => ["changeMonth" => "true",
						   "changeYear" => "true",
						   "dateFormat" => "M d, yy",
						   "minDate" => "new Date(50, 1, 1)",
						   "maxDate" => $maxBirthdayDate,
						   "yearRange" => "1950:".$maxBirthdayYear,
						   "defaultDate" => $defaultBirthdayDate,
						   "altField" => "realBirthday"],
		"validate" => ["NUMBER_ONLY"]
	]

];


if (count($arrGames) > 0) {
	// Setup Games Played Section

	$gamesPlayedOptions = [];
	$mainGameOptions = [];

	$gamesPlayedSection = [
		"gamesplayedsection" => [
			"type" => "section",
			"options" => ["section_title" => "Games Played:"],
			"sortorder" => $i++
		]];

	$mainGamePlayed = [
		"maingame" => [
			"type" => "select",
			"sortorder" => $i++,
			"display_name" => "Main Game",
			"value" => $memberInfo['maingame_id'],
			"db_name" => "maingame_id",
			"attributes" => ["class" => "textBox formInput"]

		]
	];


	foreach ($arrGames as $gameID) {
		$gameObj->select($gameID);

		$mainGameOptions[$gameID] = $gameObj->get_info_filtered("name");

		$gamesPlayedOptions["game_".$gameID] = [
			"type" => "checkbox",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox formInput", "style" => "margin-left: 10px"],
			"value" => $member->playsGame($gameID),
			"options" => ["1" => $gameObj->get_info_filtered("name")]
		];
	}


	$mainGamePlayed['maingame']['options'] = $mainGameOptions;


	$arrComponents = array_merge($arrComponents, $arrSocialMediaComponents, $arrBirthdayComponents, $gamesPlayedSection, $mainGamePlayed, $gamesPlayedOptions);
}



// Set up Custom Profile Options

$customCount = 1;
$arrCustomOptions = [];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$profileCategoryObj->select($row['profilecategory_id']);
	$arrProfileOptions = $profileCategoryObj->getAssociateIDs("ORDER BY sortnum");


	$arrCustomOptions['customsection_'.$customCount] = [
		"type" => "section",
			"options" => ["section_title" => $profileCategoryObj->get_info_filtered("name").":"],
			"sortorder" => $i++
	];

	$customCount++;
	foreach ($arrProfileOptions as $profileOptionID) {
		$profileOptionObj->select($profileOptionID);

		$profileOptionValue = $member->getProfileValue($profileOptionID, true);

		$arrSelectOptions = [];
		if ($profileOptionObj->isSelectOption()) {
			$arrSelectOptions = $profileOptionObj->getSelectValues();
			$inputType = "select";
		} else {
			$inputType = "text";
		}


		$arrCustomOptions["custom_".$profileOptionID] = [
				"display_name" => $profileOptionObj->get_info_filtered("name"),
				"type" => $inputType,
				"attributes" => ["class" => "textBox formInput"],
				"sortorder" => $i++,
				"options" => $arrSelectOptions,
				"value" => $profileOptionValue,
			];
	}
}



$arrComponents = array_merge($arrComponents, $arrCustomOptions);

$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveObject" => $member,
	"saveType" => "update",
	"afterSave" => ["saveCustomValues"],
	"saveMessage" => "Successfully Saved Profile Information!",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"description" => "Use the form below to edit your profile."
];
