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

$prevFolder = "../../";
include_once("../../_setup.php");
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/btplugin.php");
include_once("../../classes/consolecategory.php");

// Plugin Info

$PLUGIN_TABLE_NAME = $dbprefix."youtube";
$PLUGIN_NAME = "Youtube Connect";

$arrAPIKeys = array(
	'clientID' => "",
	'clientSecret' => ""
);


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pluginObj = new btPlugin($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$countErrors = 0;
	$dispError = array();
	
	// Check if already installed
	
	if(in_array($_POST['pluginDir'], $pluginObj->getPlugins("filepath"))) {
		$countErrors++;
		$dispError[] = "The selected plugin is already installed!";
	}
	
	// Check if plugin table name interferes with other tables
	
	$result = $mysqli->query("SHOW TABLES");

	while($row = $result->fetch_array()) {
		if($row[0] == $PLUGIN_TABLE_NAME || $row[0] == $dbprefix."youtube_videos") {
			$countErrors++;
			$dispError[] = "There is database table that conflicts with this plugin.";	
		}
	}
	
	
	
	if($countErrors == 0) {
		$sql = "
		
		CREATE TABLE IF NOT EXISTS `".$dbprefix."youtube` (
		  `youtube_id` int(11) NOT NULL AUTO_INCREMENT,
		  `member_id` int(11) NOT NULL,
		  `channel_id` varchar(255) NOT NULL,
		  `uploads_id` varchar(255) NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `thumbnail` text NOT NULL,
		  `access_token` varchar(255) NOT NULL,
		  `refresh_token` varchar(255) NOT NULL,
		  `allowlogin` int(11) NOT NULL,
		  `showsubscribe` int(11) NOT NULL,
		  `subscribers` int(11) NOT NULL,
		  `videocount` int(11) NOT NULL,
		  `viewcount` int(11) NOT NULL,
		  `showvideos` int(11) NOT NULL,
		  `lastupdate` int(11) NOT NULL,
		  `loginhash` varchar(32) NOT NULL,
		  PRIMARY KEY (`youtube_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	
		
		CREATE TABLE IF NOT EXISTS `".$dbprefix."youtube_videos` (
		  `youtubevideo_id` int(11) NOT NULL AUTO_INCREMENT,
		  `youtube_id` int(11) NOT NULL,
		  `member_id` int(11) NOT NULL,
		  `video_id` varchar(255) NOT NULL,
		  `thumbnail` text NOT NULL,
		  `title` text NOT NULL,
		  `dateuploaded` varchar(255) NOT NULL,
		  PRIMARY KEY (`youtubevideo_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		
		";
	
		
		if($mysqli->multi_query($sql)) {
			
			do {
				if($result = $mysqli->store_result()) {
					$result->free();
				}
			}
			while($mysqli->next_result());
			
			$jsonAPIKey = json_encode($arrAPIKeys);
			$pluginObj->addNew(array("name", "filepath", "dateinstalled", "apikey"), array($PLUGIN_NAME, $_POST['pluginDir'], time(),$jsonAPIKey));
			
			$pluginID = $pluginObj->get_info("plugin_id");
			$pluginObj->pluginPage->setCategoryKeyValue($pluginID);
			$pluginPageSortNum = $pluginObj->pluginPage->getHighestSortNum()+1;
			
			$pluginObj->pluginPage->addNew(array("plugin_id", "page", "pagepath", "sortnum"), array($pluginID, "profile", "plugins/youtube/_profile.php", $pluginPageSortNum));
			
			// Check if need to add new console category
			
			$result = $mysqli->query("SELECT consolecategory_id FROM ".$dbprefix."consolecategory WHERE name = 'Social Media Connect'");
			if($result->num_rows == 0) {
				$consoleCatObj = new ConsoleCategory($mysqli);
				$newOrderNum = $consoleCatObj->getHighestOrderNum()+1;
				$consoleCatObj->addNew(array("name", "ordernum"), array("Social Media Connect", $newOrderNum));
				$consoleCatID = $consoleCatObj->get_info("consolecategory_id");
			}
			else {
				$row = $result->fetch_assoc();
				$consoleCatID = $row['consolecategory_id'];	
			}
			
			$consoleObj->setCategoryKeyValue($consoleCatID);
			$newSortNum = $consoleObj->getHighestSortNum()+1;
			$consoleObj->addNew(array("consolecategory_id", "pagetitle", "filename", "sortnum"), array($consoleCatID, $PLUGIN_NAME, "../plugins/youtube/youtubeconnect.php", $newSortNum));
					
		}
		else {
			$countErrors++;
			$dispError[] = "Unable to create plugin database table.";
		}
	}
	
	
	$arrReturn = array();
	if($countErrors == 0) {
		$arrReturn['result'] = "success";
		$member->logAction("Installed ".$PLUGIN_NAME." Plugin.");
	}
	else {
		$arrReturn['result'] = "fail";	
		$arrReturn['errors'] = $dispError;
	}
	
	
	echo json_encode($arrReturn);
	
}
?>