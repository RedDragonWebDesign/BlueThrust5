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


$menuCatObj = new MenuCategory($mysqli);


$dispError = "";
$countErrors = 0;

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
	$intNewOrderNum = $menuCatObj->validateOrder($_POST['displayorder'], $_POST['beforeafter']);
	if($intNewOrderNum === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid header type.<br>";
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
	
	
	if($countErrors == 0) {
		
		if($_POST['headertype'] == "image" && $_FILES['headerimagefile']['name'] != "") {
			$btUploadObj = new BTUpload($_FILES['headerimagefile'], "menuheader_", "../images/menu/", array(".jpg", ".png", ".bmp", ".gif"));
		}
		elseif($_POST['headertype'] == "image") {
			$btUploadObj = new BTUpload($_POST['headerimageurl'], "menuheader_", "../images/menu/", array(".jpg", ".png", ".bmp", ".gif"), 4, true);
		}
		
		if($_POST['headertype'] == "image" && $btUploadObj->uploadFile()) {
			$headerImageURL = "images/menu/".$btUploadObj->getUploadedFileName();
		}
		elseif($_POST['headertype'] == "image") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload selected image.  Make sure it's the correct file extension and not too big.<br>";
		}
		
	}
	
	if($countErrors == 0) {
		
		$arrColumns = array("section", "name", "sortnum", "headertype", "headercode", "accesstype", "hide");
		$arrValues = array($_POST['section'], $_POST['categoryname'], $intNewOrderNum, $_POST['headertype'], $headerImageURL, $_POST['accesstype'], $_POST['hidecategory']);
		
		if($menuCatObj->addNew($arrColumns, $arrValues)) {
			
			$menuCatInfo = $menuCatObj->get_info_filtered();
			
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Menu Category: <b>".$menuCatInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add New Menu Category', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
		}
		
		
	}
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}
	
}


if(!$_POST['submit']) {
	
	$selectSection = array();
	if(isset($_GET['sectionID'])) {
		$selectSection[$_GET['sectionID']] = " selected";	
	}
	
	for($i=0; $i<$menuXML->info->section->count(); $i++) {
		$sectionoptions .= "<option value='".$i."'".$selectSection[$i].">".$menuXML->info->section[$i]."</option>";	
	}
	
	echo "
	
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new menu category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
	
				Use the form below to add a new menu category.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' name='categoryname' value='".$_POST['categoryname']."' class='textBox' style='width: 200px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Section: <a href='javascript:void(0)' onmouseover=\"showToolTip('Menu sections are determined by the theme you are currently using.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><select name='section' id='menuSection' class='textBox'>".$sectionoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'>
								<option value='before'>Before</option><option value='after'>After</option>
							</select><br>
							<select name='displayorder' id='displayOrder' class='textBox'>
								
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Header Type:</td>
						<td class='main'><select name='headertype' id='headerType' class='textBox'><option value='image'>Image</option><option value='customcode'>Custom - Code Editor</option><option value='customformat'>Custom - WYSIWYG Editor</option></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'><span id='headerLabelText'>Header Image:</span></td>
						<td class='main'>
							<div id='addMenuHeaderImage'>
								File:<br><input type='file' name='headerimagefile' class='textBox' style='width: 250px; border: 0px'><br>
								<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
								<p><b><i>OR</i></b></p>
								URL:<br><input type='text' name='headerimageurl' value='".$_POST['headerimageurl']."' class='textBox' style='width: 250px'>
							</div>
							<div id='codeEditorDiv' style='display: none'>
								<div id='customMenuHeaderEditor' class='codeEditor' style='height: 150px; width: 90%'></div>
							</div>
							<div id='wysiwygEditorDiv' style='display: none'>
								<textarea id='tinymceTextArea' name='wysiwygHTML' style='width: 80%' rows='15'>".$_POST['wysiwygHTML']."</textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Show when:</td>
						<td class='main'><select name='accesstype' class='textBox'><option value='0'>Always</option><option value='1'>Logged In</option><option value='2'>Logged Out</option></select></td>
					</tr>
					<tr>
						<td class='formLabel'>Hide Category:</td>
						<td class='main'><input type='checkbox' name='hidecategory' value='1'></td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br>
							<input type='button' id='btnFakeSubmit' value='Add Category' class='submitButton'>
							<input type='submit' name='submit' id='btnSubmit' value='submit' style='display: none'>
							<textarea id='customMenuCode' name='headercustomcode' style='display: none'></textarea>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		
		<script type='text/javascript'>
		
			var customMenuEditor = ace.edit('customMenuHeaderEditor');
			customMenuEditor.getSession().setMode('ace/mode/php');
			customMenuEditor.setTheme('ace/theme/eclipse');
			customMenuEditor.setHighlightActiveLine(false);
			customMenuEditor.setShowPrintMargin(false);
			
			$(document).ready(function() {
			
				$('#menuSection').change(function() {
					$('#displayOrder').html(\"<option value''>Loading...</option>\");
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menucategorylist.php', { section: $('#menuSection').val() }, function(data) {
						$('#displayOrder').html(data);
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
					$('#btnSubmit').click();
				
				});
				
				
				$('#tinymceTextArea').tinymce({
			
					script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
					theme: 'advanced',
					theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
					theme_advanced_resizing: true
				
				});
				
				
				$('#menuSection').change();
			});
			
		</script>
	";
	
}


?>