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

	if(!$member->hasAccess($consoleObj) || (!$eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites") && !$eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm") && $memberInfo['rank_id'] != 1)) {
		exit();
	}
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Invites\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <b>".$eventInfo['title'].":</b> Manage Invites\");
});
</script>
";


$arrInvitedMembers = $eventObj->getInvitedMembers(true);

$sqlInvitedMembers = "('".implode("','", $arrInvitedMembers)."')";

$memberoptions = "<option value=''>Select</option>";
$result = $mysqli->query("SELECT m.member_id, m.username, r.ordernum, r.name FROM ".$dbprefix."members m, ".$dbprefix."ranks r WHERE m.rank_id = r.rank_id AND m.member_id IN ".$sqlInvitedMembers." AND m.disabled = '0' AND m.rank_id != '1' ORDER BY r.ordernum DESC");
while($row = $result->fetch_assoc()) {
	$row = filterArray($row);
	$eventMemberID = $eventObj->getEventMemberID($row['member_id']);
	if($eventMemberID !== false) {

		$memberoptions .= "<option value='".$eventMemberID."'>".$row['name']." ".$row['username']."</option>";
		
	}
}


echo "

	<div class='formDiv'>
		<div class='errorDiv' style='display: none'>
		<strong>Unable to save invite information because the following errors occurred:</strong><br><br>
			<span id='dispErrorSpan'></span>
		</div>
	
		Select a member from the dropdown menu below to view more options.
		<table class='formTable'>
			<tr>
				<td class='formLabel'>Select Member:</td>
				<td class='main'><select id='selectEventMemberID' class='textBox'>".$memberoptions."</select><span class='main' id='uninviteLinkDiv'></span></td>
			</tr>
		</table>
		
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>

		<div id='manageOptionsDiv' style='display: none'>
			
		
		</div>
		
		<div id='noMemberSelectedDiv' class='shadedBox' style='width: 25%; margin: 25px auto'>
			<p class='main' align='center'>
				<i>No Member Selected</i>
			</p>
		</div>
		
	</div>

	<script type='text/javascript'>
		$(document).ready(function() {
			$('#selectEventMemberID').change(function() {
			
				if($('#selectEventMemberID').val() != \"\") {
					$('#loadingSpiral').show();
					$('#manageOptionsDiv').fadeOut(250);
					$.post('".$MAIN_ROOT."members/events/include/manageinvites_form.php', { eMemID: $('#selectEventMemberID').val() }, function(data) {
						$('#manageOptionsDiv').html(data);
						$('#loadingSpiral').hide();
						$('#manageOptionsDiv').fadeIn(250);
					});
				
				}
			
			});
		});
		
		function btnSaveClicked() {
			$(document).ready(function() {
			
				$('#loadingSpiral').show();
				$('#selectEventMemberID').attr('disabled', 'disabled');
				$('#uninviteLinkDiv');
				$('#manageOptionsDiv').fadeOut(250);
				$.post('".$MAIN_ROOT."members/events/include/manageinvites_form.php', { submit: 1, eMemID: $('#selectEventMemberID').val(), updatePositionID: $('#selectPositionID').val(), updateConfirm: $('#confirmAttendence').val() }, function(data) {
				
					$('#manageOptionsDiv').html(data);
					$('#loadingSpiral').hide();
					$('#manageOptionsDiv').fadeIn(250);
				
				});
			
			});
		}
	</script>
";


?>