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

$prevFolder = "../../../../";
include_once($prevFolder."_setup.php");

// Classes needed for console.php
include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/consoleoption.php");

$cOptObj = new ConsoleOption($mysqli);
$cID = $cOptObj->findConsoleIDByName("Manage Ranks");
$cOptObj->select($cID);

$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {

		//$cID = $cOptObj->findConsoleIDByName("Add New Rank");
		
		$memberInfo = $member->get_info();
		
		if($member->hasAccess($cOptObj)) {
			
			
			
			$rank = new Rank($mysqli);
			if($rank->select($_POST['rID'])) {
				$rankInfo = $rank->get_info_filtered();
				
				if(!isset($_POST['confirm']) || $_POST['confirm'] == "") {
					echo "
						Are you sure you want to delete the rank <b>".$rankInfo['name']."?</b>
					";
				}
				else {
					
					
					if($rank->countMembers() > 0) {
				
						echo "
						<script type='text/javascript'>
							
							
							$(document).ready(function() {
								$('#deleteMessage').html('There are currently members with the rank of <b>".$rankInfo['name']."</b>.  Please change all members with this rank before deleting it.');
								$('#deleteDiv').dialog({
									title: 'Manage Ranks - Delete Rank',
									modal: true,
									resizable: false,
									width: 400,
									show: 'scale',
									zIndex: 99999,
									buttons: {
										'OK': function() { $(this).dialog('close'); }
									}
								});
								
							});
								
						</script>
						";
					
					}
					else {
						
						if($rank->delete()) {
							echo "";
							
							echo "
							
							<script type='text/javascript'>
							
							function refreshRanks() {
								
								
							}
							
							$(document).ready(function() {
								$('#deleteMessage').html('<b>".$rankInfo['name']."</b> successfully deleted!');
								$('#loadingSpiral').show();
								$('#contentDiv').fadeOut(400);
								$('#deleteDiv').dialog({
									title: 'Manage Ranks - Delete Rank',
									modal: true,
									resizable: false,
									width: 400,
									show: 'scale',
									zIndex: 99999,
									buttons: {
										'OK': function() {
											
											$.post('".$MAIN_ROOT."members/include/admin/manageranks/main.php', { }, function(data) {
												$('#contentDiv').html(data).fadeIn(400);
												$('#loadingSpiral').hide();
											});	
										
										
											$(this).dialog('close'); 
						
										}
									}
								});
								
							});
							
							</script>
							";
							
						}
						else {
							echo "";
							
							echo "
							<script type='text/javascript'>
							
							
							$(document).ready(function() {
								$('#deleteMessage').html('Unable to delete rank from the database.  Please contact the website administrator.');
								$('#deleteDiv').dialog({
									title: 'Manage Ranks - Delete Rank',
									modal: true,
									resizeable: false,
									width: 400,
									show: 'scale',
									zIndex: 99999,
									buttons: {
										'OK': function() { $(this).dialog('close'); }
									}
								});
								
							});
								
							</script>
							";
						
						}
						
					}
				}
				
			}
			
		}
	}
}
				
				
				
				
				
?>