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

	$prevFolder = "../../../../";
	include_once($prevFolder."_setup.php");
	include_once($prevFolder."classes/member.php");
	include_once($prevFolder."classes/rank.php");
	include_once($prevFolder."classes/btplugin.php");
	
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
	$consoleObj->select($cID);
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {
		exit();
	}
	
	$pluginObj = new btPlugin($mysqli);
}

echo "
<table class='formTable' style='margin-top: 0px; border-spacing: 0px'>
	";
	$dispPlugins = "";
	$pluginsDir = scandir($prevFolder."plugins");
	$addCSS = "";
	$x = 0;
	foreach($pluginsDir as $dir) {
	
		if(is_dir($prevFolder."plugins/".$dir) && $dir != "." && $dir != ".." && !in_array($dir, $pluginObj->getPlugins("filepath")) && (file_exists($prevFolder."plugins/".$dir."/install.php") || file_exists($prevFolder."plugins/".$dir."/install_setup.php"))) {
			
			if($x == 0) {
				$x = 1;
				$addCSS = "";	
			}
			else {
				$x = 0;
				$addCSS = " alternateBGColor";	
			}
			
			$pluginName = file_get_contents($prevFolder."plugins/".$dir."/PLUGINNAME.txt");
			if($pluginName === false) {
				$pluginName = ucfirst($dir);
			}
			
			$installJSData = "";
			if(file_exists(BASE_DIRECTORY."plugins/".$dir."/install_setup.php")) {
				$installJSData = " data-install='1'";
			}
			
			$dispPlugins .= "
				<tr>
					<td class='dottedLine main manageList".$addCSS."' style='padding-left: 10px'>".$pluginName."</td>
					<td class='dottedLine main manageList".$addCSS."' style='width: 24%' align='center'><a style='cursor: pointer' id='installPlugin' data-plugin='".$dir."' data-clicked='0'".$installJSData.">Install</a></td>
				</tr>			
			";
		}
		
	}
	
	if($dispPlugins != "") {

		echo $dispPlugins;
		
	}
	else {
		echo "
			<tr>
				<td>
					<div class='shadedBox' style='width: 50%; margin: 20px auto'>
						<p class='main' align='center'>
							There are no available plugins.
						</p>
					</div>
				</td>
			</tr>
		";
		
	}
	
	echo "</table>
	
	<script type='text/javascript'>
		$(document).ready(function() {
			$(\"a[id='installPlugin']\").click(function() {
				
				var installLink = '".$MAIN_ROOT."plugins/'+$(this).attr('data-plugin')+'/install.php';
				if($(this).attr('data-install') == \"1\") {
					installLink = '".$MAIN_ROOT."plugins/install.php?plugin='+$(this).attr('data-plugin');
				}
				
			
				if($(this).attr('data-clicked') == 0) {
					$(this).html(\"<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif' class='manageListActionButton'>\");
					$(this).attr('data-clicked', 1);
					$(this).css('cursor', 'default');
					
					
					$.post(installLink, { pluginDir: $(this).attr('data-plugin') }, function(data) {
					
						postResult = JSON.parse(data);
						
						if(postResult['result'] == 'success') {
							
							$('#installMessage').html(\"<p class='main' align='center'>Successfully installed plugin!<br><br>Make sure to configure the API settings on the plugin's settings page.</p>\");
							
							
						}
						else {
						
							
							var strErrorHTML = \"<ul>\";
							
							for(var x in postResult['errors']) {
							
								strErrorHTML += \"<li class='main'>\"+postResult['errors'][x]+\"</li>\";
															
							}

							strErrorHTML += \"</ul>\";
							
							$('#installMessage').html(\"<p class='main'>Unable to install plugin because the following errors occurred:<br>\"+strErrorHTML+\"</p>\");
							
						
						}
						
						$('#installMessage').dialog({
						
							title: 'Plugin Manager',
							zIndex: 9999,
							show: 'scale',
							modal: true,
							width: 450,
							resizable: false,
							buttons: {
								'Ok': function() {
									$(this).dialog('close');
								}								
							}
						
						});
						
						
						reloadPluginLists();
					
					});
					
				}
			
			});
		});
				
	</script>
	
	
	";
	
	?>