<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


require_once("../../../_setup.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$eventObj = new Event($mysqli);


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if ($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventMember->select($_POST['eMemID'])) {

	$eventID = $eventObj->objEventMember->get_info("event_id");

	$memberInfo = $member->get_info_filtered();

	if ($eventObj->select($eventID) && $member->hasAccess($consoleObj) && (($eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites") || $eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) || $memberInfo['rank_id'] == 1)) {

		$formObj = new Form();
		$eventInfo = $eventObj->get_info_filtered();
		$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
		$objInviteMember = new Member($mysqli);
		$objInviteMember->select($eventMemberInfo['member_id']);
		$inviteMemberInfo = $objInviteMember->get_info_filtered();

		$dispAttendenceStatus = $eventObj->arrInviteStatus[$eventMemberInfo['status']];
		$positionOptions = array("None");
		foreach ($eventObj->getPositions() as $value) {

			$eventObj->objEventPosition->select($value);
			$dispPositionName = $eventObj->objEventPosition->get_info_filtered("name");

			$positionOptions[$value] = $dispPositionName;

		}

		$dispConfirmAttendence = array(
			"type" => "hidden",
			"hidden" => true,
			"attributes" => array("id" => "confirmAttendence")
		);



		if ($eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) {

			$dispSelected = "";
			if ($eventMemberInfo['attendconfirm_admin'] == 1) {
				$dispSelected = " selected";
			}


			$dispDisabledInfo = "";
			$dispDisableForm = "";
			if ($eventInfo['startdate'] > time()) {
				$dispDisabledInfo = "You must wait for the event to start before you can confirm a member's attendence.";
				$dispDisableForm = "disabled";
			}

			$dispConfirmAttendence = array(
				"type" => "select",
				"display_name" => "Confirm Attendance",
				"attributes" => array("class" => "formInput textBox", "id" => "confirmAttendence", "disable" => $dispDisableForm),
				"tooltip" => $dispDisabledInfo,
				"options" => array("Not Confirmed", "Confirmed", "Excused", "Unexcused"),
				"value" => $eventMemberInfo['attendconfirm_admin']
			);


		}

		$i = 1;
		$arrComponents = array(
			"attendancestatus" => array(
				"display_name" => "Attendance Status",
				"type" => "custom",
				"html" => "<div class='formInput'>".$dispAttendenceStatus."</div>",
				"sortorder" => $i++
			)
		);

		if ($eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites")) {

			$arrComponents['selectposition'] = array(
				"display_name" => "Set Position",
				"type" => "select",
				"sortorder" => $i++,
				"options" => $positionOptions,
				"attributes" => array("class" => "formInput textBox", "id" => "selectPositionID"),
				"value" => $eventMemberInfo['position_id']
			);

		}


		$arrComponents['confirmattendance'] = $dispConfirmAttendence;
		$arrComponents['confirmattendance']['sortorder'] = $i++;

		$arrComponents['submit'] = array(
			"type" => "button",
			"sortorder" => $i++,
			"attributes" => array("class" => "formSubmitButton submitButton", "onclick" => "btnSaveClicked()"),
			"value" => "Save"

		);


		$setupFormArgs = array(
			"name" => "console-".$cID."-manageinvites",
			"components" => $arrComponents,
			"wrapper" => array("<div>", "</div>")
		);

		$formObj->buildForm($setupFormArgs);


		if ( ! empty($_POST['submit']) ) {
			require_once(BASE_DIRECTORY."members/events/include/manageinvites_submit.php");
		}



		$formObj->show();

		if (isset($_POST['submit']) && $countErrors == 0) {

			echo "
				<p class='successFont' align='center'>
					<b>Save Successfull!</b>
				</p>
			";

		}

		echo "

			
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

		if ($eventObj->memberHasAccess($memberID, "manageinvites")) {
			echo "
					$('#uninviteLinkDiv').html($('#uninviteLink').html());
					$('#uninviteLinkDiv').show();
				";
		}

		echo "
					$('#errorDiv').hide();
					$('#noMemberSelectedDiv').hide();

		";


		if (isset($_POST['submit']) && $countErrors > 0) {

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