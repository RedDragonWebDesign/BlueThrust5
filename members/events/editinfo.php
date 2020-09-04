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

	if(!$member->hasAccess($consoleObj) || (!$eventObj->memberHasAccess($memberInfo['member_id'], "editinfo") && $memberInfo['rank_id'] != 1)) {

		exit();
	}
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Edit Event Information\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <b>".$eventInfo['title'].":</b> Edit Event Information\");
});
</script>
";

$arrTimezones = DateTimeZone::listIdentifiers();
$eventInfo = $eventObj->get_info_filtered();
if($_POST['submit']) {
	// Check Title
	if(trim($_POST['eventtitle']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Event title may not be blank.<br>";
		$countErrors++;
	}
	
	// Check Start Time
	$arrHours = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
	if(!in_array($_POST['starthour'], $arrHours)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid start hour.<br>";
		$countErrors++;
	}
	
	$arrMinutes = array();
	for($i=0;$i<=59;$i++) {
		$arrMinutes[] = $i;
	}
	
	if(!in_array($_POST['startminute'], $arrMinutes)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid start minute.<br>";
		$countErrors++;
	}
	
	
	// Calc Start Date
	$tempTimezone = date_default_timezone_get();
	date_default_timezone_set("UTC");
	$startMonth = date("n", ($_POST['startdate']/1000));
	$startDay = date("j", ($_POST['startdate']/1000));
	$startYear = date("Y", ($_POST['startdate']/1000));
	
	if($_POST['ampm'] == "pm") {
		$startHour = $_POST['starthour']+12;
	}
	else {
		$startHour = $_POST['starthour'];
	}
	
	date_default_timezone_set($tempTimezone);
	$setStartTime = mktime($startHour, $_POST['startminute'], 0, $startMonth, $startDay, $startYear);
	
	if($setStartTime < time()) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid start date.<br>";
		$countErrors++;
	}
	
	if($_POST['invitetype'] != 1) {
		$inviteType = 0;
	}
	else {
		$inviteType = 1;
	}
	
	if($_POST['openinvites'] != 0) {
		$openInvites = 1;
	}
	else {
		$openInvites = 0;
	}
	
	$arrCheckVisibility = array(0, 1, 2);
	if(!in_array($_POST['visibility'], $arrCheckVisibility)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid visibility setting.<br>";
		$countErrors++;
	}
	
	
	if($_POST['allowmessages'] != 0) {
		$allowMessages = 1;
	}
	else {
		$allowMessages = 0;
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("title", "description", "location", "startdate", "publicprivate", "visibility", "messages", "invitepermission", "timezone");
		$arrValues = array($_POST['eventtitle'], $_POST['eventdetails'], $_POST['eventlocation'], $setStartTime, $inviteType, $_POST['visibility'], $allowMessages, $openInvites, $_POST['timezone']);
		
		if($eventObj->update($arrColumns, $arrValues)) {
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully edited event information!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Edit Event Information', '".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."', 'successBox');
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
	
	$startHour = date("G", $eventInfo['startdate']);
	$startMinute = date("i", $eventInfo['startdate']);
	
	if($startHour > 11) {
		$startHour -= 12;	
	}
	
	$houroptions = "<option value='0'>12</option>";
	$dispSelected = "";
	for($i=1;$i<=11;$i++) {
		if($startHour == $i) {
			$dispSelected = " selected";	
		}
		$houroptions .= "<option value='".$i."'".$dispSelected.">".$i."</option>";
		$dispSelected = "";
	}
	
	for($i=0;$i<=59;$i++) {
		
		if($startMinute == $i) {
			$dispSelected = " selected";	
		}
		
		if($i < 10) {
			$dispI = "0".$i;
		}
		else {
			$dispI = $i;
		}
	
		$minuteoptions .= "<option value='".$i."'".$dispSelected.">".$dispI."</option>";
		$dispSelected = "";
	}
	
	
	$showStartDate = date("M", $eventInfo['startdate'])." ".date("j", $eventInfo['startdate']).", ".date("Y", $eventInfo['startdate']);
	
	$dispPMSelected = "";
	if(date("a", $eventInfo['startdate']) == "pm") {
		$dispPMSelected = " selected";	
	}
	
	
	if($eventInfo['messages'] == 1) {
		$dispCheckMessages = " checked";	
	}
	
	if($eventInfo['invitepermission'] == 1) {
		$dispCheckInvite = " checked";	
	}
	
	
	$dispInviteOnlySelected = "";
	if($eventInfo['publicprivate'] == 1) {
		$dispInviteOnlySelected = " selected";		
	}
	
	if($eventInfo['visibility'] == 1) {
		$dispMembersOnlySelected = " selected";	
	}
	elseif($eventInfo['visibility'] == 2) {
		$dispInvitedOnlySelected = " selected";	
	}
	
	$timezoneoptions = "<option value=''>[Use Default]</option>";
	foreach($arrTimezones as $timeZone) {
		
		$tz = new DateTimeZone($timeZone);
		$dispOffset = ((($tz->getOffset(new DateTime("now", $tz)))/60)/60);
		$dispSign = ($dispOffset < 0) ? "" : "+";
		
		$dispSelected = "";
		if($timeZone == $eventInfo['timezone']) {
			$dispSelected = " selected";	
		}
		
		$timezoneoptions .= "<option value='".$timeZone."'".$dispSelected.">".str_replace("_", " ", $timeZone)." (UTC".$dispSign.$dispOffset.")</option>";
	}
	
	
	echo "
	
		<form action='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=EditInfo' method='post'>
		<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit event information because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}	
	
	echo "
		Use the form below to edit the event information.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Event Title:</td>
					<td class='main'><input type='text' name='eventtitle' class='textBox' style='width: 200px' value='".$eventInfo['title']."'></td>
				</tr>
				<tr>
					<td class='formLabel'>Start Date:</td>
					<td class='main'><input type='text' class='textBox' value='".$showStartDate."' style='width: 125px; cursor: pointer' id='jqStartDate' readonly='readonly'></td>
				</tr>
				<tr>
					<td class='formLabel'>Start Time</td>
					<td class='main'><select name='starthour' class='textBox'>".$houroptions."</select> : <select name='startminute' class='textBox'>".$minuteoptions."</select> <select name='ampm' class='textBox'><option value='am'>AM</option><option value='pm'".$dispPMSelected.">PM</option></select> <select name='timezone' class='textBox'>".$timezoneoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel'>Location:</td>
					<td class='main'><input type='text' name='eventlocation' class='textBox' style='width: 200px' value='".$eventInfo['location']."'></td>
				</tr>
				";
				/*<tr>
					<td class='formLabel'>Invite Type:</td>
					<td class='main'><select name='invitetype' class='textBox'><option value='0'>Anyone Can Join</option><option value='1'".$dispInviteOnlySelected.">Invite Only</option></select>
				</tr>*/
				echo "
				<tr>
					<td class='formLabel'>Visibility: <a href='javascript:void(0)' onmouseover=\"showToolTip('This allows you to choose who can see the event details page.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
					<td class='main'>
						<select name='visibility' class='textBox'>
							<option value='0'>Anyone</option>
							<option value='1'".$dispMembersOnlySelected.">Members Only</option>
							<option value='2'".$dispInvitedOnlySelected.">Invites Only</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Allow Messages: <a href='javascript:void(0)' onmouseover=\"showToolTip('Check the box to allow all invited members to post messages on the event page.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
					<td class='main'><input type='checkbox' name='allowmessages' value='1'".$dispCheckMessages."></td>
				</tr>
				<tr>
					<td class='formLabel'>Invite Permissions: <a href='javascript:void(0)' onmouseover=\"showToolTip('Check the box to allow all invited members to send invites to other members.  If left unchecked you can still give certain invited members this ability by giving them an event position.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
					<td class='main'><input type='checkbox' name='openinvites' value='1'".$dispCheckInvite."></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Event Details:</td>
					<td class='main'><textarea class='textBox' name='eventdetails' style='width: 250px; height: 95px'>".$eventInfo['description']."</textarea></td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br>
						<input type='submit' name='submit' value='Save' style='width: 100px' class='submitButton'>
					</td>
				</tr>
			</table>				
		
		</div>
		<input type='hidden' id='realStartDate' value='".time()."' name='startdate'>
		</form>
		<script type='text/javascript'>	
		
			$(document).ready(function() {
						
				var eventDate = new Date();
				eventDate.setFullYear(".date("Y", $eventInfo['startdate']).", ".(date("n", $eventInfo['startdate'])-1).", ".date("j", $eventInfo['startdate']).");
			
				$('#jqStartDate').datepicker({
			
				";
			
	
				$minYear = date("Y");
				$minMonth = date("n")-1;
				$minDay = date("j");
	
			echo "
					changeMonth: true,
					changeYear: true,
					dateFormat: 'M d, yy',
					minDate: new Date(".$minYear.", ".$minMonth.", ".$minDay."),
					altField: '#realStartDate',
					altFormat: '@',
			
					
				});
				

				var currentDate = $('#jqStartDate').datepicker('getDate');
				$('#realStartDate').val(currentDate.valueOf());
				
			});
		
		</script>
	";
	
}

?>