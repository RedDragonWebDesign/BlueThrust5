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
$downloadExtensionObj = new Basic($mysqli, "download_extensions", "extension_id");

if(!$downloadCatObj->select($_GET['catID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");	
}


$downloadCatInfo = $downloadCatObj->get_info_filtered();

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Download Categories</a> > ".$downloadCatInfo['name']."\");
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
			
			$catOrderOrderNum = $downloadCatObj->get_info("ordernum");
			
			$addTo = -1;
			if($_POST['beforeafter'] == "before") {
				$addTo = 1;	
			}
			
			$checkOrderNum = $catOrderOrderNum+$addTo;
			if($checkOrderNum != $downloadCatInfo['ordernum']) {
				$intNewOrderSpot = $downloadCatObj->makeRoom($_POST['beforeafter']);
			}
			
		}
	
	
	}
	
	
	
	if($countErrors == 0) {
	
		
		$updateColumns = array("name", "accesstype");
		$updateValues = array($_POST['catname'], $_POST['accesstype']);
		
		if($intNewOrderSpot != "") {
			$resortOrder = true;
			
			$updateColumns[] = "ordernum";
			$updateValues[] = $intNewOrderSpot;
			
		}
		
		$downloadCatObj->select($downloadCatInfo['downloadcategory_id']);
		if($downloadCatObj->update($updateColumns, $updateValues)) {
	
			$result = $mysqli->query("DELETE FROM ".$mysqli->get_tablePrefix()."download_extensions WHERE downloadcategory_id = '".$downloadCatInfo['downloadcategory_id']."'");
			
			foreach($arrExtensions as $strExtension) {
				$downloadExtensionObj->addNew(array("downloadcategory_id", "extension"), array($downloadCatInfo['downloadcategory_id'], trim($strExtension)));
			}
	
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Edited Download Category!
				</p>
			</div>
	
			<script type='text/javascript'>
				popupDialog('Edit Download Category', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			";
			
			if($resortOrder) {
				$downloadCatObj->resortOrder();	
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
	
	
	$intHighestOrderNum = $downloadCatObj->getHighestOrderNum();
	$afterSelected = "";
	
	if($downloadCatInfo['ordernum'] == 1) {
		$selectCat = $downloadCatInfo['ordernum']+1;
		$afterSelected = "selected";
	}
	else {
		$selectCat = $downloadCatInfo['ordernum']-1;	
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory WHERE downloadcategory_id != '".$downloadCatInfo['downloadcategory_id']."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$strSelected = "";
		if($selectCat == $row['ordernum']) {
			$strSelected = "selected";
		}
		
		$catOrderOptions .= "<option value='".$row['downloadcategory_id']."' ".$strSelected.">".filterText($row['name'])."</option>";
		$countCategories++;
	}
	
	if($countCategories == 0) {
		$catOrderOptions = "<option value='first'>(no other categories)</option>";	
	}
	
	$arrDownloadExts = $downloadCatObj->getExtensions();

	$arrDispDLExts = array();
	
	foreach($arrDownloadExts as $extID) {
		$downloadExtensionObj->select($extID);
		$arrDispDLExts[] = $downloadExtensionObj->get_info_filtered("extension");
	}
	
	$dispDownloadExts = implode(", ", $arrDispDLExts);
	
	
	echo "
	
		<form action='console.php?cID=".$cID."&catID=".$_GET['catID']."&action=edit' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit download category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	$selectAccessType = ($downloadCatInfo['accesstype'] == 1) ? " selected" : "";
	echo "
				Use the form below to edit the selected download category.<br><br>
				
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' name='catname' class='textBox' value = '".$downloadCatInfo['name']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Category Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
							<select name='catorder' class='textBox'>".$catOrderOptions."</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Extensions: <a href='javascript:void(0)' onmouseover=\"showToolTip('Enter the acceptable extensions for downloads in this category.  Separate multiple extensions with a comma (,).')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><input type='text' name='catexts' class='textBox' value='".$dispDownloadExts."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Access Type:</td>
						<td class='main'><select name='accesstype' class='textBox'><option value='0'>Everyone</option><option value='1'".$selectAccessType.">Members Only</option></select></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'>
							<br><br>
							<input type='submit' name='submit' value='Edit Download Category' class='submitButton' style='width: 175px'><br><br>
						</td>
					</tr>
				</table>
			</div>
		</form>
	
	";
	
}

?>