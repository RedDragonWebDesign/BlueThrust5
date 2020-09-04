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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/medal.php");
$cID = $_GET['cID'];

$medalObj = new Medal($mysqli);


if(!$medalObj->select($_GET['mID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}


$medalInfo = $medalObj->get_info_filtered();

$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb("Manage Medals", $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb($medalInfo['name']);
echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"".$breadcrumbObj->getBreadcrumb()."\");
});
</script>
";

	
	$arrMedals = $medalObj->get_entries(array(), "ordernum DESC");
	$medalOptions = array();
	foreach($arrMedals as $eachMedalInfo) {
		$medalName = filterText($eachMedalInfo['name']);
		$medalOptions[$eachMedalInfo['medal_id']] = $medalName;
	
	}
        

	if(count($medalOptions) == 0) {
		$medalOptions['first'] = "(first medal)";
	}
	
	
	$medalOrder = $medalObj->findBeforeAfter();
	
	$medalInfo['imageurl'] = substr($medalInfo['imageurl'], strlen($MAIN_ROOT));
	$i = 1;
	$arrComponents = array(
		"generalinfo" => array(
			"type" => "section",
			"options" => array("section_title" => "General Information:"),
			"sortorder" => $i++,			
		),
		"medalname" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput"),
			"sortorder" => $i++,
			"db_name" => "name",
			"display_name" => "Medal Name",
			"validate" => array("NOT_BLANK"),
			"value" => $medalInfo['name']
		),
		"medalimage" => array(
			"type" => "file",
			"attributes" => array("class" => "textBox", "style" => "width: 100%"),
			"db_name" => "imageurl",
			"sortorder" => $i++,
			"options" => array("file_types" => array(".gif", ".png", ".jpg", ".bmp"), "file_prefix" => "medal_", "save_loc" => "../images/medals/", "ext_length" => 4, "append_db_value" => "images/medals/"),
			"display_name" => "Medal Image",
			"value" => $medalInfo['imageurl']
		),
		"medalimagewidth" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>",
			"tooltip" => "Set the Image Width to the width that you would like the Medal Image to be displayed on your website.",
			"db_name" => "imagewidth",
			"validate" => array("POSITIVE_NUMBER"),
			"display_name" => "Image Width",
			"sortorder" => $i++,
			"value" => $medalInfo['imagewidth']
		),
		"medalimageheight" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>",
			"tooltip" => "Set the Image Height to the height that you would like the Medal Image to be displayed on your website.",
			"db_name" => "imageheight",
			"validate" => array("POSITIVE_NUMBER"),
			"display_name" => "Image Height",
			"sortorder" => $i++,
			"value" => $medalInfo['imageheight']
		),
		"medaldesc" => array(
			"type" => "textarea",
			"attributes" => array("class" => "textBox formInput", "rows" => 5, "cols" => 40),
			"db_name" => "description",
			"sortorder" => $i++,
			"display_name" => "Description",
			"value" => $medalInfo['description']	
		),
		"displayorder" => array(
			"type" => "beforeafter",
			"attributes" => array("class" => "textBox"),
			"display_name" => "Display Order",
			"options" => $medalOptions,
			"db_name" => "ordernum",
			"sortorder" => $i++,
			"validate" => array(array("name" => "VALIDATE_ORDER", "orderObject" => $medalObj, "select_back" => $medalInfo['medal_id'])),
			"value" => $medalInfo['medal_id'],
			"before_after_value" => $medalOrder[0],
			"after_selected" => $medalOrder[1]
		
		),
		"autoawardinfo" => array(
			"type" => "section",
			"options" => array("section_title" => "Auto-Award Information:", "section_description" => "Set these options if you want a member to be automatically awarded for being in the clan a certain number of days or recruiting a certain amount of members. Leave blank or 0 to disable this option."),
			"sortorder" => $i++
		),
		"autodays" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"display_name" => "Auto-Days",
			"sortorder" => $i++,
			"db_name" => "autodays",
			"value" => $medalInfo['autodays']
		),
		"autorecruits" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"display_name" => "Auto-Recruits",
			"sortorder" => $i++,
			"db_name" => "autorecruits",
			"value" => $medalInfo['autorecruits']
		),
		"submit" => array(
			"type" => "submit",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"value" => "Edit Medal",
			"sortorder" => $i++		
		)
	);



$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Fill out the form below to edit the selected medal.<br><br><b><u>NOTE:</u></b> When setting the Medal Image, if both the File and URL are filled out, the File will be used.",
	"saveObject" => $medalObj,
	"saveMessage" => "Successfully Saved Medal: <b>".filterText($_POST['medalname'])."</b>!",
	"saveType" => "update",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID."&mID=".$medalInfo['medal_id']."&action=edit", "method" => "post"),
	"beforeAfter" => true
);


?>