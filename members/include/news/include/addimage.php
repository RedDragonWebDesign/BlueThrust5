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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/news.php");
include_once("../../../../classes/shoutbox.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Home Page Images");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	
	if($_POST['submit']) {
		
		// Check Image
		if($_FILES['menuimagefile']['name'] != "") {
			$btUploadObj = new BTUpload($_FILES['uploadimage'], "hpimage_", "../images/homepage/", array(".jpg", ".png", ".bmp", ".gif"));
		}
		else {
			$btUploadObj = new BTUpload($_POST['imageurl'], "hpimage_", "../images/homepage/", array(".jpg", ".png", ".bmp", ".gif"), 4, true);
		}
		
		
		
		
	}
	
	
	
	if(!$_POST['submit']) {
		
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."imageslider ORDER BY ordernum DESC");
		while($row = $result->fetch_assoc()) {

			$displayoptions .= "<option value='".$row['imageslider_id']."'>".$row['name']."</option>";
			
		}

		if($result->num_rows == 0) {

			$displayoptions = "<option value='first'>(first image)</option>";
			
		}
		
		
		echo "

			<table class='formTable'>
				<tr>
					<td class='dottedLine main' colspan='2'>
						<b>Image Information:</b>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Name: <a href='javascript:void(0)' onmouseover=\"showToolTip('This will only be used to identify the image when managing home page images.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'><input type='text' name='imagename' class='textBox'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Image:</td>
					<td class='main'>
						Upload:<br>
						<input type='file' name='uploadimage' class='textBox' style='width: 200px'>
						<br><br><span style='font-style: italic'>OR</span><br><br>
						URL:<br>
						<input type='text' name='imageurl' class='textBox' style='width: 200px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Display Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select>
						<br>
						<select name='displayorder' class='textBox'>".$displayoptions."</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Display:</td>
					<td class='main'><select name='displaytype' class='textBox'><option value='fill'>Fill</option><option value='stretch'>Stretch</option></select></td>
				</tr>
				<tr>
					<td class='main' colspan='2'><br><br>
						<div class='dottedLine' style='padding-bottom: 2px'><b>Message Information:</b></div>
						Leave this section blank to just display the image.<br><br>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Auto-fill:</td>
					<td class='main'>
						<select id='autofill' class='textBox'>
							<option value=''>Select</option>
							<option value='news'>News Post</option>
							<option value='tournament'>Tournament</option>
							<option value='event'>Event</option>
							<option value=''>Custom</option>
						</select><br><br>
						<select id='autofillID' class='textBox' disabled='disabled'>
							<option value=''>Select</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Title:</td>
					<td class='main'><input type='text' id='imageTitle' name='title' class='textBox' style='width: 200px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Message:</td>
					<td class='main'><textarea class='textBox' id='imageMessage' style='width: 200px; height: 45px' name='message'></textarea></td>
				</tr>
				<tr>
					<td class='formLabel'>Link:</td>
					<td class='main'><input type='text' id='linkURL' name='linkurl' class='textBox' style='width: 200px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Link Target:</td>
					<td class='main'><select name='linktarget' class='textBox'><option value=''>Same Window</option><option value='_blank'>New Window</option></select></td>
				</tr>
				<tr>
					<td class='formLabel'>Visibility:</td>
					<td class='main'><select name='membersonly' class='textBox'><option value='0'>Everyone</option><option value='1'>Members Only</option></select></td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br><input type='button' id='btnSubmit' value='Add Image' class='submitButton'></td>
				</tr>
			</table>
			
			<div id='autoFillInfo' style='display: none'></div>
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#autofill').change(function() {
					
						if($('#autofill').val() != '') {
							$('#autofillID').removeAttr('disabled');
							
							$.post('".$MAIN_ROOT."members/include/news/include/imageslider_getattachtype.php', { attachtype: $('#autofill').val() }, function(data) {
								$('#autofillID').html(data);
							});
							
						}
						else {
							$('#autofillID').html(\"<option value=''>Select</option>\");
							$('#autofillID').attr('disabled', 'disabled');
						}
					
					});
					
					
					$('#autofillID').change(function() {
					
						if($('#autofill').val() != '' && $('#autofillID').val() != '') {
						
						
							$.post('".$MAIN_ROOT."members/include/news/include/imageslider_getattachinfo.php', { attachtype: $('#autofill').val(), attachID: $('#autofillID').val() }, function(data) {
							
								$('#autoFillInfo').html(data);
							
							});
						
						
						}
					
					});
				
				});
			
			</script>
			
		";
		
	}
	
}


?>