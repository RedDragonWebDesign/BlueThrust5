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

include("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/pmfolder.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pmFolderObj = new PMFolder($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage PM Folders");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {

	$memberInfo = $member->get_info_filtered();
	$arrSpecialFolders = array(0, -1, -2);
	$pmFolderObj->intMemberID = $memberInfo['member_id'];
	if($member->hasAccess($consoleObj) && $pmFolderObj->select($_POST['folder']) && $pmFolderObj->isMemberFolder() && !in_array($_POST['folder'], $arrSpecialFolders)) {
		$folderInfo = $pmFolderObj->get_info_filtered();
		// Check if folder has contents
		$arrFolderContents = $pmFolderObj->getFolderContents();
		if(count($arrFolderContents[0]) > 0) {

			echo "
				<div id='showFolderError'>
					<p class='main' align='center'>Before you can delete this folder you must move all of its contents to another folder.</p>
				</div>
			
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#showFolderError').dialog({
							title: 'Delete PM Folder',
							width: 400,
							show: 'scale',
							modal: true,
							zIndex: 99999,
							resizable: false,
							buttons: {
								'OK': function() {
									$(this).dialog('close');							
								},
	
							}						
						});
					});
				</script>
			";
			
		}
		else {
			
			$pmFolderObj->delete();
			
		}
				
		
		define("SHOW_FOLDERLIST", true);
		include("folderlist.php");
		

	}
	
}
		
?>