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



include_once($prevFolder."classes/profilecategory.php");
$cID = $_GET['cID'];

$profileCatObj = new ProfileCategory($mysqli);


if(!$profileCatObj->select($_GET['catID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}

$profileCatInfo = $profileCatObj->get_info_filtered();


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Profile Categories</a> > ".$profileCatInfo['name']."\");
});
</script>
";

$dispError = "";
if($_POST['submit']) {
	
	$countErrors = 0;
	
	
	// Check Cat Name
	
	if(trim($_POST['catname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a Category Name.<br>";		
	}
	
	
	// Check Category Order
	
	$intNewOrderSpot = $profileCatObj->validateOrder($_POST['catorder'], $_POST['beforeafter'], true, $profileCatInfo['ordernum']);
	
	
	if($intNewOrderSpot === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order.<br>";
	}
	
	
	if($countErrors == 0) {
		
		$arrUpdateColumn = array("name");
		$arrUpdateValues = array($_POST['catname']);
		
		$resortOrder = false;
		if($intNewOrderSpot != $profileCatInfo['ordernum']) {
			$arrUpdateColumn[] = "ordernum";
			$arrUpdateValues[] = $intNewOrderSpot;
			$resortOrder = true;
		}
		
		
		$profileCatObj->select($profileCatInfo['profilecategory_id']);
		if($profileCatObj->update($arrUpdateColumn, $arrUpdateValues)) {
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Edited Profile Category!
				</p>
			</div>
	
			<script type='text/javascript'>
				popupDialog('Edit Profile Category', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			
			";
			
			
			$profileCatObj->resortOrder();	
			
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database! Please contact the website administrator.<br>";
		}
		
		
		
	}
	
	
	if($countErrors == 1) {
		
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
		
	}
	
	
}


if(!$_POST['submit']) {
	
	$countCategories = 0;
	
	$afterSelected = "";
	if($profileCatInfo['ordernum'] == 1) {
		$selectCat = $profileCatInfo['ordernum']+1;
		$afterSelected = "selected";
	}
	else {
		$selectCat = $profileCatInfo['ordernum']-1;	
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory WHERE profilecategory_id != '".$profileCatInfo['profilecategory_id']."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$strSelected = "";
		if($selectCat == $row['ordernum']) {
			$strSelected = "selected";
		}
		
		$catOrderOptions .= "<option value='".$row['profilecategory_id']."' ".$strSelected.">".filterText($row['name'])."</option>";
		$countCategories++;
	}
	
	if($countCategories == 0) {
		$catOrderOptions = "<option value='first'>(no other categories)</option>";	
	}
	
	
	echo "
	<form action='console.php?cID=".$cID."&catID=".$profileCatInfo['profilecategory_id']."&action=edit' method='post'>
	<div class='formDiv'>
	
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit profile category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	
	echo "
	
	Fill out the form below to edit the selected profile category.<br><br>
		
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Category Name:</td>
					<td class='main'><input type='text' name='catname' value='".$profileCatInfo['name']."' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
						<select name='catorder' class='textBox'>".$catOrderOptions."</select>
					</td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br>
						<input type='submit' name='submit' value='Edit Profile Category' style='width: 150px' class='submitButton'>
					</td>
				</tr>
			</table>
		</div>
	</form>

	";
	
	
}


?>