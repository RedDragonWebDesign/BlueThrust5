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
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];

define("MANAGEMENU_FUNCTIONS", true);
include(BASE_DIRECTORY."members/include/admin/managemenu/_functions.php");

$menuCatObj = new MenuCategory($mysqli);
$menuItemObj = new MenuItem($mysqli);


$dispError = "";
$countErrors = 0;

$itemTypeOptions = array(
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
);

$textAlignOptions = array("left" => "Left", "center" => "Center", "right" => "Right");


$menuCatOptions = array();
$arrMenuCats = $menuCatObj->get_entries(array(), "sortnum");
foreach($arrMenuCats as $menuCatInfo) {
	$menuCatOptions[$menuCatInfo['menucategory_id']] = $menuCatInfo['name'];
}


if(count($arrMenuCats) == 0) {
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
$displayOrderOptions = array();
if(isset($_POST['menucategory'])) {
	$arrMenuItems = $menuItemObj->get_entries(array("menucategory_id" => $_POST['menucategory']), "sortnum");
	foreach($arrMenuItems as $eachMenuItem) {
		$displayOrderOptions[$eachMenuItem['menuitem_id']] = $eachMenuItem['name'];
	}

	if(count($displayOrderOptions) == 0) {
		$displayOrderOptions['first'] = "(first item)";	
	}
}


$i = 1;
// Link Options
include("managemenu/include/linkoptions.php");


// Image Options
include("managemenu/include/imageoptions.php");


// Custom Page Options
include("managemenu/include/custompageoptions.php");


// Custom Form Page Options
include("managemenu/include/customformoptions.php");


// Download Page Options
include("managemenu/include/downloadoptions.php");


// Shoutbox Options
include("managemenu/include/shoutboxoptions.php");

// Poll Options
include("managemenu/include/polloptions.php");


// Custom Code Editor
include("managemenu/include/customcodeoptions.php");


// Custom Code Editor - WYSIWYG
include("managemenu/include/customcodeformatoptions.php");


// Global Link Options - Target Window, Text Align and Prefix
$globalLinkOptionsNeeded = array("link", "custompage", "customform", "downloads");
foreach($globalLinkOptionsNeeded as $optionName) {
	$globalLinkOptions[$optionName] = array(
		"targetwindow_".$optionName => array(
			"type" => "select",
			"display_name" => "Target Window",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox formInput"),
			"options" => array("" => "Same Window", "_blank" => "New Window")
		),
		"textalign_".$optionName => array(
			"type" => "select",
			"display_name" => "Text-align",
			"attributes" => array("class" => "textBox formInput"),
			"options" => $textAlignOptions,
			"sortorder" => $i++
		),
		"prefix_".$optionName => array(
			"type" => "text",
			"display_name" => "Prefix",
			"tooltip" => "Text to display before the link, i.e. a bullet point or dash.",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox formInput")
		)
	);
}


$linkOptionComponents = array_merge($linkOptionComponents, $globalLinkOptions['link']);
$customPageOptionComponents = array_merge($customPageOptionComponents, $globalLinkOptions['custompage']);
$customFormOptionComponents = array_merge($customFormOptionComponents, $globalLinkOptions['customform']);
$downloadOptionComponents = array_merge($downloadOptionComponents, $globalLinkOptions['downloads']);

$i = 1;
$arrComponents = array(

	"generalinfo" => array(
		"type" => "section",
		"options" => array("section_title" => "General Information:"),
		"sortorder" => $i++
	),
	"itemname" => array(
		"type" => "text",
		"attributes" => array("class" => "textBox formInput"),
		"validate" => array("NOT_BLANK"),
		"db_name" => "name",
		"sortorder" => $i++,
		"display_name" => "Item Name"
	),
	"menucategory" => array(
		"type" => "select",
		"display_name" => "Menu Category",
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "menucategory_id",
		"attributes" => array("class" => "textBox formInput", "id" => "menuCats"),
		"options" => $menuCatOptions,
		"value" => $selectMenuCat
	),
	"displayorder" => array(
		"type" => "beforeafter",
		"display_name" => "Display Order",
		"attributes" => array("class" => "textBox formInput"),
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS", array("name" => "VALIDATE_ORDER", "set_category" => $_POST['menucategory'], "orderObject" => $menuItemObj)),
		"db_name" => "sortnum",
		"options" => $displayOrderOptions
	),
	"itemtype" => array(
		"type" => "select",
		"display_name" => "Item Type",
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "itemtype",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "id" => "itemType"),
		"options" => $itemTypeOptions
	),
	"accesstype" => array(
		"type" => "select",
		"display_name" => "Show when",
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "accesstype",
		"options" => array("Always", "Logged In"),
		"attributes" => array("class" => "textBox formInput")
	),
	"hide" => array(
		"type" => "checkbox",
		"display_name" => "Hide",
		"attributes" => array("class" => "textBox formInput"),
		"value" => 1,
		"sortorder" => $i++,
		"db_name" => "hide"
	),
	"linkinformation" => array(
		"type" => "section",
		"options" => array("section_title" => "Link Information:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "linkOptions"),
		"components" => $linkOptionComponents,
		"validate" => array("validateMenuItem_Links")
	),
	"imageinformation" => array(
		"type" => "section",
		"options" => array("section_title" => "Image Information:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "imageOptions", "style" => "display: none"),
		"components" => $imageOptionComponents,
		"validate" => array("validateMenuItem_Images")
	),
	"custompageoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Custom Page Options:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "customPageOptions", "style" => "display: none"),
		"components" => $customPageOptionComponents,
		"validate" => array(
			array(
				"name" => 
				array(
					"function" => "validateMenuItem_CustomPageTypes", 
					"args" => array("custompage", &$customPageOptionComponents)
				)
			)
		)
	),
	"customformoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Custom Form Options:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "customFormOptions", "style" => "display: none"),
		"components" => $customFormOptionComponents,
		"validate" => array(
			array(
				"name" => 
				array(
					"function" => "validateMenuItem_CustomPageTypes", 
					"args" => array("customform", &$customFormOptionComponents)
				)
			)
		)
	),
	"downloadoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Download Page Options:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "downloadLinkOptions", "style" => "display: none"),
		"components" => $downloadOptionComponents,
		"validate" => array(
			array(
				"name" => 
				array(
					"function" => "validateMenuItem_CustomPageTypes", 
					"args" => array("downloads", &$downloadOptionComponents)
				)
			)
		)
	),
	"shoutboxoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Shoutbox Information:", "section_description" => "<b><u>NOTE:</u></b> Leave all fields blank to keep the theme's default settings."),
		"sortorder" => $i++,
		"attributes" => array("id" => "shoutBoxOptions", "style" => "display: none"),
		"components" => $shoutboxOptionComponents
	),
	"polloptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Poll Options:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "pollOptions", "style" => "display: none"),
		"components" => $pollOptionComponents
	),
	"customcodeoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Menu Item Code:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "customCodeOptions", "style" => "display: none"),
		"components" => $customCodeOptionComponents
	),
	"customformatoptions" => array(
		"type" => "section",
		"options" => array("section_title" => "Menu Item Information:"),
		"sortorder" => $i++,
		"attributes" => array("id" => "customFormatOptions", "style" => "display: none"),
		"components" => $customWYSIWYGOptionComponents
	),
	"fakeSubmit" => array(
		"type" => "button",
		"attributes" => array("class" => "submitButton formSubmitButton", "id" => "btnFakeSubmit"),
		"value" => "Add Menu Item",
		"sortorder" => $i++
	),
	"submit" => array(
		"type" => "submit",
		"value" => "submit",
		"attributes" => array("style" => "display: none", "id" => "btnSubmit"),
		"sortorder" => $i++
	)

);



