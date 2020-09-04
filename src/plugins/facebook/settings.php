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
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/btplugin.php");
include_once("facebook.php");


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];

$pluginObj = new btPlugin($mysqli);

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$prevFolder = "../../";

$PAGE_NAME = "Facebook Login - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > Facebook Login Settings";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

include("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>Facebook Login Settings</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";



// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	
	$fbObj = new Facebook($mysqli);
	$pluginObj->selectByName("Facebook Login");
	
	if($_POST['submit']) {
		
		$arrAPIKey = array(
			'appID' => $_POST['appid'],
			'appSecret' => $_POST['appsecret']
		);
		
		$jsonAPIKey = json_encode($arrAPIKey);
		if($pluginObj->update(array("apikey"), array($jsonAPIKey))) {
		
			echo "
				<div style='display: none' id='successBox'>
				<p align='center'>
				Successfully Saved Facebook Login Settings!
				</p>
				</div>
				
				<script type='text/javascript'>
				popupDialog('Facebook Login', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
				
			";
				
			$member->logAction("Changed Facebook Login Plugin Settings.");
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	
	if(!$_POST['submit']) {
		$dispNote = "";
			
		$arrFacebookAPIKeys = array("App ID"=>$fbObj->getAppID(), "App Secret"=>$fbObj->getAppSecret());
		
		foreach($arrFacebookAPIKeys as $key=>$value) {
			
			if($value == "") {
				$dispNote .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> ".$key."<br>";
			}
	
			$dispFacebookAPIKey[$key] = filterText($value);
			
			
		}
		
		echo "
			<p align='right' style='margin-bottom: 10px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Return to Plugin Manager</a></p>
			
			<form action='".$MAIN_ROOT."plugins/facebook/settings.php' method='post'>
			<div class='formDiv'>
		
			";
		
			if($dispError != "") {
				echo "
				<div class='errorDiv'>
				<strong>Unable to save Facebook Login settings because the following errors occurred:</strong><br><br>
				$dispError
				</div>
				";
			}
		
			if($dispNote != "") {
				echo "
					<div class='errorDiv'>
						<strong><u>NOTE:</u> In order for Facebook Login to work you must set the following variables in the facebook.php plugin file.</strong><br><br>
						".$dispNote."
					</div>
				";
			}
		
		
		echo "
				
				Your Facebook Login plugin settings are listed below.  You must set App ID and App Secret in order for the plugin to work properly.
		
				<table class='formTable'>
					<tr>
						<td class='formLabel'>App ID:</td>
						<td class='main'><input type='text' name='appid' class='textBox' value='".$dispFacebookAPIKey['App ID']."'></td>
					</tr>
					<tr>
						<td class='formLabel'>App Secret:</td>
						<td class='main'><input type='text' name='appsecret' class='textBox' value='".$dispFacebookAPIKey['App Secret']."'></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Save Settings' class='submitButton'>
						</td>
				</table>
	
			</div>
			</form>
			<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Return to Plugin Manager</a></p>
		";
	}
}
else {

	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");

}



include("../../themes/".$THEME."/_footer.php");


?>