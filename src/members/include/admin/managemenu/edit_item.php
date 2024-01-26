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

require_once($prevFolder."classes/btupload.php");
require_once($prevFolder."classes/downloadcategory.php");

$dispError = "";
$countErrors = 0;
$menuCatInfo = $menuCatObj->get_info();


$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb("Manage Menu Items", $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb($menuItemInfo['name']);
echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"".$breadcrumbObj->getBreadcrumb()."\");
});
</script>

";



$displayOrderOptions = [];
if (isset($_POST['menucategory'])) {
	$arrMenuItems = $menuItemObj->get_entries(["menucategory_id" => $_POST['menucategory'], "menuitem_id" => $menuItemInfo['menuitem_id']], "sortnum", true, ["menuitem_id" => "!="]);
	foreach ($arrMenuItems as $eachMenuItem) {
		$displayOrderOptions[$eachMenuItem['menuitem_id']] = $eachMenuItem['name'];
	}

	if (count($displayOrderOptions) == 0) {
		$displayOrderOptions['first'] = "(first item)";
	}
}

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


$itemTypeInclude = [
	"link" => ["file" => "linkoptions.php", "array" => "linkOptionComponents", "sectionname" => "Link Options:"],
	"image" => ["file" => "imageoptions.php", "array" => "imageOptionComponents", "sectionname" => "Image Options:"],
	"custompage" => ["file" => "custompageoptions.php", "array" => "customPageOptionComponents", "sectionname" => "Custom Page Options:"],
	"customform" => ["file" => "customformoptions.php", "array" => "customFormOptionComponents", "sectionname" => "Custom Form Options:"],
	"downloads" => ["file" => "downloadoptions.php", "array" => "downloadOptionComponents", "sectionname" => "Download Page Options:"],
	"poll" => ["file" => "polloptions.php", "array" => "pollOptionComponents", "sectionname" => "Poll Options:"],
	"customcode" => ["file" => "customcodeoptions.php", "array" => "customCodeOptionComponents", "sectionname" => "Menu Item Code:"],
	"customformat" => ["file" => "customcodeformatoptions.php", "array" => "customWYSIWYGOptionComponents", "sectionname" => "Menu Item Information:"]
];

$textAlignOptions = ["left" => "Left", "center" => "Center", "right" => "Right"];


$menuCatOptions = [];
$arrMenuCats = $menuCatObj->get_entries([], "sortnum");
foreach ($arrMenuCats as $menuCatInfo) {
	$menuCatOptions[$menuCatInfo['menucategory_id']] = $menuCatInfo['name'];
}


$menuItemOrder = $menuItemObj->findBeforeAfter();

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
		"display_name" => "Item Name",
		"value" => $menuItemInfo['name']
	],
	"menucategory" => [
		"type" => "select",
		"display_name" => "Menu Category",
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"db_name" => "menucategory_id",
		"attributes" => ["class" => "textBox formInput", "id" => "menuCats"],
		"options" => $menuCatOptions,
		"value" => $menuItemInfo['menucategory_id']
	],
	"displayorder" => [
		"type" => "beforeafter",
		"display_name" => "Display Order",
		"attributes" => ["class" => "textBox formInput", "id" => "displayOrder"],
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS", ["name" => "VALIDATE_ORDER", "set_category" => $_POST['menucategory'], "orderObject" => $menuItemObj, "edit" => true, "edit_ordernum" => $menuItemInfo['sortnum'], "select_back" => $menuItemInfo['menuitem_id']]],
		"db_name" => "sortnum",
		"options" => $displayOrderOptions,
		"before_after_value" => $menuItemOrder[0],
		"after_selected" => $menuItemOrder[1]
	],
	"itemtype" => [
		"type" => "custom",
		"display_name" => "Item Type",
		"sortorder" => $i++,
		"html" => "<div class='formInput'><b>".$itemTypeOptions[$menuItemInfo['itemtype']]."</b></div>"
	],
	"accesstype" => [
		"type" => "select",
		"display_name" => "Show when",
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"db_name" => "accesstype",
		"options" => ["Always", "Logged In", "Logged Out"],
		"attributes" => ["class" => "textBox formInput"],
		"value" => $menuItemInfo['accesstype']
	],
	"hide" => [
		"type" => "checkbox",
		"display_name" => "Hide",
		"attributes" => ["class" => "textBox formInput"],
		"value" => 1,
		"sortorder" => $i++,
		"db_name" => "hide",
		"checked" => ($menuItemInfo['hide'] == 1) ? true : false
	]
];


$arrExtraComponentSection = [];
$arrExtraComponents = "";

