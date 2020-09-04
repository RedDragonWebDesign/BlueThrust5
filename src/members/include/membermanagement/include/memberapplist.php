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

	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/basicorder.php");



	$consoleObj = new ConsoleOption($mysqli);
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$cID = $consoleObj->findConsoleIDByName("View Member Applications");
	$consoleObj->select($cID);


	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {

		exit();

	}

}

$memberAppObj = new MemberApp($mysqli);
$appComponentObj = $memberAppObj->objAppComponent;

$memberAppForm = $memberAppObj->objSignUpForm;

$setupMemberAppForm = array(
	"name" => "display-member-app",
	"wrapper" => array("<div class='dottedBox' style='margin-top: 20px; width: 90%; margin-left: auto; margin-right: auto;'>", "</div>")
);

$result = $mysqli->query("SELECT memberapp_id FROM ".$dbprefix."memberapps ORDER BY applydate DESC");
while($row = $result->fetch_assoc()) {

	$memberAppObj->select($row['memberapp_id']);
	$memberAppInfo = $memberAppObj->get_info_filtered();
	
	$dispApplyDate = getPreciseTime($memberAppInfo['applydate']);
	
	// Default Info
	$i = 0;
	$arrDefaultInfo = array(
		"dateapplied" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => "Date Applied",
			"html" => "<div class='main formInput'>".$dispApplyDate."</div>"
		),
		"username" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => "Username",
			"html" => "<div class='main formInput'>".$memberAppInfo['username']."</div>"
		),
		"ipaddress" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => "IP Address",
			"html" => "<div class='main formInput'>".$memberAppInfo['ipaddress']."</div>"
		),
		"email" => array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => "E-mail",
			"html" => "<div class='main formInput'><a href='mailto:".$memberAppInfo['email']."'>".$memberAppInfo['email']."</a></div>"
		)
	);
	
	// Custom Info

	$customAppInfo = $memberAppObj->getAppValues();

	$arrCompInfo = array();
	foreach($customAppInfo as $componentID => $customInfo) {
		$appComponentObj->select($componentID);
		$appCompName = $appComponentObj->get_info_filtered("name");
		$compName = "appcomponent_".$componentID;
		
		$dispCompValue = "";
		if(count($customInfo['display_values']) > 1) {
			$displayValueCounter = 1;
			foreach($customInfo['display_values'] as $value) {
				$dispCompValue .= $displayValueCounter.". ".$value."<br>";				
				$displayValueCounter++;
			}
		}
		elseif(isset($customInfo['display_values'][0]) && $customInfo['display_values'][0] != "") {
			$dispCompValue = $customInfo['display_values'][0];
		}
		else {
			$dispCompValue = "Not Set";	
		}
		
		$arrCompInfo[$compName] = array(
			"type" => "custom",
			"sortorder" => $i++,
			"display_name" => $appCompName,
			"html" => "<div class='main formInput'>".$dispCompValue."</div>"
		
		);
		
	}
	
	if($memberAppInfo['memberadded'] == 0) {
		$memberAppOptions = "<a href='javascript:void(0)' onclick=\"acceptApp('".$memberAppInfo['memberapp_id']."')\"><b>Accept</b></a> - <a href='javascript:void(0)' onclick=\"declineApp('".$memberAppInfo['memberapp_id']."')\"><b>Decline</b></a>";
	}
	else {
		$memberAppOptions = "<span class='successFont' style='font-weight: bold'>Member Added!</span> - <a href='javascript:void(0)' onclick=\"removeApp('".$memberAppInfo['memberapp_id']."')\"><b>Remove</b></a>";
	}
	
	
	$arrCompInfo['app_options'] = array(
		"type" => "custom",
		"sortorder" => $i++,
		"html" => "<br><p align='center'>".$memberAppOptions."</p>"
	
	);
	
	$arrComponents = array_merge($arrDefaultInfo, $arrCompInfo);
	
	$setupMemberAppForm['components'] = $arrComponents;
	
	
	$memberAppForm->buildForm($setupMemberAppForm);
	
	$memberAppForm->show();
}



if($result->num_rows == 0) {

	echo "
		<div class='shadedBox' style='width: 400px; margin-top: 50px; margin-left: auto; margin-right: auto'>
			<p class='main' align='center'>
				<i>There are currently no member applications.</i>
			</p>
		</div>
	";
	
}
else {
	$mysqli->query("UPDATE ".$dbprefix."memberapps SET seenstatus = '1' WHERE seenstatus = '0'");	
}

	
?>
