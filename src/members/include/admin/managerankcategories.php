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

include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/rankcategory.php");
$cID = $_GET['cID'];
$rankCatObj = new RankCategory($mysqli);



if(!isset($_GET['rID']) || $_GET['rID'] == "") {
	
	echo "
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='contentDiv'>
	";
	
	include("managerankcat/main.php");
	
	
	echo "
		</div>
		<div id='deleteMessage' style='display: none'></div>
	
		<script type='text/javascript'>
			function moveRankCat(strDir, intCatID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#contentDiv').hide();
					$.post('".$MAIN_ROOT."members/include/admin/managerankcat/move.php', { cDir: strDir, rID: intCatID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
				
				});
			}
			
			
			function deleteRankCat(intCatID) {
				$(document).ready(function() {				
				
					$.post('".$MAIN_ROOT."members/include/admin/managerankcat/delete.php', { rID: intCatID }, function(data) {
				
						
						$('#deleteMessage').dialog({
					
							title: 'Manage Rank Categories - Delete',
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
									$.post('".$MAIN_ROOT."members/include/admin/managerankcat/delete.php', { rID: intCatID, confirm: 1 }, function(data1) {
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
elseif($_GET['rID'] != "" AND $_GET['action'] == "edit") {
	include("managerankcat/edit.php");
}



?>

