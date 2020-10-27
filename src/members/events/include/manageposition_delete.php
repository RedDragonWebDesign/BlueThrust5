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


$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventPosition->select($_POST['posID'])) {
	
	$eventID = $eventObj->objEventPosition->get_info("event_id");
	
	$memberInfo = $member->get_info_filtered();
	$eventPositionInfo = $eventObj->objEventPosition->get_info_filtered();
	if(($memberInfo['rank_id'] == 1 || ($member->hasAccess($consoleObj)) && $eventObj->select($eventID) && ($eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions") || $memberInfo['rank_id'] == 1))) {
	
		
		if($_POST['confirmDelete'] == 1) {
			
			$eventObj->objEventPosition->delete();
			$mysqli->query("UPDATE ".$dbprefix."events_members SET position_id = '0' WHERE position_id = '".$eventPositionInfo['position_id']."'");
			
			$_GET['eID'] = $eventID;
			include("manageposition_main.php");
			
		}
		else {
			
			
			echo "
			
				<div id='confirmDeleteMessage' style='display: none'>
					<p class='main' align='center'>
						Are you sure you want to delete the position: <b>".$eventPositionInfo['name']."</b>?<br><br>
						All members with this position will lose this position once it is deleted.
					</p>
				</div>
			
				
				<script type='text/javascript'>

					$(document).ready(function() {
					
						$('#confirmDeleteMessage').dialog({
							
							title: 'Manage Event Positions - Confirm Delete',
							modal: true,
							zIndex: 99999,
							show: 'scale',
							resizable: false,
							width: 450,
							buttons: {
							
								'Yes': function() {
									
									$('#positionListDiv').fadeOut(250);
									$('#loadingSpiral').show();
									$.post('".$MAIN_ROOT."members/events/include/manageposition_delete.php', { posID: '".$eventPositionInfo['position_id']."', confirmDelete: 1 }, function(data) {
									
										$('#positionListDiv').html(data);
										$('#positionListDiv').fadeIn(250);
										$('#loadingSpiral').hide();
									
									});
									
									$(this).dialog('close');
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