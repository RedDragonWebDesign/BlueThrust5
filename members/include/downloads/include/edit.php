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


if(!isset($member) || !isset($downloadObj) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !$downloadObj->select($_GET['dlID'])) {
	exit();
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > ".$downloadInfo['name']."\");
});
</script>
";

$countErrors = 0;
$dispError = "";

if($_POST['submit']) {
	
	// Check Name
	if(trim($_POST['title']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must give your download a title.<br>";
	}
	
	if($countErrors == 0) {
		
		
		$arrColumns = array("name", "description");
		$arrValues = array($_POST['title'], $_POST['description']);
		if($downloadObj->update($arrColumns, $arrValues)) {
		
			$downloadInfo = $downloadObj->get_info_filtered();
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Saved Download Information!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Edit Download', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
			
			";
			
			
			$member->logAction("Edited <a href='".$MAIN_ROOT."downloads/index.php?catID=".$downloadInfo['downloadcategory_id']."#".$downloadInfo['download_id']."'>".$downloadInfo['name']."</a> download information.");
			
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
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&action=edit&dlID=".$_GET['dlID']."' method='post'>
			<div class='formDiv'>
				Use the form below to modify the selected download file.<br>
				<table class='formTable'>
					<tr>
						<td class='formLabel' valign='top'>Section:</td>
						<td class='main'>
							<b>".$downloadCatInfo['name']."</b>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>File:</td>
						<td class='main'>".$downloadInfo['filename']."</td>
					</tr>
					<tr>
						<td class='formLabel'>Title:</td>
						<td class='main'><input type='text' value='".$downloadInfo['name']."' class='textBox' name='title' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Description:</td>
						<td class='main'>
							<textarea name='description' class='textBox' style='width: 250px; height: 100px'>".$downloadInfo['description']."</textarea>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Save' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
	
	
	";
	
	
}


?>