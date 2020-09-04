<?php

	if(!isset($pluginInstaller)) { exit(); }

	$pluginInstaller->setPluginDirectory("twitch");
	
	$pluginInstaller->pluginName = "Twitch";
	$pluginInstaller->pluginPages = array(
		array(
			"page" => "mods",
			"pagepath" => "plugins/twitch/twitch_functions.php"
		)
	);
	
	

?>