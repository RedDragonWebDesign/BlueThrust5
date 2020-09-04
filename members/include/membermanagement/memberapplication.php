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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];

$regOptionsCID = $consoleObj->findConsoleIDByName("Registration Options");


	
echo "
	<div class='formDiv'>

		Use the form below to create a member application for the clan.
		<br><br>
		<b><u>NOTE:</u></b> In order to use member applications, you must allow open registration on the <a href='".$MAIN_ROOT."members/console.php?cID=".$regOptionsCID."'>Registration Options</a> page.
		
		<table class='formTable'>
			<tr>
				<td colspan='2' class='main'>
					<b>Member Application: <a href='javascript:void(0)' onmouseover=\"showToolTip('Member applications must start with a desired username, password and e-mail address.  You may add more components to the application by clicking the add component button.')\" onmouseout='hideToolTip()'>(?)</a></b>
					<div class='dottedLine' style='width: 95%; padding-top: 3px'></div>
				</td>
			</tr>
			<tr>
				<td class='main'>
					<p align='center'>
						<input type='button' id='addAppComponentBtn' class='submitButton' value='Add Component'><br><br>
					</p>
					<table class='formTable' style='width: 90%; margin-top: 5px'>
						<tr>
							<td class='formTitle' style='width: 50%'>Component Name:</td>
							<td class='formTitle' style='width: 25%'>Type:</td>
							<td class='formTitle' style='width: 25%'>Actions:</td>
						</tr>
					</table>
				
					
					<div id='loadingSpiral' style='display: none'>
						<p align='center' class='main'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
					
					<div id='appComponentList'>
					
						";


						include("include/appcomponentlist.php");


					echo "
					
					</div>
					<br>
				</td>
			</tr>
		</table>
	</div>
	
	<div id='appComponentForm' style='display: none; text-align: left'></div>
	
	<script type='text/javascript'>
	
	
		$(document).ready(function() {
		
		
			
		
			$('#addAppComponentBtn').click(function() {
			
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/addappcomponent.php', { }, function(data) {
				
					$('#appComponentForm').html(data);
					$('#appComponentForm').dialog({
					
					
						title: 'Add Application Component',
						modal: true,
						zIndex: 99999,
						show: 'scale',
						width: 450,
						resizable: false,
						buttons: {
						
							'Add Component': function() {

								var strComponentName = $('#componentName').val();
								var strComponentType = $('#componentType').val();
								var strComponentTooltip = $('#componentTooltip').val();
								var intComponentRequired = $('#componentRequired').val();
								var intProfileOptionID = $('#profileOptionID').val();
								
								$('#appComponentForm').fadeOut(250);
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/addappcomponent.php', { saveComponent: 1, newComponentName: strComponentName, newComponentType: strComponentType, newComponentRequired: intComponentRequired, newComponentTooltip: strComponentTooltip, profileOptionID: intProfileOptionID }, function(data1) {
									$('#appComponentForm').html(data1);
									$('#appComponentForm').fadeIn(250);						
								});
															
							},
							'Cancel': function() {
								$(this).dialog('close');							
							}
						
						}
					
					
					});
				
				});
			
			});
		
		
		});
	
		
		
		function editAppComponent(intAppCompID) {
		
			$(document).ready(function() {
			
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/editappcomponent.php', { appCompID: intAppCompID }, function(data) {
				
					$('#appComponentForm').html(data);
					$('#appComponentForm').dialog({
					
						title: 'Edit Application Component',
						modal: true,
						zIndex: 99999,
						show: 'scale',
						width: 450,
						resizable: false,
						buttons: {
							
							'Save': function() {
								var strComponentName = $('#componentName').val();
								var strComponentType = $('#componentType').val();
								var strComponentTooltip = $('#componentTooltip').val();
								var intComponentRequired = $('#componentRequired').val();
								var intProfileOptionID = $('#profileOptionID').val();
								
								$('#appComponentForm').fadeOut(250);
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/editappcomponent.php', { appCompID:  intAppCompID, saveComponent: 1, saveComponentName: strComponentName, saveComponentType: strComponentType, saveComponentRequired: intComponentRequired, saveComponentTooltip: strComponentTooltip, profileOptionID: intProfileOptionID }, function(data1) {
									$('#appComponentForm').html(data1);
									$('#appComponentForm').fadeIn(250);						
								});
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						
						}
					
					});
				
				});
			
			});
		
		}
	
	
		function moveAppComponent(strDir, intAppCompID) {
		
			$(document).ready(function() {
						
				$('#loadingSpiral').show();
				$('#appComponentList').fadeOut(250);
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/moveappcomponent.php', { acDir: strDir, acID: intAppCompID }, function(data) {
					$('#appComponentList').html(data);
					$('#loadingSpiral').hide();
					$('#appComponentList').fadeIn(250);				
				});
			
			});
		
		}
		
		
		function deleteAppComponent(intAppCompID) {
		
			$(document).ready(function() {
						
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/deleteappcomponent.php', { acID: intAppCompID }, function(data) {

				
					$('#appComponentForm').html(data);
					$('#appComponentForm').dialog({
					title: 'Delete Application Component',
						modal: true,
						zIndex: 99999,
						show: 'scale',
						width: 450,
						resizable: false,
						buttons: {
							'Yes': function() {
							
								$.post('".$MAIN_ROOT."members/include/membermanagement/include/deleteappcomponent.php', { acID: intAppCompID, confirmDelete: 1 }, function(data1) {
									$('#appComponentForm').html(data1);
								});
							
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
	

	
?>
