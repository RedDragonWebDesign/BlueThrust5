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


if(!defined("EDIT_FOLDER")) {
	exit();	
}

$folderInfo = $pmFolderObj->get_info_filtered();

if($_POST['submit']) {
	
// Check Folder Name
	if(trim($_POST['foldername']) == "") {
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Your folder name may not be blank.";	
		$countErrors++;
	}
	
	// Check Folder Order
	$pmFolderObj->setCategoryKeyValue($memberInfo['member_id']);
	$intNewOrderSpot = $pmFolderObj->validateOrder($_POST['folderorder'], $_POST['beforeafter'], true, $folderInfo['sortnum']);
	
	if($intNewOrderSpot === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid folder order.<br>";
	}
	
	$pmFolderObj->select($folderInfo['pmfolder_id']);
	if($countErrors == 0) {
		$arrColumns = array("name", "sortnum");
		$arrValues = array($_POST['foldername'], $intNewOrderSpot);
		
		if($pmFolderObj->update($arrColumns, $arrValues)) {
			
			$folderInfo = $pmFolderObj->get_info_filtered();
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Edited PM Folder: <b>".$folderInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Edit PM Folder', '".$MAIN_ROOT."members', 'successBox');
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
	
	
	$findBeforeAfter = $pmFolderObj->findBeforeAfter();
	$afterSelected = ($findBeforeAfter[1] == "after") ? " selected" : "";
	
	$pmFolderObj->select($folderInfo['pmfolder_id']);
	
	$folderOptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."privatemessage_folders WHERE pmfolder_id != '".$folderInfo['pmfolder_id']."' AND member_id = '".$memberInfo['member_id']."' ORDER BY sortnum DESC");
	while($row = $result->fetch_assoc()) {
		$dispSelected = ($findBeforeAfter[0] == $row['pmfolder_id']) ? " selected" : "";
		$folderOptions .= "<option value='".$row['pmfolder_id']."'".$dispSelected.">".filterText($row['name'])."</option>";	
	}
	
	if($folderOptions == "") {
		$folderOptions = "<option value='first'>(first folder')</option>";	
	}
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&fID=".$folderInfo['pmfolder_id']."&action=edit' method='post'>
			<div class='formDiv'>
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit folder because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Use the form below to edit the selected folder.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Folder Name:</td>
						<td class='main'><input type='text' name='foldername' class='textBox' value='".$folderInfo['name']."'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$afterSelected.">After</option></select><br>
							<select name='folderorder' class='textBox'>
								".$folderOptions."
							</select>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Edit Folder' class='submitButton'>
						</td>
					</tr>
				</table><br>
			</div>
		</form>
	";
	
}


?>