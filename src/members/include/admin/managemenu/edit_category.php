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

include_once($prevFolder."classes/btupload.php");

$dispError = "";
$countErrors = 0;
$menuCatInfo = $menuCatObj->get_info();

echo "

	<script type='text/javascript'>
		$(document).ready(function() {
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Menu Categories</a> > ".$menuCatInfo['name']."\");
		});
	</script>
";

$arrCheckType = array("image", "customcode", "customformat");
if($_POST['submit']) {
	
	// Check Name
	if(trim($_POST['categoryname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank category name.<br>";
	}
	
	// Check Section
	
	if(!is_numeric($_POST['section']) || $_POST['section'] >= $menuXML->info->section->count() || $_POST['section'] < 0) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid menu section.<br>";
	}
	
	// Check Header Type
	if(!in_array($_POST['headertype'], $arrCheckType)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid header type.<br>";
	}
	
	
	// Check Display Order
	$menuCatObj->setCategoryKeyValue($_POST['section']);
	$intNewOrderNum = $menuCatObj->validateOrder($_POST['displayorder'], $_POST['beforeafter'], true, $menuCatInfo['sortnum']);
	if($intNewOrderNum === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
	}
	
	
	if($_POST['accesstype'] != "0" && $_POST['accesstype'] != "1" && $_POST['accesstype'] != "2") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid access type.<br>";
	}
	
	if($_POST['hidecategory'] != "1") {
		$_POST['hidecategory'] = 0;
	}
	
	
	if($_POST['headertype'] == "customcode") {
		$headerImageURL = $_POST['headercustomcode'];
	}
	elseif($_POST['headertype'] == "customformat") {
		$headerImageURL = $_POST['wysiwygHTML'];
	}
	
	$newImage = false;
	if($countErrors == 0) {
		
		if($_POST['headertype'] == "image" && $_FILES['headerimagefile']['name'] != "") {
			$newImage = true;
			$btUploadObj = new BTUpload($_FILES['headerimagefile'], "menuheader_", "../images/menu/", array(".jpg", ".png", ".bmp", ".gif"));
		}
		elseif($_POST['headertype'] == "image" && $_POST['headerimageurl'] != "") {
			$newImage = true;
			$btUploadObj = new BTUpload($_POST['headerimageurl'], "menuheader_", "../images/menu/", array(".jpg", ".png", ".bmp", ".gif"), 4, true);
		}
	
		if($newImage && $_POST['headertype'] == "image" && $btUploadObj->uploadFile()) {
			$headerImageURL = "images/menu/".$btUploadObj->getUploadedFileName();
		}
		elseif($newImage && $_POST['headertype'] == "image") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload selected image.  Make sure it's the correct file extension and not too big.<br>";
		}
	
	}
	
	
	if($countErrors == 0) {
	
		if(($newImage || $_POST['headertype'] != "image") && $menuCatInfo['headertype'] == "image") {
			unlink($prevFolder.$menuCatInfo['headercode']);
		}
		elseif(!$newImage && $menuCatInfo['headertype'] == "image" && $_POST['headertype'] == "image") {
			$headerImageURL = $menuCatInfo['headercode'];
		}
		
		$arrColumns = array("section", "name", "sortnum", "headertype", "headercode", "accesstype", "hide");
		$arrValues = array($_POST['section'], $_POST['categoryname'], $intNewOrderNum, $_POST['headertype'], $headerImageURL, $_POST['accesstype'], $_POST['hidecategory']);
	
		$menuCatObj->select($menuCatInfo['menucategory_id']);
		if($menuCatObj->update($arrColumns, $arrValues)) {
	
			$menuCatInfo = $menuCatObj->get_info_filtered();
	
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Edited Menu Category: <b>".$menuCatInfo['name']."</b>!
					</p>
				</div>
	
				<script type='text/javascript'>
					popupDialog('Edit Menu Category', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
			";
		}
	
		$menuCatObj->resortOrder();
	
	}
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
	
}

