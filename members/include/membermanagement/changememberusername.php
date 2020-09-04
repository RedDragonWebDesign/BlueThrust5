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

if($_POST['submit']) {

	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	if($rankInfo['rank_id'] == 1) {
		$maxRankInfo['ordernum'] += 1;
	}
	
	$arrRanks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$arrRanks[] = $row['rank_id'];
	}
	
	// Check Member
	$newRank = 0;
	if($_POST['member'] == "" || !$member->select($_POST['member']) || $_POST['member'] == $memberInfo['member_id']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	}
	elseif(!in_array($member->get_info("rank_id"), $arrRanks)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not change the selected member's username.<br>";
	}
	
	// Check New Username
	
	if(trim($_POST['newusername']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank username.<br>";
	}
	
	if($member->select($_POST['newusername'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> There is already a user with the chosen new username.<br>";
	}
	
	if($countErrors == 0) {
		$member->select($_POST['member']);
		$oldUsername = $member->get_info_filtered("username");
		
		if($member->update(array("username"), array($_POST['newusername']))) {
			$newUserInfo = $member->get_info_filtered();
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully changed ".$oldUsername."'s username to ".$member->getMemberLink()."!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Change Member Username', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
			
			$member->select($memberInfo['member_id']);
			$member->logAction("Changed ".$oldUsername."'s username to <a href='".$MAIN_ROOT."profile.php?mID=".$newUserInfo['member_id']."'>".$newUserInfo['username']."</a>.");
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
	
	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	if($rankInfo['rank_id'] == 1) {
		$maxRankInfo['ordernum'] += 1;
	}
	
	$arrRanks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$arrRanks[] = $row['rank_id'];
	}
	
	$sqlRanks = "('".implode("','", $arrRanks)."')";
	$memberoptions = "<option value=''>Select</option>";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.member_id != '".$memberInfo['member_id']."'  ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
	
		$rankObj->select($row['rank_id']);
		$memberoptions .= "<option value='".$row['member_id']."'>".$rankObj->get_info_filtered("name")." ".filterText($row['username'])."</option>";
	
	}	
	
	
	echo "
		<div class='formDiv'>
		";

	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to change member username because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
			Use the form below to change a member's username.
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>New Username:</td>
						<td class='main'><input type='text' name='newusername' class='textBox' value='".$_POST['newusername']."'></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Change Username' class='submitButton'>
						</td>
					</tr>
				</table>
			</form><br>
		</div>
	";
	
}


?>