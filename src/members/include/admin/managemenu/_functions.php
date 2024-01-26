<?php

if (!defined("MANAGEMENU_FUNCTIONS")) {
	exit();
}

// Validate Functions

function validateMenuItem_Links() {

	if ($_POST['itemtype'] != "link") {
		return false;
	}

	global $linkOptionComponents, $formObj, $cID;

	$linkOptionComponents['linkurl_link']['validate'] = ["NOT_BLANK"];
	$linkOptionComponents['textalign_link']['validate'] = ["RESTRICT_TO_OPTIONS"];
	$linkOptionComponents['targetwindow_link']['validate'] = ["RESTRICT_TO_OPTIONS"];

	$setupFormArgs = [
		"name" => "console-".$cID."-link",
		"components" => $linkOptionComponents
	];

	$localFormObj = new Form($setupFormArgs);

	if (!$localFormObj->validate()) {
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
	}
}

function validateMenuItem_Images() {

	if ($_POST['itemtype'] != "image") {
		return false;
	}

	global $imageOptionComponents, $formObj, $cID;

	$imageOptionComponents['imagefile_image']['validate'] = ["NOT_BLANK"];
	$imageOptionComponents['width_image']['validate'] = ["POSITIVE_NUMBER"];
	$imageOptionComponents['height_image']['validate'] = ["POSITIVE_NUMBER"];
	$imageOptionComponents['textalign_image']['validate'] = ["RESTRICT_TO_OPTIONS"];
	$imageOptionComponents['targetwindow_image']['validate'] = ["RESTRICT_TO_OPTIONS"];

	$setupFormArgs = [
		"name" => "console-".$cID."-image",
		"components" => $imageOptionComponents
	];

	$localFormObj = new Form($setupFormArgs);

	if (!$localFormObj->validate()) {
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
	}
}

function validateMenuItem_CustomPageTypes($pageName, &$formComponents) {

	if ($_POST['itemtype'] != $pageName) {
		return false;
	}

	global $formObj, $cID;

	$textAlign = "textalign_".$pageName;
	$targetWindow = "targetwindow_".$pageName;

	$formComponents[$pageName]['validate'] = ["RESTRICT_TO_OPTIONS"];
	$formComponents[$textAlign]['validate'] = ["RESTRICT_TO_OPTIONS"];
	$formComponents[$targetWindow]['validate'] = ["RESTRICT_TO_OPTIONS"];

	$setupFormArgs = [
		"name" => "console-".$cID."-".$pageName,
		"components" => $formComponents
	];

	$localFormObj = new Form($setupFormArgs);

	if (!$localFormObj->validate()) {
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
	}
}

function validateMenuItem_Poll() {

	if ($_POST['itemtype'] != "poll") {
		return false;
	}

	global $pollOptionComponents, $formObj, $cID;

	$pollOptionComponents['poll']['validate'] = ["RESTRICT_TO_OPTIONS"];

	$setupFormArgs = [
		"name" => "console-".$cID."-poll",
		"components" => $pollOptionComponents
	];

	$localFormObj = new Form($setupFormArgs);

	if (!$localFormObj->validate()) {
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
	}
}


// Save Functions

/*
 * menuComponents - Form Components Array
 * saveObj - Object used to save data to database
 * arrDBNames - DB Names are not set in original component array to avoid being saved with standard menu data
 * 				Set DB table names and values for components with this array
 * ID Column name for saveObj
 *
 *
 */


function saveMenuItem(&$menuComponents, &$saveObj, $arrDBNames, $dbID, $itemType, $saveAdditionalArgs = [], $saveType = "add") {

	if ($_POST['itemtype'] != $itemType) {
		return false;
	}

	global $menuItemObj, $cID;

	foreach ($arrDBNames as $componentName => $dbName) {
		$menuComponents[$componentName]['db_name'] = $dbName;
	}

	$saveAdditional = ["menuitem_id" => $menuItemObj->get_info("menuitem_id")];
	$setupFormArgs = [
		"name" => "console-".$cID."-".$itemType,
		"components" => $menuComponents,
		"saveObject" => $saveObj,
		"saveType" => $saveType,
		"saveAdditional" => array_merge($saveAdditional, $saveAdditionalArgs)
	];

	$localFormObj = new Form($setupFormArgs);
	$localFormObj->save();
	$menuItemObj->update(["itemtype_id"], [$saveObj->get_info($dbID)]);
}


function savePoll() {

	if ($_POST['itemtype'] != "poll") {
		return false;
	}

	global $menuItemObj;

	$menuItemObj->update(["itemtype_id"], [$_POST['poll']]);
}


// Preparation Functions

function prepareItemTypeChangeJS($arr) {

	$innerJS = "";
	foreach ($arr as $ID => $value) {
		$innerJS .= "\$('#".$ID."').hide();\n";
	}

	$innerJS .= "
	switch(\$(this).val()) {
		";

	foreach ($arr as $ID => $value) {
		$innerJS .= "
		case '".$value."':
			\$('#".$ID."').show();
			break;
		";
	}

	$innerJS .= "
	}
	";

	$returnVal = "
	
		$('#itemType').change(function() {
			
		".$innerJS."
		
		});
	
		$('#itemType').change();

	";

	return $returnVal;
}
