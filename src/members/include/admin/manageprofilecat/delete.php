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
include_once("../../../../classes/profilecategory.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$profileCatObj = new ProfileCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Categories");
$_GET['cID'] = $cID;
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $profileCatObj->select($_POST['catID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$profileCatInfo = $profileCatObj->get_info_filtered();
		
		$arrCats = $profileCatObj->getAssociateIDs();
		
		if(count($arrCats) > 0) {
			
			echo "<div id='newDeleteMessage' style='display: none'><p align='center'>There are currently profile options with the profile category <b>".$profileCatInfo['name']."</b>.  Please move all profile options out of this category before deleting it.</p></div>";
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#deleteMessage').dialog('close');
						$('#newDeleteMessage').dialog({
						
							title: 'Manage Profile Categories - Delete',
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
			
			$profileCatObj->delete();
			include("main.php");
			
		}
		else {
			echo "<p align='center'>Are you sure you want to delete the profile category <b>".$profileCatInfo['name']."</b>?";
		}
		
	}
	elseif(!$profileCatObj->select($_POST['catID'])) {
	
		echo "<p align='center'>Unable find the selected profile category.  Please try again or contact the website administrator.</p>";
	
	}
	
	
	
	
}


?>