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


// Config File
$prevFolder = "../";

include($prevFolder."_setup.php");
include($prevFolder."classes/member.php");

if(!isset($_GET['code'])) {

	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."'
		</script>
	";
	exit();
}


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


$filterCode = $mysqli->real_escape_string($_GET['code']);
// Start Page
$PAGE_NAME = "Diplomacy Request";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$confirmMessage = "E-mail Code Not Found!";
$result = $mysqli->query("SELECT diplomacyrequest_id FROM ".$dbprefix."diplomacy_request WHERE confirmemail = '".$filterCode."'");
if($result->num_rows > 0) {
	$diplomacyRequestObj = new Basic($mysqli, "diplomacy_request", "diplomacyrequest_id");
	$row = $result->fetch_assoc();
	$diplomacyRequestObj->select($row['diplomacyrequest_id']);
	if($diplomacyRequestObj->update(array("confirmemail"), array("1"))) {
		
		$confirmMessage = "E-mail Address Confirmed!<br><br>Please wait for your application to be reviewed by a diplomacy manager.  You will be e-mailed when a decision is made.";

	}
	else {
		$confirmMessage = "Unable to save information to the database.  Please contact the website administrator.";
	}
}


echo "

	<div id='confirmDialogBox' style='display: none'>
		<p class='main' align='center'>
			".$confirmMessage."
		</p>
	</div>
	
	<script type='text/javascript'>
		popupDialog('Diplomacy Request', '".$MAIN_ROOT."', 'confirmDialogBox');
	</script>


";


include($prevFolder."themes/".$THEME."/_footer.php");



?>