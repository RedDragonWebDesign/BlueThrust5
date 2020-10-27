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

include_once($prevFolder."_setup.php");

$downloadCatObj = new DownloadCategory($mysqli);
$downloadObj = new Download($mysqli);

if(!$downloadCatObj->select($_GET['catID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
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

$downloadCatInfo = $downloadCatObj->get_info_filtered();

// Start Page
$PAGE_NAME = $downloadCatInfo['name']." - Downloads - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($downloadCatInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Downloads: ".$downloadCatInfo['name']);
include($prevFolder."include/breadcrumb.php");

$posterMemberObj = new Member($mysqli);
$arrDownloads = $downloadCatObj->getAssociateIDs("ORDER BY dateuploaded DESC");
foreach($arrDownloads as $dlID) {
	$downloadObj->select($dlID);
	$downloadInfo = $downloadObj->get_info_filtered();
	$posterMemberObj->select($downloadInfo['member_id']);
	$posterInfo = $posterMemberObj->get_info_filtered();
	
	if($posterInfo['avatar'] == "") {
		$posterInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
	}
	else {
		$posterInfo['avatar'] = $MAIN_ROOT.$posterInfo['avatar'];
	}
	
	
	$dispFileSize = $downloadInfo['filesize']/1024;
	
	if($dispFileSize < 1) {
		$dispFileSize = $downloadInfo['filesize']."B";
	}
	elseif(($dispFileSize/1024) < 1) {
		$dispFileSize = round($dispFileSize, 2)."KB";
	}
	else {
		$dispFileSize = round(($dispFileSize/1024),2)."MB";
	}
	
	$addS = ($downloadInfo['downloadcount'] == 1) ? "" : "s";
	echo "
		<div class='downloadDiv'>
					
		<div class='downloadInfo'>
			<div style='float: left'><img src='".$posterInfo['avatar']."' class='avatarImg'></div>
			<div style='float: left; margin-left: 15px'>posted by ".$posterMemberObj->getMemberLink()." - ".getPreciseTime($downloadInfo['dateuploaded'])." - downloaded ".$downloadInfo['downloadcount']." time".$addS."<br>
			<span class='nameText'>".$downloadInfo['name']."</span></div>
			<div style='clear: both'></div>
		</div>
		<br>
		<div class='dottedLine' style='margin-top: 5px'></div>
		<div class='downloadDescription'>
			File Name: ".$downloadInfo['filename']."<br>
			File Size: ".$dispFileSize."<br><br>
			".nl2br(parseBBCode($downloadInfo['description']))."
		</div>
		<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
		<p style='padding: 0px; margin: 0px' align='right'><b><a href='".$MAIN_ROOT."downloads/file.php?dID=".$downloadInfo['download_id']."'>Download</a></b></p>
	</div>
	";
}

if(count($arrDownloads) == 0) {
	
	echo "<div class='shadedBox' style='width: 50%; margin: 20px auto'><p align='center' class='main'><i>No downloads added to ".$downloadCatInfo['name']." yet!</i></p></div>";
	
}

include($prevFolder."themes/".$THEME."/_footer.php");

?>