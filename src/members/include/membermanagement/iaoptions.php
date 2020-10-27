<?php
 
/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Nuker_Viper & Bluethrust Web Development
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
 
	if($countErrors == 0) {
		$time = time();
  
		// Check Member
		if(!$member->select($_POST['member'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
		}
		
		// Check Status
		if($member->get_info("onia") == 1 && $_POST['ia'] == 1) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected member is already on IA.<br>";	
		}
		
		if($member->get_info("onia") == 0 && $_POST['ia'] == 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected member is not on IA.<br>";	
		}
		
 
		$arrColumns = array("onia");
		$arrValues = ($_POST['ia'] == 1) ? array(1) : array(0);
		
		if($_POST['ia'] = "1") { $ia_NAME = "On Leave"; } else { $ia_NAME = "Off Leave"; }

 
		if($member->update($arrColumns, $arrValues)) {
 
			// Check for pending IA request and delete
			$checkRequested = $member->requestedIA(true);
			if($checkRequested !== false) {
				$requestIAObj = new Basic($mysqli, "iarequest", "iarequest_id");
				$requestIAObj->select($checkRequested);
				$requestIAObj->delete();
			}
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Set Member's IA Status!
					</p>
				</div>
	 
				<script type='text/javascript'>
					popupDialog('IA Options', '".$MAIN_ROOT."members', 'successBox');
				</script>
			";
 
			if($_POST['why'] != "I") { $reasonWHY = " Until $reason"; } else { $reasonWHY = ""; }
			$member->postNotification("You are ".$ia_NAME.$reasonWHY);
			
			$dispIAMember = $member->getMemberLink();
			
			$member->select($memberInfo['member_id']);
			$member->logAction("Set ".$dispIAMember." IA status to ".$ia_NAME);
			
			
		}
  
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
 
 
	}
 
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
 
}
 
 
if(!$_POST['submit']) {
 
 	$memberoptions = "";
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.name FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."ranks.rank_id = ".$dbprefix."members.rank_id AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id != '1' ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
 
		$memberoptions .= "<option value='".$row['member_id']."'>".filterText($row['name'])." ".filterText($row['username'])."</option>";
  
	}
	
	
	$ignore_options = "<option value='Y'>Show</option><option value='I'>Don't Show</option>";
	$iaoptions = "<option value='1'>Inactive</option><option value='0'>Active</option>";
 
 
	echo "
	<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
		<div class='formDiv'>
	";
 

	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to set IA Status because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
			Use the form below to add or remove members on IA.<br><br>
		    
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Member:</td>
					<td class='main'><select name='member' class='textBox'>".$memberoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel'>IA Status:</td>
					<td class='main'><select name='ia' class='textBox'>".$iaoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel'>Notify Member: <a href='javascript:void(0)' onmouseover=\"showToolTip('When set to &quot;Show&quot;, the member will receive a notification about their IA status.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'><select name='why' class='textBox'>".$ignore_options."</select></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Reason:</td>
					<td class='main' valign='top'><textarea name='reason' cols='40' rows='3' class='textBox'>".$_POST['reason']."</textarea></td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br><br>
						<input type='submit' name='submit' value='Set IA Status' class='submitButton' style='width: 125px'>
					</td>
				</tr>
			</table>
	    
		</div>
	</form>
  
  
 ";
 
}
