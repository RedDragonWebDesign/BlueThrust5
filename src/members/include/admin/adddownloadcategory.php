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

include_once($prevFolder."classes/downloadcategory.php");
$cID = $_GET['cID'];

$downloadCatObj = new DownloadCategory($mysqli);
$downloadExtObj = new Basic($mysqli, "download_extensions", "extension_id");

if($_POST['submit']) {
	
	// Check Category Name
	
	if(trim($_POST['catname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a Category Name.<br>";
	}
	
	// Check Extensions
	
	$arrExtensions = explode(",", trim($_POST['catexts']));
	
	if(count($arrExtensions) <= 0) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter at least one download extension.<br>";
	}
	
	
	// Check Cat Order
	

	$intNewOrderSpot = "";
	if(!$downloadCatObj->select($_POST['catorder']) AND $_POST['catorder'] != "first") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order. (category)<br>";
	}
	elseif($_POST['catorder'] == "first") {
		// "(no other categories)" selected, check to see if there are actually no other categories
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory");
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
			$intNewOrderSpot = $downloadCatObj->makeRoom($_POST['beforeafter']);	
		}
		
		
	}
	
	
	
	if($countErrors == 0) {
		
		$accessKey = ($_POST['accesskey'] != 1) ? 0 : 1;
		
		if($downloadCatObj->addNew(array("name", "ordernum", "accesstype"), array($_POST['catname'], $intNewOrderSpot, $accessKey))) {
			
			$downloadCatInfo = $downloadCatObj->get_info_filtered();
			
			foreach($arrExtensions as $strExtension) {
				$downloadExtObj->addNew(array("downloadcategory_id", "extension"), array($downloadCatInfo['downloadcategory_id'], trim($strExtension)));
			}
			
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Download Category: <b>".$downloadCatInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add New Download Category', '".$MAIN_ROOT."members', 'successBox');
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
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$catOrderOptions .= "<option value='".$row['downloadcategory_id']."'>".filterText($row['name'])."</option>";
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
		<strong>Unable to add new download category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Fill out the form below to add a new download category.<br><br>
				
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
						<td class='formLabel'>Extensions: <a href='javascript:void(0)' onmouseover=\"showToolTip('Enter the acceptable extensions for downloads in this category.  Separate multiple extensions with a comma (,).')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><input type='text' name='catexts' class='textBox' value='".$_POST['catexts']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Access Type:</td>
						<td class='main'><select name='accesstype' class='textBox'><option value='0'>Everyone</option><option value='1'>Members Only</option></select></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'>
							<br><br>
							<input type='submit' name='submit' value='Add New Download Category' class='submitButton'><br><br>
						</td>
					</tr>
				</table>
			</div>
		</form>
	
	";

}


?>