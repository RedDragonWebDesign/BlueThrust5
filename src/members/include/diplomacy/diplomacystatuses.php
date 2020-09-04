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

include_once("../../../classes/basicorder.php");

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
$intAddStatusCID = $consoleObj->findConsoleIDByName("Add Diplomacy Status");


if(!isset($_GET['sID'])) {
	
	
	echo "
	
		<table class='formTable' style='border-spacing: 1px'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddStatusCID."'>Add New Diplomacy Status</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' width=\"76%\">Status Name:</td>
				<td class='formTitle' width=\"24%\">Actions:</td>
			</tr>
		</table>
		
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		<div id='statusListDiv' style='margin: 0px; padding: 0px'>
	
	";
	
	include("include/main_managestatuses.php");
	
	
	echo "
		</div>
		<div id='deleteDivInfo' style='display: none'></div>
		<script type='text/javascript'>

			function moveStatus(strDir, intStatusID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#statusListDiv').hide();
					$.post('".$MAIN_ROOT."members/include/diplomacy/include/movestatus.php', {
						sDir: strDir, sID: intStatusID }, function(data) {
							$('#statusListDiv').html(data);
							$('#loadingSpiral').hide();
							$('#statusListDiv').fadeIn(250);
						});
			
				});
			}
			
			
			function deleteStatus(intStatusID) {
			
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."members/include/diplomacy/include/deletestatus.php', { sID: intStatusID }, function(data) {
			
						$('#deleteDivInfo').html(data);
						
					
					});
				});
				
			}
		
		</script>
	
	";

}
elseif($_GET['action'] == "edit" && isset($_GET['sID'])) {
	include("include/editstatus.php");	
}



?>