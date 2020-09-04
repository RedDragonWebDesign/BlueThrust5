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



include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$diplomacyRequestsCID = $consoleObj->findConsoleIDByName("View Diplomacy Requests");
$consoleObj->select($diplomacyRequestsCID);

$diplomacyRequestObj = new Basic($mysqli, "diplomacy_request", "diplomacyrequest_id");

if($member->authorizeLogin($_SESSION['btPassword']) && $diplomacyRequestObj->select($_POST['reqID']) && $member->hasAccess($consoleObj)) {
	
	$diplomacyRequestInfo = $diplomacyRequestObj->get_info_filtered();
	if(isset($_POST['confirmDecline'])) {
		
		// Send E-mail Confirmation
		$emailTo = $diplomacyRequestInfo['email'];
		$emailFrom = "confirmemail@bluethrust.com";
		$emailSubject = $websiteInfo['clanname']." - Diplomacy Request: Declined";
		$emailMessage = "
Hi ".$diplomacyRequestInfo['name'].",\n\n
		
Your diplomacy request has been declined.\n\n
-".$websiteInfo['clanname'];
		
		//mail($emailTo, $emailSubject, $emailMessage, "From: ".$emailFrom);
		
		$diplomacyRequestObj->delete();
		
		include("diplomacyrequests.php");
		
		
		$member->logAction("Declined ".$diplomacyRequestInfo['clanname']."'s diplomacy request.");
		
	}
	else {
	
		echo "
			<div id='confirmDialogBox' style='display: none'>
				<p class='main' align='center'>
					Are you sure you want to decline <b>".$diplomacyRequestInfo['clanname']."'s</b> diplomacy request?
				</p>
			</div>
			<script type='text/javascript'>
		
				$(document).ready(function() {
				
				
					$('#confirmDialogBox').dialog({
					
						title: 'Decline Diplomacy Request',
						modal: true,
						zIndex: 99999,
						width: 400,
						show: 'scale',
						buttons: {
							'Yes': function() {
						
								$.post('".$MAIN_ROOT."members/include/diplomacy/include/declinerequest.php', { reqID: ".$_POST['reqID'].", confirmDecline: 1 }, function(data) {
									$('#diplomacyRequests').fadeOut(250);
									$('#diplomacyRequests').html(data);
									$('#diplomacyRequests').fadeIn(250);								
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


?>