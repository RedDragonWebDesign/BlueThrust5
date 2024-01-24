<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


if (!isset($member) || !isset($tournamentObj) || substr($_SERVER['PHP_SELF'], -strlen("managetournament.php")) != "managetournament.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$tournamentObj->select($tID);


	if (!$member->hasAccess($consoleObj)) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Edit Tournament Info\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Edit Tournament Info\");
});
</script>
";

require_once(BASE_DIRECTORY."members/include/tournaments/include/create_form.php");

$arrComponents['starttime']['options']['selected_timezone'] = $tournamentInfo['timezone'];

$arrComponents['starttime']['value'] = $tournamentInfo['startdate']*1000;
$arrComponents['startdate']['value'] = $tournamentInfo['startdate']*1000;

if ($tournamentInfo['requirereplay'] == 1) {
	$arrComponents['requirereplay']['checked'] = true;
}

$datePick = new DateTime();
$datePick->setTimezone(new DateTimeZone("UTC"));
$datePick->setTimestamp($tournamentInfo['startdate']);

$arrComponents['startdate']['options']['defaultDate'] = $datePick->format("M j, Y");

$arrComponents['tournamentpw']['validate'][] = "resetTournamentPassword";
$arrComponents['tournamentpw']['tooltip'] = "If you don't want to change the current password, leave both password inputs blank.";

if ($tournamentInfo['password'] != "") {

	$lastComponentOrder = $arrComponents['submit']['sortorder'];
	$arrComponents['removepw'] = array(
		"type" => "checkbox",
		"display_name" => "Remove Password",
		"tooltip" => "This tournament currently has a password in order for members to join.  Mark the check box to remove the password.",
		"sortorder" => $lastComponentOrder,
		"value" => 1,
		"attributes" => array("class" => "formInput textBox"),
		"validate" => array("removeTournamentPassword")
	);
	$arrComponents['submit']['sortorder'] = $lastComponentOrder+1;
}

$arrComponents['submit']['value'] = "Update Tournament";





$setupFormArgs['saveType'] = "update";
$setupFormArgs['prefill'] = true;
$setupFormArgs['description'] = "Use the form below to edit tournament info.";
$setupFormArgs['saveMessage'] = "Successfully edited tournament info!";
$setupFormArgs['attributes']['action'] = MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tournamentInfo['tournament_id']."&pID=EditTournamentInfo";
$setupFormArgs['components'] = $arrComponents;
$setupFormArgs['skipPrefill'] = array("startdate", "requirereplay", "tournamentpw");
$setupFormArgs['name'] .= "-editinfo";
$setupFormArgs['saveLink'] = MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id'];

unset($setupFormArgs['saveAdditional']['password']);

function removeTournamentPassword() {
	global $formObj;

	if ($_POST['removepw'] == 1) {
		$formObj->saveAdditional['password'] = "";
	}

}


function resetTournamentPassword() {
	global $formObj;

	if ($_POST['tournamentpw'] != "" && $_POST['removepw'] != 1) {
		$formObj->saveAdditional['password'] = md5($_POST['tournamentpw']);
	}
}