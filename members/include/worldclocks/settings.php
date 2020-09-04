<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	
	$arrTimezoneOptions = $clockObj->getTimezones();
	$i=0;
	$arrComponents = array(
		"default_timezone" => array(
			"type" => "select",
			"display_name" => "Default Timezone",
			"value" => $websiteInfo['default_timezone'],
			"attributes" => array("class" => "formInput textBox"),
			"sortorder" => $i++,
			"options" => $arrTimezoneOptions,
			"validate" => array("RESTRICT_TO_OPTIONS")
		),
		"date_format" => array(
			"type" => "text",
			"sortorder" => $i++,
			"display_name" => "Date Format",
			"tooltip" => "This is the format in which the date on the top of the clocks will be displayed.",
			"html" => "<br><label class='formLabel' style='display: inline-block'></label><div class='formInput tinyFont'><a href='http://www.php.net/manual/en/function.date.php' target='_blank'>Click Here</a> to view a list of the date format codes.</div><br>",
			"value" => $websiteInfo['date_format'],
			"attributes" => array("class" => "formInput textBox")	
		),
		"display_date" => array(
			"type" => "checkbox",
			"display_name" => "Show Date & Time",
			"sortorder" => $i++,
			"tooltip" => "Uncheck the box to hide the date & time at the top of the page.",
			"options" => array(1 => ""),	
			"value" => $websiteInfo['display_date'],
			"attributes" => array("class" => "formInput")
		),
		"submit" => array(
			"type" => "submit",
			"value" => "Save",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"sortorder" => $i++
		)
	
	);
	
	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"saveMessage" => "Successfully saved world clock settings!",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"description" => "Use this form to modify the default time and date settings for the world clocks.",
		"afterSave" => array("saveWorldClockSettings")
	);
	
	function saveWorldClockSettings() {
		global $webInfoObj;
			
		$arrSettings = array("default_timezone", "date_format", "display_date");
		$arrValues = array($_POST['default_timezone'], $_POST['date_format'], $_POST['display_date']);
		$webInfoObj->multiUpdate($arrSettings, $arrValues);
		
	}

?>