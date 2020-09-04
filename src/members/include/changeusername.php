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



$cID = $_GET['cID'];



$arrComponents = array(
	"newusername" => array(
		"display_name" => "New Username",
		"type" => "text",
		"sortorder" => 1,
		"attributes" => array("class" => "textBox formInput"),
		"validate" => array("NOT_BLANK", array("name" => "IS_NOT_SELECTABLE", "selectObj" => $member, "select_back" => "member_id")),
		"db_name" => "username"
	),
	"submit" => array(
		"type" => "submit",
		"sortorder" => 2,
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"value" => "Change Username"
	)

);

$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveObject" => $member,
	"saveType" => "update",
	"afterSave" => array("setMemberSessions"),
	"saveMessage" => "Successfully changed username!",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
	"description" => "Use the form below to change your username."
);


// Validation Functions

function validateUsername() {
	global $formObj, $mysqli;
	
	$checkMemberObj = new Member($mysqli);
	if($checkMemberObj->select($_POST['newusername'])) {
		$formObj->errors[] = "There is already a member with that username.";
	}
	
}

// After Save Functions

function setMemberSessions() {
	global $member;

	$_SESSION['btUsername'] = $member->get_info_filtered("username");
}

?>