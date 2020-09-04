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
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$rankInfo = $memberRank->get_info_filtered();
$cID = $_GET['cID'];

if($memberInfo['rank_id'] == 1) {

	$maxOrderNum = $mysqli->query("SELECT MAX(ordernum) FROM ".$dbprefix."ranks WHERE rank_id != '1'");
	$arrMaxOrderNum = $maxOrderNum->fetch_array(MYSQLI_NUM);

	if($maxOrderNum->num_rows > 0) {
		$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE ordernum = '".$arrMaxOrderNum[0]."'");
		$row = $result->fetch_assoc();
		$rankInfo['promotepower'] = $row['rank_id'];
	}

}

$rankObj = new Rank($mysqli);
$rankObj->select($rankInfo['promotepower']);
$maxRankInfo = $rankObj->get_info_filtered();

$arrRanks = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrRanks[] = $row['rank_id'];
}

$setRankCID = $consoleObj->findConsoleIDByName("Set Member's Rank");
$consoleObj->select($setRankCID);
$dispSetRank = false;
if($member->hasAccess($consoleObj)) {
	
	// Get Ranks
	$sqlRanks = "('".implode("','", $arrRanks)."')";
	
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id IN ".$sqlRanks." AND rank_id != '1' ORDER BY ordernum");
	while($row = $result->fetch_assoc()) {
		$rankOptions[$row['rank_id']] = filterText($row['name']);
	}
	
	$dispSetRank = true;
	
}
$consoleObj->select($cID);

$i=0;
$arrComponents = array(

	"newmember" => array(
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput"),
		"display_name" => "New Member",
		"value" => $websiteInfo['clantag'],
		"validate" => array("NOT_BLANK"),
		"db_name" => "username"
	),
	"password" => array(
		"type" => "password",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "id" => "newpassword"),
		"display_name" => "Password",
		"html" => "<br><label class='formLabel' style='display: inline-block'></label><div class='formInput tinyFont' style='margin-top: 0px; padding-left: 5px'>(Minimum 4 characters)</div>",
		"validate" => array(
			array("name" => "CHECK_LENGTH", "min_length" => 4)
		)
	),
	"password2" => array(
		"type" => "password",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "id" => "newpassword2"),
		"display_name" => "Re-type Password",
		"html" => "<div class='formInputSideText successFont formInput' id='checkPassword'></div>"
	),
	"submit" => array(
		"type" => "submit",
		"sortorder" => 999999,
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"value" => "Add New Member"
	)

);


if($dispSetRank) {
	$arrComponents['set_rank'] = array(
		"type" => "select",
		"display_name" => "Starting Rank",
		"options" => $rankOptions,
		"attributes" => array("class" => "textBox formInput"),
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"db_name" => "rank_id"
	);
}


$checkPasswordJS = "
$(document).ready(function() {
			
	$('#newpassword2').keyup(function() {
		
		if($('#newpassword').val() != \"\") {
		
			if($('#newpassword2').val() == $('#newpassword').val()) {
				$('#checkPassword').toggleClass('successFont', true);
				$('#checkPassword').toggleClass('failedFont', false);
				$('#checkPassword').html('ok!');
			}
			else {
				$('#checkPassword').toggleClass('successFont', false);
				$('#checkPassword').toggleClass('failedFont', true);
				$('#checkPassword').html('error!');
			}
		
		}
		else {
			$('#checkPassword').html('');
		}
	
	});

});
";

$newMemberObj = new Member($mysqli);
$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"saveMessage" => "Successfully added new member: <b>".filterText($_POST['newmember'])."</b>!",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
	"description" => "Fill out the form below to add a new member.",
	"embedJS" => $checkPasswordJS,
	"saveType" => "add",
	"saveObject" => $newMemberObj,
	"saveAdditional" => array(
		"datejoined" => time(),
		"recruiter" => $memberInfo['member_id'],
		"lastlogin" => time(),
		"postsperpage" => $websiteInfo['forum_postsperpage'],
		"topicsperpage" => $websiteInfo['forum_topicsperpage']
	),
	"afterSave" => array("addMemberSavePassword")
);


$result = $mysqli->query("SELECT * FROM ".$dbprefix."members ORDER BY datejoined DESC LIMIT 1");
$row = $result->fetch_assoc();

$member->select($row['member_id']);
$dispLastMember = $member->getMemberLink();

$dispLastMemberTime = getPreciseTime($row['datejoined']);

$member->select($memberInfo['member_id']);

function addMemberSavePassword() {
	global $formObj;
	
	$formObj->objSave->set_password($_POST['password']);
}

?>

<div class='main' style='padding-left: 15px; padding-bottom: 0px; margin-bottom: 0px'><b>Last Member Added:</b> <?php echo $dispLastMember; ?> - <?php echo $dispLastMemberTime; ?></div>
	