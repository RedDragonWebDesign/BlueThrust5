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


include("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$eventObj = new Event($mysqli);


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventMember->select($_POST['eMemID'])) {

	$eventID = $eventObj->objEventMember->get_info("event_id");

	$memberInfo = $member->get_info_filtered();

	if($eventObj->select($eventID) && $member->hasAccess($consoleObj) && (($eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites") || $eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) || $memberInfo['rank_id'] == 1)) {
		
		$eventInfo = $eventObj->get_info_filtered();
		$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
		$objInviteMember = new Member($mysqli);
		$objInviteMember->select($eventMemberInfo['member_id']);
		$inviteMemberInfo = $objInviteMember->get_info_filtered();
		
		$countErrors = 0;
		$dispError = "";
		if(isset($_POST['submit'])) {
			
			$arrColumns = array();
			$arrValues = array();
			
			
			if($eventObj->memberHasAccess($memberInfo['member_id'], "mangeinvites")) {
				$arrColumns[] ="position_id";
				$arrValues[] = $_POST['updatePositionID'];
				$checkSelectPosition = $eventObj->objEventPosition->select($_POST['updatePositionID']);
				// Check Position ID
				if($_POST['updatePositionID'] != 0 && (!$checkSelectPosition || ($checkSelectPosition && $eventObj->objEventPosition->get_info("event_id") != $eventID))) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid position.<br>";
				}			
				
			}
			
			
			if($eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) {
				
				if($eventInfo['startdate'] <= time() && ($_POST['updateConfirm'] == 1 || $_POST['updateConfirm'] == 0)) {
					$arrColumns[] = "attendconfirm_admin";
					$arrValues[] = 1;
				}	
				
			}
			

			
			if($countErrors == 0) {
				if(!$eventObj->objEventMember->update($arrColumns, $arrValues)) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
				}
				else {
					$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
				}
			}

		}
		
		$dispAttendenceStatus = $eventObj->arrInviteStatus[$eventMemberInfo['status']];
		$positionoptions = "<option value='0'>None</option>";
		foreach($eventObj->getPositions() as $value) {
			
			$dispSelected = "";
			if($eventMemberInfo['position_id'] == $value) {
				$dispSelected = " selected";				
			}
			
			$eventObj->objEventPosition->select($value);
			$dispPositionName = $eventObj->objEventPosition->get_info_filtered("name");
			
			$positionoptions .= "<option value='".$value."'".$dispSelected.">".$dispPositionName."</option>";
			
		}
		
		$dispConfirmAttendence = "<input type='hidden' id='confirmAttendence'>";
		if($eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) {
			
			$dispSelected = "";
			if($eventMemberInfo['attendconfirm_admin'] == 1) {
				$dispSelected = " selected";	
			}
			
			
			$dispDisabledInfo = "";
			$dispDisableForm = "";
			if($eventInfo['startdate'] > time()) {
				$dispDisabledInfo = "<a href='javascript:void(0)' onmouseover=\"showToolTip('You must wait for the event to start before you can confirm a member\'s attendence.')\" onmouseout='hideToolTip()'>(?)</a>";
				$dispDisableForm = " disabled = 'disabled'";
			}
			
			$dispConfirmAttendence = "
			
				<tr>
					<td class='formLabel'>Confirm Attendence: ".$dispDisabledInfo."</td>
					<td class='main'><select id='confirmAttendence' class='textBox'".$dispDisableForm."><option value='0'>Not Confirmed</option><option value='1'".$dispSelected.">Confirmed</option></select></td>
				</tr>
			
			";
			
		}
		
		
		echo "
			<div id='dispErrorDiv' style='display: none'>".$dispError."</div>	
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Attendence Status:</td>
					<td class='main'>".$dispAttendenceStatus."</td>
				</tr>
				
				";
		
				if($eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites")) {
					echo "
						<tr>
							<td class='formLabel'>Set Position:</td>
							<td class='main'><select id='selectPositionID' class='textBox'>".$positionoptions."</select></td>
						</tr>
					";
				}
				
				echo "
				".$dispConfirmAttendence."
				<tr>
					<td colspan='2' class='main' align='center'>
						<br><br>
						<input type='button' class='submitButton' onclick='btnSaveClicked()' value='Save' style='width: 100px' id='btnSave'>
						";
		
		if(isset($_POST['submit']) && $countErrors == 0) {
		
			echo "
				<p class='successFont' align='center'>
					<b>Save Successfull!</b>
				</p>
			";
		
		}
		
		echo "
					</td>
				</tr>
			</table>
		
			
			<div id='uninviteMessageDiv' style='display: none'>
				<p class='main' align='center'>
					Are you sure you want to uninvite <b>".$inviteMemberInfo['username']."</b>?
				</p>
			</div>
			
			<div id='uninviteLink' style='display: none'>
				- <a href='javascript:void(0)' onclick=\"uninviteMember('".$eventMemberInfo['eventmember_id']."')\"><b>Uninvite Member</b></a>
			</p>
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#selectEventMemberID').removeAttr('disabled');
					
					";
		
		if($eventObj->memberHasAccess($memberID, "manageinvites")) {
			echo "
					$('#uninviteLinkDiv').html($('#uninviteLink').html());
					$('#uninviteLinkDiv').show();
				";
		}
		
		echo "
					$('#errorDiv').hide();
					$('#noMemberSelectedDiv').hide();

		";

		
		if(isset($_POST['submit']) && $countErrors > 0) {
		
			echo "
				
				$('#dispErrorSpan').html($('#dispErrorDiv').html());
				$('html, body').animate({ scrollTop: 0 });
				$('#errorDiv').fadeIn(250);
				
			";
		
		}
		
		
		echo "
					
				});
				
				
				function uninviteMember(intEventMemberID) {
					
					$(document).ready(function() {
					
						$('#uninviteMessageDiv').dialog({
						
							title: 'Uninvite Member - Manage My Events',
							zIndex: 99999,
							modal: true,
							show: 'scale',
							width: 400,
							resizable: false,
							buttons: {
								'Yes': function() {
									
									$.post('".$MAIN_ROOT."members/events/include/manageinvites_uninvite.php', { eMemID: intEventMemberID }, function(data) {
									
										$('#uninviteLinkDiv').hide();	
										$('#selectEventMemberID').html(data);
										$('#manageOptionsDiv').hide();
										$('#noMemberSelectedDiv').show();
									
									});
									
									$(this).dialog('close');
									
								},
								'Cancel': function() {
									$(this).dialog('close');
								}
							}
						
						});
					
					});
				
				}
				
			</script>
		";

		
	}
	
}


?>