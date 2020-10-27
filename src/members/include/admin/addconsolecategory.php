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

if($_POST['submit']) {
	
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
		
		if($num_rows > 0) {
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
			$intNewOrderSpot = $consoleCatObj->makeRoom($_POST['beforeafter']);	
		}
		
		
	}
	
	
	
	if($countErrors == 0) {
		
		if($consoleCatObj->addNew(array("name", "ordernum"), array($_POST['catname'], $intNewOrderSpot))) {
			
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Console Category!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add New Console Category', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
		}
		
	}
	else {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
	
	
}


if(!$_POST['submit']) {

	$countCategories = 0;
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory WHERE adminoption = '0' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$catOrderOptions .= "<option value='".$row['consolecategory_id']."'>".filterText($row['name'])."</option>";
		$countCategories++;
	}
	
	if($countCategories == 0) {
		$catOrderOptions = "<option value='first'>(no other categories)</option>";	
	}
	
	echo "
	
		<form action='console.php?cID=$cID' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new console category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Fill out the form below to add a new console category.<br><br>
				
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' name='catname' class='textBox' value='".$_POST['catname']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Category Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='catorder' class='textBox'>".$catOrderOptions."</select>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'>
							<br><br>
							<input type='submit' name='submit' value='Add New Console Category' class='submitButton'><br><br>
						</td>
					</tr>
				</table>
			</div>
		</form>
	
	";

}


?>