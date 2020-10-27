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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/squad.php");

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$pID = "manageranks";
$squadObj = new Squad($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($member->get_info("member_id"), $pID)) {

	$squadInfo = $squadObj->get_info_filtered();
	$memberInfo = $member->get_info_filtered();
	$intFounderRankID = $squadObj->getFounderRankID();
	
	
	if($squadObj->objSquadRank->select($_POST['rID']) && $_POST['rID'] != $intFounderRankID) {
		
		$squadRankInfo = $squadObj->objSquadRank->get_info_filtered();
		
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."squads_members WHERE squad_id = '".$squadRankInfo['squad_id']."' AND squadrank_id = '".$squadRankInfo['squadrank_id']."'");
		$totalMembers = $result->num_rows;
		
		if($totalMembers > 0) {
			echo "hi
				<div id='newDeleteMessage' style='display: none'>
				<p align='center' class='main'>
					There are currently members with the squad rank of <b>".$squadRankInfo['name']."</b>.  Please change all members with this rank before deleting it.
				</p>
				</div>
				
				<script type='text/javascript'>
					$(document).ready(function() {
					
						$('#deleteMessage').dialog('close');
						
						$('#newDeleteMessage').dialog({
						
							title: 'Manage Squad Ranks - Delete',
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
		elseif($totalMembers == 0 && $_POST['confirm'] == 1) {
			
			$squadObj->objSquadRank->delete();
			
			include("ranklist.php");
			
		}
		else {

			echo "
				<p align='center' class='main'>
					Are you sure you want to delete the rank: <b>".$squadRankInfo['name']."</b>?
				</p>
			";
			
		}
		
	}
	
	
	
}

?>