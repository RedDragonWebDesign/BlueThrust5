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


include_once("../classes/btplugin.php");
$cID = $_GET['cID'];

$pluginObj = new btPlugin($mysqli);

$dispError = "";
$countErrors = 0;


echo "

	<div class='formDiv'>

		<table class='formTable'>
			<tr>
				<td class='main' colspan='2'>
					<div class='dottedLine' style='padding-bottom: 3px'><b>Installed Plugins:</b></div><br></td>
				</td>
			</tr>
			<tr>
				<td class='formTitle'>Plugin Name:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
			</table>
			
		<div id='installedDiv'>
	";
		include("include/admin/plugins/installed.php");
	echo "
		</div>
		<div id='loadingSpiralInstalled' style='display: none; margin-bottom: 20px'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'></div>
			
			<table class='formTable'>
				<tr>
					<td class='main' colspan='2'>
						<div class='dottedLine' style='padding-bottom: 3px'><b>Available Plugins: <a style='cursor: pointer' onmouseover=\"showToolTip('To add an available plugin, place new plugins in the plugin folder in your main directory.')\" onmouseout='hideToolTip()'>(?)</b></div><br></td>
					</td>
				</tr>
				<tr>
					<td class='formTitle'>Plugin Name:</td>
					<td class='formTitle' style='width: 24%'>Actions:</td>
				</tr>
			</table>
	";
	
	
	echo "
		<div id='availableDiv'>
	";
	include("include/admin/plugins/available.php");
	echo "
		</div>
		<div id='loadingSpiralAvailable' style='display: none'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'></div>
		<br>	
	</div>
	
	<div id='installMessage' style='display: none'></div>
	<div id='termsMessage'></div>
	<script type='text/javascript'>
	
		function reloadPluginLists() {
		
			$(document).ready(function() {
		
				$.post('".$MAIN_ROOT."members/include/admin/plugins/available.php', { }, function(data) {
				
					$('#availableDiv').html(data);
				
				});
				
				$.post('".$MAIN_ROOT."members/include/admin/plugins/installed.php', { }, function(data) {
				
					$('#installedDiv').html(data);
				
				});
			
			});
		
		}
	
	</script>
	";


?>