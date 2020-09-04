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
include_once("../../../classes/tournament.php");
include_once("../../../classes/squad.php");

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Tournaments");
$consoleObj->select($cID);


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$countErrors = 0;
$dispError = "";

$tournamentObj = new Tournament($mysqli);
$squadObj = new Squad($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && $squadObj->select($_POST['squadID']) && $tournamentObj->objTeam->select($_POST['teamID']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info_filtered();
	$teamInfo = $tournamentObj->objTeam->get_info_filtered();
	$tournamentObj->select($teamInfo['tournament_id']);
	$tournamentInfo = $tournamentObj->get_info();
	if($tournamentInfo['member_id'] == $memberInfo['member_id'] || $memberInfo['rank_id'] == 1 || $tournamentObj->isManager($memberInfo['member_id'])) {
		
		$arrSquadMembers = $squadObj->getMemberListSorted();
	
		echo "
			<input type='hidden' value='".$squadObj->get_info_filtered("name")."' id='squadName'>
			<div id='squadMemberList' style='max-height: 200px; overflow-y: auto'>
			<table class='formTable' style='width: 95%'>
				<tr>
					<td></td>
					<td class='main' align='center'>
						<a href='javascript:void(0)' id='checkAllLink'>Check All</a>
					</td>
				</tr>
			";
		
		foreach($arrSquadMembers as $value) {
			if($member->select($value)) {
				$tempMemberInfo = $member->get_info_filtered();
				echo "
					<tr>
						<td class='formLabel'>".$tempMemberInfo['username']."</td>
						<td class='main' align='center'><input type='checkbox' value='".$tempMemberInfo['member_id']."'></td>
					</tr>
				";
			}
		}
		$member->select($memberInfo['member_id']);
		echo "
			</table>
			</div>
			
			<script type='text/javascript'>
			
				var intCheckAll = 1;
			
				$(document).ready(function() {
				
				
					$('#checkAllLink').click(function() {
					
						$('#squadMemberList input[type=checkbox]').each(function() {
							if(intCheckAll == 1) {
								$(this).attr('checked', true);
							}
							else {
								$(this).attr('checked', false);
							}
						});
						
						if(intCheckAll == 1) {
							intCheckAll = 0;
							$('#checkAllLink').html('Uncheck All');
						}
						else {
							intCheckAll = 1;
							$('#checkAllLink').html('Check All');
						}
						
					});
				
				});
			
			</script>
			
		";
		
	}
	
}



?>