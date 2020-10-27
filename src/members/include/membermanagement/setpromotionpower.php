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

$rankObj = new Rank($mysqli);

// Determine affected members and disallow posting to the page if no member is in the list
$arrCIDs[] = $consoleObj->findConsoleIDByName("Promote Member");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Demote Member");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Disable a Member");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Undisable Member");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Set Member's Rank");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Award Medal");
$arrCIDs[] = $consoleObj->findConsoleIDByName("Revoke Medal");


$sqlCID = "('".implode("','", $arrCIDs)."')";
$memberoptions = "";
$result = $mysqli->query("SELECT ".$dbprefix."members.member_id, ".$dbprefix."members.username, ".$dbprefix."ranks.name FROM ".$dbprefix."console_members, ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."console_members.member_id = ".$dbprefix."members.member_id AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."console_members.console_id IN ".$sqlCID." AND ".$dbprefix."console_members.allowdeny = '1' AND ".$dbprefix."members.disabled = '0' ORDER BY ".$dbprefix."ranks.ordernum DESC");
while($row = $result->fetch_assoc()) {
	
	$member->select($row['member_id']);
	$rankObj->select($row['rank_id']);
	$rankObj->select($member->get_info("rank_id"));
	$rankInfo = $rankObj->get_info();
	
	$dispDefaultPower = "Can't Promote";
	if($rankInfo['promotepower'] != 0 && $rankObj->select($rankInfo['promotepower'])) {
		$dispDefaultPower = $rankObj->get_info_filtered("name");
	}
	
	
	$memberoptions .= "<option value='".$row['member_id']."' data-maxrank='".$member->get_info("promotepower")."' data-defaultpower=\"".$dispDefaultPower."\">".$row['name']." ".$row['username']."</option>";
	
	
}

if($memberoptions == "") {
	$_POST['submit'] = false;	
}




if($_POST['submit']) {
	
	// Check Member
	if(!$member->select($_POST['member'])) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";	
	}
	
	
	
	
	// Check Maximum Rank
	if($_POST['maximumrank'] != -1 && $_POST['maximumrank'] != 0 && !$rankObj->select($_POST['maximumrank'])) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank.<br>";	
	}
	
	if($countErrors == 0) {
		
		if($member->update(array("promotepower"), array($_POST['maximumrank']))) {
			$dispMemberName = $member->getMemberLink();
			$dispRankName = "Default";
			if($_POST['maximumrank'] == -1) {
				$dispRankName = "(Can't Promote)";
			}
			elseif($_POST['maximumrank'] != 0 && $rankObj->select($_POST['maximumrank'])) {
				$dispRankName = $rankObj->get_info_filtered("name");	
			}
			
			echo "
				
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully set ".$member->getMemberLink()."'s promote power to <b>".$dispRankName."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Set Member Rank', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			
			";
			
			$member->select($memberInfo['member_id']);			
			$member->logAction("Set maximum promote power for ".$dispMemberName." to ".$dispRankName);
			
		}
		else {
			
		}
		
	}
	
	
	if($countErrors > 0) {
		$member->select($memberInfo['member_id']);
		$_POST['submit'] = false;		
	}
	
	
}



if(!$_POST['submit']) {
	
	$rankoptions = "<option value='0' id='defaultPower'>Default</option><option value='-1'>(Can't Promote)</option>";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$rankoptions .= "<option value='".$row['rank_id']."'>".filterText($row['name'])."</option>";		
	}
	
	$member->select($memberInfo['member_id']);
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to set promotion power because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				The drop down list below shows all members that were given individual access to the console options Set Rank, Promote/Demote Member, Disable/Undisable Member and/or Award/Revoke medal.<br><br>
				Use this page to set the maximum rank a member can promote/demote, disable/undisable and award/revoke medals.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select name='member' id='memberSelect' class='textBox'>".$memberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>Maximum Rank:</td>
						<td class='main'><select id='maxRank' class='textBox' name='maximumrank'>".$rankoptions."</select></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' class='submitButton' value='Save'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		<script type='text/javascript'>
			$(document).ready(function() {
			
				$('#memberSelect').change(function() {
		
					$('#defaultPower').html(\"Default: \"+$(this).find(':selected').attr('data-defaultpower'));
					$('#maxRank').val($(this).find(':selected').attr('data-maxrank'));					
				
				});

				$('#memberSelect').change();
				
			});
		</script>
		
	";
	
}


?>