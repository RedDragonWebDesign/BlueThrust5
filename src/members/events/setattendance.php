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

if (!isset($member) || !isset($eventObj) || substr($_SERVER['PHP_SELF'], -strlen("manage.php")) != "manage.php") {
	exit();
} else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$eventObj->select($eID);

	if (!$member->hasAccess($consoleObj) || (!$eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm") && $memberInfo['rank_id'] != 1)) {
		exit();
	}
}

$breadcrumbObj->setTitle("Set Attendance");
$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb($consoleTitle, MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']);
$breadcrumbObj->addCrumb($eventInfo['title'].":</b> Set Attendance");

$breadcrumbObj->updateBreadcrumb();

$rankTable = $dbprefix."ranks";
$memberTable = $dbprefix."members";
$eventsMembersTable = $dbprefix."events_members";
$eventsTable = $dbprefix."events";

$arrInvites = $eventObj->getInvitedMembers(true);
$invitesSQL = "('".implode("','", $arrInvites)."')";

$query = "SELECT ".$rankTable.".ordernum, ".$rankTable.".name, ".$memberTable.".member_id, ".$memberTable.".username FROM ".$memberTable.", ".$rankTable." WHERE ".$memberTable.".rank_id = ".$rankTable.".rank_id AND ".$memberTable.".member_id IN ".$invitesSQL." ORDER BY ".$rankTable.".ordernum DESC";
$result = $mysqli->query($query);


$statusSelectBox = new SelectBox();
$statusSelectBox->setAttributes(array("class" => "textBox"));
$statusSelectBox->setComponentName("status");
$statusSelectBox->setOptions(array("Unconfirmed", "Attended", "Excused Absence", "Unexcused Absence"));
?>


<form action='<?php echo MAIN_ROOT; ?>members/events/manage.php?eID=<?php echo $eventInfo['event_id']; ?>&pID=SetAttendance' method='post'>
	<p align='center'>
		<span class='formLabel'>With Selected:</span> <?php $statusSelectBox->display(); ?> <input type='submit' name='submit' value='Go' class='submitButton'>
	</p>
	
	<table class='formTable' style='width: 45%; border-spacing: 0px; margin-top: 0px'>
		<tr>
			<td class='formTitle' style='border-right: 0px' align='center'><span id='checkAllX' style='cursor: pointer'>X</span></td>
			<td class='formTitle' style='border-left: 0px'>Member:</td>
		</tr>
	<?php

	if ( ! empty($_POST['submit']) ) {
		$arrColumns = array("attendconfirm_admin");
		$arrValues = array($_POST['status']);
		foreach ($_POST as $value) {
			if (is_numeric($value) && $eventObj->objEventMember->select($value)) {
				$checkEventID = $eventObj->objEventMember->get_info("event_id");
				if ($checkEventID == $eventInfo['event_id']) {
					$eventObj->objEventMember->update($arrColumns, $arrValues);
				}
			}
		}

		$formObj = new Form();
		$formObj->saveLink = MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=SetAttendance";
		$formObj->saveMessageTitle = "Set Attendance";
		$formObj->saveMessage = "Successfully set attendance!";
		$formObj->showSuccessDialog();
	}

	$counter = 0;
	$eventMemberObj = new Member($mysqli);
	while ($row = $result->fetch_assoc()) {
		if ($counter == 1) {
			$addCSS = " alternateBGColor";
			$counter = 0;
		} else {
			$addCSS = "";
			$counter = 1;
		}

		$eventMemberObj->select($row['member_id']);
		$eventMemberID = $eventObj->getEventMemberID($row['member_id']);

		$formComponentName = "eventmember_".$eventMemberID;

		echo "
			<tr>
				<td class='main manageList dottedLine".$addCSS."' align='center'><input type='checkbox' name='".$formComponentName."' value='".$eventMemberID."'></td>
				<td class='main manageList dottedLine".$addCSS."'><b>".$eventMemberObj->getMemberLink()."</b></td>	
			</tr>
			";
	}

	?>
	
	</table>
</form>
<script type='text/javascript'>

	$(document).ready(function() {

		var intCheckAll = 0;
		
		$('#checkAllX').click(function() {


			if(intCheckAll == 0) {
				$('input:checkbox').attr('checked', true);
				intCheckAll = 1;
			}
			else {
				$('input:checkbox').attr('checked', false);
				intCheckAll = 0;
			}

		});
	});
	
</script>
