<?php

if(!defined("MANAGEMENU_FUNCTIONS")) { exit(); }

// Validate Functions

function validateMenuItem_Links() {

	if($_POST['itemtype'] != "link") { return false; }
	
	
	global $linkOptionComponents, $formObj, $menuItemObj;
	
	$linkOptionComponents['linkurl_link']['validate'] = array("NOT_BLANK");
	$linkOptionComponents['textalign_link']['validate'] = array("RESTRICT_TO_OPTIONS");
	$linkOptionComponents['targetwindow_link']['validate'] = array("RESTRICT_TO_OPTIONS");
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-link",
		"components" => $linkOptionComponents
	);
	
	$localFormObj = new Form($setupFormArgs);
	
	if(!$localFormObj->validate()) {
		
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
		
	}
	
}

function validateMenuItem_Images() {

	if($_POST['itemtype'] != "image") { return false; }	
	
	global $imageOptionComponents, $formObj, $menuItemObj;
	
	$imageOptionComponents['imagefile_image']['validate'] = array("NOT_BLANK");
	$imageOptionComponents['width_image']['validate'] = array("POSITIVE_NUMBER");
	$imageOptionComponents['height_image']['validate'] = array("POSITIVE_NUMBER");
	$imageOptionComponents['textalign_image']['validate'] = array("RESTRICT_TO_OPTIONS");
	$imageOptionComponents['targetwindow_image']['validate'] = array("RESTRICT_TO_OPTIONS");
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-image",
		"components" => $imageOptionComponents
	);
	
	$localFormObj = new Form($setupFormArgs);
	
	if(!$localFormObj->validate()) {
		
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
		
	}
	
}

function validateMenuItem_CustomPageTypes($pageName, &$formComponents) {

	if($_POST['itemtype'] != $pageName) { return false; }
	
	global $formObj, $menuItemObj;
	
	$textAlign = "textalign_".$pageName;
	$targetWindow = "targetwindow_".$pageName;
	
	$formComponents[$pageName]['validate'] = array("RESTRICT_TO_OPTIONS");
	$formComponents[$textAlign]['validate'] = array("RESTRICT_TO_OPTIONS");
	$formComponents[$targetWindow]['validate'] = array("RESTRICT_TO_OPTIONS");
	
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-".$pageName,
		"components" => $formComponents
	);
	
	$localFormObj = new Form($setupFormArgs);
	
	if(!$localFormObj->validate()) {
		
		$formObj->errors = array_merge($formObj->errors, $localFormObj->errors);
		
	}
	
}

function validateMenuItem_Poll() {
	
	if($_POST['itemtype'] != "poll") { return false; }	
	
	global $pollOptionComponents, $formObj, $menuItemObj;
	
	$pollOptionComponents['poll']['validate'] = array("RESTRICT_TO_OPTIONS");
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-poll",
		"components" => $pollOptionComponents
	);
	
	$localFormObj = new Form($setupFormArgs);
	
	if(!$localFormObj->validate()) {
		
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


function saveMenuItem(&$menuComponents, &$saveObj, $arrDBNames, $dbID, $itemType, $saveAdditionalArgs=array(), $saveType="add") {
	
	if($_POST['itemtype'] != $itemType) { return false; }	
	
	global $formObj, $menuItemObj;

	foreach($arrDBNames as $componentName => $dbName) {		
		$menuComponents[$componentName]['db_name'] = $dbName;
	}
	

	$saveAdditional = array("menuitem_id" => $menuItemObj->get_info("menuitem_id"));
	$setupFormArgs = array(
		"name" => "console-".$cID."-".$itemType,
		"components" => $menuComponents,
		"saveObject" => $saveObj,
		"saveType" => $saveType,
		"saveAdditional" => array_merge($saveAdditional, $saveAdditionalArgs)
	);

	
	$localFormObj = new Form($setupFormArgs);
	$localFormObj->save();
	$menuItemObj->update(array("itemtype_id"), array($saveObj->get_info($dbID)));
	
	
}


function savePoll() {

	if($_POST['itemtype'] != "poll") { return false; }
	
	global $formObj, $menuItemObj, $pollOptionComponents;
	
	$menuItemObj->update(array("itemtype_id"), array($_POST['poll']));
	
}


// Preparation Functions

function prepareItemTypeChangeJS($arr) {

	$innerJS = "";
	foreach($arr as $ID => $value) {
		
		$innerJS .= "\$('#".$ID."').hide();\n";
		
	}
	
	$innerJS .= "
	switch(\$(this).val()) {
		";
	
	foreach($arr as $ID => $value) {
	
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

?>