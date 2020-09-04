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
include_once("../classes/btupload.php");

if($_POST['submit']) {
	
	$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");
	
	// Check Name
	
	if(trim($_POST['statusname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Status name may not be blank.<br>";
		$countErrors++;
	}
	
	// Check Display Order
	$intNewOrderNum = $diplomacyStatusObj->validateOrder($_POST['displayorder'], $_POST['beforeafter']);
	if($intNewOrderNum === false) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		$countErrors++;
	}
	
	
	$statusImageURL = "";
	if($countErrors == 0) {
		// If no errors, check for image upload and try to upload the image
		if($_FILES['statusimagefile']['name'] != "") {
			
			$uploadImg = new BTUpload($_FILES['statusimagefile'], "status_", "../images/diplomacy/", array(".jpg", ".png", ".gif", ".bmp"));
			if(!$uploadImg->uploadFile()) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload status image. Please make sure the file size is not too big and it has an acceptable file extension.<br>";
			}
			else {
				$statusImageURL = "images/diplomacy/".$uploadImg->getUploadedFileName();		
			}
			
			
		}
		else {
			
			$uploadImg = new BTUpload($_POST['statusimageurl'], "status_", "../images/diplomacy/", array(".jpg", ".png", ".gif", ".bmp"), 4, true);
			if(!$uploadImg->uploadFile()) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to download status image from remote url. You may need to first download the image and upload normally.<br>";
			}
			else {
				$statusImageURL = "images/diplomacy/".$uploadImg->getUploadedFileName();
			}
			
			
			//$statusImageURL = $_POST['statusimageurl'];	
			
		}
		
		// If there are still no errors after uploading the image, add to db
		if($countErrors == 0) {
			
			$arrColumns = array("name", "imageurl", "imagewidth", "imageheight", "ordernum");
			$arrValues = array($_POST['statusname'], $statusImageURL, $_POST['imagewidth'], $_POST['imageheight'], $intNewOrderNum);
		
			if($diplomacyStatusObj->addNew($arrColumns, $arrValues)) {
				
				echo "
				
					<div style='display: none' id='successBox'>
						<p align='center'>
							Successfully added the ".$diplomacyStatusObj->get_info_filtered("name")." status to the diplomacy page!
						</p>
					</div>
					
					<script type='text/javascript'>
						popupDialog('Add New Diplomacy Status', '".$MAIN_ROOT."members', 'successBox');
					</script>
				
				
				";
				
				
				
				$member->logAction("Added the ".$_POST['statusname']." status to the diplomacy page.");
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
			}
		}
		
	}
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);	
		$_POST['submit'] = false;
	}
	
	
	
	

}


if(!$_POST['submit']) {
	
	$orderoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$orderoptions .= "<option value='".$row['diplomacystatus_id']."'>".filterText($row['name'])."</option>";
		
	}
	
	if($orderoptions == "") {
		$orderoptions = "<option value='first'>This is the first status</option>";	
	}
	
	
	echo "
		<div class='formDiv'>
		";
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to add new diplomacy status because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
	
		echo "
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post' enctype='multipart/form-data'>
				Use the form below to add a new diplomacy status type to your diplomacy page.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Name:</td>
						<td class='main'><input type='text' name='statusname' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Status Image: <a href='javascript:void(0)' onmouseover=\"showToolTip('Using an image is optional.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							File:<br><input type='file' name='statusimagefile' class='textBox' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
							<p><b><i>OR</i></b></p>
							URL:<br><input type='text' name='statusimageurl' value='".$_POST['statusimageurl']."' class='textBox' style='width: 250px'>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Image Width:</td>
						<td class='main'><input type='text' name='imagewidth' class='textBox' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Image Height:</td>
						<td class='main'><input type='text' name='imageheight' class='textBox' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='displayorder' class='textBox' style='margin-top: 3px'>
								".$orderoptions."
							</select>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Add Status' class='submitButton' style='width: 125px'>
						</td>
					</tr>
				</table>
			</form>
		</div>
		
	";

}

?>