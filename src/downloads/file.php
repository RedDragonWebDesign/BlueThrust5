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

$prevFolder = "../";
include($prevFolder."_setup.php");

include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/downloadcategory.php");
include_once($prevFolder."classes/download.php");


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


$LOGGED_IN = false;
if(isset($_SESSION['btUsername']) AND isset($_SESSION['btPassword'])) {
	$memberObj = new Member($mysqli);
	if($memberObj->select($_SESSION['btUsername'])) {
		if($memberObj->authorizeLogin($_SESSION['btPassword'])) {
			$LOGGED_IN = true;
		}
	}
}

$downloadCatObj = new DownloadCategory($mysqli);
$downloadObj = new Download($mysqli);
$blnShowDownload = false;

if($downloadObj->select($_GET['dID'])) {
	
	$downloadInfo = $downloadObj->get_info_filtered();
	$downloadCatObj->select($downloadInfo['downloadcategory_id']);
	
	$accessType = $downloadCatObj->get_info("accesstype");
	
	
	if($accessType == 1 && $LOGGED_IN) {
		$blnShowDownload = true;	
	}
	elseif($accessType == 0) {
		$blnShowDownload = true;	
	}
	
	$fileContents1 = file_get_contents($prevFolder.$downloadInfo['splitfile1']);
	$fileContents2 = file_get_contents($prevFolder.$downloadInfo['splitfile2']);
	
	if($blnShowDownload && $fileContents1 !== false && $fileContents2 !== false) {
		
		$numOfHits = $downloadObj->get_info("downloadcount")+1;
		$downloadObj->update(array("downloadcount"), array($numOfHits));
		
		header("Content-Description: File Transfer");
		header("Content-Length: ".$downloadInfo['filesize'].";");
		header("Content-disposition: attachment; filename=".$downloadInfo['filename']);
		header("Content-type: ".$downloadInfo['mimetype']);

		echo $fileContents1.$fileContents2;
		
	}
	else {
		echo "File Not Found!";
	}

}


if(!$blnShowDownload) {
	
	// Start Page
	$PAGE_NAME = "Download - ";
	$dispBreadCrumb = "";
	include($prevFolder."themes/".$THEME."/_header.php");

	echo "
		<div class='breadCrumbTitle'>Download</div>
		<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px; margin-bottom: 20px'>
		<a href='".$MAIN_ROOT."'>Home</a> > Download
		</div>
		
		<div class='shadedBox main' style='text-align: center; margin: 20px auto; width: 50%'>
			<p>
			Unable download file!
			</p>
		</div>
	";
	
	include($prevFolder."themes/".$THEME."/_footer.php");
	
}


?>