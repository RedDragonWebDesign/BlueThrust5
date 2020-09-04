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

include("../classes/pmfolder.php");

$pmFolderObj = new PMFolder($mysqli);
$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;

if($_POST['submit']) {
	
	// Check Folder Name
	if(trim($_POST['foldername']) == "") {
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Your folder name may not be blank.";	
		$countErrors++;
	}
	
	// Check Folder Order
	$pmFolderObj->setCategoryKeyValue($memberInfo['member_id']);
	$intNewOrderSpot = $pmFolderObj->validateOrder($_POST['folderorder'], $_POST['beforeafter']);
	
	if($intNewOrderSpot === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid folder order.<br>";
	}
	
	if($countErrors == 0) {
		$arrColumns = array("member_id", "name", "sortnum");
		$arrValues = array($memberInfo['member_id'], $_POST['foldername'], $intNewOrderSpot);
		
		if($pmFolderObj->addNew($arrColumns, $arrValues)) {
			
			$folderInfo = $pmFolderObj->get_info_filtered();
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New PM Folder: <b>".$folderInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add PM Folder', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
			$pmFolderObj->resortOrder();
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save folder to the database.  Please contact the website administrator.<br>";	
		}
		
	}
	
	
	
	if($countErrors > 0) {		
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
	
}

if(!$_POST['submit']) {

	$arrFolders = $pmFolderObj->listFolders($memberInfo['member_id']);
	$folderOptions = "";
	foreach($arrFolders as $folderID => $folderName) {
		$folderOptions .= "<option value='".$folderID."'>".filterText($folderName)."</option>";		
	}
	
	if($folderOptions == "") {
		$folderOptions = "<option value='first'>(first folder)</option>";	
	}
	
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
		";	

	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add folder because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}

	echo "
				Use the form below to add new folder for your private messages.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Folder Name:</td>
						<td class='main'><input type='text' name='foldername' value='".$_POST['foldername']."' class='textBox'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='folderorder' class='textBox'>".$folderOptions."</select>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' class='submitButton' value='Add Folder'>
						</td>
					</tr>
				</table>
				<br>
			</div>
		</form>
	";
}


?>
