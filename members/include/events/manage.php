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

include_once("../classes/event.php");
$_SESSION['btEventID'] = "";
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
$intAddEventCID = $consoleObj->findConsoleIDByName("Add Event");

$counter = 0;
$clickCounter = 0;
$eventObj = new Event($mysqli);

$intViewInvitesCID = $consoleObj->findConsoleIDByName("View Event Invitations");


$result = $mysqli->query("SELECT event_id FROM ".$dbprefix."events_members WHERE member_id = '".$memberInfo['member_id']."'");
while($row = $result->fetch_assoc()) {
	$arrEvents[] = $row['event_id'];	
}

$sqlEvents = "('".implode("','", $arrEvents)."')";

if($eventObj->getManageAllStatus()) {
	$query = "SELECT * FROM ".$dbprefix."events ORDER BY title";
}
else {
	$query = "SELECT * FROM ".$dbprefix."events WHERE member_id = '".$memberInfo['member_id']."' OR event_id IN ".$sqlEvents." ORDER BY title";
}

$result = $mysqli->query($query);
while($row = $result->fetch_assoc()) {

	
	$row = filterArray($row);
	
	$arrPositionOptions['invitemembers'] = "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=InviteMembers'>Invite Members</a><br>";
	$arrPositionOptions['manageinvites'] = "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=ManageInvites'>Manage Invites</a><br>";
	$arrPositionOptions['editinfo'] = "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=EditInfo'>Edit Event Information</a><br>";
	$arrPositionOptions['eventpositions'] = "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=AddPosition'>Add Event Position</a><br><b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=ManagePositions'>Manage Event Positions</a><br>";
	$arrPositionOptions['attendenceconfirm'] = "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=ManageInvites'>Manage Invites</a><br>";
	
	
	
	$dispEventTitle = $row['title'];
	if(strlen($dispEventTitle) >= 25) {
		$dispEventTitle = "<span title='".$row['title']."'>".substr($dispEventTitle, 0, 20)."...</span>";
	}

	$categoryCSS = "consoleCategory_clicked";
	$hideoptions = "";
	if($counter > 0) {
		$hideoptions = "style='display: none'";
		$categoryCSS = "consoleCategory";
	}
	
	$counter++;
	
	if($_GET['select'] == $row['event_id']) {
		$clickCounter = $counter;
	}
	
	
	
	$dispEventTitles .= "<div class='".$categoryCSS."' style='width: 200px; margin: 3px' id='categoryName".$counter."' onmouseover=\"moverCategory('".$counter."')\" onmouseout=\"moutCategory('".$counter."')\" onclick=\"selectCategory('".$counter."')\">".$dispEventTitle."</div>";
	$dispEventOptions .= "<div id='categoryOption".$counter."' ".$hideoptions.">";
	$dispEventOptions .= "
	<div class='dottedLine' style='padding-bottom: 3px; margin-bottom: 5px'>
	<b>Manage Event - ".$dispEventTitle."</b>
	</div>
	<div style='padding-left: 5px'>
	";
	if($row['member_id'] == $memberInfo['member_id'] || $eventObj->getManageAllStatus()) {
		// Event Creator
		$dispEventOptions .= "
			<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=EditInfo'>Edit Event Information</a><br>
			<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=InviteMembers'>Invite Members</a><br>
			<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=ManageInvites'>Manage Invites</a><br>
			<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=AddPosition'>Add Event Position</a><br>
			<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=ManagePositions'>Manage Event Positions</a><br>
			<b>&middot;</b> <a href='javascript:void(0)' onclick=\"deleteEvent('".$row['event_id']."')\">Delete This Event</a><br>
		
		";
		
		
		
	}
	else {
		// Invited Member	
		
		$eventObj->select($row['event_id']);
		$eventObj->getEventMemberID($memberInfo['member_id'], true);
		$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();

		if($eventMemberInfo['status'] == 1) {
		
			if($eventMemberInfo['position_id'] == 0 && $row['invitepermission'] == 1) {
				$dispEventOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$row['event_id']."&pID=InviteMembers'>Invite Members</a><br>";
			}
			elseif($eventMemberInfo['position_id'] != 0 && $eventObj->objEventPosition->select($eventMemberInfo['position_id']) && $eventObj->objEventPosition->get_info("event_id") == $row['event_id']) {
				$eventPositionInfo = $eventObj->objEventPosition->get_info();
				foreach($arrPositionOptions as $key => $value) {
	
					if($eventPositionInfo[$key] == 1 && $key != "attendenceconfirm") {
						$dispEventOptions .= $value;
					}
					elseif($eventPositionInfo[$key] == 1 && $key == "attendenceconfirm" && $eventPositionInfo['manageinvites'] == 0) {
						$dispEventOptions .= $value;					
					}
					
					
					
				}
				
				
			}
		}
		
		
		$dispEventOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."members/console.php?cID=".$intViewInvitesCID."&note=1'>Confirm Your Attendence</a><br>";
	}
	
	$dispEventOptions .= "
	<b>&middot;</b> <a href='".$MAIN_ROOT."events/info.php?eID=".$row['event_id']."'>View Event Page</a>
	</div></div>";
	
	
}

if($result->num_rows > 0) {
	echo "
		<div style='float: left; text-align: left; width: 225px; padding: 10px 0px 0px 40px'>
			$dispEventTitles
		</div>
		<div style='float: right; text-align: left; width: 300px; padding: 10px 40px 0px 10px'>
			$dispEventOptions
		</div>
	
		<div style='clear:both; height: 30px; margin-top: 20px'></div>
	";

}

if($clickCounter != 0) {

	echo "
	<script type='text/javascript'>
	selectCategory('".$clickCounter."');
	</script>
	";

}


if($result->num_rows == 0) {
	
	echo "
		<div class='shadedBox' style='width: 400px; margin-bottom: 50px; margin-left: auto; margin-right: auto;'>
			<p align='center' class='main'>
				<i>You aren't going to any events and you haven't created any events!</i><br><br><b><a href='".$MAIN_ROOT."members/console.php?cID=".$intAddEventCID."'>Click Here</a></b> to make an event!
			</p>
		</div>
	";


}

?>
<div id='deleteEventDiv' style='display: none'></div>

<script type='text/javascript'>
		
	function deleteEvent(intEventID) {
	
		$(document).ready(function() {
		
			$.post('<?php echo $MAIN_ROOT; ?>members/events/include/deleteevent.php', { eID: intEventID }, function(data) {

				$('#deleteEventDiv').html(data);

			});
		
		});
	
	}

</script>

