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

$dispError = "";
$countErrors = 0;

$arrDiplomacyStatus = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrDiplomacyStatus[$row['diplomacystatus_id']] = filterText($row['name']);	
}

$diplomacyRequestObj = new Basic($mysqli, "diplomacy_request", "diplomacyrequest_id");

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
		
		$diplomacyObj = new Basic($mysqli, "diplomacy", "diplomacy_id");
		
		
		$arrColumns = array("member_id", "dateadded", "clanname", "diplomacystatus_id", "website", "clansize", "clantag", "skill", "gamesplayed", "extrainfo", "leaders");
		$arrValues = array($memberInfo['member_id'], time(), $_POST['clanname'], $_POST['status'], $_POST['website'], $_POST['clansize'], $_POST['tag'], $_POST['skill'], $_POST['gamesplayed'], $_POST['extrainfo'], $_POST['leaders']);
		
		if($diplomacyObj->addNew($arrColumns, $arrValues)) {
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully added ".$diplomacyObj->get_info_filtered("clanname")." to the diplomacy page!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Add New Clan', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			
			";
			
			
			$member->logAction("Added ".$_POST['clanname']." to the diplomacy page with ".$diplomacyStatusObj->get_info_filtered("name")." status.");
		
			if(isset($_POST['reqID']) && $diplomacyRequestObj->select($_POST['reqID'])) {
				
				$diplomacyRequestInfo = $diplomacyRequestObj->get_info_filtered();
				$dispStatus = $arrDiplomacyStatus[$_POST['status']];
				// Send E-mail Confirmation
				$emailTo = $diplomacyRequestInfo['email'];
				$emailFrom = "confirmemail@bluethrust.com";
				$emailSubject = $websiteInfo['clanname']." - Diplomacy Request: Accepted";
				$emailMessage = "
Hi ".$diplomacyRequestInfo['name'].",\n\n
				
Your diplomacy request has been accepted.  Your clan has been given the status of ".$dispStatus.".\n\n
Thanks,\n
".$websiteInfo['clanname'];
				
				mail($emailTo, $emailSubject, $emailMessage, "From: ".$emailFrom);
				
				$diplomacyRequestObj->delete();
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
	
	
	
	
	$arrSelectSize['large'] = "";
	$arrSelectSize['medium'] = "";
	$arrSelectSize['small'] = "";
	
	$dispReqID = "";
	if(isset($_GET['reqID']) && $diplomacyRequestObj->select($_GET['reqID'])) {
		$diplomacyRequestInfo = $diplomacyRequestObj->get_info_filtered();

		
		$_POST['clanname'] = $diplomacyRequestInfo['clanname'];
		$_POST['leaders'] = $diplomacyRequestInfo['leaders'];
		$_POST['website'] = $diplomacyRequestInfo['website'];
		$_POST['gamesplayed'] = $diplomacyRequestInfo['gamesplayed'];
		$_POST['tag'] = $diplomacyRequestInfo['clantag'];
		$_POST['status'] = $diplomacyRequestInfo['diplomacystatus_id'];
		
		$arrSelectSize[$diplomacyRequestInfo['clansize']] = " selected";
		
		$dispReqID = "<input type='hidden' value='".$_GET['reqID']."' name='reqID'>";
	}
	
	
	
	echo "
		<div class='formDiv'>
		";
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to add new clan because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
	
		echo "
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
				Use the form below to add a new clan to the diplomacy page.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Clan Name:</td>
						<td class='main'><input type='text' name='clanname' value='".$_POST['clanname']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Status:</td>
						<td class='main'>
							<select name='status' class='textBox'>
							";
								
								foreach($arrDiplomacyStatus as $key=>$value) {
									
									$dispSelected = "";
									if($_POST['status'] == $key) {
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
						<td class='main'><input type='text' name='leaders' value='".$_POST['leaders']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Website:</td>
						<td class='main'><input type='text' name='website' class='textBox' value='".$_POST['website']."' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Games Played:</td>
						<td class='main'><input type='text' name='gamesplayed' value='".$_POST['gamesplayed']."' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Skill Level:</td>
						<td class='main'><input type='text' name='skill' class='textBox' value='".$_POST['skill']."' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Clan Size:</td>
						<td class='main'>
							<select name='clansize' class='textBox'>
								<option value='small'".$arrSelectSize['small'].">Small</option>
								<option value='medium'".$arrSelectSize['medium'].">Medium</option>
								<option value='large'".$arrSelectSize['large'].">Large</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Tag:</td>
						<td class='main'><input type='text' name='tag' class='textBox' value='".$_POST['tag']."' style='width: 50px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Extra Info:</td>
						<td class='main' valign='top'>
							<textarea rows='4' cols='55' name='extrainfo' class='textBox'>".$_POST['extrainfo']."</textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br>
						
							<input type='submit' name='submit' value='Add Clan' class='submitButton' style='width: 125px'>
						
						</td>
					</tr>
				</table>
				".$dispReqID."
			</form>
		</div>	
	";
	
	
}




?>