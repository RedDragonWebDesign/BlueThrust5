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

include_once($prevFolder."classes/squad.php");
$cID = $_GET['cID'];

$squadObj = new Squad($mysqli);
$dispError = "";
$countErrors = 0;
if($_POST['submit']) {
	
	// Check Squad
	if(!$squadObj->select($_POST['squad'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad!<br>";
	}
	else {
		$outstandingApps = $squadObj->getOutstandingApplications();

		if(in_array($memberInfo['member_id'], $outstandingApps)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have already applied to this squad!  Please wait for a decision to be made before re-applying.<br>";
		}
		
	}
	
	if($countErrors == 0) {
		$squadInfo = $squadObj->get_info_filtered();
		$squadAppObj = new Basic($mysqli, "squadapps", "squadapp_id");
		$arrColumns = array("member_id", "squad_id", "message", "applydate", "status");
		$arrValues = array($memberInfo['member_id'], $_POST['squad'], $_POST['message'], time(), 0);
		
		if($squadAppObj->addNew($arrColumns, $arrValues)) {
			
			$arrRecruiterMembers = $squadObj->getRecruiterMembers();
			foreach($arrRecruiterMembers as $recruiterID) {
				
				$member->select($recruiterID);
				$member->postNotification("A new member has applied to join the squad <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>.  <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$squadInfo['squad_id']."&pID=AcceptApps'>Click Here</a> to review squad applications.");
				
			}
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Applied to Squad: <b>".$squadInfo['name']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Apply to a Squad', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
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
	
	$arrMemberSquads= $member->getSquadList();
	$sqlSquadList = "('".implode("','", $arrMemberSquads)."')";
	
	$counter = 0;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."squads WHERE squad_id NOT IN ".$sqlSquadList." ORDER BY name");
	while($row = $result->fetch_assoc()) {
		
		$dispSelected = "";
		if($_GET['select'] == $row['squad_id']) {
			$dispSelected = "selected";	
		}
		
		
		$dispSquadOptions .= "<option value='".$row['squad_id']."' ".$dispSelected.">".filterText($row['name'])."</option>";
		$counter++;
	}
	
	if($counter == 0) {
		$dispSquadOptions = "<option value='none'>No Squads Available!</option>";	
	}
	
	
	echo "
		<div class='formDiv'>
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to apply to the squad because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "			
				Use the form below to apply to a squad.  Your application must be accepted by a squad member in order for you to join the squad.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Squad:</td>
						<td class='main'><select name='squad' class='textBox'>".$dispSquadOptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Message:</td>
						<td class='main'><textarea name='message' class='textBox' rows='5' cols='40'>".$_POST['message']."</textarea></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Apply to Squad' class='submitButton' style='width: 125px'>
						</td>
					</tr>
				</table>
				
			</form>
		</div>
	";
	
}




?>