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

include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/downloadcategory.php");

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



$displayOrderOptions = array();
if(isset($_POST['menucategory'])) {
	$arrMenuItems = $menuItemObj->get_entries(array("menucategory_id" => $_POST['menucategory'], "menuitem_id" => $menuItemInfo['menuitem_id']), "sortnum", true, array("menuitem_id" => "!="));
	foreach($arrMenuItems as $eachMenuItem) {
		$displayOrderOptions[$eachMenuItem['menuitem_id']] = $eachMenuItem['name'];
	}

	if(count($displayOrderOptions) == 0) {
		$displayOrderOptions['first'] = "(first item)";	
	}
}

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


$itemTypeInclude = array(
	"link" => array("file" => "linkoptions.php", "array" => "linkOptionComponents", "sectionname" => "Link Options:"),
	"image" => array("file" => "imageoptions.php", "array" => "imageOptionComponents", "sectionname" => "Image Options:"),
	"custompage" => array("file" => "custompageoptions.php", "array" => "customPageOptionComponents", "sectionname" => "Custom Page Options:"),
	"customform" => array("file" => "customformoptions.php", "array" => "customFormOptionComponents", "sectionname" => "Custom Form Options:"),
	"downloads" => array("file" => "downloadoptions.php", "array" => "downloadOptionComponents", "sectionname" => "Download Page Options:"),
	"poll" => array("file" => "polloptions.php", "array" => "pollOptionComponents", "sectionname" => "Poll Options:"),
	"customcode" => array("file" => "customcodeoptions.php", "array" => "customCodeOptionComponents", "sectionname" => "Menu Item Code:"),
	"customformat" => array("file" => "customcodeformatoptions.php", "array" => "customWYSIWYGOptionComponents", "sectionname" => "Menu Item Information:")
);

$textAlignOptions = array("left" => "Left", "center" => "Center", "right" => "Right");


$menuCatOptions = array();
$arrMenuCats = $menuCatObj->get_entries(array(), "sortnum");
foreach($arrMenuCats as $menuCatInfo) {
	$menuCatOptions[$menuCatInfo['menucategory_id']] = $menuCatInfo['name'];
}


$menuItemOrder = $menuItemObj->findBeforeAfter();

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
		"display_name" => "Item Name",
		"value" => $menuItemInfo['name']
	),
	"menucategory" => array(
		"type" => "select",
		"display_name" => "Menu Category",
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "menucategory_id",
		"attributes" => array("class" => "textBox formInput", "id" => "menuCats"),
		"options" => $menuCatOptions,
		"value" => $menuItemInfo['menucategory_id']
	),
	"displayorder" => array(
		"type" => "beforeafter",
		"display_name" => "Display Order",
		"attributes" => array("class" => "textBox formInput", "id" => "displayOrder"),
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS", array("name" => "VALIDATE_ORDER", "set_category" => $_POST['menucategory'], "orderObject" => $menuItemObj, "edit" => true, "edit_ordernum" => $menuItemInfo['sortnum'], "select_back" => $menuItemInfo['menuitem_id'])),
		"db_name" => "sortnum",
		"options" => $displayOrderOptions,
		"before_after_value" => $menuItemOrder[0],
		"after_selected" => $menuItemOrder[1]
	),
	"itemtype" => array(
		"type" => "custom",
		"display_name" => "Item Type",
		"sortorder" => $i++,
		"html" => "<div class='formInput'><b>".$itemTypeOptions[$menuItemInfo['itemtype']]."</b></div>"
	),
	"accesstype" => array(
		"type" => "select",
		"display_name" => "Show when",
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "accesstype",
		"options" => array("Always", "Logged In"),
		"attributes" => array("class" => "textBox formInput"),
		"value" => $menuItemInfo['accesstype']
	),
	"hide" => array(
		"type" => "checkbox",
		"display_name" => "Hide",
		"attributes" => array("class" => "textBox formInput"),
		"value" => 1,
		"sortorder" => $i++,
		"db_name" => "hide",
		"checked" => ($menuItemInfo['hide'] == 1) ? true : false
	)
);


$arrExtraComponentSection = array();
$arrExtraComponents = "";

foreach($itemTypeInclude as $key => $itemTypeInfo) {
	if($key == $menuItemInfo['itemtype']) {
		include("include/".$itemTypeInfo['file']);
		
		$arrExtraComponentSection['extra_info'] = array(
			"type" => "section",
			"options" => array("section_title" => $itemTypeInfo['sectionname'], "section_description" => $itemTypeInfo['sectioninfo']),
			"sortorder" => $i++
		);
		
		
		$arrExtraComponents = ${$itemTypeInfo['array']};		
	
	}
}

