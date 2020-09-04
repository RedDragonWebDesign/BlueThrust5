<?php

	if(!defined("SOCIALMEDIA_FORM")) { exit(); }

	$socialOrderObj = new Social($mysqli);	
	$socialOptions = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."social ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$socialOptions[$row['social_id']] = filterText($row['name']);
	}
	
	if(count($socialOptions) == 0) {
		$socialOptions['first'] = "(first icon)";	
	}
	
	$i=0;
	$arrComponents = array(
		"name" => array(
			"type" => "text",
			"sortorder" => $i++,
			"db_name" => "name",
			"attributes" => array("class" => "formInput textBox"),
			"display_name" => "Name",
			"validate" => array("NOT_BLANK")
		),
		"icon" => array(
			"type" => "file",
			"display_name" => "Icon",
			"options" => array("file_types" => array(".gif", ".png", ".jpg", ".bmp"), "file_prefix" => "social_", "save_loc" => BASE_DIRECTORY."images/socialmedia/", "ext_length" => 4, "append_db_value" => "images/socialmedia/"),
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox", "style" => "width: 100%"),
			"db_name" => "icon",
			"validate" => array("NOT_BLANK")
		),
		"iconwidth" => array(
			"type" => "text",
			"display_name" => "Icon Width",
			"tooltip" => "Leave blank to use default dimensions.",
			"db_name" => "iconwidth",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox", "style" => "width: 5%"),
			"html" => "<div class='formInput main' style='vertical-align: middle; padding-left: 3px; padding-top: 3px'><i>px</i></div>"
		),
		"iconheight" => array(
			"type" => "text",
			"display_name" => "Icon Height",
			"tooltip" => "Leave blank to use default dimensions.",
			"db_name" => "iconheight",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox", "style" => "width: 5%"),
			"html" => "<div class='formInput main formInputSideText'><i>px</i></div>"
		),
		"displayorder" => array(
			"type" => "beforeafter",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox"),
			"display_name" => "Display Order",
			"options" => $socialOptions,
			"validate" => array(array("name" => "VALIDATE_ORDER", "orderObject" => $socialOrderObj)),
			"db_name" => "ordernum"
		),
		"url" => array(
			"type" => "text",
			"display_name" => "Prepend URL",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox bigTextBox"),
			"tooltip" => "This URL will be added to the beginning of what a member inputs on their profile.",
			"db_name" => "url"
		),
		"tooltip" => array(
			"type" => "text",
			"display_name" => "Tooltip",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox formInput bigTextBox"),
			"tooltip" => "Display extra info in a tooltip for when a member is editing their profile.",
			"db_name" => "tooltip"
		),
		"submit" => array(
			"type" => "submit",
			"sortorder" => $i++,
			"value" => "Add Icon",
			"attributes" => array("class" => "submitButton formSubmitButton")
		)
	
	);
	

	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"description" => "Use the form below to add new social media icons for member profiles.",
		"saveObject" => $socialObj,
		"saveMessage" => "Successfully Added New Social Media Icon!",
		"saveType" => "add",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"beforeAfter" => true
	);
	
	
?>