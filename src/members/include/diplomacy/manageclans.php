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
$intAddClanCID = $consoleObj->findConsoleIDByName("Diplomacy: Add a Clan");

if(!isset($_GET['dID'])) {
	
	echo "
		
		<table class='formTable' style='border-spacing: 1px'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddClanCID."'>Add New Clan</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' width=\"80%\">Clan Name:</td>
				<td class='formTitle' width=\"20%\">Actions:</td>
			</tr>
		</table>
		
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		<div id='clanListDiv' style='margin: 0px; padding: 0px'>
	";
	
	include("include/main_manageclans.php");
	
	echo "
		</div>
		<div id='confirmDelete' style='display: none'></div>
		<script type='text/javascript'>
	
			function deleteClan(intClanID) {
				$(document).ready(function() {

					$.post('".$MAIN_ROOT."members/include/diplomacy/include/deleteclan.php', { dClanID: intClanID }, function(data) {
						$('#confirmDelete').html(data);
						$('#confirmDelete').dialog({
						
							title: 'Confirm Delete',
							zIndex: 99999,
							modal: true,
							show: 'scale',
							width: 400,
							buttons: {
								'Yes': function() {
								
									$('#clanListDiv').hide();
									$('#loadingSpiral').show();
									$.post('".$MAIN_ROOT."members/include/diplomacy/include/deleteclan.php', { dClanID: intClanID, confirmDelete: 1 }, function(data1) {
									
										$('#clanListDiv').html(data1);
										$('#clanListDiv').fadeIn(250);
										$('#loadingSpiral').hide();
										
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
elseif(isset($_GET['dID']) && $_GET['action'] == "edit") {
	
	include("include/editclan.php");
	
}



?>