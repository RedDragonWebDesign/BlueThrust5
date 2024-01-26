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

define("MANAGEMENU_FUNCTIONS", true);
require_once(BASE_DIRECTORY."members/include/admin/managemenu/_functions.php");

$menuCatObj = new MenuCategory($mysqli);
$menuItemObj = new MenuItem($mysqli);


$dispError = "";
$countErrors = 0;

$itemTypeOptions = [
	"link" => "Link",
	"image" => "Image",
	"custompage" => "Custom Page",
	"customform" => "Custom Form Page",
	"downloads" => "Download Page",
	"top-players" => "Top Players",
	"shoutbox" => "Shoutbox",
	"forumactivity" => "Latest Forum Activity",
	"newestmembers" => "Newest Members",
	"poll" => "Poll",
	"login" => "Default Login",
	"customcode" => "Custom Block - Code Editor",
	"customformat" => "Custom Block - WYSIWYG Editor"
];

$textAlignOptions = ["left" => "Left", "center" => "Center", "right" => "Right"];


$menuCatOptions = [];
$arrMenuCats = $menuCatObj->get_entries([], "sortnum");
foreach ($arrMenuCats as $menuCatInfo) {
	$menuCatOptions[$menuCatInfo['menucategory_id']] = $menuCatInfo['name'];
}


if (count($arrMenuCats) == 0) {
	echo "
	<div style='display: none' id='errorBox'>
		<p align='center'>
			You must add a menu category before adding any items!
		</p>
	</div>
	
	<script type='text/javascript'>
		popupDialog('Add New Menu Item', '".$MAIN_ROOT."members', 'errorBox');
	</script>
	";

	exit();
}

$selectMenuCat = isset($_GET['mcID']) ? $_GET['mcID'] : "";
$displayOrderOptions = [];
if (isset($_POST['menucategory'])) {
	$arrMenuItems = $menuItemObj->get_entries(["menucategory_id" => $_POST['menucategory']], "sortnum");
	foreach ($arrMenuItems as $eachMenuItem) {
		$displayOrderOptions[$eachMenuItem['menuitem_id']] = $eachMenuItem['name'];
	}

	if (count($displayOrderOptions) == 0) {
		$displayOrderOptions['first'] = "(first item)";
	}
}


$i = 1;
// Link Options
require_once("managemenu/include/linkoptions.php");


// Image Options
require_once("managemenu/include/imageoptions.php");


// Custom Page Options
require_once("managemenu/include/custompageoptions.php");


// Custom Form Page Options
require_once("managemenu/include/customformoptions.php");


// Download Page Options
require_once("managemenu/include/downloadoptions.php");


// Shoutbox Options
require_once("managemenu/include/shoutboxoptions.php");

// Poll Options
require_once("managemenu/include/polloptions.php");


// Custom Code Editor
require_once("managemenu/include/customcodeoptions.php");


// Custom Code Editor - WYSIWYG
require_once("managemenu/include/customcodeformatoptions.php");


// Global Link Options - Target Window, Text Align and Prefix
$globalLinkOptionsNeeded = ["link", "custompage", "customform", "downloads"];
foreach ($globalLinkOptionsNeeded as $optionName) {
	$globalLinkOptions[$optionName] = [
		"targetwindow_".$optionName => [
			"type" => "select",
			"display_name" => "Target Window",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox formInput"],
			"options" => ["" => "Same Window", "_blank" => "New Window"]
		],
		"textalign_".$optionName => [
			"type" => "select",
			"display_name" => "Text-align",
			"attributes" => ["class" => "textBox formInput"],
			"options" => $textAlignOptions,
			"sortorder" => $i++
		],
		"prefix_".$optionName => [
			"type" => "text",
			"display_name" => "Prefix",
			"tooltip" => "Text to display before the link, i.e. a bullet point or dash.",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox formInput"]
		]
	];
}


$linkOptionComponents = array_merge($linkOptionComponents, $globalLinkOptions['link']);
$customPageOptionComponents = array_merge($customPageOptionComponents, $globalLinkOptions['custompage']);
$customFormOptionComponents = array_merge($customFormOptionComponents, $globalLinkOptions['customform']);
$downloadOptionComponents = array_merge($downloadOptionComponents, $globalLinkOptions['downloads']);

