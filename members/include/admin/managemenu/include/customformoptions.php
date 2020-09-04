<?php


// Custom Form Page Options
$customFormPageObj = new CustomForm($mysqli);
$arrCustomFormPages = $customFormPageObj->get_entries();
$customFormPageOptions = array();
foreach($arrCustomFormPages as $eachCustomFormPage) {
	$customFormPageOptions[$eachCustomFormPage['customform_id']] = $eachCustomFormPage['name'];	
}

if(count($customFormPageOptions) == 0) {
	$customFormPageOptions = array("No Custom Form Pages");	
}

$customFormOptionComponents = array(
	"customform" => array(
		"type" => "select",
		"attributes" => array("class" => "textBox formInput"),
		"options" => $customFormPageOptions,
		"sortorder" => $i++,
		"display_name" => "Custom Form"
	)

);

?>