<?php


// Custom Form Page Options
$customFormPageObj = new CustomForm($mysqli);
$arrCustomFormPages = $customFormPageObj->get_entries();
$customFormPageOptions = [];
foreach ($arrCustomFormPages as $eachCustomFormPage) {
	$customFormPageOptions[$eachCustomFormPage['customform_id']] = $eachCustomFormPage['name'];
}

if (count($customFormPageOptions) == 0) {
	$customFormPageOptions = ["No Custom Form Pages"];
}

$customFormOptionComponents = [
	"customform" => [
		"type" => "select",
		"attributes" => ["class" => "textBox formInput"],
		"options" => $customFormPageOptions,
		"sortorder" => $i++,
		"display_name" => "Custom Form"
	]

];
