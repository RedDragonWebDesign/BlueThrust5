<?php


// Custom Page Options

$customPageObj = new Basic($mysqli, "custompages", "custompage_id");
$arrCustomPages = $customPageObj->get_entries();
$customPageOptions = [];
foreach ($arrCustomPages as $eachCustomPage) {
	$customPageOptions[$eachCustomPage['custompage_id']] = $eachCustomPage['pagename'];
}

if (count($customPageOptions) == 0) {
	$customPageOptions = ["No Custom Pages"];
}

$customPageOptionComponents = [
	"custompage" => [
		"type" => "select",
		"attributes" => ["class" => "textBox formInput"],
		"options" => $customPageOptions,
		"sortorder" => $i++,
		"display_name" => "Custom Page"
	]

];
