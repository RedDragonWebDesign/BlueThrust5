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



$rankInfo = $memberRank->get_info_filtered();
if($memberInfo['promotepower'] != 0) {
	$rankInfo['promotepower'] = $memberInfo['promotepower'];	
}
elseif($memberInfo['promotepower'] == -1) {
	$rankInfo['promotepower'] = 0;	
}

$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;
if($memberInfo['rank_id'] == 1) {

	$maxOrderNum = $mysqli->query("SELECT MAX(ordernum) FROM ".$dbprefix."ranks WHERE rank_id != '1'");
	$arrMaxOrderNum = $maxOrderNum->fetch_array(MYSQLI_NUM);

	if($maxOrderNum->num_rows > 0) {
		$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE ordernum = '".$arrMaxOrderNum[0]."'");
		$row = $result->fetch_assoc();
		$rankInfo['promotepower'] = $row['rank_id'];
	}

}

$rankObj = new Rank($mysqli);
$rankObj->select($rankInfo['promotepower']);
$maxRankInfo = $rankObj->get_info_filtered();

$arrRanks = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrRanks[] = $row['rank_id'];
}


if($_POST['submit']) {
	
	// Check Member
	
	if(!$member->select($_POST['member'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	}
	elseif($member->select($_POST['member']) && !in_array($member->get_info("rank_id"), $arrRanks)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not change that member's password.<br>";
	}
	
	
	
	// Check Password
	
	if(strlen($_POST['newpassword']) < 4) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Your password must be at least 4 characters long.<br>";
	}
	
	
	if($_POST['newpassword'] != $_POST['newpassword1']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Your passwords did not match.<br>";
	}
	
	
	if($countErrors == 0) {
		
		$logMessage = "Changed ".$member->getMemberLink()."'s password.";
		
		if($member->set_password($_POST['newpassword'])) {
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center' class='main'>
						Successfully changed ".$member->getMemberLink()."'s password!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Reset Password', '".$MAIN_ROOT."members', 'successBox');
				</script>
			";
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
		$member->select($memberInfo['member_id']);
		$member->logAction($logMessage);
		
	}
	
	
	if($countErrors > 0) {
		$_POST['submit'] = false;	
	}
	
	
}




if(!$_POST['submit']) {
	
	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	$sqlRanks = "('".implode("','", $arrRanks)."')";
	$memberoptions = "<option value=''>Select</option>";
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.* FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id IN ".$sqlRanks." ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
		
		
		$memberoptions .= "<option value='".$row['member_id']."'>".filterText($row['name'])." ".filterText($row['username'])."</option>";
	
	}
	
	
	
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to reset password because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Use the form below to reset a member's password.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>New Password:</td>
						<td class='tinyFont'><input type='password' id='newpassword' name='newpassword' class='textBox' style='width: 125px'><br>(Minimum 4 characters)</td>
					</tr>
					<tr>
						<td class='formLabel'>Re-type New Password:</td>
						<td class='main'><input type='password' id='newpassword1' name='newpassword1' class='textBox' style='width: 125px'><span id='checkPassword' style='padding-left: 5px'></span></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br><br>
							<input type='submit' name='submit' value='Reset Password' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>	
		
		
		<script type='text/javascript'>
			
			$(document).ready(function() {
			
				$('#newpassword1').keyup(function() {
					
					if($('#newpassword').val() != \"\") {
					
						if($('#newpassword1').val() == $('#newpassword').val()) {
							$('#checkPassword').toggleClass('successFont', true);
							$('#checkPassword').toggleClass('failedFont', false);
							$('#checkPassword').html('ok!');
						}
						else {
							$('#checkPassword').toggleClass('successFont', false);
							$('#checkPassword').toggleClass('failedFont', true);
							$('#checkPassword').html('error!');
						}
					
					}
					else {
						$('#checkPassword').html('');
					}
				
				});
			
			});
		
		</script>
		
	";
	
}
