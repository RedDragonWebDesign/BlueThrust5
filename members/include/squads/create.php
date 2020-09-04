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

include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/squad.php");
$cID = $_GET['cID'];
$dispError = "";
$countErrors = 0;
if($_POST['submit']) {
	
	
	// Check Squad Name
	if(trim($_POST['squadname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a squad name.<br>";
	}
	
	
	if($_FILES['uploadlogo']['name'] != "") {

		$uploadLogoObj = new BTUpload($_FILES['uploadlogo'], "squad_", "../images/squads/", array(".png", ".jpg", ".gif", ".bmp"));
		
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
		$newSquadObj = new Squad($mysqli);
		
		if($_POST['recruiting'] != 0) {
			$_POST['recruiting'] = 1;	
		}
		
		if($_POST['shoutbox'] != 0) {
			$_POST['shoutbox'] = 1;	
		}
		
		$time = time();
		$arrColumns = array("member_id", "name", "description", "logourl", "recruitingstatus", "datecreated", "privateshoutbox", "website");
		$arrValues = array($memberInfo['member_id'], $_POST['squadname'], $_POST['squaddesc'], $logoImageURL, $_POST['recruiting'], $time, $_POST['shoutbox'], $_POST['squadsite']);
		
		if($newSquadObj->addNew($arrColumns, $arrValues)) {
			
			$newSquadInfo = $newSquadObj->get_info_filtered();
			
			$arrColumns = array("squad_id", "name", "sortnum", "postnews", "managenews", "postshoutbox", "manageshoutbox", "addrank", "manageranks", "editprofile", "sendinvites", "acceptapps", "setrank", "removemember");
			$arrValues = array($newSquadInfo['squad_id'], "Founder", 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
			
			$checkAddRank = $newSquadObj->objSquadRank->addNew($arrColumns, $arrValues);
			$squadRankInfo = $newSquadObj->objSquadRank->get_info();
			
			$checkAddMember = $newSquadObj->objSquadMember->addNew(array("squad_id", "member_id", "squadrank_id", "datejoined"), array($newSquadInfo['squad_id'], $memberInfo['member_id'], $squadRankInfo['squadrank_id'], $time));
			
			if($checkAddRank && $checkAddMember) {
				
				echo "
				
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Created New Squad: <b>".$newSquadInfo['name']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Create a Squad', '".$MAIN_ROOT."members', 'successBox');
				</script>
				
				";
				
				
				
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
			}
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
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
		<strong>Unable to create squad because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Use the form below to create a squad.<br><br>
				
				<b><span style='text-decoration: underline'>NOTE:</span></b> Once you create your squad, you will be given the rank of founder.  You will be able to add ranks and set squad rank permissions once your squad is created.<br><br>
	
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Squad Name:</td>
						<td class='main'><input type='text' name='squadname' value='".$_POST['squadname']."' class='textBox' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Squad Logo:</td>
						<td class='main'>File:<br>
							<input type='file' class='textBox' name='uploadlogo' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>&nbsp;&nbsp;&nbsp;<b>&middot;</b> Dimensions: 400x100 pixels<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> File Types: .jpg, .gif, .png, .bmp<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
							<p><br><b><i>OR</i></b><br></p>
							URL:<br>
							<input type='text' class='textBox' name='logourl' value='".$_POST['logourl']."' style='width: 250px'>
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Squad Website: <a href='javascript(0)' onmouseover=\"showToolTip('Leave blank to disable website from showing on the squad profile page.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<input type='text' name='squadsite' value='".$_POST['squadsite']."' class='textBox' style='width: 250px'>
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>About the Squad:</td>
						<td class='main'>
							<textarea name='squaddesc' class='textBox' cols='40' rows='5'>".$_POST['squaddesc']."</textarea>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Recruiting Status:</td>
						<td class='main'>
							<select name='recruiting' class='textBox'><option value='1'>Open</option><option value='0'>Closed</option></select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Shoutbox Status:</td>
						<td class='main'>
							<select name='shoutbox' class='textBox'><option value='1'>Public</option><option value='0'>Private</option></select>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br><br>
							<input type='submit' name='submit' value='Create Squad' style='width: 125px' class='submitButton'>
						</td>
					</tr>
					
				</table>
			</div>
		</form>
	";
	
}



?>