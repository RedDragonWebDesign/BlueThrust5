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
		
		if($maxRankInfo['ordernum'] > $row['ordernum']) {
			$arrMemRanks[] = $row['rank_id'];
		}
		
	}
	
	
	// Check Member
	if(!$member->select($_POST['member']) || $_POST['member'] == $memberInfo['member_id']) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	}
	elseif(!in_array($member->get_info("rank_id"), $arrMemRanks)) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not change the selected member's rank.<br>";
	}
	
	
	// Check Rank 
	if(!in_array($_POST['newrank'], $arrRanks)) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank.<br>";
	}
	
	
	// Check Freeze Time
	
	if(!is_numeric($_POST['freezetime']) && $_POST['freezetime'] <= 36500) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid freeze time.<br>";
	}
	
	
	// Load Plugins
	
	
	
	if($countErrors == 0) {
		
		$freezeTime = (86400*$_POST['freezetime'])+time();
		
		$arrColumns = array("rank_id", "freezerank");
		$arrValues = array($_POST['newrank'], $freezeTime);
		
		$member->select($_POST['member']);
		
		$rankObj->select($_POST['newrank']);		
		$newRankInfo = $rankObj->get_info_filtered();
		
		$rankObj->select($member->get_info("rank_id"));
		$oldRankInfo = $rankObj->get_info_filtered();
		
		$actionWord = "set";
		if($newRankInfo['ordernum'] > $oldRankInfo['ordernum']) {
			$actionWord = "promoted";
			$arrColumns[] = "lastpromotion";
			$arrValues[] = time();
		}
		elseif($newRankInfo['ordernum'] < $oldRankInfo['ordernum']) {
			$actionWord = "demoted";
			$arrColumns[] = "lastdemotion";
			$arrValues[] = time();
		}
		
		if($member->update($arrColumns, $arrValues)) {
			
			$logMessage = $member->getMemberLink()." ".$actionWord." to rank ".$newRankInfo['name']." from ".$oldRankInfo['name'].".<br><br><b>Reason:</b><br>".filterText($_POST['reason']);
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully set ".$member->getMemberLink()."'s rank to <b>".$newRankInfo['name']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Set Member Rank', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
			$member->postNotification("Your rank has been set to ".$newRankInfo['name']."!");
			
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
	
	
	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	if($rankInfo['rank_id'] == 1) {
		$maxRankInfo['ordernum'] += 1;
	}

	
	$arrRanks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$rankoptions .= "<option value='".$row['rank_id']."'>".filterText($row['name'])."</option>";
		$arrRanks[] = $row['rank_id'];
		
		if($maxRankInfo['ordernum'] > $row['ordernum']) {
			$arrMemRanks[] = $row['rank_id'];
		}
		
	}
	
	$sqlRanks = "('".implode("','", $arrMemRanks)."')";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.member_id != '".$memberInfo['member_id']."'  ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
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
			<strong>Unable to set member's rank because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
				Use the form below to set a member's rank.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>New Rank:</td>
						<td class='main'><select name='newrank' class='textBox'>".$rankoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Reason:</td>
						<td class='main' valign='top'><textarea name='reason' cols='40' rows='3' class='textBox'>".$_POST['reason']."</textarea></td>
					</tr>
					<tr>
						<td class='formLabel'>Freeze Member: <a href='javascript:void(0)' onmouseover=\"showToolTip('When demoting a member, they may be auto-promoted due to the number of days they are in the clan.  Set how long you want to keep the member demoted before being auto-promoted again.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<select name='freezetime' class='textBox'>
								<option value='0'>Don't Freeze</option>
								<option value='1'>1 day</option>
								<option value='3'>3 days</option>
								<option value='7'>7 days</option>
								<option value='10'>10 days</option>
								<option value='14'>14 days</option>
								<option value='21'>21 days</option>
								<option value='30'>30 days</option>
								<option value='45'>45 days</option>
								<option value='60'>60 days</option>
								<option value='75'>75 days</option>
								<option value='90'>90 days</option>
								<option value='36500'>Forever</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>		
							<input type='submit' name='submit' value='Set Rank' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
	";
	
	
}




?>