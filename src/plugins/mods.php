<?php

	

	function minecraftSkins() {
		global $arrSections;
		
		$arrSections = addArraySpace($arrSections, 5, 2);
		
		$arrSections[2] = "include/profile/_minecraftskin.php";

	}

	$hooksObj->addHook("profile_sections", "minecraftSkins");
	
	
	
	
	// Include installed plugin functions
	
	$modPluginsObj = new btPlugin($mysqli);
	$arrPlugins = $modPluginsObj->getPluginPage("mods");
	
	foreach($arrPlugins as $pluginInfo) {
		include_once(BASE_DIRECTORY.$pluginInfo['pagepath']);
	}
	
	
	function replaceRichTextEditor() {
		
		$GLOBALS['richtextEditor'] = "

			CKEDITOR.replace('".$GLOBALS['rtCompID']."');
		
		";
		
	}
	
	//$hooksObj->addHook("form_richtexteditor", "replaceRichTextEditor");
	
?>