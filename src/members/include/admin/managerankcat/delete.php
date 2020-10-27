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
include_once("../../../../classes/rankcategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$rankCatObj = new RankCategory($mysqli);

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Rank Categories");
$consoleObj->select($cID);
$_GET['cID'] = $cID;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $rankCatObj->select($_POST['rID'])) {
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$rankCatInfo = $rankCatObj->get_info_filtered();
		
		$arrRanks = $rankCatObj->getAssociateIDs();
		
		if(count($arrRanks) > 0) {
			
			echo "<div id='newDeleteMessage' style='display: none'><p align='center'>There are currently ranks under the rank category <b>".$rankCatInfo['name']."</b>.  Please change all ranks with this category before deleting it.</p></div>";
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#deleteMessage').dialog('close');
						$('#newDeleteMessage').dialog({
						
							title: 'Manage Rank Categories - Delete',
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
		elseif(isset($_POST['confirm']) && $_POST['confirm'] == "1") {
			
			$rankCatObj->delete();
			include("main.php");
			
		}
		else {
			echo "<p align='center'>Are you sure you want to delete the rank category <b>".$rankCatInfo['name']."</b>?";
		}
		
	}
	elseif(!$rankCatObj->select($_POST['rID'])) {
	
		echo "<p align='center'>Unable find the selected rank category.  Please try again or contact the website administrator.</p>";
	
	}
	
	
	
	
}


?>