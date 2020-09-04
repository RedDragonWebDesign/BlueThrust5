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

$prevFolder = "../";
include("../_setup.php");
$pluginObj = new btPlugin($mysqli);

if(!isset($_GET['plugin']) || !$pluginObj->selectByName($_GET['plugin'])) { echo "<script type='text/javascript'>window.location = '".$MAIN_ROOT."';"; exit(); }

$pluginInfo = $pluginObj->get_info_filtered();


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$PAGE_NAME = $pluginInfo['name']." Plugin Settings - ".$consoleTitle." - ";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

$formObj = new Form();
require(BASE_DIRECTORY."plugins/".$pluginInfo['filepath']."/settings_form.php");	
$hooksObj->run("pluginsettings-".$pluginInfo['filepath']);	


include(BASE_DIRECTORY."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($pluginInfo['name']." Plugin Settings");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("My Account", $MAIN_ROOT."members");
$breadcrumbObj->addCrumb($consoleTitle, $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb($pluginInfo['name']." Plugin Settings");

include(BASE_DIRECTORY."include/breadcrumb.php");

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$formObj->buildForm($setupFormArgs);	
	
	if($_POST['submit'] && $formObj->save()) {
		$formObj->saveMessageTitle = $pluginInfo['name']." Plugin Settings";
		$formObj->showSuccessDialog();
	}
	
	
	
	$formObj->show();	
	
}
else {

	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");

}



include(BASE_DIRECTORY."themes/".$THEME."/_footer.php");


?>
