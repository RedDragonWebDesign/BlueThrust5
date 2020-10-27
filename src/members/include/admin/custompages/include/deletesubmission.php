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




include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/customform.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("View Custom Form Submissions");
$consoleObj->select($cID);

$customFormPageObj = new CustomForm($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $customFormPageObj->objSubmission->select($_POST['subID'])) {
	
	$submissionID = $customFormPageObj->objSubmission->get_info("submission_id");
	$mysqli->query("DELETE FROM ".$dbprefix."customform_values WHERE submission_id = '".$submissionID."'");
	
	$customFormPageObj->objSubmission->delete();
	
}



include("../submissiondetail.php");

?>