if(!$_POST['submit']) {
	
	for($i=0; $i<$menuXML->info->section->count(); $i++) {
		$dispSelected = "";
		if($menuCatInfo['section'] == $i) {
			$dispSelected = " selected";
		}
		$sectionoptions .= "<option value='".$i."'".$dispSelected.">".$menuXML->info->section[$i]."</option>";
	}
	
	$afterSelected = "";
	$dispBeforeAfter = $menuCatObj->findBeforeAfter();
	if($dispBeforeAfter[1] == "after") {
		$afterSelected = " selected";	
	}
	
	
	foreach($arrCheckType as $typeName) {
		if($menuCatInfo['headertype'] == $typeName) {
			$arrMenuTypes[$typeName] = " selected";
		}
		else {
			$arrMenuTypes[$typeName] = "";
		}
	}
	
	$arrShowWhen[0] = "";
	$arrShowWhen[1] = "";
	$arrShowWhen[2] = "";
	
	if($menuCatInfo['accesstype'] == 1) {
		$arrShowWhen[1] = " selected";
	}
	elseif($menuCatInfo['accesstype'] == 2) {
		$arrShowWhen[2] = " selected";	
	}
	
	$dispHideChecked = ($menuCatInfo['hide'] == 1) ? " checked" : "";
	
	$dispCustomFormat = "";
	$dispCustomCode = "";
	if($menuCatInfo['headertype'] == "customformat") {
		$dispCustomFormat = $menuCatInfo['headercode'];
	}
	elseif($menuCatInfo['headertype'] == "customcode") {
		$dispCustomCode = filterText($menuCatInfo['headercode']);
	}
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&mcID=".$_GET['mcID']."&action=edit' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
	
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit menu category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
	
				Use the form below to edit the selected menu category.
				<p><b><u>NOTE:</u></b> If you want to keep the same category image, leave both image inputs blank.  Changing the current image will cause it to be deleted.</p>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' name='categoryname' value='".$menuCatInfo['name']."' class='textBox' style='width: 200px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Section: <a href='javascript:void(0)' onmouseover=\"showToolTip('Menu sections are determined by the theme you are currently using.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><select name='section' id='menuSection' class='textBox'>".$sectionoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'>
								<option value='before'>Before</option><option value='after'".$afterSelected.">After</option>
							</select><br>
							<select name='displayorder' id='displayOrder' class='textBox'>
								
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Header Type:</td>
						<td class='main'><select name='headertype' id='headerType' class='textBox'><option value='image'".$arrMenuTypes['image'].">Image</option><option value='customcode'".$arrMenuTypes['customcode'].">Custom - Code Editor</option><option value='customformat'".$arrMenuTypes['customformat'].">Custom - WYSIWYG Editor</option></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'><span id='headerLabelText'>Header Image:</span></td>
						<td class='main'>
							<div id='addMenuHeaderImage'>
								File:<br><input type='file' name='headerimagefile' class='textBox' style='width: 250px; border: 0px'><br>
								<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
								"; 
								if($menuCatInfo['headertype'] == "image") { 
									echo "<br><i>Current Image: <a href='javascript:void(0)' id='previewImageLink'>View Image</a></i>"; 
								} 
							echo "
								<p><b><i>OR</i></b></p>
								URL:<br><input type='text' name='headerimageurl' value='' class='textBox' style='width: 250px'>
							</div>
							<div id='codeEditorDiv' style='display: none'>
								<div id='customMenuHeaderEditor' class='codeEditor' style='height: 150px; width: 90%'>".$dispCustomCode."</div>
							</div>
							<div id='wysiwygEditorDiv' style='display: none'>
								<textarea id='tinymceTextArea' name='wysiwygHTML' style='width: 80%' rows='15'>".$dispCustomFormat."</textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Show when:</td>
						<td class='main'><select name='accesstype' class='textBox'><option value='0'>Always</option><option value='1'".$arrShowWhen[1].">Logged In</option><option value='2'".$arrShowWhen[2].">Logged Out</option></select></td>
					</tr>
					<tr>
						<td class='formLabel'>Hide Category:</td>
						<td class='main'><input type='checkbox' name='hidecategory' value='1'".$dispHideChecked."></td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br>
							<input type='button' id='btnFakeSubmit' value='Edit Category' class='submitButton'>
							<input type='submit' name='submit' id='btnSubmit' value='submit' style='display: none'>
							<textarea id='customMenuCode' name='headercustomcode' style='display: none'></textarea>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		";
	
	if($menuCatInfo['headertype'] == "image") {
		
		$checkURL = parse_url($menuCatInfo['headercode']);
		$dispImgWidth = 400;
		$dispImgHeight = 200;
		if($checkURL['scheme'] == "") {
			$imageSize = getimagesize($prevFolder.$menuCatInfo['headercode']);
			$menuCatInfo['headercode'] = $MAIN_ROOT.$menuCatInfo['headercode'];
			$dispImgWidth = $imageSize[0]+25;
			$dispImgHeight = $imageSize[1]+25;
		}
		
		echo "
			<div id='previewImageDiv' style='display: none'>
				<div style='margin-left: auto; margin-right: auto; width: ".$dispImgWidth."px; height: ".$dispImgHeight."px'>
					<p align='center'><img src='".$menuCatInfo['headercode']."' style='max-width: 100%; max-height: 100%'></p>
				</div>
			</div>
		";	
	}
	
	echo "
		<script type='text/javascript'>
		
			var customMenuEditor = ace.edit('customMenuHeaderEditor');
			customMenuEditor.getSession().setMode('ace/mode/php');
			customMenuEditor.setTheme('ace/theme/eclipse');
			customMenuEditor.setHighlightActiveLine(false);
			customMenuEditor.setShowPrintMargin(false);
			
			$(document).ready(function() {
			
				$('#menuSection').change(function() {
					$('#displayOrder').html(\"<option value''>Loading...</option>\");
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menucategorylist.php', { section: $('#menuSection').val(), mcID: '".$_GET['mcID']."' }, function(data) {
						$('#displayOrder').html(data);
					});
				});
			
				$('#previewImageLink').click(function() {
				
					$('#previewImageDiv').dialog({
						title: 'Edit Menu Category - Preview Image',
						width: 450,
						show: 'scale',
						zIndex: 99999,
						modal: true,
						resizable: false,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}
						}
					
					});
				
				});
				
				$('#headerType').change(function() {
					if($(this).val() == 'image') {
						$('#addMenuHeaderImage').show();
						$('#codeEditorDiv').hide();
						$('#wysiwygEditorDiv').hide();
						$('#headerLabelText').html('Header Image:');
					}
					else if($(this).val() == 'customcode') {
						$('#addMenuHeaderImage').hide();
						$('#codeEditorDiv').show();
						$('#wysiwygEditorDiv').hide();
						$('#headerLabelText').html('Code Editor:');
					}
					else {
						$('#addMenuHeaderImage').hide();
						$('#codeEditorDiv').hide();
						$('#wysiwygEditorDiv').show();
						$('#headerLabelText').html('WYSIWYG Editor:');
					}
				});
				
				
				$('#btnFakeSubmit').click(function() {

					$('#customMenuCode').html(customMenuEditor.getValue());
					//alert(customMenuEditor.getValue());
					$('#btnSubmit').click();
				
				});
				
				
				$('#tinymceTextArea').tinymce({
			
					script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
					theme: 'advanced',
					theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
					theme_advanced_resizing: true
				
				});
				
				
				$('#headerType').change();
				$('#menuSection').change();
			});
			
		</script>
	
	
	";
	
}