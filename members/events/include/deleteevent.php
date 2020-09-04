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
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");
include_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$objMember = new Member($mysqli);

$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);


if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->select($_POST['eID'])) {
	
	$memberInfo = $member->get_info();
	$eventInfo = $eventObj->get_info_filtered();
	
	if($eventInfo['member_id'] == $memberInfo['member_id']) {
		
		if($_POST['confirmDelete'] == 1) {
			
			$eventObj->delete();
			
		}
		else {
			
			echo "
			
				<div id='confirmDeleteMessage' style='display: none'>
				
					<p class='main' align='center'>Are you sure you want to delete the event, <b>".$eventInfo['title']."</b>?</p>
				
				</div>			
			
				<script type='text/javascript'>
				
					$(document).ready(function() {
					
						$('#confirmDeleteMessage').dialog({
						
							title: 'Delete Event',
							modal: true,
							zIndex: 99999,
							show: 'scale',
							width: 400,
							resizable: false,
							buttons: {
							
								'Yes': function() {
								
									$.post('".$MAIN_ROOT."members/events/include/deleteevent.php', { confirmDelete: 1, eID: '".$eventInfo['event_id']."' }, function(data) {
									
										window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."'
									
									});
								
								},
								'Cancel': function() {
								
									$(this).dialog('close');
								
								}
							
							}
						
						
						});
					
					});
				
				</script>
				
				
			";
			
			
		}
		
		
	}
	
}



?>