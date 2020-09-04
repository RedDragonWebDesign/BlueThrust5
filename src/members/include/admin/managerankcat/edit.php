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


if(!$rankCatObj->select($_GET['rID'])) {
	
	
	echo "
	<script type='text/javascript'>
		window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
	</script>
	";
	exit();
	
}
$rankCatInfo = $rankCatObj->get_info_filtered();



echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Rank Categories</a> > ".$rankCatInfo['name']."\");
});
</script>
";



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

	$resetCatOrder = false;
	if($_POST['catorder'] != "first") {
		
		
		if(!$rankCatObj->select($_POST['catorder'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (category).<br>";
		}
		else {
			
			
			$arrBeforeAfter['before'] = 1;
			$arrBeforeAfter['after'] = -1;
			
			$testNewCatOrderNum = $arrBeforeAfter[$_POST['beforeafter']]+$rankCatObj->get_info("ordernum");
			
			
			
			if($testNewCatOrderNum != $rankCatInfo['ordernum']) {
				$intNewCatOrderNum = $rankCatObj->makeRoom($_POST['beforeafter']);
				if($intNewCatOrderNum == "false") {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid category order (category).<br>";			
				}
				
				$resetCatOrder = true;
			}
			else {
				$intNewCatOrderNum = $rankCatInfo['ordernum'];
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
			elseif($_POST['catimageurl'] != "") {
				
				$strCatImageURL = $_POST['catimageurl'];
				
			}
			else {
				$strCatImageURL = $rankCatInfo['imageurl'];	
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
		
		$rankCatObj->select($_GET['rID']);
		
		if($rankCatObj->update($arrColumns, $arrValues)) {
			
			if($resetCatOrder) {
				$rankCatObj->resortOrder();	
			}
			
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Rank Category!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Edit Rank Category', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			";
			
			
		}
		else {
			$_POST['submit'] = false;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to edit category.  Please try again.<br>";
		}
		
	}
	else {
		$_POST['submit'] = false;
	}


	
}
	
	
if(!isset($_POST['submit']) || !$_POST['submit']) {
	
	$afterSelected = "";
	$intCatBeforeAfter = "";
	
	$intNextCatOrder = $rankCatInfo['ordernum']-1;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory WHERE ordernum = '".$intNextCatOrder."'");
	if($result->num_rows == 1) {
		$beforeCatInfo = $result->fetch_assoc();
		$intCatBeforeAfter = $beforeCatInfo['rankcategory_id'];
	}
	else {
		// Editing First Category Need to select "After" option
		$intCatAfter = $rankCatInfo['ordernum']+1;
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory WHERE ordernum = '".$intCatAfter."'");
		if($result->num_rows == 1) {
			$afterCatInfo = $result->fetch_assoc();
			$intCatBeforeAfter = $afterCatInfo['rankcategory_id'];
			$afterSelected = " selected";
		}
	}
	$counter = 0;
	$catOrderOptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory WHERE rankcategory_id != '".((isset($gameInfo['rankcategory_id'])) ? $gameInfo['rankcategory_id'] : 0)."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$counter++;
		$catName = filterText($row['name']);
		if($row['rankcategory_id'] == $intCatBeforeAfter) {
			$catOrderOptions .= "<option value='".$row['rankcategory_id']."' selected>".$catName."</option>";
		}
		else {
			$catOrderOptions .= "<option value='".$row['rankcategory_id']."'>".$catName."</option>";
		}
		
	}
	
	if($counter == 0) {
		$catOrderOptions = "<option value='first'>(no other games)</option>";
	}
	
	
	$rankoptions = "";
	$arrRanksInCat = $rankCatObj->getAssociateIDs();
	$sqlRanksInCat = "('".implode("','", $arrRanksInCat)."')";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' AND rank_id IN ".$sqlRanksInCat." ORDER BY ordernum DESC");
	
	$rankcounter = 1;
	$manageRanksCID = $consoleObj->findConsoleIDByName("Manage Ranks");
	while($row = $result->fetch_assoc()) {

		
		$rankoptions .= $rankcounter.". <a href='console.php?cID=".$manageRanksCID."&rID=".$row['rank_id']."&action=edit'>".$row['name']."</a><br>";
		$rankcounter++;
		
	}
	
	$rankoptionheight = 20*$rankcounter;
	
	if($rankoptionheight > 300) { $rankoptionheight = 300; }
	
	if($rankoptions == "") {
		$rankoptions = "<i>no ranks in this category!</i>";
	}
	
	
	$useImageChecked = "";
	$dispImageWidth = "";
	$dispImageHeight = "";
	$dispImagePopup = "";
	$dispImagePopupLink = "";
	
	if($rankCatInfo['useimage'] == 1 AND $rankCatInfo['imageurl'] != "") {
		$useImageChecked = "checked";
		
		$imageURL = $rankCatObj->getLocalImageURL();
		$imageSize = "";
		
		if($imageURL !== false) {
			$imageSize = getimagesize($prevFolder.$imageURL);
		}
		
		$dispImageWidth = $rankCatInfo['imagewidth'];
		$dispImageHeight = $rankCatInfo['imageheight'];
		if($rankCatInfo['imagewidth'] == 0) {
			$dispImageWidth = $imageSize[0];
		}
		
		if($rankCatInfo['imageheight'] == 0) {
			$dispImageHeight = $imageSize[1];	
		}
		
		$intDialogWidth = $dispImageWidth+150;
		
		$dispImagePopup = "
			<div id='showCatImageDiv' style='display: none'><p align='center'><img src='".$rankCatInfo['imageurl']."' width='".$dispImageWidth."' height='".$dispImageHeight."'></p></div>
			<script type='text/javascript'>
				function showCatImage() {
					
					$(document).ready(function() {
					
						$('#showCatImageDiv').dialog({
							title: 'Manage Rank Categories - ".$rankCatInfo['name']."',
							modal: true,
							zIndex: 9999,
							width: ".$intDialogWidth.",
							show: 'scale',
							resizable: false,
							buttons: {
								'OK': function() {
									$(this).dialog('close');								
								}
							}
						});
					
					});
				
				}
			</script>
		";
		
		$dispImagePopupLink = "<i><a href='javascript:void(0)' onclick='showCatImage()'>View Current Image</a></i><br>";
		
	}
	
	
	$hideCatChecked = "";
	if($rankCatInfo['hidecat'] == 1) {
		$hideCatChecked = "checked";	
	}
	
	
	echo "
	
	<form action='console.php?cID=".$cID."&rID=".$_GET['rID']."&action=edit' method='post' enctype='multipart/form-data'>
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
	
	echo $dispImagePopup;
	echo "
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#rankcolor').miniColors({
				change: function(hex, rgb) { }
			});
		});
	</script>
			Fill out the form below to add a new rank category.<br><br>
			<b><u>NOTE:</u></b> When editing a Category Image, if both the File and URL are filled out, the File will be used.  If you don't want to change an already uploaded image, leave both the File and URL blank.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Category Name:</td>
					<td class='main'>
						<input type='text' name='catname' value='".$rankCatInfo['name']."' class='textBox' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Image:<br><span style='font-weight: normal'><i>(optional)</i></span></td>
					<td class='main'><b>Use Image</b> <input type='checkbox' class='textBox' id='rcUseImage' name='useimage' value='1' onclick='showImageOptions()' ".$useImageChecked."><br><br>
					
						<div id='catImageDiv' style='display: none'>
							".$dispImagePopupLink."
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
						<div id='catimagewidthInput' style='display: none'><input type='text' name='catimagewidth' value='".$dispImageWidth."' class='textBox' style='width: 40px'> <i>px</i></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'><div style='display: none' id='catimageheightLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Category Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></div></td>
					<td class='main'>
						<div style='display: none' id='catimageheightInput'><input type='text' name='catimageheight' value='".$dispImageHeight."' class='textBox' style='width: 40px'> <i>px</i></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Description:<br><span style='font-weight: normal'><i>(optional)</i></span></td>
					<td class='main'>
						<textarea rows='5' cols='40' class='textBox' name='catdesc'>".$rankCatInfo['description']."</textarea>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Category Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
						<select name='catorder' class='textBox'>".$catOrderOptions."</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Hide Category:</td>
					<td class='main'>
						<input type='checkbox' name='hidecat' value='1' class='textBox' onmouseover=\"showToolTip('If you hide a category, no members in the category will be shown on the members page.')\" onmouseout='hideToolTip()' ".$hideCatChecked.">
						<br><br>
					</td>
				</tr>
				<tr>
				<td class='formLabel'>Color:</td>
					<td class='main'><input type='text' id='rankcolor' name='rankcolor' value='".$rankCatInfo['color']."' class='textBox' style='width: 70px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Ranks: <a href='javascript:void(0)' onmouseover=\"showToolTip('You can change a particular rank\'s category from the Manage Ranks page.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<div class='main' style='margin-left: 0px; overflow-y: auto; width: 70%; height: ".$rankoptionheight."px'>
							".$rankoptions."
						</div>
					</td>
				</tr>
				<tr>
					<td class='main' align='center' style='padding-right: 30px' colspan='2'><br><br>
						<input type='submit' name='submit' value='Edit Rank Category' class='submitButton' style='width: 140px'>
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
		
		showImageOptions();
	</script>
	
	";

	
	
	
}


?>