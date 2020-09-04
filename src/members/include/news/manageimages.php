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

include_once($prevFolder."classes/imageslider.php");

$cID = $_GET['cID'];
$imageSliderObj = new ImageSlider($mysqli);

if($_GET['action'] == "edit" && $imageSliderObj->select($_GET['imgID'])) {
	
	include("include/news/include/editimage.php");
	
}
else {
	
	
	$addImageCID = $consoleObj->findConsoleIDByName("Add Home Page Image");
	$selectWidthUnit = ($websiteInfo['hpimagewidthunit'] == "px") ? "" : " selected";
	$selectHeightUnit = ($websiteInfo['hpimageheightunit'] == "px") ? "" : " selected";
	$selectDisplayStyle = ($websiteInfo['hpimagetype'] == "slider") ? "" : " selected";
	
		echo "
			<div class='formDiv'>
				Use this page to manage the home page image slider.  You can attach news posts, tournaments, events or any kind of custom message that you want to the images in the image slider.
				
				<div id='errorDiv' style='display: none' class='errorDiv'>
					
				</div>
				
				<table class='formTable'>
					<tr>
						<td class='main' colspan='2' align='right'><b>&raquo;</b> <a href='".$MAIN_ROOT."members/console.php?cID=".$addImageCID."'>Add New Image</a> <b>&laquo;</b></td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
							<div class='dottedLine' style='margin-bottom: 3px; padding-bottom: 2px'><b>Display Settings:</b></div>
								Use this section to modify the settings for home page images.<br><br>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Width:</td>
						<td class='main'><input type='text' id='containerwidth' value='".$websiteInfo['hpimagewidth']."' class='textBox' style='width: 50px'> <select id='containerwidthunit' class='textBox'><option value='1'>px</option><!-- <option value='2'".$selectWidthUnit.">%</option> --></select></td>
					</tr>
					<tr>
						<td class='formLabel'>Height:</td>
						<td class='main'><input type='text' id='containerheight' value='".$websiteInfo['hpimageheight']."' class='textBox' style='width: 50px'> <select id='containerheightunit' class='textBox'><option value='1'>px</option><!-- <option value='2'".$selectHeightUnit.">%</option> --></select></td>
					</tr>
					<tr>
						<td class='formLabel'>Display Style:</td>
						<td class='main'><select id='displaystyle' class='textBox'><option value='slider'>Image Slider</option><option value='random'".$selectDisplayStyle.">Random Images</option></select></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='button' id='btnSaveSettings' class='submitButton' value='Save'>
							<div class='main' style='text-align: center; margin-top: 10px; display: none' id='saveLoading'>
								Saving...
							</div>
							<div style='text-align: center; margin-top: 10px; display: none' id='saveSuccess' class='successFont'>
								<b>Saved!</b>
							</div>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
							<div class='dottedLine' style='margin-top: 50px; margin-bottom: 3px; padding-bottom: 2px'><b>Images:</b></div><br>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
						
							<table class='formTable'>
								<tr>
									<td class='formTitle' style='width: 76%'>Image Name:</td>
									<td class='formTitle' style='width: 24%'>Actions:</td>
								</tr>
							</table>
							<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
								</p>
							</div>
							<div id='imageList'></div>
						</td>
					</tr>
				</table>
			</div>
			<div id='deleteMessage' style='display: none'></div>
			<div id='saveDump' style='display: none'></div>
			<script type='text/javascript'>
			
				$(document).ready(function() {
					$('#loadingSpiral').show();	
					$.post('".$MAIN_ROOT."members/include/news/include/imagelist.php', { }, function(data) {
					
						$('#imageList').html(data);
						$('#loadingSpiral').hide();
					
					});
					
					
					$('#btnSaveSettings').click(function() {
					
						$('#errorDiv').hide();
						$('#saveLoading').show();
						$.post('".$MAIN_ROOT."members/include/news/include/imageslider_savesettings.php', { containerWidth: $('#containerwidth').val(), containerHeight: $('#containerheight').val(), containerWidthUnit: $('#containerwidthunit').val(), containerHeightUnit: $('#containerheightunit').val(), displayStyle: $('#displaystyle').val() }, function(data) {
						
							$('#saveDump').html(data);
							$('#saveLoading').hide();
						
						});
						
					
					});
					
					
				});
				
				
				
				function moveImg(intID, strDir) {
				
					$(document).ready(function() {
					
						$('#loadingSpiral').show();
						$('#imageList').fadeOut(200);
						$.post('".$MAIN_ROOT."members/include/news/include/move_image.php', { imgID: intID, iDir: strDir }, function(data) {
						
							$('#imageList').html(data);
							$('#loadingSpiral').hide();
							$('#imageList').fadeIn(200);
						
						});
					
					});
				
				}
				
				function deleteImg(intID) {
				
					$(document).ready(function() {
					
						$.post('".$MAIN_ROOT."members/include/news/include/delete_image.php', { imgID: intID }, function(data) {
						
							$('#deleteMessage').html(data);
						
						});
					
					});
				
				}
			
			</script>
			
			
		";


}

?>