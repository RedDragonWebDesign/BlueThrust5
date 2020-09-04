<?php


// Custom Page Options

$customPageObj = new Basic($mysqli, "custompages", "custompage_id");
$arrCustomPages = $customPageObj->get_entries();
$customPageOptions = array();
foreach($arrCustomPages as $eachCustomPage) {
	$customPageOptions[$eachCustomPage['custompage_id']] = $eachCustomPage['pagename'];	
}

if(count($customPageOptions) == 0) {
	$customPageOptions = array("No Custom Pages");	
}

$customPageOptionComponents = array(
	"custompage" => array(
		"type" => "select",
		"attributes" => array("class" => "textBox formInput"),
		"options" => $customPageOptions,
		"sortorder" => $i++,
		"display_name" => "Custom Page"
	)

);

?>