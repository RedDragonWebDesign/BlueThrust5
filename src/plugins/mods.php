<?php

	// Include installed plugin functions

$modPluginsObj = new btPlugin($mysqli);
$arrPlugins = $modPluginsObj->getPluginPage("mods");

foreach ($arrPlugins as $pluginInfo) {
	require_once(BASE_DIRECTORY.$pluginInfo['pagepath']);
}


function replaceRichTextEditor() {

	$GLOBALS['richtextEditor'] = "

			CKEDITOR.replace('".$GLOBALS['rtCompID']."');
		
		";
}


	//$hooksObj->addHook("form_richtexteditor", "replaceRichTextEditor");