$globalLinkOptionsNeeded = array("link", "custompage", "customform", "downloads");

if(in_array($menuItemInfo['itemtype'], $globalLinkOptionsNeeded)) {
	$optionName = $menuItemInfo['itemtype'];
	
	$globalLinkOptions = array(
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
	
	$arrExtraComponents = array_merge($arrExtraComponents, $globalLinkOptions);	
}

$arrAfterSave = "";
switch($menuItemInfo['itemtype']) {
	case "link":
		$menuItemObj->objLink->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objLink->get_info_filtered();
		$arrExtraComponents['linkurl_link']['value'] = $menuItemExtraInfo['link'];
		$arrExtraComponents['targetwindow_link']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_link']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_link']['value'] = $menuItemExtraInfo['prefix'];
	
		$arrAfterSave = array(array(
			"function" => "saveMenuItem", 
			"args" => array(
				&$arrExtraComponents, 
				&$menuItemObj->objLink, 
				array(	"linkurl_link" => "link", 
						"targetwindow_link" => "linktarget", 
						"textalign_link" => "textalign", 
						"prefix_link" => "prefix"), 
				"menulink_id", 
				"link", array(), "update")
			));
		
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
		
		$arrAfterSave = array(
			array(
			"function" => "saveMenuItem", 
			"args" => array(
				&$arrExtraComponents, 
				&$menuItemObj->objImage, 
				array(	"imagefile_image" => "imageurl", 
						"width_image" => "width", 
						"height_image" => "height", 
						"linkurl_image" => "link",
						"targetwindow_image" => "linktarget",
						"textalign_image" => "imagealign"), 
				"menuimage_id", 
				"image", array(), "update")
			));
		
		break;
	case "custompage":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['custompage']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_custompage']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_custompage']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_custompage']['value'] = $menuItemExtraInfo['prefix'];
		
		$arrAfterSave = array(
			array(
			"function" => "saveMenuItem",
			"args" => array(
				&$arrExtraComponents,
				&$menuItemObj->objCustomPage,
				array(	"custompage" => "custompage_id",
						"targetwindow_custompage" => "linktarget",
						"textalign_custompage" => "textalign",
						"prefix_custompage" => "prefix"),
				"menucustompage_id",
				"custompage", array(), "update"
			)
		));
		
		break;
	case "customform":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['customform']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_customform']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_customform']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_customform']['value'] = $menuItemExtraInfo['prefix'];
		
		$arrAfterSave = array(
			array(
			"function" => "saveMenuItem",
			"args" => array(
				&$arrExtraComponents,
				&$menuItemObj->objCustomPage,
				array(	"customform" => "custompage_id",
						"targetwindow_customform" => "linktarget",
						"textalign_customform" => "textalign",
						"prefix_customform" => "prefix"),
				"menucustompage_id",
				"customform", array(), "update"
			)
		));
		
		break;
	case "downloads":
		$menuItemObj->objCustomPage->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomPage->get_info_filtered();
		$arrExtraComponents['downloadpage']['value'] = $menuItemExtraInfo['custompage_id'];
		$arrExtraComponents['targetwindow_downloads']['value'] = $menuItemExtraInfo['linktarget'];
		$arrExtraComponents['textalign_downloads']['value'] = $menuItemExtraInfo['textalign'];
		$arrExtraComponents['prefix_downloads']['value'] = $menuItemExtraInfo['prefix'];
	
		$arrAfterSave = array(
			array(
				"function" => "saveMenuItem",
				"args" => array(
					&$arrExtraComponents,
					&$menuItemObj->objCustomPage,
					array(	"downloadpage" => "custompage_id",
							"targetwindow_downloads" => "linktarget",
							"textalign_downloads" => "textalign",
							"prefix_downloads" => "prefix"),
					"menucustompage_id",
					"downloads", array(), "update"
				)
			)
		);
		
		break;
	case "shoutbox":
		$menuItemObj->objShoutbox->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objShoutbox->get_info_filtered();
		
		$shoutboxWidthPercentSelected = ($menuItemExtraInfo['percentwidth'] == 1) ? " selected" : "";
		$shoutboxHeightPercentSelected = ($menuItemExtraInfo['percentheight'] == 1) ? " selected" : "";
		
		include("include/shoutboxoptions.php");
		
		$arrExtraComponentSection['extra_info'] = array(
			"type" => "section",
			"options" => array("section_title" => "Shoutbox Information:", "section_description" => "<b><u>NOTE:</u></b> Leave all fields blank to keep the theme's default settings."),
			"sortorder" => $i++
		);
		
		
		$arrExtraComponents = $shoutboxOptionComponents;	
		
		
		$arrExtraComponents['width_shoutbox']['value'] = $menuItemExtraInfo['width'];
		$arrExtraComponents['height_shoutbox']['value'] = $menuItemExtraInfo['height'];
		$arrExtraComponents['textboxwidth_shoutbox']['value'] = $menuItemExtraInfo['textboxwidth'];
	
		$arrAfterSave = array(
		
			array(
				"function" => "saveMenuItem",
				"args" => array(
					&$arrExtraComponents,
					&$menuItemObj->objShoutbox,
					array(	"width_shoutbox" => "width",
							"height_shoutbox" => "height",
							"textboxwidth_shoutbox" => "textboxwidth"),
					"menushoutbox_id",
					"shoutbox",
					array("percentwidth" => $_POST['widthunit_shoutbox'], "percentheight" => $_POST['heightunit_shoutbox']),
					"update"
				)
			
			)
		
		);
		
		break;
	case "poll":
		$arrExtraComponents['poll']['value'] = $menuItemInfo['itemtype_id'];
		$arrAfterSave = array("savePoll");
		
		break;
	case "customcode":
		$menuItemObj->objCustomBlock->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomBlock->get_info_filtered();
		$arrExtraComponents['customcode']['value'] = $menuItemExtraInfo['code'];
		
		$arrAfterSave = array(
			array(
				"function" => "saveMenuItem",
				"args" => array(
					&$arrExtraComponents,
					&$menuItemObj->objCustomBlock,
					array("customcode" => "code"),
					"menucustomblock_id",
					"customcode",
					array("blocktype" => "code"),
					"update"
				)
			)
		
		);
		
		break;
	case "customformat":
		$menuItemObj->objCustomBlock->select($menuItemInfo['itemtype_id']);
		$menuItemExtraInfo = $menuItemObj->objCustomBlock->get_info_filtered();
		$arrExtraComponents['wysiwygEditor']['value'] = $menuItemExtraInfo['code'];
		
		
		$arrAfterSave = array(
		
			array(
				"function" => "saveMenuItem",
				"args" => array(
					&$arrExtraComponents,
					&$menuItemObj->objCustomBlock,
					array("wysiwygEditor" => "code"),
					"menucustomblock_id",
					"customformat",
					array("blocktype" => "format"),
					"update"
				)
			)
		);
		
		break;
}

