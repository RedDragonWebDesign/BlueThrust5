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
$manageStatusCID = $consoleObj->findConsoleIDByName("Manage Diplomacy Statuses");
$consoleObj->select($manageStatusCID);

$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");
$diplomacyStatusObj->set_assocTableName("diplomacy");
$diplomacyStatusObj->set_assocTableKey("diplomacy_id");


if($member->authorizeLogin($_SESSION['btPassword']) && $diplomacyStatusObj->select($_POST['sID']) && $member->hasAccess($consoleObj)) {
	
	$statusName = $diplomacyStatusObj->get_info_filtered("name");
	
	$arrAssociates = $diplomacyStatusObj->getAssociateIDs();

	if(count($arrAssociates) > 0) {
		
		echo "
			
			<div id='deleteDialogBox' style='display: none'>
				<p align='center' class='main'>
					There are currently clans on the diplomacy page with the ".$statusName." status.  You must change their status before deleting.
				</p>
			</div>
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#deleteDialogBox').dialog({
						title: 'Delete Diplomacy Status',
						width: 400,
						show: 'scale',
						modal: true,
						zIndex: 99999,
						resizable: false,
						buttons: {
							'OK': function() {
								$(this).dialog('close');							
							},

						}
						
					});
				
				});
			
			</script>
			
		";
		
	}
	elseif(count($arrAssociates) == 0 && !isset($_POST['confirmDelete'])) {
		echo "
			
			<div id='deleteDialogBox' style='display: none'>
				<p align='center' class='main'>
					Are you sure you want to delete the <b>".$statusName."</b> diplomacy status?
				</p>
			</div>
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#deleteDialogBox').dialog({
						title: 'Delete Diplomacy Status',
						width: 400,
						show: 'scale',
						modal: true,
						zIndex: 99999,
						resizable: false,
						buttons: {
							'Yes': function() {
								
								$('#loadingSpiral').show();
								$('#statusListDiv').hide();
							
								$.post('".$MAIN_ROOT."members/include/diplomacy/include/deletestatus.php', { sID: '".$_POST['sID']."', confirmDelete: 1 }, function(data1) {
								
									$('#statusListDiv').html(data1);
									$('#loadingSpiral').hide();
									$('#statusListDiv').fadeIn(250);
									
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
	elseif(count($arrAssociates) == 0 && isset($_POST['confirmDelete'])) {
		
		$diplomacyStatusObj->set_assocTableName("");
		$diplomacyStatusObj->delete();
		
		$member->logAction("Deleted the ".$statusName." diplomacy status.");
		
		include("main_managestatuses.php");
		
	}
	
	
	
}

?>