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



if(!isset($member) || !isset($eventObj) || substr($_SERVER['PHP_SELF'], -strlen("manage.php")) != "manage.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$eventObj->select($eID);

	if(!$member->hasAccess($consoleObj) || !$eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions")) {

		exit();
	}
}

$eventPositionInfo = $eventObj->objEventPosition->get_info_filtered();

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Event Positions\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=ManagePositions'><b>".$eventInfo['title'].":</b> Manage Event Positions</a> > ".$eventPositionInfo['name']."\");
});
</script>
";

$countErrors = 0;
$dispError = "";

if($_POST['submit']) {
	
	// Check position name
	if(trim($_POST['positionname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank position name.<br>";
	}
	
	// Check display order
	$intNewOrderNum = $eventObj->objEventPosition->validateOrder($_POST['displayorder'], $_POST['beforeafter'], true, $eventPositionInfo['sortnum']);
	if($intNewOrderNum === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
	}
	
	// Filter Position Options
	$arrPositionOptions = $eventObj->arrPositionOptions;
	foreach($arrPositionOptions as $positionOptionName) {
		if($_POST[$positionOptionName] != 0) {
			$_POST[$positionOptionName] = 1;
		}
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("name", "sortnum", "modchat", "invitemembers", "manageinvites", "postmessages", "managemessages", "attendenceconfirm", "editinfo", "eventpositions", "description");
		$arrValues = array($_POST['positionname'], $intNewOrderNum, $_POST['modchat'], $_POST['invitemembers'], $_POST['manageinvites'], $_POST['postmessages'], $_POST['managemessages'], $_POST['attendenceconfirm'], $_POST['editinfo'], $_POST['eventpositions'], $_POST['description']);
		$eventObj->objEventPosition->select($eventPositionInfo['position_id']);
		
		if($eventObj->objEventPosition->update($arrColumns, $arrValues)) {
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully edited the event position, <b>".$eventObj->objEventPosition->get_info_filtered("name")."!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Edit Event Position', '".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=ManagePositions', 'successBox');
				</script>
			
			";
			
			$eventObj->objEventPosition->resortOrder();
			
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
	
	
	$arrPositions = $eventObj->getPositions(" ORDER BY sortnum");
	$orderoptions = "";
	
	$findBeforeAfter = $eventObj->objEventPosition->findBeforeAfter();
	
	foreach($arrPositions as $positionID) {
		
		if($positionID != $eventPositionInfo['position_id']) {
			
			$dispSelected = "";
			if($findBeforeAfter[0] == $positionID) {
				$dispSelected = " selected";	
			}
			
			$eventObj->objEventPosition->select($positionID);
			$positionInfo = $eventObj->objEventPosition->get_info_filtered();
			$orderoptions .= "<option value='".$positionID."'".$dispSelected.">".$positionInfo['name']."</option>";
		}
	}
	
	$afterSelected = "";
	if($findBeforeAfter[1] == "first") {
		$orderoptions = "<option value='first'>(first position)</option>";
	}
	elseif($findBeforeAfter[1] == "after") {
		$afterSelected = " selected";	
	}
	

	foreach($eventObj->arrPositionOptions as $optionName) {
		if($eventPositionInfo[$optionName] == 1) {
			$arrCheckOption[$optionName] = " checked";
		}
		else {
			$arrCheckOption[$optionName] = "";
		}
	}
	
	$postMessages = "";
	if($eventInfo['messages'] == 1) {
		$postMessages = " onmouseover=\"showToolTip('You have allowed all invited members to post messages on the event page.  Uncheck this box to create a position that will prevent members posting messages.')\" onmouseout='hideToolTip()'";
	}
	
	echo "
	
		<form action='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=ManagePositions&posID=".$eventPositionInfo['position_id']."&action=edit' method='post'>
			<div class='formDiv'>
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit event position because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Event positions allow you to give certain members who will be attending the event to have greater responsibilities.  Assign invited members a position on the <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$eID."&pID=ManageInvites'>Manage Invites</a> page.
				<br><br>
				<table class='formTable'>
					<tr>
						<td colspan='2' class='main'>
							<b>General Information</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Position Name:</td>
						<td class='main'><input type='text' class='textBox' name='positionname' value='".$eventPositionInfo['name']."' style='width: 200px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main' valign='top'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$afterSelected.">After</option></select><br>
							<select name='displayorder' class='textBox'>".$orderoptions."</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Description:</td>
						<td class='main'>
							<textarea name='description' class='textBox' rows='3' cols='40'>".$eventPositionInfo['description']."</textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Privileges:</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Chat Moderator:</td>
						<td class='main'><input type='checkbox' name='modchat' value='1'".$arrCheckOption['modchat']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Invite Members:</td>
						<td class='main'><input type='checkbox' name='invitemembers' value='1'".$arrCheckOption['invitemembers']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Manage Invites:</td>
						<td class='main'><input type='checkbox' name='manageinvites' value='1'".$arrCheckOption['manageinvites']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Post Messages:</td>
						<td class='main'><input type='checkbox' name='postmessages' value='1'".$postMessages.$arrCheckOption['postmessages']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Manage Messages:</td>
						<td class='main'><input type='checkbox' name='managemessages' value='1'".$arrCheckOption['managemessages']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Confirm Attendence:</td>
						<td class='main'><input type='checkbox' name='attendenceconfirm' value='1' onmouseover=\"showToolTip('All members will be able to confirm their own attendence.  This privilege will allow a member to confirm that other members have attended as well.')\" onmouseout='hideToolTip()'".$arrCheckOption['attendenceconfirm']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Edit Event Info:</td>
						<td class='main'><input type='checkbox' name='editinfo' value='1'".$arrCheckOption['editinfo']."></td>
					</tr>
					<tr>
						<td class='formLabel' style='padding-left: 10px'>Create/Manage Positions:</td>
						<td class='main'><input type='checkbox' name='eventpositions' value='1'".$arrCheckOption['eventpositions']."></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' value='Edit Position' style='width: 125px' class='submitButton'>
						</td>
					</tr>
				</table>
			
			</div>
		</form>
	
	";
	
}



?>