foreach ($itemTypeInclude as $key => $itemTypeInfo) {
	if ($key == $menuItemInfo['itemtype']) {
		require_once("include/".$itemTypeInfo['file']);

		$arrExtraComponentSection['extra_info'] = [
			"type" => "section",
			"options" => ["section_title" => $itemTypeInfo['sectionname'], "section_description" => $itemTypeInfo['sectioninfo']],
			"sortorder" => $i++
		];


		$arrExtraComponents = ${$itemTypeInfo['array']};
	}
}

$globalLinkOptionsNeeded = ["link", "custompage", "customform", "downloads"];

if (in_array($menuItemInfo['itemtype'], $globalLinkOptionsNeeded)) {
	$optionName = $menuItemInfo['itemtype'];

	$globalLinkOptions = [
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

	$arrExtraComponents = array_merge($arrExtraComponents, $globalLinkOptions);
}

$arrAfterSave = "";
switch ($menuItemInfo['itemtype']) {
	case "link":
		$menuItemObj->objLink->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objLink->get_info_filtered();
		$arrExtraComponents['linkurl_link']['value'] = $menuItemExtraInfo['link'];
		$arrExtraComponents['targetwindow_link']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_link']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_link']['value'] = $menuItemExtraInfo['prefix'];

		$arrAfterSave = [[
			"function" => "saveMenuItem",
			"args" => [
				&$arrExtraComponents,
				&$menuItemObj->objLink,
				[	"linkurl_link" => "link",
						"targetwindow_link" => "linktarget",
						"textalign_link" => "textalign",
						"prefix_link" => "prefix"],
				"menulink_id",
				"link", [], "update"]
			]];

		break;
	case "image":
		$menuItemObj->objImage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objImage->get_info_filtered();
		$arrExtraComponents['imagefile_image']['value'] = $menuItemExtraInfo['imageurl'];
		$arrExtraComponents['width_image']['value'] = $menuItemExtraInfo['width'];
		$arrExtraComponents['height_image']['value'] = $menuItemExtraInfo['height'];
		$arrExtraComponents['linkurl_image']['value'] = $menuItemExtraInfo['link'];
		$arrExtraComponents['targetwindow_image']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_image']['value'] = $menuItemExtraInfo['imagealign'];

		$arrAfterSave = [
			[
			"function" => "saveMenuItem",
			"args" => [
				&$arrExtraComponents,
				&$menuItemObj->objImage,
				[	"imagefile_image" => "imageurl",
						"width_image" => "width",
						"height_image" => "height",
						"linkurl_image" => "link",
						"targetwindow_image" => "linktarget",
						"textalign_image" => "imagealign"],
				"menuimage_id",
				"image", [], "update"]
			]];

		break;
	case "custompage":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['custompage']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_custompage']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_custompage']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_custompage']['value'] = $menuItemExtraInfo['prefix'];

		$arrAfterSave = [
			[
			"function" => "saveMenuItem",
			"args" => [
				&$arrExtraComponents,
				&$menuItemObj->objCustomPage,
				[	"custompage" => "custompage_id",
						"targetwindow_custompage" => "linktarget",
						"textalign_custompage" => "textalign",
						"prefix_custompage" => "prefix"],
				"menucustompage_id",
				"custompage", [], "update"
			]
			]];

		break;
	case "customform":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['customform']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_customform']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_customform']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_customform']['value'] = $menuItemExtraInfo['prefix'];

		$arrAfterSave = [
			[
			"function" => "saveMenuItem",
			"args" => [
				&$arrExtraComponents,
				&$menuItemObj->objCustomPage,
				[	"customform" => "custompage_id",
						"targetwindow_customform" => "linktarget",
						"textalign_customform" => "textalign",
						"prefix_customform" => "prefix"],
				"menucustompage_id",
				"customform", [], "update"
			]
			]];

		break;
	case "downloads":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['downloadpage']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_downloads']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_downloads']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_downloads']['value'] = $menuItemExtraInfo['prefix'];

		$arrAfterSave = [
			[
				"function" => "saveMenuItem",
				"args" => [
					&$arrExtraComponents,
					&$menuItemObj->objCustomPage,
					[	"downloadpage" => "custompage_id",
							"targetwindow_downloads" => "linktarget",
							"textalign_downloads" => "textalign",
							"prefix_downloads" => "prefix"],
					"menucustompage_id",
					"downloads", [], "update"
				]
			]
		];

		break;
	case "shoutbox":
		$menuItemObj->objShoutbox->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objShoutbox->get_info_filtered();

		$shoutboxWidthPercentSelected = ($menuItemExtraInfo['percentwidth'] == 1) ? " selected" : "";
		$shoutboxHeightPercentSelected = ($menuItemExtraInfo['percentheight'] == 1) ? " selected" : "";

		require_once("include/shoutboxoptions.php");

		$arrExtraComponentSection['extra_info'] = [
			"type" => "section",
			"options" => ["section_title" => "Shoutbox Information:", "section_description" => "<b><u>NOTE:</u></b> Leave all fields blank to keep the theme's default settings."],
			"sortorder" => $i++
		];


		$arrExtraComponents = $shoutboxOptionComponents;


		$arrExtraComponents['width_shoutbox']['value'] = $menuItemExtraInfo['width'];
		$arrExtraComponents['height_shoutbox']['value'] = $menuItemExtraInfo['height'];
		$arrExtraComponents['textboxwidth_shoutbox']['value'] = $menuItemExtraInfo['textboxwidth'];

		$arrAfterSave = [

			[
				"function" => "saveMenuItem",
				"args" => [
					&$arrExtraComponents,
					&$menuItemObj->objShoutbox,
					[	"width_shoutbox" => "width",
							"height_shoutbox" => "height",
							"textboxwidth_shoutbox" => "textboxwidth"],
					"menushoutbox_id",
					"shoutbox",
					["percentwidth" => $_POST['widthunit_shoutbox'], "percentheight" => $_POST['heightunit_shoutbox']],
					"update"
				]

			]

		];

		break;
	case "poll":
		$arrExtraComponents['poll']['value'] = $menuItemInfo['itemtype_id'];
		$arrAfterSave = ["savePoll"];

		break;
	case "customcode":
		$menuItemObj->objCustomBlock->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomBlock->get_info_filtered();
		$arrExtraComponents['customcode']['value'] = $menuItemExtraInfo['code'];

		$arrAfterSave = [
			[
				"function" => "saveMenuItem",
				"args" => [
					&$arrExtraComponents,
					&$menuItemObj->objCustomBlock,
					["customcode" => "code"],
					"menucustomblock_id",
					"customcode",
					["blocktype" => "code"],
					"update"
				]
			]

		];

		break;
	case "customformat":
		$menuItemObj->objCustomBlock->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomBlock->get_info_filtered();
		$arrExtraComponents['wysiwygEditor']['value'] = $menuItemExtraInfo['code'];


		$arrAfterSave = [

			[
				"function" => "saveMenuItem",
				"args" => [
					&$arrExtraComponents,
					&$menuItemObj->objCustomBlock,
					["wysiwygEditor" => "code"],
					"menucustomblock_id",
					"customformat",
					["blocktype" => "format"],
					"update"
				]
			]
		];

		break;
}

