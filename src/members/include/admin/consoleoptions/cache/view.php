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


include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/rank.php");



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$consoleObj = new ConsoleOption($mysqli);

$intAddConsoleCID = $consoleObj->findConsoleIDByName("Add Console Option");
$consoleObj->select($intAddConsoleCID);
$checkAccess1 = $member->hasAccess($consoleObj);


$intManageConsoleCID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($intManageConsoleCID);
$checkAccess2 = $member->hasAccess($consoleObj);


$rank = new Rank($mysqli);
if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($checkAccess1 || $checkAccess2) {


		echo "
		
			<table align='left' border='0' cellspacing='2' cellpadding='2' width=\"90%\">
				<tr>
					<td class='formTitle' width=\"60%\">Member:</td>
					<td class='formTitle' width=\"20%\">Access:</td>
					<td class='formTitle' width=\"20%\">Actions:</td>
				</tr>
				
			";
		
		$counter=0;
		foreach($_SESSION['btAccessRules'] as $key => $accessInfo) {
			if($member->select($accessInfo['mID']) AND ($accessInfo['accessRule'] == "allow" OR $accessInfo['accessRule'] == "deny")) {
		
				$tempMemInfo = $member->get_info_filtered();
				$rank->select($tempMemInfo['rank_id']);
				$dispRankName = $rank->get_info_filtered("name");
				
				if($accessInfo['accessRule'] == "allow") { 
					$dispAccess = "<span class='allowText'>Allow</span>";
				}
				else {
					$dispAccess = "<span class='denyText'>Deny</span>";
				}
				
				echo "
					<tr>
						<td class='main'><a href='".$MAIN_ROOT."profile.php?mID=".$tempMemInfo['username']."'>".$dispRankName." ".$tempMemInfo['username']."</a></td>
						<td class='main' align='center'>".$dispAccess."</td>
						<td class='main' align='center'><a href='javascript:void(0)' onclick=\"deleteAccessRule('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete'></a></td>
					</tr>
				";
				$counter++;
			}
		}
		
		if($counter == 0) {
			echo "
				<tr>
					<td class='main' colspan='3'>
						<p align='center' style='padding-top: 10px'><i>No special member access rules set!</i></p>
					</td>
				</tr>
			";
			
		}
		echo "
				
			</table>
		
		";
		
	}
	
	
}


?>