$i = 1;
$arrComponents = [

	"generalinfo" => [
		"type" => "section",
		"options" => ["section_title" => "General Information:"],
		"sortorder" => $i++
	],
	"itemname" => [
		"type" => "text",
		"attributes" => ["class" => "textBox formInput"],
		"validate" => ["NOT_BLANK"],
		"db_name" => "name",
		"sortorder" => $i++,
		"display_name" => "Item Name"
	],
	"menucategory" => [
		"type" => "select",
		"display_name" => "Menu Category",
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"db_name" => "menucategory_id",
		"attributes" => ["class" => "textBox formInput", "id" => "menuCats"],
		"options" => $menuCatOptions,
		"value" => $selectMenuCat
	],
	"displayorder" => [
		"type" => "beforeafter",
		"display_name" => "Display Order",
		"attributes" => ["class" => "textBox formInput"],
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS", ["name" => "VALIDATE_ORDER", "set_category" => ($_POST['menucategory'] ?? ''), "orderObject" => $menuItemObj]],
		"db_name" => "sortnum",
		"options" => $displayOrderOptions
	],
	"itemtype" => [
		"type" => "select",
		"display_name" => "Item Type",
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"db_name" => "itemtype",
		"sortorder" => $i++,
		"attributes" => ["class" => "textBox formInput", "id" => "itemType"],
		"options" => $itemTypeOptions
	],
	"accesstype" => [
		"type" => "select",
		"display_name" => "Show when",
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"db_name" => "accesstype",
		"options" => ["Always", "Logged In", "Logged Out"],
		"attributes" => ["class" => "textBox formInput"]
	],
	"hide" => [
		"type" => "checkbox",
		"display_name" => "Hide",
		"attributes" => ["class" => "textBox formInput"],
		"value" => 1,
		"sortorder" => $i++,
		"db_name" => "hide"
	],
	"linkinformation" => [
		"type" => "section",
		"options" => ["section_title" => "Link Information:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "linkOptions"],
		"components" => $linkOptionComponents,
		"validate" => ["validateMenuItem_Links"]
	],
	"imageinformation" => [
		"type" => "section",
		"options" => ["section_title" => "Image Information:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "imageOptions", "style" => "display: none"],
		"components" => $imageOptionComponents,
		"validate" => ["validateMenuItem_Images"]
	],
	"custompageoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Custom Page Options:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "customPageOptions", "style" => "display: none"],
		"components" => $customPageOptionComponents,
		"validate" => [
			[
				"name" =>
				[
					"function" => "validateMenuItem_CustomPageTypes",
					"args" => ["custompage", &$customPageOptionComponents]
				]
			]
		]
	],
	"customformoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Custom Form Options:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "customFormOptions", "style" => "display: none"],
		"components" => $customFormOptionComponents,
		"validate" => [
			[
				"name" =>
				[
					"function" => "validateMenuItem_CustomPageTypes",
					"args" => ["customform", &$customFormOptionComponents]
				]
			]
		]
	],
	"downloadoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Download Page Options:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "downloadLinkOptions", "style" => "display: none"],
		"components" => $downloadOptionComponents,
		"validate" => [
			[
				"name" =>
				[
					"function" => "validateMenuItem_CustomPageTypes",
					"args" => ["downloads", &$downloadOptionComponents]
				]
			]
		]
	],
	"shoutboxoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Shoutbox Information:", "section_description" => "<b><u>NOTE:</u></b> Leave all fields blank to keep the theme's default settings."],
		"sortorder" => $i++,
		"attributes" => ["id" => "shoutBoxOptions", "style" => "display: none"],
		"components" => $shoutboxOptionComponents
	],
	"polloptions" => [
		"type" => "section",
		"options" => ["section_title" => "Poll Options:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "pollOptions", "style" => "display: none"],
		"components" => $pollOptionComponents
	],
	"customcodeoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Menu Item Code:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "customCodeOptions", "style" => "display: none"],
		"components" => $customCodeOptionComponents
	],
	"customformatoptions" => [
		"type" => "section",
		"options" => ["section_title" => "Menu Item Information:"],
		"sortorder" => $i++,
		"attributes" => ["id" => "customFormatOptions", "style" => "display: none"],
		"components" => $customWYSIWYGOptionComponents
	],
	"fakeSubmit" => [
		"type" => "button",
		"attributes" => ["class" => "submitButton formSubmitButton", "id" => "btnFakeSubmit"],
		"value" => "Add Menu Item",
		"sortorder" => $i++
	],
	"submit" => [
		"type" => "submit",
		"value" => "submit",
		"attributes" => ["style" => "display: none", "id" => "btnSubmit"],
		"sortorder" => $i++
	]

];



