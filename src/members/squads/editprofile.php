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


if(!isset($member) || !isset($squadObj) || substr($_SERVER['PHP_SELF'], -strlen("managesquad.php")) != "managesquad.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$squadObj->select($sID);


	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "editprofile")) {

		exit();
	}
}
include_once("../../classes/btupload.php");

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Edit Squad Profile\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Edit Squad Profile\");
});
</script>
";



if($_POST['submit']) {
	
	// Check Squad Name
	if(trim($_POST['squadname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a squad name.<br>";
	}
	
	
	if($_FILES['uploadlogo']['name'] != "") {
	
		$uploadLogoObj = new BTUpload($_FILES['uploadlogo'], "squad_", "../../images/squads/", array(".png", ".jpg", ".gif", ".bmp"));
	
		if(!$uploadLogoObj->uploadFile()) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload the squad logo. Please make sure the file extension is either .jpg, .png, .gif or .bmp and that the file size is not too big.<br>";
		}
		else {
			$logoImageURL = $MAIN_ROOT."images/squads/".$uploadLogoObj->getUploadedFileName();
		}
	
	}
	else {
		$logoImageURL = $_POST['logourl'];
	}
	
	
	if($countErrors == 0) {
	
		if($_POST['recruiting'] != 0) {
			$_POST['recruiting'] = 1;
		}
	
		if($_POST['shoutbox'] != 0) {
			$_POST['shoutbox'] = 1;
		}
	
		$time = time();
		$arrColumns = array("name", "description", "logourl", "recruitingstatus", "privateshoutbox", "website");
		$arrValues = array($_POST['squadname'], $_POST['squaddesc'], $logoImageURL, $_POST['recruiting'], $_POST['shoutbox'], $_POST['squadsite']);
	
		if($squadObj->update($arrColumns, $arrValues)) {
	
			$squadInfo = $squadObj->get_info_filtered();

			echo "

			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Squad Profile</b>!
			</p>
			</div>

			<script type='text/javascript'>
			popupDialog('Edit Squad Profile', '".$MAIN_ROOT."squads/profile.php?sID=".$_GET['sID']."', 'successBox');
			</script>

			";
	
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
	
	}
	
}

$privateSelected = "";
if($squadInfo['privateshoutbox'] == 0) {
	$privateSelected = "selected";	
}


$closeSelected = "";
if($squadInfo['recruitingstatus'] == 0) {
	$closeSelected = "selected";
}


echo "
	<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=EditProfile' method='post' enctype='multipart/form-data'>
		<div class='formDiv'>

";

if($dispError != "") {
	echo "
	<div class='errorDiv'>
	<strong>Unable to create squad because the following errors occurred:</strong><br><br>
	$dispError
	</div>
	";
}

echo "

	Use the form below to edit your squad information.<br><br>
			
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Squad Name:</td>
					<td class='main'><input type='text' name='squadname' value='".$squadInfo['name']."' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Squad Logo:</td>
					<td class='main'>File:<br>
						<input type='file' class='textBox' name='uploadlogo' style='width: 250px; border: 0px'><br>
						<span style='font-size: 10px'>&nbsp;&nbsp;&nbsp;<b>&middot;</b> Dimensions: 400x100 pixels<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> File Types: .jpg, .gif, .png, .bmp<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						<p><br><b><i>OR</i></b><br></p>
						URL:<br>
						<input type='text' class='textBox' name='logourl' value='".$squadInfo['logourl']."' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Squad Website: <a href='javascript:void(0)' onmouseover=\"showToolTip('Leave blank to disable website from showing on the squad profile page.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<input type='text' name='squadsite' value='".$squadInfo['website']."' class='textBox' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>About the Squad:</td>
					<td class='main'>
						<textarea name='squaddesc' class='textBox' cols='40' rows='5'>".$squadInfo['description']."</textarea>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Recruiting Status:</td>
					<td class='main'>
						<select name='recruiting' class='textBox'><option value='1'>Open</option><option value='0' ".$closeSelected.">Closed</option></select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Shoutbox Status:</td>
					<td class='main'>
						<select name='shoutbox' class='textBox'><option value='1'>Public</option><option value='0' ".$privateSelected.">Private</option></select>
					</td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br><br>
						<input type='submit' name='submit' value='Edit Squad Profile' style='width: 145px' class='submitButton'>
					</td>
				</tr>
				
			</table>
		</div>
	</form>

";