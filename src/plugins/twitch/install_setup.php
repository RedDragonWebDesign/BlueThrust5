<?php

if (!isset($pluginInstaller)) {
	exit();
}

$pluginInstaller->setPluginDirectory("twitch");

$pluginInstaller->pluginName = "Twitch";
$pluginInstaller->pluginPages = [
		[
			"page" => "mods",
			"pagepath" => "plugins/twitch/twitch_functions.php"
		]
	];
