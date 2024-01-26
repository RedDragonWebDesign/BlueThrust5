<?php

// Download Page Options
$downloadPageObj = new DownloadCategory($mysqli);
$arrDownloadPages = $downloadPageObj->get_entries(["specialkey" => ""]);
$downloadPageOptions = [];
foreach ($arrDownloadPages as $eachDownloadPage) {
	$downloadPageOptions[$eachDownloadPage['downloadcategory_id']] = $eachDownloadPage['name'];
}

if (count($downloadPageOptions) == 0) {
	$downloadPageOptions = ["No Download Categories"];
}

$downloadOptionComponents = [
	"downloadpage" => [
		"type" => "select",
		"attributes" => ["class" => "textBox formInput"],
		"options" => $downloadPageOptions,
		"sortorder" => $i++,
		"display_name" => "Download Page"
	]

];