if(is_array($arrExtraComponents)) {
	$arrExtraComponentSection['extra_info']['components'] = $arrExtraComponents;
}

$arrComponents = array_merge($arrComponents, $arrExtraComponentSection);

$submitButtonArray = array(

	"fakeSubmit" => array(
		"type" => "button",
		"attributes" => array("class" => "submitButton formSubmitButton", "id" => "btnFakeSubmit"),
		"value" => "Edit Menu Item",
		"sortorder" => $i++
	),
	"submit" => array(
		"type" => "submit",
		"value" => "submit",
		"attributes" => array("style" => "display: none", "id" => "btnSubmit"),
		"sortorder" => $i++
	)
);

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

	if($menuItemInfo['itemtype'] == "customcode") {

		$afterJS .= "$('#menuCodeEditor_code').val(menuCodeEditor.getValue());";
		
	}


$afterJS .= "
	$('#btnSubmit').click();

});";

if($menuItemInfo['itemtype'] == "shoutbox") {
	
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

$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to edit the selected menu item.",
	"saveObject" => $menuItemObj,
	"saveMessage" => "Successfully Saved Menu Item: <b>".filterText($_POST['itemname'])."</b>!",
	"saveType" => "update",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID."&menuID=".$menuItemInfo['menuitem_id']."&action=edit", "method" => "post", "enctype" => "multipart/form-data"),
	"afterSave" => $arrAfterSave,
	"embedJS" => $afterJS
);

$_POST['itemtype'] = $menuItemInfo['itemtype'];
define("MANAGEMENU_FUNCTIONS", true);
include("_functions.php");

?>