if (is_array($arrExtraComponents)) {
	$arrExtraComponentSection['extra_info']['components'] = $arrExtraComponents;
}

$arrComponents = array_merge($arrComponents, $arrExtraComponentSection);

$submitButtonArray = [

	"fakeSubmit" => [
		"type" => "button",
		"attributes" => ["class" => "submitButton formSubmitButton", "id" => "btnFakeSubmit"],
		"value" => "Edit Menu Item",
		"sortorder" => $i++
	],
	"submit" => [
		"type" => "submit",
		"value" => "submit",
		"attributes" => ["style" => "display: none", "id" => "btnSubmit"],
		"sortorder" => $i++
	]
];

$arrComponents = array_merge($arrComponents, $submitButtonArray);

$afterJS = "


	$(document).ready(function() {
	
		$('#menuCats').change(function() {
			$('select[name=displayorder]').html(\"<option value''>Loading...</option>\");
			$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menuitemlist.php', { menuCatID: $('#menuCats').val(), itemID: '".$menuItemInfo['menuitem_id']."' }, function(data) {
				$('select[name=displayorder]').html(data);
			});
		});
		
		$('#menuCats').change();
		
		$('#btnFakeSubmit').click(function() {
	";

if ($menuItemInfo['itemtype'] == "customcode") {
	$afterJS .= "$('#menuCodeEditor_code').val(menuCodeEditor.getValue());";
}


$afterJS .= "
	$('#btnSubmit').click();

});";

if ($menuItemInfo['itemtype'] == "shoutbox") {
	$afterJS .= "
	
		$('#shoutBoxWidthPercent').change(function() {
					
			if($(this).val() == '0') {
				$('#shoutBoxTextBoxWidth').html('pixels');
			}
			else {
				$('#shoutBoxTextBoxWidth').html('percent');
			}
		
		});
		
		$('#shoutBoxWidthPercent').change();
	";
}


$afterJS .= "});";

$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to edit the selected menu item.",
	"saveObject" => $menuItemObj,
	"saveMessage" => "Successfully Saved Menu Item: <b>".filterText($_POST['itemname'])."</b>!",
	"saveType" => "update",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID."&menuID=".$menuItemInfo['menuitem_id']."&action=edit", "method" => "post", "enctype" => "multipart/form-data"],
	"afterSave" => $arrAfterSave,
	"embedJS" => $afterJS
];

$_POST['itemtype'] = $menuItemInfo['itemtype'];
define("MANAGEMENU_FUNCTIONS", true);
require_once("_functions.php");
