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

include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/rank.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$cID = $consoleObj->findConsoleIDByName("Add Profile Option");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = $checkAccess1 || $checkAccess2;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($checkAccess) {

		
		if(is_array($_SESSION['btProfileCache']) && count($_SESSION['btProfileCache']) > 0) {
		
			if($_SESSION['btProfileCacheRefresh']) {
				echo "
					<p class='failedFont' align='center'><b>Select Values Modified!  Your member's currently saved information will be reset.</b></p>
				";
			}
			
			
			echo "
				<table id='selectValueTable' align='center' border='0' cellspacing='2' cellpadding='2' width=\"75%\">
					<tr>
						<td class='formTitle'>Select Value:</td>
						<td class='formTitle'>Actions:</td>
					</tr>
					
			";
			$counter = 0;
			$totalOptions = count($_SESSION['btProfileCache']);
			foreach($_SESSION['btProfileCache'] as $key => $selectValue) {
				
				$counter++;
				
				
				$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveOption('up', '".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' title='Move Up' width='24' height='24'></a>";
				if($counter == 1) {
					$dispUpArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
				}
				
				$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveOption('down', '".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' title='Move Down' width='24' height='24'></a>";
				if($totalOptions == $counter) {
					$dispDownArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
				}
				
				
				echo "
					<tr>
						<td class='main' style='padding-left: 3px' width=\"65%\">".$selectValue."</td>
						<td class='main' width=\"35%\">
							".$dispUpArrow.$dispDownArrow."
							<a href='javascript:void(0)' onclick=\"editSelectValue('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' title='Edit' width='24' height='24'></a>
							<a href='javascript:void(0)' onclick=\"deleteOption('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete' width='24' height='24'></a>
						</td>
					</tr>
				";
				
			}
			
			echo "</table>";
			
			if($counter == 0) {
				
				echo "
					<script type='text/javascript'>
						$(document).ready(function() {
							$('#selectValueTable').css('display', 'none');
						});
					</script>
				";
				
			}
			
			
			echo "
				<script type='text/javascript'>
					function moveOption(strDir, intKey) {
						$(document).ready(function() {
						
							$('#loadingSpiral').show();
							$('#selectValueList').hide();
							$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/cache/move.php', { moveDir: strDir, moveKey: intKey }, function(data) {
								$('#selectValueList').hide()
								$('#selectValueList').html(data);
								$('#loadingSpiral').hide();
								$('#selectValueList').fadeIn(400);		
							});
						
						});
					}
					
					
					function deleteOption(intKey) {
					
						$(document).ready(function() {
						
							$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/cache/delete.php', { deleteKey: intKey }, function(data) {
							
								$('#selectValueList').hide();
								$('#selectValueList').html(data);
								$('#selectValueList').fadeIn(400);
							
							});
							
						});
					
					}
					
					
					
					function editSelectValue(intKey) {
		
						$(document).ready(function() {
						
							$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/cache/edit.php', { editKey: intKey }, function(data) {
								$('#editValuePopup').html(data);
								
								$('#editValuePopup').dialog({
								
									title: 'Profile Options - Edit',
									modal: true,
									zIndex: 999999,
									show: 'scale',
									resizable: false,
									width: 400,
									buttons: {
									
										'Ok': function() {
										
											$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/cache/edit.php', { editKey: intKey, submit: 1, editValue: $('#editvalue').val() }, function(data1) {
											
												
												$('#selectValueList').hide();
												$('#selectValueList').html(data1);
												$('#selectValueList').fadeIn(400);
												
												
											
											});
											$(this).dialog('close');
											
										},
										'Cancel': function() {
											$(this).dialog('close');
										}
									
									
									
									}
								
								
								
								});
							
							});
						
						});
					
					
					}
					
					
					
				</script>
			";
		
		}
		
	}
	
	
}


?>