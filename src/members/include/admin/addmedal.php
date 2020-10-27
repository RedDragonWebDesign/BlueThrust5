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


include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/medal.php");
$cID = $_GET['cID'];
$medalObj = new Medal($mysqli);

	
	$getMedals = $mysqli->query("SELECT * FROM ".$dbprefix."medals ORDER BY ordernum DESC");
	$medalOptions = array();
	while($arrMedals = $getMedals->fetch_assoc()) {
		$medalName = filterText($arrMedals['name']);
		$medalOptions[$arrMedals['medal_id']] = $medalName;
	
	}
        

	if(count($medalOptions) == 0) {
		$medalOptions['first'] = "(first medal)";
	}
	
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
			"validate" => array("NOT_BLANK")
		),
		"medalimage" => array(
			"type" => "file",
			"attributes" => array("class" => "textBox", "style" => "width: 100%"),
			"db_name" => "imageurl",
			"sortorder" => $i++,
			"options" => array("file_types" => array(".gif", ".png", ".jpg", ".bmp"), "file_prefix" => "medal_", "save_loc" => "../images/medals/", "ext_length" => 4, "append_db_value" => "images/medals/"),
			"display_name" => "Medal Image",
			"validate" => array("NOT_BLANK")
		),
		"medalimagewidth" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>",
			"tooltip" => "Set the Image Width to the width that you would like the Medal Image to be displayed on your website.",
			"db_name" => "imagewidth",
			"validate" => array("POSITIVE_NUMBER"),
			"display_name" => "Image Width",
			"sortorder" => $i++
		),
		"medalimageheight" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>",
			"tooltip" => "Set the Image Height to the height that you would like the Medal Image to be displayed on your website.",
			"db_name" => "imageheight",
			"validate" => array("POSITIVE_NUMBER"),
			"display_name" => "Image Height",
			"sortorder" => $i++
		),
		"medaldesc" => array(
			"type" => "textarea",
			"attributes" => array("class" => "textBox formInput", "rows" => 5, "cols" => 40),
			"db_name" => "description",
			"sortorder" => $i++,
			"display_name" => "Description"		
		),
		"displayorder" => array(
			"type" => "beforeafter",
			"attributes" => array("class" => "textBox"),
			"display_name" => "Display Order",
			"options" => $medalOptions,
			"db_name" => "ordernum",
			"sortorder" => $i++,
			"validate" => array(array("name" => "VALIDATE_ORDER", "orderObject" => $medalObj))
		
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
			"db_name" => "autodays"
		),
		"autorecruits" => array(
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
			"display_name" => "Auto-Recruits",
			"sortorder" => $i++,
			"db_name" => "autorecruits"
		),
		"submit" => array(
			"type" => "submit",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"value" => "Add Medal",
			"sortorder" => $i++		
		)
	);

	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"description" => "Fill out the form below to add a new medal.<br><br><b><u>NOTE:</u></b> When adding a Medal Image, if both the File and URL are filled out, the File will be used.",
		"saveObject" => $medalObj,
		"saveMessage" => "Successfully Added New Medal: <b>".filterText($_POST['medalname'])."</b>!",
		"saveType" => "add",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"beforeAfter" => true
	);

?>