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

	if(!defined("LOGGED_IN") || !LOGGED_IN) { die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."'</script>"); }
	
	
	$actionsWidth = count($setupManageListArgs['actions'])*6;
	$titleWidth = 100-($actionsWidth);
	
	
	// Setup default values if not given
	$actionsTitleName = ($setupManageListArgs['action_title'] == "") ? "Actions:" : $setupManageListArgs['action_title'];
	$itemTitleName = ($setupManageListArgs['item_title'] == "") ? "Item:" : $setupManageListArgs['item_title'];
	
	$dispAddNewLink = (!isset($setupManageListArgs['add_new_link']['url']) || $setupManageListArgs['add_new_link']['url'] == "") ? "" : "&raquo; <a href='".$setupManageListArgs['add_new_link']['url']."'>".$setupManageListArgs['add_new_link']['name']."</a> &laquo;";
	
	$setupManageListArgs['list_div_name'] = ($setupManageListArgs['list_div_name'] == "") ? "manageListDiv" : $setupManageListArgs['list_div_name'];
	
	$setupManageListArgs['loading_spiral'] = ($setupManageListArgs['loading_spiral'] == "") ? "manageListLoadingSpiral" : $setupManageListArgs['loading_spiral'];
	
	
	
	
	// Display Manage List
	
	echo "

		<table class='formTable'>
			<tr>
				<td colspan='2' align='right'>".$dispAddNewLink."<br><br></td>
			</tr>
			<tr>
				<td class='formTitle' style='width: ".$titleWidth."%'>".$itemTitleName."</td>
				<td class='formTitle' style='width: ".$actionsWidth."%'>".$actionsTitleName."</td>
			</tr>
		</table>
		
		<div class='loadingSpiral' id='".$setupManageListArgs['loading_spiral']."'><p align='center'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading...</p></div>
		
		<div id='".$setupManageListArgs['list_div_name']."'>
	";
		
		include("console.managelist.list.php");
		
	echo "</div>
	
	
		<div id='confirmDeleteDialog'></div>
		<script type='text/javascript'>
		
			function moveItem(move_dir, item_id) {
		
				$(document).ready(function() {
				
					$('#".$setupManageListArgs['loading_spiral']."').show();
					$('#".$setupManageListArgs['list_div_name']."').fadeOut(250);
					//".$setupManageListArgs['move_link']."
					$.post('".$MAIN_ROOT."members/console.managelist.move.php?cID=".filterText($_GET['cID'])."', { itemID: item_id, moveDir: move_dir }, function(data) {
					
						$('#".$setupManageListArgs['loading_spiral']."').hide();
						$('#".$setupManageListArgs['list_div_name']."').html(data).fadeIn(250);
						
					});
					
				
				});
			
			}
			
			function deleteItem(item_id) {
			
				$(document).ready(function() {
				
			";
	
			if(!$setupManageListArgs['confirm_delete']) {

				echo "
					$('#".$setupManageListArgs['loading_spiral']."').show();
					$('#".$setupManageListArgs['list_div_name']."').fadeOut(250);
				";
				
			}
			
	
			echo "
					
					$.post('".$setupManageListArgs['delete_link']."', { itemID: item_id }, function(data) {
					
					";

					if($setupManageListArgs['confirm_delete']) {
						
						echo "
							$('#confirmDeleteDialog').html(data);
							$('#confirmDeleteDialog').dialog({
								
								title: '".$consoleInfo['pagetitle']." - Delete',
								width: 400,
								modal: true,
								zIndex: 9999,
								resizable: false,
								show: 'scale',
								buttons: {
									'Yes': function() {
										
										$('#".$setupManageListArgs['loading_spiral']."').show();
										$('#".$setupManageListArgs['list_div_name']."').fadeOut(250);
										$(this).dialog('close');
										
										$.post('".$setupManageListArgs['delete_link']."', { itemID: item_id, confirm: 1 }, function(data1) {
											$('#".$setupManageListArgs['loading_spiral']."').hide();
											$('#".$setupManageListArgs['list_div_name']."').html(data1).fadeIn(250);
										});
									
									},
									'Cancel': function() {
									
										$(this).dialog('close');
									
									}
								}
								
							});
						";
						
					}
					else {
						echo "
							$('#".$setupManageListArgs['loading_spiral']."').hide();
							$('#".$setupManageListArgs['list_div_name']."').html(data).fadeIn(250);
						";
					}
						
				echo "	
					});
					
				
				});
			
			}
		
		</script>
	
	";
	
	
	
	
?>