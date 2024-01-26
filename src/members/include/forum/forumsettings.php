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
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];

$arrTopicsPerPage = [10, 25, 50, 75, 100];


$pixelPercentBox = new SelectBox();
$pixelPercentBox->setOptions(["px" => "px", "%" => "%"]);
$pixelPercentBox->setAttributes(["class" => "textBox"]);



$i=1;
$arrComponents = [
	"generalsettings" => [
		"type" => "section",
		"options" => ["section_title" => "General Settings"],
		"sortorder" => $i++
	],
	"defaulttopics" => [
		"type" => "select",
		"display_name" => "Default Topics Per Page",
		"attributes" => ["class" => "formInput textBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"value" => $websiteInfo['forum_topicsperpage'],
		"sortorder" => $i++,
		"db_name" => "forum_topicsperpage"
	],
	"defaultposts" => [
		"type" => "select",
		"display_name" => "Default Posts Per Page",
		"attributes" => ["class" => "formInput textBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"value" => $websiteInfo['forum_postsperpage'],
		"sortorder" => $i++,
		"db_name" => "forum_postsperpage"
	],
	"avatarwidth" => [
		"type" => "text",
		"display_name" => "Avatar Width",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_avatarwidth'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("avatarwidthunit", $websiteInfo['forum_avatarwidthunit'])."</div>",
		"db_name" => "forum_avatarwidth"
	],
	"avatarheight" => [
		"type" => "text",
		"display_name" => "Avatar Height",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_avatarheight'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("avatarheightunit", $websiteInfo['forum_avatarheightunit'])."</div>",
		"db_name" => "forum_avatarheight"
	],
	"newindicator" => [
		"type" => "text",
		"display_name" => "New Indicator",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "medalCount"],
		"value" => $websiteInfo['forum_newindicator'],
		"sortorder" => $i++,
		"tooltip" => "Enter the number of days for the new indicator to appear for unread posts. Set to 0 to always show new indicator.",
		"db_name" => "forum_newindicator"
	],
	"imagesettings" => [
		"type" => "section",
		"options" => ["section_title" => "Image Display Settings"],
		"sortorder" => $i++
	],
	"imagewidth" => [
		"type" => "text",
		"display_name" => "Max Image Width",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_imagewidth'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("imagewidthunit", $websiteInfo['forum_imagewidthunit'])."</div>",
		"db_name" => "forum_imagewidth"
	],
	"imageheight" => [
		"type" => "text",
		"display_name" => "Max Image Height",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_imageheight'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("imageheightunit", $websiteInfo['forum_imageheightunit'])."</div>",
		"db_name" => "forum_imageheight"
	],
	"linkimages" => [
		"type" => "checkbox",
		"display_name" => "Auto Link Images",
		"attributes" => ["class" => "formInput"],
		"value" => 1,
		"checked" => ($websiteInfo['forum_linkimages'] == 1),
		"sortorder" => $i++,
		"tooltip" => "Auto link images to view full size.",
		"db_name" => "forum_linkimages"
	],
	"signaturesettings" => [
		"type" => "section",
		"options" => ["section_title" => "Signature Display Settings"],
		"sortorder" => $i++
	],
	"sigwidth" => [
		"type" => "text",
		"display_name" => "Max Width",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_sigwidth'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("sigwidthunit", $websiteInfo['forum_sigwidthunit'])."</div>",
		"db_name" => "forum_sigwidth"
	],
	"sigheight" => [
		"type" => "text",
		"display_name" => "Max Height",
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_sigheight'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("sigheightunit", $websiteInfo['forum_sigheightunit'])."</div>",
		"db_name" => "forum_sigheight"
	],
	"hidesig" => [
		"type" => "checkbox",
		"display_name" => "Hide Signatures",
		"attributes" => ["class" => "formInput"],
		"value" => 1,
		"checked" => ($websiteInfo['forum_hidesignatures'] == 1),
		"sortorder" => $i++,
		"db_name" => "forum_hidesignatures"
	],
	"ranksettings" => [
		"type" => "section",
		"options" => ["section_title" => "Rank Display Settings"],
		"sortorder" => $i++
	],
	"showrank" => [
		"type" => "checkbox",
		"display_name" => "Show Rank",
		"attributes" => ["class" => "formInput", "id" => "showRank"],
		"value" => 1,
		"checked" => ($websiteInfo['forum_showrank'] == 1),
		"sortorder" => $i++,
		"tooltip" => "Check the box to the right to show a member's rank below their post count on forum posts.",
		"db_name" => "forum_showrank"
	],
	"rankwidth" => [
		"type" => "text",
		"display_name" => "Rank Width",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "rankWidth"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_rankwidth'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("rankwidthunit", $websiteInfo['forum_rankwidthunit'], ["id" => "rankWidthUnit", "class" => "textBox"])."</div>",
		"db_name" => "forum_rankwidth"
	],
	"rankheight" => [
		"type" => "text",
		"display_name" => "Rank Height",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "rankHeight"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_rankheight'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("rankheightunit", $websiteInfo['forum_rankheightunit'], ["id" => "rankHeightUnit", "class" => "textBox"])."</div>",
		"db_name" => "forum_rankheight"
	],
	"medalsettings" => [
		"type" => "section",
		"options" => ["section_title" => "Medal Display Settings"],
		"sortorder" => $i++
	],
	"showmedals" => [
		"type" => "checkbox",
		"display_name" => "Show Medals",
		"attributes" => ["class" => "formInput", "id" => "showMedals"],
		"value" => 1,
		"checked" => ($websiteInfo['forum_showrank'] == 1),
		"sortorder" => $i++,
		"tooltip" => "Check the box to the right to list a member's medals below their post count on forum posts.",
		"db_name" => "forum_showrank"
	],
	"medalwidth" => [
		"type" => "text",
		"display_name" => "Medal Width",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "rankWidth"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_medalwidth'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("medalwidthunit", $websiteInfo['forum_medalwidthunit'], ["id" => "medalWidthUnit", "class" => "textBox"])."</div>",
		"db_name" => "forum_medalwidth"
	],
	"medalheight" => [
		"type" => "text",
		"display_name" => "Medal Height",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "rankHeight"],
		"options" => $arrTopicsPerPage,
		"validate" => ["NOT_BLANK", "NUMERIC_ONLY", "POSITIVE_NUMBER"],
		"value" => $websiteInfo['forum_medalheight'],
		"sortorder" => $i++,
		"html" => "<div class='formInput formInputSideComponent'>".$pixelPercentBox->getHTML("medalheightunit", $websiteInfo['forum_medalheightunit'], ["id" => "medalHeightUnit", "class" => "textBox"])."</div>",
		"db_name" => "forum_medalheight"
	],
	"medalcount" => [
		"type" => "text",
		"display_name" => "Medal Count",
		"attributes" => ["class" => "formInput textBox smallTextBox", "id" => "medalCount"],
		"value" => $websiteInfo['forum_medalcount'],
		"sortorder" => $i++,
		"tooltip" => "Use this field to set how many medal's to show.  If left blank, 5 medals will show.",
		"db_name" => "forum_medalcount"
	],
	"submit" => [
		"type" => "submit",
		"value" => "Save",
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"sortorder" => $i++
	]

];