$arrAfterJS = array();
$arrAfterJS['menuCats'] = "

	$('#menuCats').change(function() {
		$('#displayOrder').html(\"<option value''>Loading...</option>\");
		$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menuitemlist.php', { menuCatID: $('#menuCats').val() }, function(data) {
			$('select[name=displayorder]').html(data);
		});
	});
			
	$('#menuCats').change();

";

$arrItemTypeChangesJS = array(
	"linkOptions" => "link",
	"imageOptions" => "image",
	"shoutBoxOptions" => "shoutbox",
	"customPageOptions" => "custompage",
	"customFormOptions" => "customform",
	"customCodeOptions" => "customcode",
	"customFormatOptions" => "customformat",
	"downloadLinkOptions" => "downloads",
	"pollOptions" => "poll"
);

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
		
	foreach($arrAfterJS as $value) {
		
		$afterJS .= $value."\n";	
		
	}
		
$afterJS .= "		
	});
	
";




$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to add a menu item.",
	"saveObject" => $menuItemObj,
	"saveMessage" => "Successfully Added New Menu Item: <b>".filterText($_POST['itemname'])."</b>!",
	"saveType" => "add",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post", "enctype" => "multipart/form-data"),
	"embedJS" => $afterJS,
	"afterSave" => array(
		array(
			"function" => "saveMenuItem", 
			"args" => array(
				&$linkOptionComponents, 
				&$menuItemObj->objLink, 
				array(	"linkurl_link" => "link", 
						"targetwindow_link" => "linktarget", 
						"textalign_link" => "textalign", 
						"prefix_link" => "prefix"), 
				"menulink_id", 
				"link")
		),
		array(
			"function" => "saveMenuItem", 
			"args" => array(
				&$imageOptionComponents, 
				&$menuItemObj->objImage, 
				array(	"imagefile_image" => "imageurl", 
						"width_image" => "width", 
						"height_image" => "height", 
						"linkurl_image" => "link",
						"targetwindow_image" => "linktarget",
						"textalign_image" => "imagealign"), 
				"menuimage_id", 
				"image")
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$customPageOptionComponents,
				&$menuItemObj->objCustomPage,
				array(	"custompage" => "custompage_id",
						"targetwindow_custompage" => "linktarget",
						"textalign_custompage" => "textalign",
						"prefix_custompage" => "prefix"),
				"menucustompage_id",
				"custompage"
			)
		
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$customFormOptionComponents,
				&$menuItemObj->objCustomPage,
				array(	"customform" => "custompage_id",
						"targetwindow_customform" => "linktarget",
						"textalign_customform" => "textalign",
						"prefix_customform" => "prefix"),
				"menucustompage_id",
				"customform"
			)
		
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$downloadOptionComponents,
				&$menuItemObj->objCustomPage,
				array(	"downloadpage" => "custompage_id",
						"targetwindow_downloads" => "linktarget",
						"textalign_downloads" => "textalign",
						"prefix_downloads" => "prefix"),
				"menucustompage_id",
				"downloads"
			)
		
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$shoutboxOptionComponents,
				&$menuItemObj->objShoutbox,
				array(	"width_shoutbox" => "width",
						"height_shoutbox" => "height",
						"textboxwidth_shoutbox" => "textboxwidth"),
				"menushoutbox_id",
				"shoutbox",
				array("percentwidth" => $_POST['widthunit_shoutbox'], "percentheight" => $_POST['heightunit_shoutbox'])
			)
		
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$customCodeOptionComponents,
				&$menuItemObj->objCustomBlock,
				array("customcode" => "code"),
				"menucustomblock_id",
				"customcode",
				array("blocktype" => "code")
			)
		),
		array(
			"function" => "saveMenuItem",
			"args" => array(
				&$customWYSIWYGOptionComponents,
				&$menuItemObj->objCustomBlock,
				array("wysiwygEditor" => "code"),
				"menucustomblock_id",
				"customformat",
				array("blocktype" => "format")
			)
		),
		"savePoll"
	)
);

?>