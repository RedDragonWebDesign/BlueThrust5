<?php


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$consoleCatID = $consoleObj->get_info("consolecategory_id");
$campaignInfo = $campaignObj->get_info_filtered();

$breadcrumbObj->popCrumb();
$breadcrumbObj->addCrumb($consoleObj->get_info_filtered("pagetitle"), $MAIN_ROOT."members/console.php?cID=".$cID);
$breadcrumbObj->addCrumb($campaignInfo['title']);

$breadcrumbObj->updateBreadcrumb();

define("CAMPAIGN_FORM", true);

// Default End Date

$setTimestamp = ($campaignInfo['dateend'] == 0) ? time() : $campaignInfo['dateend'];

$endDate = new DateTime();
$endDate->setTimestamp($setTimestamp);
$endDate->setTimezone(new DateTimeZone("UTC"));

$defaultEndDate = $endDate->format("M j, Y");
$setRecurringBox = ($campaignInfo['recurringunit'] != "") ? 1 : 0;


include(BASE_DIRECTORY."plugins/donations/console/campaign_form.php");

$arrComponents['submit']['value'] = "Save";
$arrComponents['rununtil']['value'] = ($campaignInfo['dateend'] == 0) ? "forever" : "choose";
$arrComponents['enddate']['value'] = $campaignInfo['dateend']*1000;
$arrComponents['enddate']['options']['defaultDate'] = $defaultEndDate;

$arrComponents['viewpage'] = array(
	"type" => "custom",
	"html" => "<p align='center' class='main'><a href='".$campaignObj->getLink()."' target='_blank'>View Donation Page</a></p>",
	"sortorder" => $i++
);


$setupFormArgs['saveType'] = "update";
$setupFormArgs['components'] = $arrComponents;
$setupFormArgs['attributes']['action'] .= "&campaignID=".$_GET['campaignID']."&action=edit";
$setupFormArgs['saveMessage'] = "Successfully saved donation campaign!";
$setupFormArgs['saveLink'] = $MAIN_ROOT."members/console.php?cID=".$_GET['cID'];

if(!$_POST['submit']) {
	$setupFormArgs['prefill'] = true;
	$setupFormArgs['skipPrefill'] = array("dateend", "currentperiod");
}

unset($setupFormArgs['saveAdditional']['datestarted']);

?>