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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

include_once($prevFolder."classes/squad.php");
$cID = $_GET['cID'];


$squadObj = new Squad($mysqli);
$counter = 0;
$dispSquadNames = "";
$arrSquads = $member->getSquadList();

if($squadObj->getManageAllStatus()) {
	$arrSquads = array();
	$result = $mysqli->query("SELECT squad_id FROM ".$dbprefix."squads ORDER BY name");
	while($row = $result->fetch_assoc()) {
		$arrSquads[] = $row['squad_id'];
	}
}

$clickCounter = 0;
if(count($arrSquads) > 0) {

	foreach($arrSquads as $squadID) {
		
		if($squadObj->select($squadID)) {
			$intSquadMemberID = $squadObj->getSquadMemberID($memberInfo['member_id']);
			
			if($squadObj->objSquadMember->select($intSquadMemberID) || $squadObj->getManageAllStatus()) {
				$squadMemberInfo = $squadObj->objSquadMember->get_info_filtered();
				
				if($squadObj->objSquadRank->select($squadMemberInfo['squadrank_id']) || $squadObj->getManageAllStatus()) {
				
					$squadRankInfo = $squadObj->objSquadRank->get_info_filtered();
					
					$categoryCSS = "consoleCategory_clicked";
					$hideoptions = "";
					if($counter > 0) {
						$hideoptions = "style='display: none'";
						$categoryCSS = "consoleCategory";
					}
					$counter++;
					$squadInfo = $squadObj->get_info_filtered();
					
					if($_GET['select'] == $squadInfo['squad_id']) {
						$clickCounter = $counter;
					}
					
					$dispSquadNames .= "<div class='".$categoryCSS."' style='width: 200px; margin: 3px' id='categoryName".$counter."' onmouseover=\"moverCategory('".$counter."')\" onmouseout=\"moutCategory('".$counter."')\" onclick=\"selectCategory('".$counter."')\">".$squadInfo['name']."</div>";
					$dispSquadOptions .= "<div id='categoryOption".$counter."' ".$hideoptions.">";
					$dispSquadOptions .= "
					<div class='dottedLine' style='padding-bottom: 3px; margin-bottom: 5px'>
					<b>Manage Squad - ".$squadInfo['name']."</b>
					</div>
					<div style='padding-left: 5px'>
					";
					
					
					$arrSquadOptions = array("postnews", "managenews", "manageshoutbox", "addrank", "manageranks", "setrank", "editprofile", "sendinvites", "acceptapps",  "removemember");
					$arrSquadOptionsPageID = array("PostNews", "ManageNews", "ManageShoutbox", "AddRank", "ManageRanks", "SetRank", "EditProfile", "SendInvites", "ViewApps",  "RemoveMember");
					$arrSquadOptionsDispName = array("Post News", "Manage News", "Manage Shoutbox Posts", "Add Rank", "Manage Ranks", "Set Member Rank", "Edit Squad Profile", "Send Squad Invite", "View Applications", "Remove Member");
					foreach($arrSquadOptions as $key=>$squadOption) {
					
						if($squadRankInfo[$squadOption] == 1 || $squadObj->getManageAllStatus()) {
							
							$dispSquadOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$squadInfo['squad_id']."&pID=".$arrSquadOptionsPageID[$key]."'>".$arrSquadOptionsDispName[$key]."</a><br>";
							
						}
					
					}
					
					$dispSquadOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>View Squad Profile</a><br>";
					
					if($squadInfo['member_id'] == $memberInfo['member_id'] || $squadObj->getManageAllStatus()) {
						$dispSquadOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$squadInfo['squad_id']."&pID=CloseSquad'>Close Squad</a><br>";
					}
					else {
						$dispSquadOptions .= "<b>&middot;</b> <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$squadInfo['squad_id']."&pID=LeaveSquad'>Leave Squad</a><br>";
					}
					
					
					$dispSquadOptions .= "</div></div>";
					
					
				
				}
			}
			
			
			
		}
		
	}
	
	echo "
	
		<div style='float: left; text-align: left; width: 225px; padding: 10px 0px 0px 40px'>
			$dispSquadNames
		</div>
		<div style='float: right; text-align: left; width: 300px; padding: 10px 40px 0px 10px'>
			$dispSquadOptions
		</div>
	
		<div style='clear:both; height: 30px; margin-top: 20px'></div>

	";
	
	if($clickCounter != 0) {
		
		echo "
			<script type='text/javascript'>
				selectCategory('".$clickCounter."');
			</script>
		";
		
	}
	
}
else {
	$intApplyToSquadCID = $consoleObj->findConsoleIDByName("Apply to a Squad");
	echo "
	<div class='shadedBox' style='width: 400px; margin-top: 50px; margin-bottom: 50px; margin-left: auto; margin-right: auto;'>
		<p align='center' class='main'>
			<i>You currently do not belong to any squads!  You may <a href='console.php?cID=".$intApplyToSquadCID."'>Apply to a Squad</a>, if you wish to join one.
		</p>
	</div>
	";
	
	
}