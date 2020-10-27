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

include_once("../../_setup.php");


// Delete expired compose list sessions
foreach($_SESSION['btComposeList'] as $key => $arr) {
	if(time() > $arr['exptime']) {
		unset($_SESSION['btComposeList'][$key]);
	}
}


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$PAGE_NAME = "Compose Message - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".MAIN_ROOT."'>Home</a> > <a href='".MAIN_ROOT."members'>My Account</a> > <a href='".MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > Compose Message";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".MAIN_ROOT."members/js/main.js'></script>

<style>
	.ui-autocomplete {
		max-height: 150px;
		overflow-y: auto;
	}
</style>
";

$prevFolder = "../../";
include(BASE_DIRECTORY."themes/".$THEME."/_header.php");


$breadcrumbObj->setTitle("Compose Message");
$breadcrumbObj->addCrumb("Home", MAIN_ROOT);
$breadcrumbObj->addCrumb("My Account", MAIN_ROOT."members");
$breadcrumbObj->addCrumb($consoleTitle, MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb("Compose Message");
include(BASE_DIRECTORY."include/breadcrumb.php");

$pmObj = new BasicOrder($mysqli, "privatemessages", "pm_id");
$rankCatObj = new RankCategory($mysqli);
$squadObj = new Squad($mysqli);
$tournamentObj = new Tournament($mysqli);
$multiMemPMObj = new Basic($mysqli, "privatemessage_members", "pmmember_id");

$pmObj->set_assocTableName("privatemessage_members");
$pmObj->set_assocTableKey("member_id");

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info_filtered();
	$formObj = new Form();
	
	
	include(BASE_DIRECTORY."members/privatemessages/include/compose_submit.php");
	include(BASE_DIRECTORY."members/privatemessages/include/compose_setup.php");
	
	$i = 1;
	$arrComponents = array(
		"tomember" => array(
			"type" => "custom",
			"display_name" => "To",
			"html" => "<div class='pmComposeTextBox'>
									<div id='composeList' style='float: left'>
										<div id='composeTextBox' style='float: left'><input type='text' id='tomember' name='tomember' class='textBox'></div>
									</div>
									<div style='clear: both'></div>
								</div>",
			"sortorder" => $i++,
		
		),
		"subject" => array(
			"type" => "text",
			"display_name" => "Subject",
			"attributes" => array("class" => "formInput textBox bigTextBox"),
			"sortorder" => $i++,
			"value" => $_POST['subject']
		),
		"message" => array(
			"type" => "textarea",
			"display_name" => "Message",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox", "rows" => "8", "cols" => "50"),
			"validate" => array("NOT_BLANK")
		),
		"submit" => array(
			"type" => "submit",
			"value" => "Send Message",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"sortorder" => $i++	
		),
		"pmsessionid" => array(
			"type" => "hidden",
			"value" => $pmSessionID,
			"hidden" => true,
			"sortorder" => $i++
		)
	
	);
	
	
	if(isset($_GET['threadID']) && is_numeric($_GET['threadID'])) {
		$replyPMID = $_GET['threadID'];
	}
	else {
		$replyPMID = 0;
	}
	
	
	$arrComponents['replypmid'] = array(
		"type" => "hidden",
		"value" => $replyPMID,
		"hidden" => true,
		"sortorder" => $i++
	);
	
	
	// Send as Email
	$emailPMCID = $consoleObj->findConsoleIDByName("Email Private Messages");
	$consoleObj->select($emailPMCID);
	if($member->hasAccess($consoleObj)) {
				
		$formObj->addComponentSortSpace(2, $arrComponents);
		$arrComponents = $formObj->components;
		
		$arrComponents['emailpm'] = array(
			"type" => "checkbox",
			"value" => 1,
			"sortorder" => 2,
			"display_name" => "Send as E-mail",
			"tooltip" => "Checking this box will force an e-mail to be sent to the member(s) as well.",
			"attributes" => array("class" => "formInput")
		);
				
	}
	$consoleObj->select($cID);
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-compose",
		"components" => $arrComponents,
		"saveMessage" => "Successfully Sent Private Message!",
		"attributes" => array("action" => MAIN_ROOT."members/privatemessages/compose.php", "method" => "post"),
		"description" => "Use the form below to send a private message.<br><br><b><u>Extra Information:</u></b><br>You may send private messages in batches to squads, tournaments, or ranks by typing in their associated name.  Typing in a squad name, tournament title or rank name will send to that group.<br><br>",
		"embedJS" => $composePageJS
	);
	
	
	
	include_once(BASE_DIRECTORY."members/console.form.php");
	
	
}
else {

	die("<script type='text/javascript'>window.location = '".MAIN_ROOT."login.php';</script>");

}



include(BASE_DIRECTORY."themes/".$THEME."/_footer.php");