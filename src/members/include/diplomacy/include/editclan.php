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


$diplomacyClanObj = new Basic($mysqli, "diplomacy", "diplomacy_id");

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !$diplomacyClanObj->select($_GET['dID'])) {
	exit();
}

$diplomacyClanInfo = $diplomacyClanObj->get_info_filtered();


$dispError = "";
$countErrors = 0;

$arrDiplomacyStatus = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrDiplomacyStatus[$row['diplomacystatus_id']] = filterText($row['name']);
}


if($_POST['submit']) {

	$diplomacyStatusObj = new Basic($mysqli, "diplomacy_status", "diplomacystatus_id");
	// Check for clan name
	
	if(trim($_POST['clanname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Clan name may not be blank.<br>";
		$countErrors++;
	}
	
	// Check Status
	
	$allowedStatuses = array_keys($arrDiplomacyStatus);
	if(!in_array($_POST['status'], $allowedStatuses) || !$diplomacyStatusObj->select($_POST['status'])) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid status.<br>";
		$countErrors++;
	}
	
	// Check Clan Size
	
	$allowedSizes = array("large", "medium", "small");
	if(!in_array($_POST['clansize'], $allowedSizes)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid clan size.<br>";
		$countErrors++;
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("clanname", "diplomacystatus_id", "website", "clansize", "clantag", "skill", "gamesplayed", "extrainfo", "leaders");
		$arrValues = array($_POST['clanname'], $_POST['status'], $_POST['website'], $_POST['clansize'], $_POST['tag'], $_POST['skill'], $_POST['gamesplayed'], $_POST['extrainfo'], $_POST['leaders']);
		
		if($diplomacyClanObj->update($arrColumns, $arrValues)) {

			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully edited ".$diplomacyClanObj->get_info_filtered("clanname")."'s information!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Edit Clan', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
			
			";
			
			$member->logAction("Edited ".$_POST['clanname']." diplomacy page information.  Set status to ".$diplomacyStatusObj->get_info_filtered("name"));
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST['submit'] = false;
	}
	
}


if(!$_POST['submit']) {
	
	echo "<div class='formDiv'>";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit clan information because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
	
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&dID=".$_GET['dID']."&action=edit' method='post'>
				Use the form below to edit ".$diplomacyClanInfo['clanname']."'s information for the diplomacy page.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Clan Name:</td>
						<td class='main'><input type='text' name='clanname' value='".$diplomacyClanInfo['clanname']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Status:</td>
						<td class='main'>
							<select name='status' class='textBox'>
							";
								
								foreach($arrDiplomacyStatus as $key=>$value) {
									$dispSelected = "";
									if($diplomacyClanInfo['diplomacystatus_id'] == $key) {
										$dispSelected = " selected";	
									}
									echo "<option value='".$key."'".$dispSelected.">".$value."</option>";
									
								}
		
							echo "
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Leader(s):</td>
						<td class='main'><input type='text' name='leaders' value='".$diplomacyClanInfo['leaders']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Website:</td>
						<td class='main'><input type='text' name='website' class='textBox' value='".$diplomacyClanInfo['website']."' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Games Played:</td>
						<td class='main'><input type='text' name='gamesplayed' value='".$diplomacyClanInfo['gamesplayed']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Skill Level:</td>
						<td class='main'><input type='text' name='skill' class='textBox' value='".$diplomacyClanInfo['skill']."' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Clan Size:</td>
						<td class='main'>
							<select name='clansize' class='textBox'>
								
								";
							$arrClanSize = array("Small", "Medium", "Large");
							foreach($arrClanSize as $clanSize) {
								$dispSelected = "";
								$clanSizeLC = strtolower($clanSize);
								if($diplomacyClanInfo['clansize'] == $clanSizeLC) {
									$dispSelected = " selected";	
								}
								
								echo "<option value='".$clanSizeLC."'".$dispSelected.">".$clanSize."</option>";
								
							}
							echo "
							
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Tag:</td>
						<td class='main'><input type='text' name='tag' class='textBox' value='".$diplomacyClanInfo['clantag']."' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Extra Info:</td>
						<td class='main' valign='top'>
							<textarea rows='4' cols='55' name='extrainfo' class='textBox'>".$diplomacyClanInfo['extrainfo']."</textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br>
						
							<input type='submit' name='submit' value='Edit Clan' class='submitButton' style='width: 125px'>
						
						</td>
					</tr>
				</table>
			</form>
		</div>	
	
	";
	
	
}





?>