$arrAfterJS = [];
$arrAfterJS['menuCats'] = "

	$('#menuCats').change(function() {
		$('#displayOrder').html(\"<option value''>Loading...</option>\");
		$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menuitemlist.php', { menuCatID: $('#menuCats').val() }, function(data) {
			$('select[name=displayorder]').html(data);
		});
	});
			
	$('#menuCats').change();

";

$arrItemTypeChangesJS = [
	"linkOptions" => "link",
	"imageOptions" => "image",
	"shoutBoxOptions" => "shoutbox",
	"customPageOptions" => "custompage",
	"customFormOptions" => "customform",
	"customCodeOptions" => "customcode",
	"customFormatOptions" => "customformat",
	"downloadLinkOptions" => "downloads",
	"pollOptions" => "poll"
];

$arrAfterJS['itemType'] = prepareItemTypeChangeJS($arrItemTypeChangesJS);

$arrAfterJS['shoutbox'] = "

$('#shoutBoxWidthPercent').change(function() {
					
	if($(this).val() == '0') {
		$('#shoutBoxTextBoxWidth').html('pixels');
	}
	else {
		$('#shoutBoxTextBoxWidth').html('percent');
	}

});

";


$arrAfterJS['submit'] = "

$('#btnFakeSubmit').click(function() {
	$('#menuCodeEditor_code').val(menuCodeEditor.getValue());
	$('#btnSubmit').click();

});

";

$afterJS = "

	$(document).ready(function() {
	";

foreach ($arrAfterJS as $value) {
	$afterJS .= $value."\n";
}

$afterJS .= "		
	});
	
";




$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to add a menu item.",
	"saveObject" => $menuItemObj,
	"saveMessage" => "Successfully Added New Menu Item: <b>".filterText($_POST['itemname'] ?? '')."</b>!",
	"saveType" => "add",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post", "enctype" => "multipart/form-data"],
	"embedJS" => $afterJS,
	"afterSave" => [
		[
			"function" => "saveMenuItem",
			"args" => [
				&$linkOptionComponents,
				&$menuItemObj->objLink,
				[	"linkurl_link" => "link",
						"targetwindow_link" => "linktarget",
						"textalign_link" => "textalign",
						"prefix_link" => "prefix"],
				"menulink_id",
				"link"]
		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$imageOptionComponents,
				&$menuItemObj->objImage,
				[	"imagefile_image" => "imageurl",
						"width_image" => "width",
						"height_image" => "height",
						"linkurl_image" => "link",
						"targetwindow_image" => "linktarget",
						"textalign_image" => "imagealign"],
				"menuimage_id",
				"image"]
		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$customPageOptionComponents,
				&$menuItemObj->objCustomPage,
				[	"custompage" => "custompage_id",
						"targetwindow_custompage" => "linktarget",
						"textalign_custompage" => "textalign",
						"prefix_custompage" => "prefix"],
				"menucustompage_id",
				"custompage"
			]

		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$customFormOptionComponents,
				&$menuItemObj->objCustomPage,
				[	"customform" => "custompage_id",
						"targetwindow_customform" => "linktarget",
						"textalign_customform" => "textalign",
						"prefix_customform" => "prefix"],
				"menucustompage_id",
				"customform"
			]

		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$downloadOptionComponents,
				&$menuItemObj->objCustomPage,
				[	"downloadpage" => "custompage_id",
						"targetwindow_downloads" => "linktarget",
						"textalign_downloads" => "textalign",
						"prefix_downloads" => "prefix"],
				"menucustompage_id",
				"downloads"
			]

		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$shoutboxOptionComponents,
				&$menuItemObj->objShoutbox,
				[
					"width_shoutbox" => "width",
					"height_shoutbox" => "height",
					"textboxwidth_shoutbox" => "textboxwidth"
				],
				"menushoutbox_id",
				"shoutbox",
				[
					"percentwidth" => ($_POST['widthunit_shoutbox'] ?? ''),
					"percentheight" => ($_POST['heightunit_shoutbox'] ?? '')
				],
			]
		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$customCodeOptionComponents,
				&$menuItemObj->objCustomBlock,
				["customcode" => "code"],
				"menucustomblock_id",
				"customcode",
				["blocktype" => "code"]
			]
		],
		[
			"function" => "saveMenuItem",
			"args" => [
				&$customWYSIWYGOptionComponents,
				&$menuItemObj->objCustomBlock,
				["wysiwygEditor" => "code"],
				"menucustomblock_id",
				"customformat",
				["blocktype" => "format"]
			]
		],
		"savePoll"
	]
];
