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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/poll.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);



$createPollCID = $consoleObj->findConsoleIDByName("Create a Poll");
$consoleObj->select($createPollCID);
$blnConsoleCheck1 = $member->hasAccess($consoleObj);

$managePollsCID = $consoleObj->findConsoleIDByName("Manage Polls");
$consoleObj->select($managePollsCID);
$blnConsoleCheck2 = $member->hasAccess($consoleObj);


$blnConsoleCheck = $blnConsoleCheck1 || $blnConsoleCheck2;


$pollObj = new Poll($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $blnConsoleCheck) {
	
	$pollObj->cacheID = $_POST['cacheID'];
	
	if($_POST['submit']) {
		
		$arrOptionInfo = array();
		$arrErrors = array();
		$arrReturn = array();
		
		
		// Check Value
		if(trim($_POST['optionValue']) == "") {
			$arrErrors[] = "Option value may not be blank.";
		}
		
		
		// Check Color
		if(trim($_POST['optionColor']) == "") {
			$_POST['optionColor'] = "#FFFFFF";	
		}
		
		// Check Display Order
		
		if(count($_SESSION['btPollOptionCache'][$pollObj->cacheID]) > 1 && (!is_numeric($_POST['optionOrder']) || !isset($_POST['optionOrder']) || ($_POST['optionOrderBeforeAfter'] != "before" && $_POST['optionOrderBeforeAfter'] != "after"))) {
			$arrErrors[] = "You selected an invalid display order.";
		}	
		
		if(count($arrErrors) == 0) {

			$arrOptionInfo = $_SESSION['btPollOptionCache'][$pollObj->cacheID][$_POST['pollOption']];
			
			unset($_SESSION['btPollOptionCache'][$pollObj->cacheID][$_POST['pollOption']]);
			
			$newSortNum = $pollObj->makeCacheRoom($_POST['optionOrderBeforeAfter'], $_POST['optionOrder']);
			
			$arrReturn['result'] = "success";

			$arrOptionInfo['value'] = $_POST['optionValue'];
			$arrOptionInfo['color'] = $_POST['optionColor'];
			
			
			$_SESSION['btPollOptionCache'][$pollObj->cacheID][$newSortNum] = $arrOptionInfo;

			$pollObj->resortCacheOrder();
			
		}
		
		if(count($arrErrors) > 0) {
			
			$arrReturn['result'] = "fail";
			$arrReturn['errors'] = $arrErrors;
			
		}
		
		
		echo json_encode($arrReturn);
	}
	
	
	if(!$_POST['submit']) {
		echo "	
		
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#optionColor').miniColors({
						change: function(hex, rgb) { }
					});
				});
			</script>
		
			<div class='errorDiv main' style='display: none' id='dialogErrors'></div>
			<input id='pollOption' value='".$_POST['pollOption']."' type='hidden'>
			<table class='formTable' style='width: 100%'>
				<tr>
					<td class='main'><b>Option Value:</b></td>
					<td class='main'><input type='text' id='optionValue' value='".filterText($_SESSION['btPollOptionCache'][$pollObj->cacheID][$_POST['pollOption']]['value'])."' class='textBox' style='width: 100%'></td>
				</tr>
				<tr>
					<td class='main'><b>Color:</b></td>
					<td class='main'><input type='text' id='optionColor' class='textBox' value='".filterText($_SESSION['btPollOptionCache'][$pollObj->cacheID][$_POST['pollOption']]['color'])."' style='width: 35%'></td>
				</tr>
				
			";
		
		if(count($_SESSION['btPollOptionCache'][$pollObj->cacheID]) > 1) {
			
			
			// Find Before After
			
			$selectKey = $_POST['pollOption']+1;
			$afterSelected = "";
			if($selectKey == count($_SESSION['btPollOptionCache'][$pollObj->cacheID])) {
				$selectKey = $_POST['pollOption']-1;
				$afterSelected = " selected";	
			}
			
			foreach($_SESSION['btPollOptionCache'][$pollObj->cacheID] as $key=>$optionInfo) {
	
				$selectOption = ($key == $selectKey) ? " selected" : "";
				
				if($key != $_POST['pollOption']) {
					$displayOrder .= "<option value='".$key."'".$selectOption.">".$optionInfo['value']."</option>";
				}
			}
						
			
			echo "
				<tr>
					<td class='main'><b>Display Order:</b></td>
					<td class='main'>
						<select id='optionOrderBeforeAfter' class='textBox'>
							<option value='before'>Before</option><option value='after'".$afterSelected.">After</option>
						</select>
						<br>
						<select id='optionOrder' class='textBox'>
						".$displayOrder."
						</select>
			";
		}
		
		echo "
			</table>
		";
		
	}
}

?>