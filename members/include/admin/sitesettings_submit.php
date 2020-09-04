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

include("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$submitSuccess = false;
$scrollTop = true;

$cID = $consoleObj->findConsoleIDByName("Website Settings");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info();

	if($member->hasAccess($consoleObj)) {
		$countErrors = 0;	
		
		
		// Check Clan Name
		
		if(trim($_POST['clanName']) == "") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a name for your clan.<br>";
		}
		
		// Check Theme
		
		
		$arrThemes = scandir("../../../themes");
		$arrCheckTheme = array();
		foreach($arrThemes as $strTheme) {
		
			$themeURL = "../../../themes/".$strTheme;
		
			if(is_dir($themeURL) && $strTheme != "." && $strTheme != "..") {
				$arrCheckTheme[] = $strTheme;
			}
		}

		if(!in_array($_POST['themeName'], $arrCheckTheme)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid theme.<br>";
		}
		
		
		// Check Max Diplomacy
		
		if(!is_numeric($_POST['maxDiplomacy']) || (is_numeric($_POST['maxDiplomacy']) && $_POST['maxDiplomacy'] < 0)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter postive number or zero for max diplomacy requests.<br>";
		}
		
		// Check Failed Logins
		
		if(!is_numeric($_POST['failedLogins']) || (is_numeric($_POST['failedLogins']) && $_POST['failedLogins'] < 0)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter postive number or zero for failed login attempts.<br>";
		}
		
		// Check Max Days
		
		if($_POST['maxDSL'] != "" && (!is_numeric($_POST['maxDSL']) || (is_numeric($_POST['maxDSL']) && $_POST['maxDSL'] < 0))) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter postive number or zero for max days.<br>";
		}
		
		// Check Medal Order
		
		$arrCheckMedalOrder = array(0, 1, 2);
		if(!in_array($_POST['medalOrder'], $arrCheckMedalOrder)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid medal display order.<br>";
		}
		
		if($_POST['debugMode'] != 1) {
			$_POST['debugMode'] = 0;
		}
		
		
		if($_POST['hideInactive'] != 1) {
			$_POST['hideInactive'] = 0;	
		}
		
		$numOfNewsPosts = 0;
		if($_POST['showHPNews'] == "yes" && $_POST['numOfNewsPosts'] == "custom" && is_numeric($_POST['customNewsAmount']) && $_POST['customNewsAmount'] > 0) {
			$numOfNewsPosts = $_POST['customNewsAmount'];
		}
		elseif($_POST['showHPNews'] == "yes" && $_POST['numOfNewsPosts'] == "all") {
			$numOfNewsPosts = -1;	
		}
		elseif($_POST['showHPNews'] == "yes" &&  is_numeric($_POST['numOfNewsPosts']) && $_POST['numOfNewsPosts'] > 0) {
			$numOfNewsPosts = $_POST['numOfNewsPosts'];
		}
		
		if(!is_numeric($_POST['newsPostsPerPage']) && $_POST['newsPostsPerPage'] > 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> News Posts Per Page must be a positive numeric value.";	
		}
		
		
		
		if($countErrors == 0) {
			
			$updateSettings = array("clanname", "clantag", "logourl", "theme", "maxdiplomacy", "failedlogins", "maxdsl", "lowdsl", "meddsl", "highdsl", "medalorder", "debugmode", "hideinactive", "hpnews", "news_postsperpage");
			$updateSettingVals = array($_POST['clanName'], $_POST['clanTag'], $_POST['logoURL'], $_POST['themeName'], $_POST['maxDiplomacy'], $_POST['failedLogins'], $_POST['maxDSL'], $_POST['lowDSL'], $_POST['medDSL'], $_POST['highDSL'], $_POST['medalOrder'], $_POST['debugMode'], $_POST['hideInactive'], $numOfNewsPosts, $_POST['newsPostsPerPage']);
			
			
			if(!$webInfoObj->multiUpdate($updateSettings, $updateSettingVals)) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save the information to the database.<br>";
			}
			
		}
		
		
		
		if($countErrors == 0) {
			$dispTime = date("l F j, Y g:i:s A");
			echo "
				<script type='text/javascript'>
					$('#saveMessage').html(\"<b><span class='successFont'>Website Settings Saved: </span> ".$dispTime."</b>\");
					$('#saveMessage').fadeIn(400);
					$('#errorDiv').hide();
				</script>
			";
			
		}
		else {
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
				
						$('#errorMessage').html('".$dispError."');
						$('#errorDiv').fadeIn(400);
						$('#saveMessage').html(\"<span class='failedFont'><b>Website Settings Not Saved!</b></span>\");
						$('#saveMessage').fadeIn(400);
						$('html, body').animate({ scrollTop: 0 });
						
					});
				</script>
			";
		}
		
	}
}


?>