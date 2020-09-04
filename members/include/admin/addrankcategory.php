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
include_once($prevFolder."classes/rankcategory.php");
$cID = $_GET['cID'];


if(isset($_POST['submit']) && $_POST['submit']) {
	
	$countErrors = 0;
	$dispError = "";
	
	
	// Check Category Name
	
	if(trim($_POST['catname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a Category Name.<br>";
	}
	
	// Check Before/After
	
	if($_POST['beforeafter'] != "before" AND $_POST['beforeafter'] != "after") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (before/after).<br>";
	}

	
	// Check image width
	
	if($_FILES['catimagefile']['name'] == "" AND trim($_POST['catimageurl']) != "" AND $_POST['useimage'] == "1" AND (trim($_POST['catimagewidth']) == "" OR $_POST['catimagewidth'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a valid image width when using an external image.<br>";
	}
	
	
	// Check image height
	
	if($_FILES['catimagefile']['name'] == "" AND trim($_POST['catimageurl']) != "" AND $_POST['useimage'] == "1" AND (trim($_POST['catimageheight']) == "" OR $_POST['catimageheight'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a valid image height when using an external image.<br>";
	}

	
	// Check Order
	
	$rankCatObj = new RankCategory($mysqli);
	
	if($_POST['catorder'] != "first") {
		if(!$rankCatObj->select($_POST['catorder'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (category).<br>";
		}
		else {
			
			$intNewCatOrderNum = $rankCatObj->makeRoom($_POST['beforeafter']);
			if($intNewCatOrderNum == "false") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (category).<br>";			
			}
			
		}
	}
	else {
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory ORDER BY ordernum");
		if($result->num_rows > 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (category).<br>";
		}
		else {
			$intNewCatOrderNum = 1;
		}
	}
	
	
	$strCatImageURL = "";
	// Check Image
	if(isset($_POST['useimage']) && $_POST['useimage'] == 1) {
		
		// Use Image Selected, check for no errors
		
		if($countErrors == 0) {
			
			if($_FILES['catimagefile']['name'] != "") {
				// Image File Selected.... Upload it
				
				$uploadFile = new BTUpload($_FILES['catimagefile'], "rankcat_", "../images/ranks/", array(".jpg",".png",".gif",".bmp"));
				
				if(!$uploadFile->uploadFile()) {
					$countErrors++;
					$dispError .= "<b>&middot;</b> Unable to upload category image file.  Please make sure the file extension is either .jpg, .png, .gif or .bmp<br>";
				}
				else {
					$strCatImageURL = "images/ranks/".$uploadFile->getUploadedFileName();
				}
				
				
			}
			else {
				
				$strCatImageURL = $_POST['catimageurl'];
				
			}
			
		}
		
		if($strCatImageURL == "") {
			$_POST['useimage'] = 0;
		}
		
	}
	
	if($countErrors == 0) {
		// No errors... Add to DB
		
		
		$arrColumns = array("name", "imageurl", "ordernum", "hidecat", "useimage", "description", "imagewidth", "imageheight", "color");
		$arrValues = array($_POST['catname'], $strCatImageURL, $intNewCatOrderNum, $_POST['hidecat'], $_POST['useimage'], $_POST['catdesc'], $_POST['catimagewidth'], $_POST['catimageheight'], $_POST['rankcolor']);
		
		$newCat = new RankCategory($mysqli);
		if($newCat->addNew($arrColumns, $arrValues)) {
			
			
			// Added New Category... Now set the ranks in this category
			
			$newCatInfo = $newCat->get_info();
			$rankObj = new Rank($mysqli);
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1'");
			while($row = $result->fetch_assoc()) {
				$postVar = "rankid_".$row['rank_id'];
				
				if(isset($_POST[$postVar]) && $_POST[$postVar] == 1) {
					if($rankObj->select($row['rank_id'])) {
						$arrColumn = array("rankcategory_id");
						$arrValue = array($newCatInfo['rankcategory_id']);
						$rankObj->update($arrColumn, $arrValue);
					}
				}
				
			}
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Rank Category!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add New Rank Category', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
		}
		else {
			$_POST['submit'] = false;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to add category.  Please try again.<br>";
		}
		
	}
	else {
		$_POST['submit'] = false;
	}
	
	
}



if(!isset($_POST['submit']) || !$_POST['submit']) {
	
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory ORDER BY ordernum DESC");
	$orderoptions = "";
	while($row = $result->fetch_assoc()) {
		$rankCatName = filterText($row['name']);
		$orderoptions .= "<option value='".$row['rankcategory_id']."'>".$rankCatName."</option>";
	}
	
	if($orderoptions == "") {
		$orderoptions = "<option value='first'>(first category)</option>";
	}
	
	
	$rankoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' ORDER BY ordernum DESC");
	
	$rankcounter = 1;
	while($row = $result->fetch_assoc()) {
		$rankcounter++;
		$rankoptions .= "<input type='checkbox' value='1' class='textBox' style='cursor: pointer' name='rankid_".$row['rank_id']."'> ".$row['name']."<br>";
	}
	
	$rankoptionheight = 20*$rankcounter;
	
	if($rankoptionheight > 300) { $rankoptionheight = 300; }
	
	if($rankoptions == "") {
		$rankoptions = "(no ranks added yet)";
	}
	
	echo "
	
	<form action='console.php?cID=$cID' method='post' enctype='multipart/form-data'>
		<div class='formDiv'>
		
		";
	
	if(isset($dispError) && $dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new rank because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
	
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#rankcolor').miniColors({
				change: function(hex, rgb) { }
			});
		});
	</script>
			Fill out the form below to add a new rank category.<br><br>
			<b><u>NOTE:</u></b> When adding a Category Image, if both the File and URL are filled out, the File will be used.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Category Name:</td>
					<td class='main'>
						<input type='text' name='catname' value='".((isset($_POST['catname'])) ? $_POST['catname'] : "")."' class='textBox' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Image:<br><span style='font-weight: normal'><i>(optional)</i></span></td>
					<td class='main'><b>Use Image</b> <input type='checkbox' class='textBox' id='rcUseImage' name='useimage' value='1' onclick='showImageOptions()'><br><br>
					
						<div id='catImageDiv' style='display: none'>
							File:<br>
							<input type='file' name='catimagefile' class='textBox' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
							<br><br>
							<b><i>OR</i></b>
							<br><br>
							URL:<br>
							<input type='text' name='catimageurl' value='".((isset($_POST['catimageurl'])) ? $_POST['catimageurl'] : "")."' class='textBox' style='width: 250px'>
						</div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'><div style='display: none' id='catimagewidthLabel'>Image Width: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Width to the width that you would like the Category Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></div></td>
					<td class='main'>
						<div id='catimagewidthInput' style='display: none'><input type='text' name='catimagewidth' value='".((isset($_POST['catimagewidth'])) ? $_POST['catimagewidth'] : "")."' class='textBox' style='width: 40px'> <i>px</i></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'><div style='display: none' id='catimageheightLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Category Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></div></td>
					<td class='main'>
						<div style='display: none' id='catimageheightInput'><input type='text' name='catimageheight' value='".((isset($_POST['catimageheight'])) ? $_POST['catimageheight'] : "")."' class='textBox' style='width: 40px'> <i>px</i></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Description:<br><span style='font-weight: normal'><i>(optional)</i></span></td>
					<td class='main'>
						<textarea rows='5' cols='40' class='textBox' name='catdesc'>".((isset($_POST['catdesc'])) ? $_POST['catdesc'] : "")."</textarea>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
						<select name='catorder' class='textBox'>".$orderoptions."</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Hide Category:</td>
					<td class='main'>
						<input type='checkbox' name='hidecat' value='1' class='textBox' onmouseover=\"showToolTip('If you hide a category, no members in the category will be shown on the members page.')\" onmouseout='hideToolTip()'>
						<br><br>
					</td>
				</tr>
				<tr>
				<td class='formLabel'>Color:</td>
					<td class='main'><input type='text' id='rankcolor' name='rankcolor' value='".$_POST['rankcolor']."' class='textBox' style='width: 70px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Ranks:</td>
					<td class='main'>
						<div class='main' style='margin-left: 0px; overflow-y: auto; width: 70%; height: ".$rankoptionheight."px'>
							".$rankoptions."
						</div>
					</td>
				</tr>
				<tr>
					<td class='main' align='center' style='padding-right: 30px' colspan='2'><br><br>
						<input type='submit' name='submit' value='Add Rank Category' class='submitButton'>
					</td>
				</tr>
			</table>
		
		</div>
	</form>
	
	
	<script type='text/javascript'>
	
		function showImageOptions() {
	
			$(document).ready(function() {
				if($('#rcUseImage').is(':checked')) {
					$('#catImageDiv').show();
					$('#catimagewidthInput').show();
					$('#catimagewidthLabel').show();
					$('#catimageheightInput').show();
					$('#catimageheightLabel').show();
				}
				else {
					$('#catImageDiv').hide();
					$('#catimagewidthInput').hide();
					$('#catimagewidthLabel').hide();
					$('#catimageheightInput').hide();
					$('#catimageheightLabel').hide();
				}
			});
		
		}
		
	</script>
	
	";

	
	
	
}




?>