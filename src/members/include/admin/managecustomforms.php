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
$cID = $_GET['cID'];
$intAddCustomPageID = $consoleObj->findConsoleIDByName("Add Custom Form Page");
if($_GET['cfID'] == "") {
	
	
	echo "
	
		<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
			<tr>
				<td class='main' colspan='2' align='right'>
				&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCustomPageID."'>Add Custom Form Page</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' width=\"80%\">Custom Page Name:</td>
				<td class='formTitle' width=\"20%\">Actions:</td>
			</tr>
		</table>
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		<div id='contentDiv'>
	
	";
	include("custompages/main_customforms.php");
	
	echo "
	</div>
	<div id='deleteMessage' style='display: none'></div>
	<script type='text/javascript'>
	function deleteCustomPage(intCPID) {
		$(document).ready(function() {
	
			$.post('".$MAIN_ROOT."members/include/admin/custompages/delete_customforms.php', {
				cpID: intCPID }, function(data) {
	
	
					$('#deleteMessage').dialog({
	
						title: 'Manage Custom Forms - Delete',
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
							$.post('".$MAIN_ROOT."members/include/admin/custompages/delete_customforms.php', {
								cpID: intCPID, confirm: 1 }, function(data1) {
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
elseif($_GET['cfID'] != "" && $_GET['action'] == "edit") {
	
	include("custompages/edit_customforms.php");	
}



?>