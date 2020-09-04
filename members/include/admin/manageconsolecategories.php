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

$cID = $_GET['cID'];
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

include_once($prevFolder."classes/consolecategory.php");


$consoleCatObj = new ConsoleCategory($mysqli);




if($_GET['catID'] == "") {
	echo "
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
	
		<div id='contentDiv'>";
	include("manageconsolecat/main.php");
	echo "
		</div>
		<div id='deleteMessage' style='display: none'></div>
	
	";
	
	echo "
	
	<script type='text/javascript'>
		function moveConsoleCat(strDir, intCatID) {
			$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
				$.post('".$MAIN_ROOT."members/include/admin/manageconsolecat/move.php', {
					cDir: strDir, catID: intCatID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
		
			});
		}
		
		
		function deleteConsoleCat(intCatID) {
			$(document).ready(function() {				
			
				$.post('".$MAIN_ROOT."members/include/admin/manageconsolecat/delete.php', { catID: intCatID }, function(data) {
			
					
					$('#deleteMessage').dialog({
				
						title: 'Manage Console Categories - Delete',
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
								$.post('".$MAIN_ROOT."members/include/admin/manageconsolecat/delete.php', { catID: intCatID, confirm: 1 }, function(data1) {
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
elseif($_GET['catID'] != "" AND $_GET['action'] == "edit") {
	
	include("manageconsolecat/edit.php");
	
}


?>