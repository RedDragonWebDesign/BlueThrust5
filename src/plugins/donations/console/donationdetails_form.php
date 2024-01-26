<?php

if (!defined("MAIN_ROOT")) {
	exit();
}

$donationMember = new Member($mysqli);
$dispMemberName = $donationMember->select($donationInfo['member_id']) ? $donationMember->getMemberLink() : "";

$dispName = $donationInfo['name'];
if ($donationInfo['name'] == "" && $dispMemberName == "") {
	$dispName = "Anonymous";
} elseif ($donationInfo['name'] != "" && $dispMemberName != "") {
	$dispName = $dispMemberName." <i>(".$donationInfo['name'].")</i>";
}


$dispMessage = ($donationInfo['message'] == "") ? "None" : nl2br(parseBBCode($donationInfo['message']));

$i=0;
$arrComponents = [
		"campaign" => [
			"type" => "custom",
			"html" => "<div class='formInput main'><a href='".$campaignObj->getLink()."'>".$campaignInfo['title']."</a></div>",
			"sortorder" => $i++,
			"display_name" => "Campaign"
		],
		"datesent" => [
			"type" => "custom",
			"html" => "<div class='formInput main'>".getPreciseTime($donationInfo['datesent'])."</div>",
			"sortorder" => $i++,
			"display_name" => "Date Sent"
		],
		"paypalid" => [
			"type" => "custom",
			"html" => "<div class='formInput main'>".$donationInfo['transaction_id']."</div>",
			"sortorder" => $i++,
			"display_name" => "PayPal Transaction ID"
		],
		"amount" => [
			"type" => "custom",
			"html" => "<div class='formInput main'>".$campaignObj->formatAmount($donationInfo['amount'])."</div>",
			"sortorder" => $i++,
			"display_name" => "Amount"
		],
		"donationfrom" => [
			"type" => "custom",
			"html" => "<div class='formInput main'>".$dispName."</div>",
			"sortorder" => $i++,
			"display_name" => "Donated From"
		],
		"message" => [
			"type" => "custom",
			"html" => "<div class='formInput main'>".$dispMessage."</div><br>",
			"sortorder" => $i++,
			"display_name" => "Message"
		]
	];


$setupFormArgs = [
		"name" => "console-".$cID."-donationdetails-".$donationInfo['donation_id'],
		"components" => $arrComponents
	];
