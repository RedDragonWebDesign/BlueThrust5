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

$memberObj = new Member($mysqli);
$rankObj = new Rank($mysqli);

$rankObj->select($memberInfo['rank_id']);
$rankInfo = $rankObj->get_info();
if($memberInfo['promotepower'] != 0) {
	$rankInfo['promotepower'] = $memberInfo['promotepower'];	
}
elseif($memberInfo['promotepower'] == -1) {
	$rankInfo['promotepower'] = 0;	
}

if($memberInfo['rank_id'] == 1) {
	$highestOrderNum = $rankObj->getHighestOrderNum();
	$rankObj->selectByOrder($highestOrderNum);
	$powerRankInfo = $rankObj->get_info();
}
else {
	$rankObj->select($rankInfo['promotepower']);
	$powerRankInfo = $rankObj->get_info();
}

if($_POST['submit']) {
	
	
	if(!$memberObj->select($_POST['member'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	}
	elseif($memberObj->select($_POST['member'])) {
		
		$tempMemInfo = $memberObj->get_info();
		$rankObj->select($tempMemInfo['rank_id']);
		$tempRankInfo = $rankObj->get_info();
		
		if($powerRankInfo['ordernum'] < $tempRankInfo['ordernum']) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not change the selected member's recruit date.<br>";
		}

	}
	
	
	$recruitDate = $_POST['newrecruitdate']/1000;

	if($recruitDate > time()) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid date.<br>";
	}
	
	if($countErrors == 0) {
		
		$arrColumn = array("datejoined");
		$arrValue = array($recruitDate);
		
		if($memberObj->update($arrColumn, $arrValue)) {
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully changed ".$memberObj->getMemberLink()."'s recruit date!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Set Member\'s Recruit Date', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
			$logMessage = "Changed ".$tempMemInfo['username']."'s recruit date to ".date("D M j, Y g:i a", $recruitDate).".";
			$member->logAction($logMessage);
			
			
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
	
	$result = $mysqli->query("SELECT ".$dbprefix."members.member_id FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."ranks.rank_id = ".$dbprefix."members.rank_id AND ".$dbprefix."ranks.ordernum <= '".$powerRankInfo['ordernum']."' AND ".$dbprefix."members.rank_id != '1' AND ".$dbprefix."members.disabled = '0' ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
		$memberObj->select($row['member_id']);
		$tempMemInfo = $memberObj->get_info_filtered();
		$rankObj->select($tempMemInfo['rank_id']);
		$tempRankInfo = $rankObj->get_info_filtered();
		$memberoptions .= "<option value='".$row['member_id']."'>".$tempRankInfo['name']." ".$tempMemInfo['username']."</option>";
		
	}
	
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to change recruit date because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Use the form below to change a member's recruit date.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>Recruit Date:</td>
						<td class='main'><input type='text' class='textBox' id='recruitdate' readonly='readonly'></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Change Date' class='submitButton'>
						</td>
					</tr>
				</table>
				<input type='hidden' name='newrecruitdate' id='realrecruitdate'>
			</div>
		</form>
		
		<script type='text/javascript'>
			$(document).ready(function() {
			
				$('#recruitdate').datepicker({
				
				";
					$dispMonth = date("n")-1;
					echo "
					changeMonth: true,
					yearRange: '".(date("Y")-20).":".date("Y")."',
					changeYear: true,
					dateFormat: 'M d, yy',
					maxDate: new Date(".date("Y").", ".$dispMonth.", ".date("j")."),
					altField: '#realrecruitdate',
					altFormat: '@'
				
				});
			});
		</script>
	";
	
}

