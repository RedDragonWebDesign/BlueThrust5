<?php

// Poll Options
$pollObj = new Poll($mysqli);
$arrPolls = $pollObj->get_entries();
$pollOptions = [];
foreach ($arrPolls as $eachPoll) {
	$pollOptions[$eachPoll['poll_id']] = $eachPoll['question'];
}

if (count($pollOptions) == 0) {
	$pollOptions = ["No Polls Added"];
}

$pollOptionComponents = [
	"poll" => [
		"type" => "select",
		"display_name" => "Select Poll",
		"attributes" => ["class" => "formInput textBox"],
		"sortorder" => $i++,
		"options" => $pollOptions
	]
];
