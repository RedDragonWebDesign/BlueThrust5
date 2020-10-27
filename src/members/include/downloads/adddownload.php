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

include_once($prevFolder."classes/downloadcategory.php");
include_once($prevFolder."classes/download.php");

$downloadObj = new Download($mysqli);
$downloadCatObj = new DownloadCategory($mysqli);
$downloadExtObj = new Basic($mysqli, "download_extensions", "extension_id");


$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;

$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory WHERE specialkey = '' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrDownloadCat[] = $row['downloadcategory_id'];

	$dispSelected = (isset($_GET['catID']) && $_GET['catID'] == $row['downloadcategory_id']) ? " selected" : "";
	
	$downloadcatoptions .= "<option value='".$row['downloadcategory_id']."'".$dispSelected.">".$row['name']."</option>";
	$downloadCatObj->select($row['downloadcategory_id']);
	$arrExtensions = array();
	foreach($downloadCatObj->getExtensions() as $downloadExtID) {
		$downloadExtObj->select($downloadExtID);
		$arrExtensions[] = $downloadExtObj->get_info_filtered("extension");
	}
	$dispExtensions = implode(", ", $arrExtensions);
	$downloadCatJS .= "arrCatExtension[".$row['downloadcategory_id']."] = '".$dispExtensions."';
	";
}

if(count($arrDownloadCat) == 0) {
	
	echo "
		<div style='display: none' id='errorBox'>
			<p align='center'>
				A download category must be added before adding downloads!
			</p>
		</div>
		
		<script type='text/javascript'>
			popupDialog('Add Download', '".$MAIN_ROOT."members', 'errorBox');
		</script>
	";
	
	exit();
}




if($_POST['submit']) {
	
	// Check Name
	if(trim($_POST['title']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must give your download a title.<br>";
	}
	
	
	// Check Section
	
	if(!in_array($_POST['section'], $arrDownloadCat)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid download section.<br>";
	}
	
	
	
	$blnUploaded = false;
	// Check Upload
	if(trim($_FILES['uploadfile']['name']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must select a file to upload.<br>";
	}
	elseif($countErrors == 0 && $downloadObj->uploadFile($_FILES['uploadfile'], $prevFolder."downloads/files/", $_POST['section'])) {
		
		$blnUploaded = true;
		$arrDLColumns = array("downloadcategory_id", "member_id", "dateuploaded", "filename", "mimetype", "filesize", "splitfile1", "splitfile2", "name", "description");
		$splitFiles = $downloadObj->getSplitNames();
		$fileSize = $downloadObj->getFileSize();
		$mimeType = $downloadObj->getMIMEType();
		
		$arrDLValues = array($_POST['section'], $memberInfo['member_id'], time(), $_FILES['uploadfile']['name'], $mimeType, $fileSize, "downloads/files/".$splitFiles[0], "downloads/files/".$splitFiles[1], $_POST['title'], $_POST['description']);
		
		if($downloadObj->addNew($arrDLColumns, $arrDLValues)) {
		
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Added Download!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Add Download', '".$MAIN_ROOT."members', 'successBox');
				</script>

			";
			
		}
		else {
			
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
			
			
		}
		
	}
	
	
	if(!$blnUploaded) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload file.  Make sure the file is not too big and has a valid extension.<br>";
	}
	
	
	if($countErrors > 0) {
		
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
		
	}
	
	
}



if(!$_POST['submit']) {
	
	echo "
		<form action='console.php?cID=".$cID."' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add download because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Use the form below to upload a file to the downloads section.<br>
				<table class='formTable'>
					<tr>
						<td class='formLabel' valign='top'>Section:</td>
						<td class='main'>
							<select name='section' id='downloadSection' class='textBox'>".$downloadcatoptions."</select><br>
							<span class='tinyFont'>File Types: <span id='extensionDiv'>".$dispExtensions."</span></div> | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Upload:</td>
						<td class='main'><input type='file' name='uploadfile' class='textBox' style='border: 0px; width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Title:</td>
						<td class='main'><input type='text' value='".$_POST['title']."' class='textBox' name='title' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Description:</td>
						<td class='main'>
							<textarea name='description' class='textBox' style='width: 250px; height: 100px'>".$_POST['description']."</textarea>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Add Download' class='submitButton'>
						</td>
					</tr>
				</table>
			
			</div>
		</form>
		<script type='text/javascript'>
			
			arrCatExtension = new Array();
			".$downloadCatJS."
		
			$(document).ready(function() {
			
				$('#downloadSection').change(function() {
					$('#extensionDiv').html(arrCatExtension[$('#downloadSection').val()]);
				
				});
				
				$('#downloadSection').change();
			
			});
		
		</script>
		
	";
	
}


?>