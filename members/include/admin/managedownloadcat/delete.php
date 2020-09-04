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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");
include_once("../../../../classes/downloadcategory.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$downloadCatObj = new DownloadCategory($mysqli);


$cID = $consoleObj->findConsoleIDByName("Manage Download Categories");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $downloadCatObj->select($_POST['catID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$downloadCatInfo = $downloadCatObj->get_info_filtered();
		
		$arrCats = $downloadCatObj->getAssociateIDs();
		
		if(count($arrCats) > 0) {
			
			echo "<div id='newDeleteMessage' style='display: none'><p align='center'>There are currently downloads with the download category <b>".$downloadCatInfo['name']."</b>.  Please move all downloads out of this category before deleting it.</p></div>";
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#deleteMessage').dialog('close');
						$('#newDeleteMessage').dialog({
						
							title: 'Manage Download Categories - Delete',
							modal: true,
							zIndex: 9999,
							resizable: false,
							show: 'scale',
							width: 400,
							buttons: {
								'OK': function() {
									$(this).dialog('close');
								}
							}
						});
					
					});
				</script>
			";
		
			
		}
		elseif($_POST['confirm'] == "1") {
			
			$downloadCatObj->delete();
			include("main.php");
			
		}
		else {
			echo "<p align='center'>Are you sure you want to delete the download category <b>".$downloadCatInfo['name']."</b>?</p>";
		}
		
	}
	elseif(!$downloadCatObj->select($_POST['catID'])) {
	
		echo "<p align='center'>Unable find the selected download category.  Please try again or contact the website administrator.</p>";
	
	}
	
	
	
	
}


?>