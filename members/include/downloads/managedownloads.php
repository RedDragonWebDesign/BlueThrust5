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

include_once($prevFolder."classes/downloadcategory.php");
include_once($prevFolder."classes/download.php");

$downloadObj = new Download($mysqli);
$downloadCatObj = new DownloadCategory($mysqli);


if(isset($_GET['dlID']) && $downloadObj->select($_GET['dlID'])) {
	
	$downloadInfo = $downloadObj->get_info_filtered();
	$downloadCatObj->select($downloadInfo['downloadcategory_id']);
	$downloadCatInfo = $downloadCatObj->get_info_filtered();
	include("include/edit.php");
	
}
else {
	
	$addDLCID = $consoleObj->findConsoleIDByName("Add Download");
	
	echo "
			
			<table class='formTable'>
				<tr>
					<td class='main' colspan='3' align='right'>&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$addDLCID."'>Add New Download</a> &laquo;</td>
				</tr>
				<tr>
					<td class='formTitle' style='width: 46%'>Download:</td>
					<td class='formTitle' style='width: 30%'>Date Uploaded:</td>
					<td class='formTitle' style='width: 24%'>Actions:</td>
				</tr>
			</table>
			<div id='loadingSpiral' class='loadingSpiral'>
				<p align='center'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
				</p>
			</div>
			<div id='downloadList'>
			
				";
	
	include("include/downloadlist.php");
	
	echo "
			
			</div>
			
			<div id='deleteMessage' style='display: none'></div>
			<script type='text/javascript'>
			
				function deleteDL(intDLID) {
					$(document).ready(function() {
						$.post('".$MAIN_ROOT."members/include/downloads/include/delete.php', { dlID: intDLID }, function(data) {
							$('#deleteMessage').html(data);
							$('#deleteMessage').dialog({
								title: 'Delete Download - Confirm',
								modal: true,
								zIndex: 99999,
								resizable: false,
								show: 'scale',
								width: 400,
								buttons: {
									'Yes': function() {
										$('#downloadList').fadeOut(200);
										$('#loadingSpiral').show();
										$.post('".$MAIN_ROOT."members/include/downloads/include/delete.php', { dlID: intDLID, confirm: 1 }, function(data1) {
											$('#downloadList').html(data1);
											$('#loadingSpiral').hide();
											$('#downloadList').fadeIn(200);
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



?>