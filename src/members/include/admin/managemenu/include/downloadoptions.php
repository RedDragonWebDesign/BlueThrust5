<?php

// Download Page Options
$downloadPageObj = new DownloadCategory($mysqli);
$arrDownloadPages = $downloadPageObj->get_entries(array("specialkey" => ""));
$downloadPageOptions = array();
foreach($arrDownloadPages as $eachDownloadPage) {
	$downloadPageOptions[$eachDownloadPage['downloadcategory_id']] = $eachDownloadPage['name'];	
}

if(count($downloadPageOptions) == 0) {
	$downloadPageOptions = array("No Download Categories");	
}

$downloadOptionComponents = array(
	"downloadpage" => array(
		"type" => "select",
		"attributes" => array("class" => "textBox formInput"),
		"options" => $downloadPageOptions,
		"sortorder" => $i++,
		"display_name" => "Download Page"
	)

);

?>