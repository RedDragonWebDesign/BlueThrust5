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
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

include_once($prevFolder."classes/profilecategory.php");
include_once($prevFolder."classes/profileoption.php");


$profileCatObj = new ProfileCategory($mysqli);
$profileOptionObj = new ProfileOption($mysqli);

if($_POST['submit']) {

	// Check Option Name
	
	if(trim($_POST['optionname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must give the profile option a name.<br>";
	}

	
	// Check Category
	
	if(!$profileCatObj->select($_POST['optioncategory'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid profile category.<br>";
	}
	else {
		
		// Check Order
		$arrProfileOptionIDs = $profileCatObj->getAssociateIDs();
		
		$blnOrderCheck1 = $_POST['optionorder'] == "first" && count($arrProfileOptionIDs) > 0;
		$blnOrderCheck2 = !in_array($_POST['optionorder'], $arrProfileOptionIDs) && $_POST['optionorder'] != "first";
		$blnOrderCheck3 = !$profileOptionObj->select($_POST['optionorder']) && $_POST['optionorder'] != "first";
		
		
		if($blnOrderCheck1 || $blnOrderCheck2 || $blnOrderCheck3) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid profile option order.<br>";	
		}
		elseif($_POST['optionorder'] == "first") {
			$intNewSortNum = 1;
		}
		else {
			
			if($_POST['beforeafter'] == "before" || $_POST['beforeafter'] == "after") {
				
				$intNewSortNum = $profileOptionObj->makeRoom($_POST['beforeafter']);
				
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid profile option order.<br>";
			}
			
			
		}
		
		
	}
	

	if($countErrors == 0) {
		
		if($_POST['optiontype'] != "select") {
			$_POST['optiontype'] = "input";
		}
		
		
		
		$arrColumnNames = array("profilecategory_id", "name", "optiontype", "sortnum");
		$arrColumnValues = array($_POST['optioncategory'], $_POST['optionname'], $_POST['optiontype'], $intNewSortNum);
		
		
		if($profileOptionObj->addNew($arrColumnNames, $arrColumnValues)) {
			
			if($_POST['optiontype'] == "select") {
				
				$counter = 1;
				foreach($_SESSION['btProfileCache'] as $selectValue) {
					
					$profileOptionObj->addNewSelectValue($selectValue, $counter);
					$counter++;
										
				}
				
			}
			$newProfileInfo = $profileOptionObj->get_info_filtered();
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Profile Option: <b>".$newProfileInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Add New Profile Option', '".$MAIN_ROOT."members/console.php', 'successBox');
			</script>
			";
			
			
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database!  Please contact the website administrator.<br>";
		}
		
		
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}

}

if(!$_POST['submit']) {
	$_SESSION['btProfileCache'] = array();
	$catoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$catoptions .= "<option value='".$row['profilecategory_id']."'>".$row['name']."</option>";
	}

	if($result->num_rows == 0) {
		$catoptions = "<option value='none'>No Categories Added!</option>";	
	}
	
	echo "
	<form action='console.php?cID=".$cID."' method='post'>
		<div class='formDiv'>
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new profile option because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
			Fill out the form below to add a new profile option.<br><br>
			
			<table class='formTable'>
				<tr>
					<td colspan='2' class='main'>
						<b>General Information</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Option Name:</td>
					<td class='main'><input type='text' name='optionname' value='".$_POST['optionname']."' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Profile Category:</td>
					<td class='main'><select name='optioncategory' id='optioncategory' class='textBox' onchange='refreshProfileOrder()'>".$catoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Option Order:</td>
					<td class='main' valign='top'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
						<select name='optionorder' id='optionorder' class='textBox'></select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Option Type: <a href='javascript:void(0)' onmouseover=\"showToolTip('An <b>input</b> option type will allow members to type whatever they want into a textbox.  A <b>select</b> option type only has a set number of choices to pick from that you can set below.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<select name='optiontype' id='optiontype' class='textBox' onchange='showMoreOptions()'><option value='input'>Input</option><option value='select'>Select</option></select>
					</td>
				</tr>
			</table>
			<div id='moreOptions' style='display: none; margin-top: 5px'>
				<table class='formTable'>
					<tr>
						<td colspan='3' class='main'>
							<b>Select Values</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
							<div style='padding-left: 3px; padding-bottom: 15px'>
								Use this section to add new values to the select box for this profile option.
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>
							New Select Value:
						</td>
						<td class='main' style='width: 205px'>
							<input type='text' id='newSelectValue' class='textBox' style='width: 200px'>
						</td>
						<td class='main'>
							<input type='button' value='Add' class='submitButton' onclick='addSelectValue()'>
						</td>
					</tr>
				</table>
				<div id='loadingSpiral' class='loadingSpiral'>
					<p align='center'>
						<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
					</p>
				</div>
				<div id='selectValueList' style='margin-top: 25px'>
				
				</div>
				<br><br>
			</div>
			<p align='center'>
				<br>
				<input type='submit' name='submit' value='Add Profile Option' class='submitButton'>
			</p>
		</div>
	</form>
	<div id='editValuePopup' style='display: none'></div>
	<script type='text/javascript'>
		function refreshProfileOrder() {
			
			$(document).ready(function() {
			
				$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/profilelist.php', { catID: $('#optioncategory').val() }, function(data) {
				
					$('#optionorder').html(data);
				
				});
			
			});
		
		}
		
		function showMoreOptions() {
			if($('#optiontype').val() == \"select\") {
				$('#moreOptions').fadeIn(400);
			}
			else {
				$('#moreOptions').hide();
			}
		}
		
		
		
		function addSelectValue() {
		
			$(document).ready(function() {
			
				$('#loadingSpiral').show();
				$('#selectValueList').hide();
				$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/cache/add.php', { selectValue: $('#newSelectValue').val() }, function(data) {
					$('#newSelectValue').val('');
					$('#selectValueList').html(data);
					$('#loadingSpiral').hide();
					$('#selectValueList').fadeIn(400);
					
				});
				
			});
		
		}
		
		
		
		
		
		refreshProfileOrder();
		
	</script>
	";

}

?>