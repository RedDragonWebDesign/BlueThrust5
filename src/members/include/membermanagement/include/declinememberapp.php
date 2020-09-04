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


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();
$newMemberObj = new Member($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Member Applications");
$consoleObj->select($cID);

$memberAppObj = new MemberApp($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $memberAppObj->select($_POST['mAppID'])) {
	
	$arrMemAppInfo = $memberAppObj->get_info_filtered();
	
	if($_POST['confirmDecline'] && $arrMemAppInfo['memberadded'] == 0) {
		
		if($memberAppObj->delete()) {
			
			$memberAppObj->notifyNewMember(false);			
			
			$member->logAction("Declined ".$arrMemAppInfo['username']."'s member application.");
			
			echo "
			
				<div id='resultDeclineMessage' style='display: none'>
					<p class='main' align='center'>".$arrMemAppInfo['username']."'s member application has been declined!</p>
				</div>
			
			";
			
		}
		else {
			echo "
			
			<div id='resultDeclineMessage' style='display: none'>
				<p class='main' align='center'>Unable to decline ".$arrMemAppInfo['username']."'s member application!  Please contact the website administrator.</p>
			</div>
			
			";
		}
		
		
		echo "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#resultDeclineMessage').dialog({
						title: 'Decline Member Application',
						modal: true,
						width: 400,
						zIndex: 99999,
						show: 'scale',
						buttons: {
							'OK': function() {
								$('#loadingSpiral').show();
								$('#memberApplications').fadeOut(250);
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/memberapplist.php', { }, function(data) {
									$('#memberApplications').html(data);
									$('#loadingSpiral').hide();
									$('#memberApplications').fadeIn(250);
								});
								
								$(this).dialog('close');
							}
						}
						
					});
					
					$('#confirmDeclineMessage').dialog('close');
					
				});
			</script>
		";
		
	}
	else {

		echo "
			<div id='confirmDeclineMessage' style='display: none'>
				<p class='main' align='center'>
					Are you sure you want to decline ".$arrMemAppInfo['username']."'s application?
				
					<div id='declineLoadingSpiral' style='display: none'>
						<p align='center'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
				
				</p>
				
				
			</div>
			
			<script type='text/javascript'>
				$(document).ready(function() {
				
					$('#confirmDeclineMessage').dialog({
					
						title: 'Decline Member Application - Confirm',
						modal: true,
						width: 400,
						zIndex: 99999,
						show: 'scale',
						buttons: {
						
							'Yes': function() {
								
								$('#declineLoadingSpiral').show();
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/declinememberapp.php', { mAppID: '".$_POST['mAppID']."', confirmDecline: 1 }, function(data) {
									$('#declineLoadingSpiral').hide();
									$('#confirmDeclineMessage').html(data);
								});
							
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						
						}
					
					});
				
					$('.ui-dialog :button').blur();
					
				});
			</script>
		";
		
	}
	
	
}

?>