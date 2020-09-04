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
include_once("../../../../classes/basicorder.php");



$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Member Application");
$consoleObj->select($cID);

$appComponentObj = new BasicOrder($mysqli, "app_components", "appcomponent_id");
$appComponentObj->set_assocTableName("app_selectvalues");
$appComponentObj->set_assocTableKey("appselectvalue_id");

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {


	if($appComponentObj->select($_POST['acID'])) {
	
		$arrCompInfo = $appComponentObj->get_info_filtered();

		
		if(!$_POST['confirmDelete']) {
			
			echo "
				<p align='center' class='main'>
					Are you sure you want to delete <b>".$arrCompInfo['name']."</b> from the member application?
				</p>
			";
			
		}
		elseif($_POST['confirmDelete']) {
			
			
			if($appComponentObj->delete()) {
				
				$appComponentObj->resortOrder();
				$member->logAction("Deleted a member application component.");
				echo "
					
					<div id='confirmDeleteMessage' style='display: none'>
						<p align='center' class='main'>
							<b>".$arrCompInfo['name']."</b> was successfully deleted from the member application!
						</p>
					</div>
					
					<script type='text/javascript'>
						
						function reloadAppCompList() {
							$(document).ready(function() {
							
								$('#loadingSpiral').show();
								$('#appComponentList').fadeOut(250);
								
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/appcomponentlist.php', { }, function(data) {
									$('#appComponentList').html(data);
									$('#loadingSpiral').hide();
									$('#appComponentList').fadeIn(250);
								});
								
							});
						}
					
					</script>
					";
			}
			else {
				
				echo "
				
					<div id='confirmDeleteMessage' style='display: none'>
						<p align='center' class='main'>
							Unable to delete <b>".$arrCompInfo['name']."</b> from the member application!  You may need to delete it manually.
						</p>
					</div>
				
					<script type='text/javascript'>
						function reloadAppCompList() {
							
						}
					</script>
					
				";
				
			}
			
			echo "
				<script type='text/javascript'>
					
					$(document).ready(function() {
					
						$('#confirmDeleteMessage').dialog({
						
							title: 'Delete Application Component',
							modal: true,
							zIndex: 99999,
							show: 'scale',
							width: 400,
							resizable: false,
							buttons: {
								'OK': function() {
									reloadAppCompList();
									$(this).dialog('close');
								}
							}
						
						});
					
						$('#appComponentForm').dialog('close');
						
					});
				
				</script>
				";

		}

	}
	
}


?>