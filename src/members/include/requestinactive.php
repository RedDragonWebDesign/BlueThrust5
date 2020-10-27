<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	
	// Posted Message?
	
	include_once("../../_setup.php");
	include_once("../../classes/member.php");

	$consoleObj = new ConsoleOption($mysqli);
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$cID = $consoleObj->findConsoleIDByName("Inactive Request");
	$consoleObj->select($cID);


	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj) || !$member->requestedIA()) {
		exit();
	}
	
	$memberInfo = $member->get_info_filtered();
	
	$iaRequestObj = new Basic($mysqli, "iarequest", "iarequest_id");
	$iaRequestObj->select($member->requestedIA(true));
	
	$requestInfo = $iaRequestObj->get_info_filtered();
	
	
	if(trim($_POST['message']) != "" && $requestInfo['requeststatus'] == 0) {
		$iaRequestMessageObj = new Basic($mysqli, "iarequest_messages", "iamessage_id");
		
		$arrColumns = array("iarequest_id", "member_id", "messagedate", "message");
		$arrValues = array($requestInfo['iarequest_id'], $memberInfo['member_id'], time(), $_POST['message']);
		
		$iaRequestMessageObj->addNew($arrColumns, $arrValues);
		
	}
	
	
	
	$iaMember = new Member($mysqli);
	$counter = 1;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."iarequest_messages WHERE iarequest_id = '".$requestInfo['iarequest_id']."' ORDER BY messagedate DESC");
	while($row = $result->fetch_assoc()) {
		
		if($counter == 0) {
			$addCSS = "";
			$counter = 1;
		}
		else {
			$addCSS = " alternateBGColor";		
			$counter = 0;
		}
		
		$iaMember->select($row['member_id']);
		echo "
			<div class='dottedLine".$addCSS."' style='padding: 10px 5px; width: 80%; margin-left: auto; margin-right: auto;'>
				".$iaMember->getMemberLink()." - ".getPreciseTime($row['messagedate'])."<br><br>
				<div style='padding-left: 5px'>".nl2br(filterText($row['message']))."</div>
			</div>
		";
		
	}
	

	if($result->num_rows == 0) {

		echo "
			<div class='shadedBox' style='margin: 20px auto; width: 50%'>
				<p align='center'><i>No Messages</i></p>					
			</div>
		";
		
	}
	else {
		echo "<br><br>";	
	}
	
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}



if(!$member->requestedIA()) {

	$i = 1;
	$arrComponents = array(
		"reason" => array(
			"display_name" => "Reason",
			"type" => "textarea",
			"tooltip" => "Leave a reason and for how long you will be inactive for a better chance of being approved.",
			"attributes" => array("class" => "textBox formInput", "style" => "width: 35%", "rows" => "4"),
			"db_name" => "reason",
			"sortorder" => $i++),
		"submit" => array(
			"value" => "Send Request",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"sortorder" => $i++,
			"type" => "submit")
	);
	
	$requestIAObj = new Basic($mysqli, "iarequest", "iarequest_id");
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"saveObject" => $requestIAObj,
		"saveType" => "add",
		"saveAdditional" => array("member_id" => $memberInfo['member_id'], "requestdate" => time()),
		"saveMessage" => "Inactive Request Sent!",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"description" => "Use the form below to request to be inactive.  When inactive, you will be able to log in, however you will not have access to any console options.  A higher ranking member will have to approve your request before your status is set to inactive."
	);

}
else {
	// Already requested to be inactive
	$iaRequestObj = new Basic($mysqli, "iarequest", "iarequest_id");
	$iaRequestObj->select($member->requestedIA(true));
	
	$requestInfo = $iaRequestObj->get_info_filtered();	
	
	
	$dispRequestStatus = "<span class='pendingFont'>Pending</span>";
	$dispSendMessages = " You may send additional messages using the form below.";
	if($requestInfo['requeststatus'] == 1) {
		$member->select($requestInfo['reviewer_id']);
		$dispRequestStatus = "<span class='allowText'>Approved</span> by ".$member->getMemberLink()." - ".getPreciseTime($requestInfo['reviewdate']);
		$member->select($memberInfo['member_id']);
		$dispSendMessages = "  A higher ranking member must delete the request before you can issue another request.";
	}
	elseif($requestInfo['requeststatus'] == 2) {
		$member->select($requestInfo['reviewer_id']);
		$dispRequestStatus = "<span class='denyText'>Denied</span> by ".$member->getMemberLink()." - ".getPreciseTime($requestInfo['reviewdate']);
		$member->select($memberInfo['member_id']);
		$dispSendMessages = "  A higher ranking member must delete the request before you can issue another one.";
	}
	
	$i = 1;
	$arrComponents = array(
		"requestinfosection" => array(
			"type" => "section",
			"sortorder" => $i++,
			"options" => array("section_title" => "Request Information:")
		),
		"requestdate" => array(
			"display_name" => "Request Date",
			"type" => "custom",
			"html" => "<div class='formInput'>".getPreciseTime($requestInfo['requestdate'])."</div>",
			"sortorder" => $i++
		),
		"status" => array(
			"display_name" => "Status",
			"type" => "custom",
			"html" => "<div class='formInput'>".$dispRequestStatus."</div>",
			"sortorder" => $i++
		),
		"reason" => array(
			"display_name" => "Reason",
			"type" => "custom",
			"html" => "<div class='formInput'>".nl2br($requestInfo['reason'])."</div>",
			"sortorder" => $i++
		),
		"messagessection" => array(
			"type" => "section",
			"sortorder" => $i++,
			"options" => array("section_title" => "Messages:")
		),
		"messages" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"html" => "<div id='loadingSpiral' style='display: none'><p align='center' class='main'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading</p></div><div id='iaMessages'></div>"		
		)
	
	);
	
	if($requestInfo['requeststatus'] == 0) {
		$arrSendMesssageComponents = array(
			"txtmessage" => array(
				"display_name" => "Leave Message",
				"attributes" => array("class" => "textBox formInput", "style" => "width: 35%", "rows" => "4", "id" => "txtMessage"),
				"type" => "textarea",
				"sortorder" => $i++		
			),
			"sendmessagebutton" => array(
				"type" => "button",
				"value" => "Send Message",
				"attributes" => array("class" => "submitButton formSubmitButton", "id" => "btnSend"),
				"sortorder" => $i++			
			)
		);
		
		$arrComponents = array_merge($arrComponents, $arrSendMesssageComponents);
	}
	else {
		echo "<input type='hidden' id='btnSend'>";	
	}
	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"description" => "You currently have an open inactive request.".$dispSendMessages,
		"components" => $arrComponents
	);
	
	
	echo "		
		<script type='text/javascript'>
			
			$(document).ready(function() {
			
				$('#btnSend').click(function() {
					
					$('#iaMessages').fadeOut(250);
					$('#loadingSpiral').show();
					
					$.post('".$MAIN_ROOT."members/include/requestinactive.php', { message: $('#txtMessage').val() }, function(data) {
					
						$('#iaMessages').html(data);
						$('#iaMessages').fadeIn(250);
						$('#loadingSpiral').hide();
											
					});
					
					$('#txtMessage').val('');
				});
				
				$('#btnSend').click();
							
			});
		
		</script>
		
	";
	
}

?>