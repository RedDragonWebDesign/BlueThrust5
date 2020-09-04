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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

include($prevFolder."classes/profilecategory.php");

$profileCatObj = new ProfileCategory($mysqli);


if($_GET['action'] == "") {
	
	echo "
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
	
	<div id='contentDiv'>
	";	
	
	include("manageprofilecat/main.php");
	echo "
	</div>
	<div id='deleteMessage' style='display: none'></div>
	
	
	<script type='text/javascript'>
		function moveProfileCat(strDir, intCatID) {
			$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
				$.post('".$MAIN_ROOT."members/include/admin/manageprofilecat/move.php', {
					cDir: strDir, catID: intCatID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
		
			});
		}
		
		
		function deleteProfileCat(intCatID) {
			$(document).ready(function() {				
			
				$.post('".$MAIN_ROOT."members/include/admin/manageprofilecat/delete.php', { catID: intCatID }, function(data) {
			
					
					$('#deleteMessage').dialog({
				
						title: 'Manage Profile Categories - Delete',
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
								$.post('".$MAIN_ROOT."members/include/admin/manageprofilecat/delete.php', { catID: intCatID, confirm: 1 }, function(data1) {
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
					
					$('#deleteMessage').html(data);
				
				});

			});			
		}
			
	</script>
	
	
	
	
	
	";
	
	
	
	
}
elseif($_GET['catID'] != "" && $_GET['action'] == "edit") {
	include("manageprofilecat/edit.php");	
}

?>

