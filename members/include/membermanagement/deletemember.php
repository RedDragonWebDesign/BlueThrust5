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

$disableMemberCID = $consoleObj->findConsoleIDByName("Disable a Member");

$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;

if($_POST['submit']) {
	
	$delMemberObj = new Member($mysqli);
	
	if(!$delMemberObj->select($_POST['deletemember']) || !is_numeric($_POST['deletemember'])) {
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
		$countErrors++;
	}
	else {
		
		// Check if member is disabled
		if($delMemberObj->get_info("disabled") != 1) {
			$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may only delete members who are currently disabled.<br>";
			$countErrors++;
		}
		
	}
	
	
	if($countErrors == 0) {
		$delMemberUsername = $delMemberObj->get_info_filtered("username");
		if($delMemberObj->delete()) {
			
			echo "
			
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully deleted ".$delMemberUsername." from the website!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Delete Member', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			
			";
			
			
			$member->logAction("Deleted ".$delMemberUsername." from the website.");
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to delete member from the database.  Please contact the website administrator.<br>";
		}
		
	}
	
	
	
	if($countErrors > 0) {
		$_POST['submit'] = false;	
	}
	
	
}



if(!$_POST['submit']) {
	
	$memberoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE disabled = '1' ORDER BY username");
	while($row = $result->fetch_assoc()) {
	
		
		$memberoptions .= "<option value='".$row['member_id']."'>".filterText($row['username'])."</option>";
		
	}
	
	if($result->num_rows == 0) {
		$memberoptions .= "<option value=''>None</option>";	
	}
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
		<div class='formDiv'>
		";
	
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to disable member because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
		
			Use the form below to delete a member from the website.  You must first <a href='".$MAIN_ROOT."members/console.php?cID=".$disableMemberCID."'>Disable a Member</a> before you can delete them from the website.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Username:</td>
					<td class='main'><select name='deletemember' class='textBox'>".$memberoptions."</select></td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br>
						<input type='submit' name='submit' value='Delete Member' class='submitButton'>
					</td>
				</tr>			
			</table>
		</div>
		</form>
	";
	
}