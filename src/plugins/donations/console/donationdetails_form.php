<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	$donationMember = new Member($mysqli);
	$dispMemberName = $donationMember->select($donationInfo['member_id']) ? $donationMember->getMemberLink() : "";

	$dispName = $donationInfo['name'];
	if($donationInfo['name'] == "" && $dispMemberName == "") {
		$dispName = "Anonymous";	
	}
	elseif($donationInfo['name'] != "" && $dispMemberName != "") {
		$dispName = $dispMemberName." <i>(".$donationInfo['name'].")</i>";	
	}
	

	$dispMessage = ($donationInfo['message'] == "") ? "None" : nl2br(parseBBCode($donationInfo['message']));
	
	$i=0;
	$arrComponents = array(
		"campaign" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'><a href='".$campaignObj->getLink()."'>".$campaignInfo['title']."</a></div>",
			"sortorder" => $i++,
			"display_name" => "Campaign"
		),
		"datesent" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'>".getPreciseTime($donationInfo['datesent'])."</div>",
			"sortorder" => $i++,
			"display_name" => "Date Sent"
		),
		"paypalid" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'>".$donationInfo['transaction_id']."</div>",
			"sortorder" => $i++,
			"display_name" => "PayPal Transaction ID"
		),
		"amount" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'>".$campaignObj->formatAmount($donationInfo['amount'])."</div>",
			"sortorder" => $i++,
			"display_name" => "Amount"
		),
		"donationfrom" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'>".$dispName."</div>",
			"sortorder" => $i++,
			"display_name" => "Donated From"
		),
		"message" => array(
			"type" => "custom",
			"html" => "<div class='formInput main'>".$dispMessage."</div><br>",
			"sortorder" => $i++,
			"display_name" => "Message"
		)
	);
	
	
	$setupFormArgs = array(
		"name" => "console-".$cID."-donationdetails-".$donationInfo['donation_id'],
		"components" => $arrComponents
	);

?>