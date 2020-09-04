<?php

// Poll Options
$pollObj = new Poll($mysqli);
$arrPolls = $pollObj->get_entries();
$pollOptions = array();
foreach($arrPolls as $eachPoll) {
	$pollOptions[$eachPoll['poll_id']] = $eachPoll['question'];
}

if(count($pollOptions) == 0) {
	$pollOptions = array("No Polls Added");	
}

$pollOptionComponents = array(
	"poll" => array(
		"type" => "select",
		"display_name" => "Select Poll",
		"attributes" => array("class" => "formInput textBox"),
		"sortorder" => $i++,
		"options" => $pollOptions
	)
);


?>