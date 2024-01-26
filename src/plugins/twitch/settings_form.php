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


if (!isset($pluginObj)) {
	exit();
}

$configInfo = $pluginObj->getConfigInfo();


$selectedSocialID = "";
$addTwitchInfo = "<div class='formInput formInputSideText'><img id='addTwitchLoading' src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif' style='width: 18px; height: 18px; margin: 0px 5px; display: none'> <a id='addTwitch' href='javascript:void(0)'>Haven't added Twitch yet? Click Here!</a></div>";
$socialObj = new Social($mysqli);
$result = $mysqli->query("SELECT * FROM ".$dbprefix."social WHERE name LIKE '%Twitch%'");
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$selectedSocialID = $row['social_id'];
	$addTwitchInfo = "";
}

$result = $mysqli->query("SELECT social_id,name FROM ".$dbprefix."social ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$arrSocialOptions[$row['social_id']] = $row['name'];
}

if ($configInfo['twitchsocial_id'] != "") {
	$selectedSocialID = $configInfo['twitchsocial_id'];
}

// Default values

$configInfo['stream_width'] = ($configInfo['stream_width'] == "") ? 640 : $configInfo['stream_width'];
$configInfo['stream_height'] = ($configInfo['stream_height'] == "") ? 360 : $configInfo['stream_height'];
$configInfo['streamchat_height'] = ($configInfo['streamchat_height'] == "") ? 300 : $configInfo['streamchat_height'];


$i=0;

$arrComponents = [
	"pagelink" => [
		"type" => "custom",
		"sortorder" => $i++,
		"display_name" => "Twitch Page Link",
		"html" => "<div class='formInput main'><a href='".$MAIN_ROOT."plugins/twitch' target='_blank'>".FULL_SITE_URL."plugins/twitch</a></div>",
		"tooltip" => "Add this link as a menu item if you would like to show who streams in your clan."
	],
	"twitchsocial_id" => [
		"type" => "select",
		"sortorder" => $i++,
		"display_name" => "Social Media",
		"attributes" => ["class" => "formInput textBox", "id" => "twitchsocial_id"],
		"options" => $arrSocialOptions,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"value" => $selectedSocialID,
		"tooltip" => "This is a list of your social media icons that have been added to the site.  Please select the one that is associated with Twitch to configure correctly.",
		"html" => $addTwitchInfo
	],
	"stream_width" => [
		"type" => "text",
		"sortorder" => $i++,
		"display_name" => "Stream Width",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"validate" => ["POSITIVE_NUMBER"],
		"html" => "<div class='formInput formInputSideText'>px</div>",
		"value" => $configInfo['stream_width']
	],
	"stream_height" => [
		"type" => "text",
		"sortorder" => $i++,
		"display_name" => "Stream Height",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"validate" => ["POSITIVE_NUMBER"],
		"html" => "<div class='formInput formInputSideText'>px</div>",
		"value" => $configInfo['stream_height']
	],
	"streamchat_height" => [
		"type" => "text",
		"sortorder" => $i++,
		"display_name" => "Stream Chat Height",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"validate" => ["POSITIVE_NUMBER"],
		"html" => "<div class='formInput formInputSideText'>px</div>",
		"value" => $configInfo['streamchat_height']
	],
	"autoplay" => [
		"type" => "select",
		"sortorder" => $i++,
		"display_name" => "Auto-Play Stream",
		"options" => ["1" => "Yes", "0" => "No"],
		"attributes" => ["class" => "formInput textBox"],
		"value" => $configInfo['autoplay']
	],
	"autohidechat" => [
		"type" => "select",
		"sortorder" => $i++,
		"display_name" => "Auto-Hide Chat",
		"options" => ["1" => "Yes", "0" => "No"],
		"attributes" => ["class" => "formInput textBox"],
		"value" => $configInfo['autoshowchat']
	],
	"submit" => [
		"type" => "submit",
		"sortorder" => $i++,
		"attributes" => ["class" => "formSubmitButton submitButton"],
		"value" => "Save"
	],
	"custom" => [
		"type" => "custom",
		"sortorder" => $i++,
		"html" => "<div id='addTwitchJSDump'></div>"
	]
];

$additionalTwitchInfo = "";
$embedJS = "";
if ($addTwitchInfo != "") {
	$addSocialMediaConsole = new ConsoleOption($mysqli);
	$addSocialMediaConsole->findConsoleIDByName("Add Social Media Icon");
	$additionalTwitchInfo = " If you haven't <a href='".$addSocialMediaConsole->getLink()."'>added</a> a Twitch social media icon to user profiles, you can click the \"Haven't added Twitch?\" link below.";

	$embedJS = "

		$(document).ready(function() {
	
			$('#addTwitch').click(function() {
				$('#addTwitch').hide();
				$('#addTwitchLoading').show();
				$.post('".$MAIN_ROOT."plugins/twitch/include/addtwitch.php', { }, function(data) {
					$('#addTwitchJSDump').html(data);
				});
	
			});
	
		});
	
	";
}



$setupFormArgs = [
	"name" => "pluginsettings-".$_GET['plugin'],
	"components" => $arrComponents,
	"description" => "Fill out the form below to configure the Twitch plugin.  This plugin uses the Twitch username entered on user's profiles.  Any member who enters their Twitch name in their profile will appear on the <a href='".$MAIN_ROOT."plugins/twitch' target='_blank'>Twitch Page</a>.".$additionalTwitchInfo,
	"attributes" => ["action" => $MAIN_ROOT."plugins/settings.php?plugin=".$_GET['plugin'], "method" => "post"],
	"afterSave" => ["saveTwitchSettings"],
	"saveMessage" => "Twitch Settings Saved!",
	"saveLink" => $MAIN_ROOT."members/console.php?cID=".$cID,
	"embedJS" => $embedJS
];

function saveTwitchSettings() {
	global $pluginObj;

	$pluginObj->addConfigValue("twitchsocial_id", $_POST['twitchsocial_id']);
	$pluginObj->addConfigValue("stream_width", $_POST['stream_width']);
	$pluginObj->addConfigValue("stream_height", $_POST['stream_height']);
	$pluginObj->addConfigValue("streamchat_height", $_POST['streamchat_height']);
	$pluginObj->addConfigValue("autoplay", $_POST['autoplay']);
	$pluginObj->addConfigValue("autohidechat", $_POST['autohidechat']);
}
