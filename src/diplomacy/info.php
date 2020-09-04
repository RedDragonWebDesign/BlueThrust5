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

$diplomacyObj = new Basic($mysqli, "diplomacy", "diplomacy_id");

if(!$diplomacyObj->select($_GET['dID'])) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."diplomacy'
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



$diplomacyInfo = $diplomacyObj->get_info_filtered();

$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");


$diplomacyStatusObj->select($diplomacyInfo['diplomacystatus_id']);

$statusInfo = $diplomacyStatusObj->get_info_filtered();


if($statusInfo['imageurl'] == "") {
	$dispStatus = $statusInfo['name'];
}
else {

	if(strpos($statusInfo['imageurl'], "http://") === false) {
		$statusInfo['imageurl'] = "../".$statusInfo['imageurl'];
	}


	$dispImgWidth = "";
	$dispImgHeight = "";
	if($statusInfo['imagewidth'] != 0) {
		$dispImgWidth = " width = '".$statusInfo['imagewidth']."' ";
	}

	if($statusInfo['imageheight'] != 0) {
		$dispImgWidth = " height = '".$statusInfo['imageheight']."' ";
	}

	$dispStatus = "<img src='".$statusInfo['imageurl']."'".$dispImgWidth.$dispImgHeight." title='".$statusInfo['name']."'>";

}


// Start Page
$PAGE_NAME = $diplomacyInfo['clanname']." - Diplomacy - ";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($diplomacyInfo['clanname']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Diplomacy", $MAIN_ROOT."diplomacy");
$breadcrumbObj->addCrumb($diplomacyInfo['clanname']);
include($prevFolder."include/breadcrumb.php");
?>

<div style='margin: 25px auto; '>
	
	<table class='profileTable' style='width: 65%; margin-left: auto; margin-right: auto'>
		<tr>
			<td colspan='2' class='formTitle' align='center' style='padding: 0px; height: 20px; border: 0px'>Diplomacy Information</td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Clan Name:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['clanname']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Date Added:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo getPreciseTime($diplomacyInfo['dateadded']); ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Status:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $dispStatus; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Leader(s):</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['leaders']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Clan Tag:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['clantag']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Website:</td>
			<td class='main' style='padding-left: 10px' valign='top'><a href='<?php echo $diplomacyInfo['website']; ?>' target='_blank'><?php echo $diplomacyInfo['website']; ?></a></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Clan Size:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['clansize']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Skill Level:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['skill']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Games Played:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo $diplomacyInfo['gamesplayed']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>Extra Info:</td>
			<td class='main' style='padding-left: 10px' valign='top'><?php echo nl2br($diplomacyInfo['extrainfo']); ?></td>
		</tr>
	</table>
	
	<p align='center' class='main'><br>
		<a href='<?php echo $MAIN_ROOT; ?>diplomacy'>Return to Diplomacy</a>
	</p>

</div>



<?php
include($prevFolder."themes/".$THEME."/_footer.php");
?>