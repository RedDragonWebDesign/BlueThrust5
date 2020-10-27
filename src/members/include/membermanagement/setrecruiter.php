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

$memberInfo = $member->get_info_filtered();
$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;

$rankObj = new Rank($mysqli);
$memberObj = new Member($mysqli);
if($_POST['submit']) {
	
	
	// Check Member
	if(!$memberObj->select($_POST['member'])) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	}
	else {
		$newMemberInfo = $memberObj->get_info_filtered();
		$memberObj->select($newMemberInfo['recruiter']);
		$oldRecruiterInfo = $memberObj->get_info_filtered();
	}
	
	
	// Check Recruiter 
	if(!$memberObj->select($_POST['newrecruiter'])) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid Recruiter.<br>";
	}
	else {
		$newRecruiterInfo = $memberObj->get_info_filtered();
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("recruiter");
		$arrValues = array($_POST['newrecruiter']);
		
		$memberObj->select($_POST['member']);
		if($memberObj->update($arrColumns, $arrValues)) {
			
			$logMessage = $member->getMemberLink()." changed ".$newMemberInfo['username']."'s recruiter from ".$oldRecruiterInfo['username']." to ".$newRecruiterInfo['username'].".<br><br><b>Reason:</b><br>".filterText($_POST['reason']);
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully set ".$memberObj->getMemberLink()."'s recruiter to <b>".$newRecruiterInfo['username']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Set Member\'s Recruiter', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
			$memberObj->postNotification("Your recruiter has been set to ".$newMemberInfo['username']."!");
			
			$member->select($memberInfo['member_id']);
			$member->logAction($logMessage);
			
			
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
	
	$result = $mysqli->query("SELECT ".$dbprefix."members.* FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."ranks.rank_id = ".$dbprefix."members.rank_id AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id != '1' ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
		
		$rankObj->select($row['rank_id']);
		$memberoptions .= "<option value='".$row['member_id']."'>".$rankObj->get_info_filtered("name")." ".filterText($row['username'])."</option>";
		
	}
	
	
	echo "
	
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
	";
	
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to set member's recruiter because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
				Use the form below to set a member's recruiter.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>New Recruiter:</td>
						<td class='main'><select name='newrecruiter' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Reason:</td>
						<td class='main' valign='top'><textarea name='reason' cols='40' rows='3' class='textBox'>".$_POST['reason']."</textarea></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>		
							<input type='submit' name='submit' value='Set Recruiter' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
	";
	
	
}




?>