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
include_once($prevFolder."classes/consolecategory.php");
$cID = $_GET['cID'];

$consoleCatObj = new ConsoleCategory($mysqli);


if(!$consoleCatObj->select($_GET['catID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");	
}


$consoleCatInfo = $consoleCatObj->get_info_filtered();

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Console Categories</a> > ".$consoleCatInfo['name']."\");
});
</script>
";

if($_POST['submit']) {
	
	$resortOrder = false;
	// Check Category Name
	
	if(trim($_POST['catname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a Category Name.<br>";
	}
	
	
	
	// Check Cat Order
	
	
	$intNewOrderSpot = "";
	if(!$consoleCatObj->select($_POST['catorder']) AND $_POST['catorder'] != "first") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order. (category)<br>";
	}
	elseif($_POST['catorder'] == "first") {
		// "(no other categories)" selected, check to see if there are actually no other categories
	
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory WHERE adminoption = '0'");
		$num_rows = $result->num_rows;
	
		if($num_rows > 1) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order. (category)<br>";
		}
		else {
			$intNewOrderSpot = 1;
		}
	
	}
	else {
	
		if($_POST['beforeafter'] != "before" AND $_POST['beforeafter'] != "after") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order. (before/after)<br>";
		}
		else {
			
			$catOrderOrderNum = $consoleCatObj->get_info("ordernum");
			
			$addTo = -1;
			if($_POST['beforeafter'] == "before") {
				$addTo = 1;	
			}
			
			$checkOrderNum = $catOrderOrderNum+$addTo;
			if($checkOrderNum != $consoleCatInfo['ordernum']) {
				$intNewOrderSpot = $consoleCatObj->makeRoom($_POST['beforeafter']);
			}
			
		}
	
	
	}
	
	
	
	if($countErrors == 0) {
	
		
		$updateColumns = array("name");
		$updateValues = array($_POST['catname']);
		
		if($intNewOrderSpot != "") {
			$resortOrder = true;
			
			$updateColumns[] = "ordernum";
			$updateValues[] = $intNewOrderSpot;
			
		}
		
		$consoleCatObj->select($consoleCatInfo['consolecategory_id']);
		if($consoleCatObj->update($updateColumns, $updateValues)) {
	
	
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Console Category!
			</p>
			</div>
	
			<script type='text/javascript'>
			popupDialog('Edit Console Category', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			";
			
			if($resortOrder) {
				$consoleCatObj->resortOrder();	
			}
			
		}
	
	}
	else {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
}



if(!$_POST['submit']) {

	$countCategories = 0;
	
	
	$intHighestOrderNum = $consoleCatObj->getHighestOrderNum();
	$afterSelected = "";
	
	if($consoleCatInfo['ordernum'] == 1) {
		$selectCat = $consoleCatInfo['ordernum']+1;
		$afterSelected = "selected";
	}
	else {
		$selectCat = $consoleCatInfo['ordernum']-1;	
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory WHERE adminoption = '0' AND consolecategory_id != '".$consoleCatInfo['consolecategory_id']."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$strSelected = "";
		if($selectCat == $row['ordernum']) {
			$strSelected = "selected";
		}
		
		$catOrderOptions .= "<option value='".$row['consolecategory_id']."' ".$strSelected.">".filterText($row['name'])."</option>";
		$countCategories++;
	}
	
	if($countCategories == 0) {
		$catOrderOptions = "<option value='first'>(no other categories)</option>";	
	}
	
	echo "
	
		<form action='console.php?cID=".$cID."&catID=".$_GET['catID']."&action=edit' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit console category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Use the form below to edit the selected console category.<br><br>
				
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' name='catname' class='textBox' value = '".$consoleCatInfo['name']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Category Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
							<select name='catorder' class='textBox'>".$catOrderOptions."</select>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'>
							<br><br>
							<input type='submit' name='submit' value='Edit Console Category' class='submitButton' style='width: 175px'><br><br>
						</td>
					</tr>
				</table>
			</div>
		</form>
	
	";
	
}

?>