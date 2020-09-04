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


$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !$diplomacyStatusObj->select($_GET['sID'])) {
	echo "hi";
	exit();
}


$diplomacyStatusInfo = $diplomacyStatusObj->get_info_filtered();

if($_POST['submit']) {
	
	// Check Name
	
	if(trim($_POST['statusname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Status name may not be blank.<br>";
		$countErrors++;
	}
	
	// Check Display Order
	$intNewOrderNum = $diplomacyStatusObj->validateOrder($_POST['displayorder'], $_POST['beforeafter'], true, $diplomacyStatusInfo['ordernum']);
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
		elseif($_POST['statusimageurl'] != "" && $_POST['statusimageurl'] != $diplomacyStatusInfo['imageurl']) {
	
			$uploadImg = new BTUpload($_POST['statusimageurl'], "status_", "../images/diplomacy/", array(".jpg", ".png", ".gif", ".bmp"), 4, true);
			if(!$uploadImg->uploadFile()) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to download status image from remote url. You may need to first download the image and upload normally.<br>";
			}
			else {
				$statusImageURL = "images/diplomacy/".$uploadImg->getUploadedFileName();
			}
	
		}
		else {
			$statusImageURL = $diplomacyStatusInfo['imageurl'];	
		}
		
		
		// If there are still no errors after uploading the image, add to db
		if($countErrors == 0) {
		
			$arrColumns = array("name", "imageurl", "imagewidth", "imageheight", "ordernum");
			$arrValues = array($_POST['statusname'], $statusImageURL, $_POST['imagewidth'], $_POST['imageheight'], $intNewOrderNum);
		
			$diplomacyStatusObj->select($diplomacyStatusInfo['diplomacystatus_id']);
			
			if($diplomacyStatusObj->update($arrColumns, $arrValues)) {
				
				echo "
				
					<div style='display: none' id='successBox'>
						<p align='center'>
							Successfully edited the ".$diplomacyStatusObj->get_info_filtered("name")." diplomacy status!
						</p>
					</div>
					
					<script type='text/javascript'>
						popupDialog('Edit Diplomacy Status', '".$MAIN_ROOT."members', 'successBox');
					</script>
				
				";
				
				$diplomacyStatusObj->resortOrder();
				
				$member->logAction("Edited the ".$_POST['statusname']." diplomacy status.");
				
				
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
			}
			
		}
		
		
		
		
	}
	
	if($countErrors > 0) {
		$_POST['submit'] = false;
	}
	
	
}


if(!$_POST['submit']) {
	
	
	$arrBeforeAfter = $diplomacyStatusObj->findBeforeAfter();
	
	$afterSelected = "";
	if($arrBeforeAfter[1] == "after") {
		$afterSelected = " selected";	
	}
	
	$orderoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status WHERE diplomacystatus_id != '".$diplomacyStatusInfo['diplomacystatus_id']."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
	
		$dispSelected = "";
		if($arrBeforeAfter[0] == $row['diplomacystatus_id']) {
			$dispSelected = " selected";
		}
		
		$orderoptions .= "<option value='".$row['diplomacystatus_id']."'".$dispSelected.">".filterText($row['name'])."</option>";
		
	
	}
	
	if($orderoptions == "") {
		$orderoptions = "<option value='first'>No other statuses</option>";
	}
	
	
	if(strpos($diplomacyStatusInfo['imageurl'], "http://") === false) {
		
		$arrImageInfo = getimagesize("../".$diplomacyStatusInfo['imageurl']);
	
		if($diplomacyStatusInfo['imagewidth'] == 0) {
			$diplomacyStatusInfo['imagewidth'] = $arrImageInfo[0];
		}
		
		if($diplomacyStatusInfo['imageheight'] == 0) {
			$diplomacyStatusInfo['imageheight'] = $arrImageInfo[1];
		}
		

		
	}
	elseif($diplomacyStatusInfo['imagewidth'] == 0) {
		$popupWidth = 400;
	}
	
	
	echo "<div class='formDiv'>";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit diplomacy status because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	
	
	if(!isset($popupWidth)) { 
			$popupWidth = $diplomacyStatusInfo['imagewidth']+50;
	}
	

	echo "
	<script type='text/javascript'>
	
		function showStatusImage() {
			
			$(document).ready(function() {
				$('#popupStatusImage').dialog({
					title: 'View Status Image',
					modal: true,
					zIndex: 99999,
					width: ".$popupWidth.",
					resizable: false,
					show: \"fade\",
					buttons: {
						\"Ok\": function() {
							$(this).dialog(\"close\");
						}
					}
				});
			});
			$('.ui-dialog :button').blur();
		}
	
	</script>
	";
	
	
	echo "
		<div style='display: none' id='popupStatusImage'><p align='center'><img src='".$MAIN_ROOT.$diplomacyStatusInfo['imageurl']."' width='".$diplomacyStatusInfo['imagewidth']."' height='".$diplomacyStatusInfo['imageheight']."'></div>
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&sID=".$_GET['sID']."&action=edit' method='post' enctype='multipart/form-data'>
				Use the form below to edit the diplomacy status, ".$diplomacyStatusInfo['name'].".
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Name:</td>
						<td class='main'><input type='text' name='statusname' value='".$diplomacyStatusInfo['name']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Status Image: <a href='javascript:void(0)' onmouseover=\"showToolTip('Using an image is optional.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<i>Current Image: <a href='javascript:void(0)' onclick='showStatusImage()'>View Status Image</a></i><br>
							File:<br><input type='file' name='statusimagefile' class='textBox' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
							<p><b><i>OR</i></b></p>
							URL:<br><input type='text' name='statusimageurl' value='".$diplomacyStatusInfo['imageurl']."' class='textBox' style='width: 250px'>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Image Width:</td>
						<td class='main'><input type='text' name='imagewidth' value='".$diplomacyStatusInfo['imagewidth']."' class='textBox' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Image Height:</td>
						<td class='main'><input type='text' name='imageheight' class='textBox' value='".$diplomacyStatusInfo['imageheight']."' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$afterSelected.">After</option></select><br>
							<select name='displayorder' class='textBox' style='margin-top: 3px'>
								".$orderoptions."
							</select>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Edit Status' class='submitButton' style='width: 125px'>
						</td>
					</tr>
				</table>
			</form>
		</div>
	
	
	";
	
	
}



?>