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
$_SESSION['btCountMindChanges'] = array();
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

$eventObj = new Event($mysqli);

if(isset($_GET['note'])) {
	echo "
		<div class='formDiv'>
			<b><u>NOTE:</u></b> If you are here after clicking the confirm your attendence link, you must wait for the event to start before confirming your attendence at the event.  You can however, RSVP to an event from this page.
		</div>
	";
}

echo "

	<table class='formTable'>
		<tr>
			<td class='formTitle' style='width: 40%'>Event Title:</td>
			<td class='formTitle' style='width: 20%'>Invited By:</td>
			<td class='formTitle' style='width: 20%'>Start Date:</td>
			<td class='formTitle' style='width: 20%'>Actions:</td>
		</tr>
	</table>
	<div id='loadingSpiral' class='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='eventListDiv'>
";


include("include/invitelist.php");

echo "</div>";
?>

<script type='text/javascript'>

	function rsvpEvent(intMemberID, intRSVP) {
		$(document).ready(function() {
			$('#loadingSpiral').show();
			$('#eventListDiv').fadeOut(250);
			$.post('<?php echo $MAIN_ROOT; ?>members/include/events/include/rsvpevent.php', { emID: intMemberID, rsvpNum: intRSVP }, function(data) {
				$('#eventListDiv').html(data);
				$('#loadingSpiral').hide();
				$('#eventListDiv').fadeIn(250);
			});
		});
	}

	function hideEvent(intMemberID) {
		$(document).ready(function() {
			$('#loadingSpiral').show();
			$('#eventListDiv').fadeOut(250);
			$.post('<?php echo $MAIN_ROOT; ?>members/include/events/include/hideevent.php', { emID: intMemberID }, function(data) {
				$('#eventListDiv').html(data);
				$('#loadingSpiral').hide();
				$('#eventListDiv').fadeIn(250);
			});
		});
	}

	function confirmAttendence(intMemberID) {
		$(document).ready(function() {
			$('#loadingSpiral').show();
			$('#eventListDiv').fadeOut(250);
			$.post('<?php echo $MAIN_ROOT; ?>members/include/events/include/confirmattendence.php', { emID: intMemberID }, function(data) {
				$('#eventListDiv').html(data);
				$('#loadingSpiral').hide();
				$('#eventListDiv').fadeIn(250);
			});
		});
	}
	
</script>
