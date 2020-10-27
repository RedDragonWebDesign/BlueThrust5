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


include($prevFolder."classes/profilecategory.php");
$profileCatObj = new ProfileCategory($mysqli);
$cID = $_GET['cID'];



if($_POST['submit']) {
	
	$countErrors = 0;
	
	// Check Category Name
	
	if(trim($_POST['catname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a Category Name.<br>";
	}
	
	
	// Check Category Order
	
	$intNewOrderSpot = $profileCatObj->validateOrder($_POST['catorder'], $_POST['beforeafter']);
	
	
	if($intNewOrderSpot === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order.<br>";
	}
	
	

	
	if($countErrors == 0) {
		
		$arrColumns = array("name", "ordernum");
		$arrValues = array($_POST['catname'], $intNewOrderSpot);
		
		if($profileCatObj->addNew($arrColumns, $arrValues)) {
			$profileCatInfo = $profileCatObj->get_info_filtered();
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Profile Category: <b>".$profileCatInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add Profile Category', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save category to the database.  Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	
	if($countErrors > 0) {
		
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
		
	}
	
	
}

if(!$_POST['submit']) {
	
	
	
	$countCategories = 0;
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$catOrderOptions .= "<option value='".$row['profilecategory_id']."'>".filterText($row['name'])."</option>";
		$countCategories++;
	}

	if($countCategories == 0) {
		$catOrderOptions = "<option value='first'>(no other categories)</option>";
	}
	
	
	echo "
	<form action='console.php?cID=".$cID."' method='post'>
		<div class='formDiv'>
		
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new profile category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	
	echo "
		
			Fill out the form below to add a new profile category.<br><br>
		
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Category Name:</td>
					<td class='main'><input type='text' name='catname' value='".$_POST['catname']."' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
						<select name='catorder' class='textBox'>".$catOrderOptions."</select>
					</td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br>
						<input type='submit' name='submit' value='Add Profile Category' class='submitButton'>
					</td>
				</tr>
			</table>
		</div>
	</form>
	";
	
}



?>