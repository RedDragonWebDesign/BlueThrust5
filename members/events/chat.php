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

include_once("../../classes/chatroom.php");

if(!isset($member) || !isset($eventObj) || substr($_SERVER['PHP_SELF'], -strlen("manage.php")) != "manage.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	

	if(!$member->hasAccess($consoleObj) || !$eventObj->select($eID)) {

		exit();
	}
	
	$eventInfo = $eventObj->get_info_filtered();
}


if($eventInfo['member_id'] != $memberInfo['member_id'] && !in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true))) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members';
		</script>
	";
	exit();
}


$eventChatObj = new ChatRoom($mysqli);
$eventChatID = $eventObj->chatRoomStarted();



if($eventChatID === false && $memberInfo['member_id'] != $eventInfo['member_id']) {
	
	echo "
		<div style='display: none' id='successBox'>
			<p align='center'>
				An event chat room has not been created.  The event creator must start one!
			</p>
		</div>
		
		<script type='text/javascript'>
			popupDialog('Event Chatroom', '".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."', 'successBox');
		</script>
	";
	
	
	exit();
}
elseif($eventChatID === false && $memberInfo['member_id'] == $eventInfo['member_id']) {
	
	$eventChatObj->addNew(array("event_id", "datestarted"), array($eventInfo['event_id'], time()));
	
	$eventObj->notifyEventInvites("A chatroom has been started for the event, <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=Chat'>".$eventInfo['title']."</a>!");
	
	
}
elseif($eventChatObj->select($eventChatID)) {
	
	$eventChatInfo = $eventChatObj->get_info_filtered();
	
	
}




echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Chat\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <b>".$eventInfo['title'].":</b> Chat\");
});
</script>



<div class='eventChatContainer'>

	
	<div style='position: relative; margin-left: 5px; margin-bottom: 5px'>
		<div class='main' style='display: table-cell; vertical-align: middle'>Auto-scroll:</div>
		<div style='display: table-cell; vertical-align: middle'><input type='checkbox'></div>
	</div>
	
	<div style='clear: both'></div>
	<div class='eventChatRoom'></div>
	
	<div class='eventChatList'></div>
	<div style='clear: both'></div>
	<div class='chatTextBoxContainer'>
		<div style='float: left; width: 85%'><textarea class='textBox'></textarea></div><div style='float: right; width: 10%'><input type='button' class='submitButton' value='Chat'></div>
	
	</div>
	
</div>



<div id='jsDump' style='display: none'></div>

<script type='text/javascript'>

	function updateChat() {
	
		$(document).ready(function() {
		
			$.post('".$MAIN_ROOT."members/events/include/updatechat.php', { ecID: '".$eventChatInfo['eventchat_id']."' }, function(data) {
			
				$('#jsDump').html(data);
			
			});
		
		});
	
	}

</script>
";






?>