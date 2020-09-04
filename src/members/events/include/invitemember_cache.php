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



include("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");
include_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$objInviteMember = new Member($mysqli);

$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->select($_SESSION['btEventID'])) {

	$eventID = $eventObj->get_info("event_id");

	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "invitemembers") || $memberInfo['rank_id'] == 1)) {

		$arrInviteList = $eventObj->getInvitedMembers(true);
		$arrInviteList = array_merge($arrInviteList, $_SESSION['btInviteList']);
		
		switch($_POST['action']) {
		
			case "add":
				
				if($objInviteMember->select($_POST['memberID'])) {
					$inviteMemberInfo = $objInviteMember->get_info_filtered();
					
					if(!in_array($inviteMemberInfo['member_id'], $arrInviteList)) {
						$_SESSION['btInviteList'][] = $inviteMemberInfo['member_id'];
					}
					else {
						
						echo "
							<div id='dupInviteDiv' style='display: none'>
								<p class='main' align='center'>
									The selected member, <b>".$inviteMemberInfo['username']."</b> is already on the invite list!
								</p>
							</div>
							
							<script type='text/javascript'>
								$(document).ready(function() {
								
									$('#dupInviteDiv').dialog({
										title: 'Invite Members - Error',
										modal: true,
										zIndex: 99999,
										show: 'scale',
										resizable: false,
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
					
					
				}
				
				
				
				
				break;
			case "delete":
				unset($_SESSION['btInviteList'][$_POST['memberID']]);
				break;
			
			
			
		}
		
		
		foreach($_SESSION['btInviteList'] as $key => $value) {
			$objInviteMember->select($value);
		
			echo "
				<div class='mttPlayerSlot' style='width: 95%'>".$objInviteMember->get_info_filtered("username")."<div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"removeMember('".$key."')\">X</a></div></div>
			";
		
		}
		
		if(count($_SESSION['btInviteList']) == 0) {
			echo "<p align='center'><i>- Empty -</i></p>";	
		}
		
		$arrInvitedMembers = $eventObj->getInvitedMembers(true);
		$arrInvitedMembers = array_merge($arrInvitedMembers, $_SESSION['btInviteList']);
		
		
		$sqlInvitedMembers = "('".implode("','", $arrInvitedMembers)."')";
		$memberoptions = "<option value=''>Select</option>";
		$result = $mysqli->query("SELECT m.username,m.member_id,r.ordernum,r.name FROM ".$dbprefix."members m, ".$dbprefix."ranks r WHERE m.rank_id = r.rank_id AND m.member_id NOT IN ".$sqlInvitedMembers." AND m.disabled = '0' AND m.rank_id != '1' ORDER BY r.ordernum DESC");
		while($row = $result->fetch_assoc()) {
			$memberoptions .= "<option value='".$row['member_id']."'>".filterText($row['name'])." ".filterText($row['username'])."</option>";
		}
		
		
		echo "
			<div id='newMemberOptionsDiv' style='display: none'>".$memberoptions."</div>
			<script type='text/javascript'>
		
				$(document).ready(function() {
					$('#selectMemberID').html($('#newMemberOptionsDiv').html());
				});
		
			</script>
		";

		
	}
	
	
}

?>
