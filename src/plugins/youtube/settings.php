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
include_once("youtube.php");


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

$PAGE_NAME = "Youtube Connect - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > Youtube Connect Settings";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

include("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>Youtube Connect Settings</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";



// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$ytObj = new Youtube($mysqli);
	$memberInfo = $member->get_info_filtered();
	
	$pluginObj->selectByName("Youtube Connect");
	$pluginInfo = $pluginObj->get_info();

	$pluginObj->pluginPage->setCategoryKeyValue($pluginInfo['plugin_id']);
	
	$pluginPageInfo = $pluginObj->getPluginPage("profile", $pluginInfo['plugin_id']);
	
	$arrProfileModules = array("User Information", "Custom Profile Options", "Games Statistics", "Squads", "Medals");
	
	$countErrors = 0;
	$dispError = "";
	
	
	if($_POST['submit']) {
		
		
	// Check Display Order (before/after)
		if($_POST['beforeafter'] != "before" && $_POST['beforeafter'] != "after") {
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order (before/after).<br>";
			$countErrors++;
		}
		
		// Check Display Order
		
		if(!in_array($_POST['displayorder'], array_keys($arrProfileModules))) {
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
			$countErrors++;
		}
		
		if($countErrors == 0) {
			$arrAPIKey = array(
				'clientID' => $_POST['clientid'],
				'clientSecret' => $_POST['clientsecret']
			);
			
			$jsonAPIKey = json_encode($arrAPIKey);
			
			$setSortNum = $_POST['displayorder'];
			if($_POST['beforeafter'] == "after") {
				$setSortNum = $_POST['displayorder']+1;
			}
			
			
			if($_POST['profiledisplay'] == "no") {
				$setSortNum = -1;	
			}

			if($pluginObj->update(array("apikey"), array($jsonAPIKey)) && $pluginObj->pluginPage->update(array("sortnum"), array($setSortNum))) {
				
				echo "
				<div style='display: none' id='successBox'>
				<p align='center'>
				Successfully Saved Youtube Connect Settings!
				</p>
				</div>
				
				<script type='text/javascript'>
				popupDialog('Youtube Connect', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
				
				";
				
				$member->logAction("Changed Youtube Connect Plugin Settings.");
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
			}
		
			
			
		}
		
		if($countErrors > 0) {
			$_POST['submit'] = false;
		}
		
		
	}
	
	
	if(!$_POST['submit']) {
		
		$selectAfter = "";
		if(count($arrProfileModules) == $pluginPageInfo[0]['sortnum']) {
			$selectAfter = " selected";	
		}
		
		$selectNoDisplay = "";
		if($pluginPageInfo[0]['sortnum'] == -1) {
			$selectNoDisplay = " selected";	
		}
		
		$dispNote = "";
		
		$arrYoutubeAPIKeys = array("Client ID"=>$ytObj->getClientID(), "Client Secret"=>$ytObj->getClientSecret());
		
		foreach($arrYoutubeAPIKeys as $key=>$value) {
			
			if($value == "") {
				$dispNote .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> ".$key."<br>";
			}
			
			$dispYTAPIKey[$key] = $value;
			
		}
		
		echo "
			<p align='right' style='margin-bottom: 10px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Return to Plugin Manager</a></p>
		
			<form action='".$MAIN_ROOT."plugins/youtube/settings.php' method='post'>
				<div class='formDiv'>
					
				";
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to Youtube Connect settings because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		
		if($dispNote != "") {
			echo "
				<div class='errorDiv'>
					<strong><u>NOTE:</u> In order for Youtube Connect to work you must set the following variables.</strong><br><br>
					".$dispNote."
				</div>
			";
		}
		
		
		echo "
				
				
					Your Youtube Connect plugin settings are listed below.  You must set the Client ID and Client Secret in order for the plugin to work properly.
					<table class='formTable'>
						<tr>
							<td class='main' colspan='2'>
								<div class='dottedLine' style='padding-bottom: 3px'>
									<b>Youtube API Settings:</b>
								</div>
							</td>
						</tr>
						<tr>
							<td class='formLabel'>Client ID:</td>
							<td class='main'><input type='text' name='clientid' class='textBox' value='".$dispYTAPIKey['Client ID']."'></td>
						</tr>
						<tr>
							<td class='formLabel'>Client Secret:</td>
							<td class='main'><input type='text' name='clientsecret' class='textBox' value='".$dispYTAPIKey['Client Secret']."'></td>
						</tr>
						<tr>
							<td class='main' colspan='2'><br>
								<div class='dottedLine' style='padding-bottom: 3px'>
									<b>Profile Display Settings:</b>
								</div>
							</td>
						</tr>
						<tr>
							<td class='formLabel'>Display in Profile:</td>
							<td class='main'><select name='profiledisplay' class='textBox'><option value='yes'>Yes</option><option value='no'".$selectNoDisplay.">No</option></select></td>
						</tr>
						<tr>
							<td class='formLabel' valign='top'>Display Order:</td>
							<td class='main'>
								<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$selectAfter.">After</option></select><br>
								<select name='displayorder' class='textBox'>
								";
		
		foreach($arrProfileModules as $key=>$module) {
			
			$selectKey = "";
			if($pluginPageInfo[0]['sortnum'] == $key) {
				$selectKey = " selected";
			}
			elseif($key == (count($arrProfileModules)-1) && $selectAfter == " selected") {
				$selectKey = " selected";	
			}
			
			echo "<option value='".$key."'".$selectKey.">".$module."</option>";
		}
		
		echo "
								</select>
							</td>
						</tr>
						<tr>
							<td class='main' align='center' colspan='2'><br>
								<input type='submit' name='submit' value='Save Settings' class='submitButton'>
							</td>
						</tr>
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