$embedJS = "
		function clickShowRank() {
			if($('#showRank').is(':checked')) {
				$('#rankWidth').removeAttr('disabled');
				$('#rankHeight').removeAttr('disabled');
				$('#rankWidthUnit').removeAttr('disabled');
				$('#rankHeightUnit').removeAttr('disabled');
			}
			else {
				$('#rankWidth').attr('disabled', 'disabled');
				$('#rankHeight').attr('disabled', 'disabled');
				$('#rankWidthUnit').attr('disabled', 'disabled');
				$('#rankHeightUnit').attr('disabled', 'disabled');
			}
		}
		
		
		function clickShowMedals() {
			if($('#showMedals').is(':checked')) {
				$('#medalWidth').removeAttr('disabled');
				$('#medalHeight').removeAttr('disabled');
				$('#medalWidthUnit').removeAttr('disabled');
				$('#medalHeightUnit').removeAttr('disabled');
				$('#medalCount').removeAttr('disabled');
			}
			else {
				$('#medalWidth').attr('disabled', 'disabled');
				$('#medalHeight').attr('disabled', 'disabled');
				$('#medalWidthUnit').attr('disabled', 'disabled');
				$('#medalHeightUnit').attr('disabled', 'disabled');
				$('#medalCount').attr('disabled', 'disabled');
			}
		}
	
	
		$(document).ready(function() {
		
			$('#showRank').click(function() {
			
				clickShowRank();
			
			});
			
			$('#showMedals').click(function() {
			
				clickShowMedals();
			
			});
			
		});
	
		clickShowMedals();
		clickShowRank();

";


$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveObject" => $webInfoObj,
	"saveType" => "multiUpdate",
	"saveMessage" => "Successfully saved forum settings!",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"description" => "Use the form below to modify your forum's settings.",
	"beforeAfter" => true,
	"embedJS" => $embedJS
];
