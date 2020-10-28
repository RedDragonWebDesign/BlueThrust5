<?php

/*
 * BlueThrust Clan Scripts
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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

require_once($prevFolder."classes/profilecategory.php");
require_once($prevFolder."classes/profileoption.php");


$profileCatObj = new ProfileCategory($mysqli);
$profileOptionObj = new ProfileOption($mysqli);

if($_GET['oID'] == "") {
	
	echo "
	<div id='loadingSpiral' class='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='contentDiv'>";
	require_once("manageprofileoptions/main.php");
	
	
	echo "
	</div>
	<div id='deleteMessage' style='display: none'></div>
	<script type='text/javascript'>
		function moveOption(strDir, intOptionID) {
			$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
				$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/move.php', { oDir: strDir, oID: intOptionID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
		
			});
		}
		
		
		function deleteOption(intProfileID) {
			$(document).ready(function() {				
			
				$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/delete.php', { oID: intProfileID }, function(data) {
					$('#deleteMessage').html(data);
					
					$('#deleteMessage').dialog({
				
						title: 'Manage Profile Options - Delete',
						width: 400,
						modal: true,
						zIndex: 9999,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {
								
								$('#loadingSpiral').show();
								$('#contentDiv').hide();
								$(this).dialog('close');
								$.post('".$MAIN_ROOT."members/include/admin/manageprofileoptions/delete.php', { oID: intProfileID, confirm: 1 }, function(data1) {
									$('#contentDiv').html(data1);
									$('#loadingSpiral').hide();
									$('#contentDiv').fadeIn(400);	
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
	
	
}
elseif($_GET['oID'] != "" && $_GET['action'] == "edit") {
	require_once("manageprofileoptions/edit.php");	
}