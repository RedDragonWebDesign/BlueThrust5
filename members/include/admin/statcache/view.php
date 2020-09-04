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



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$manageCID = $consoleObj->findConsoleIDByName("Manage Games Played");
$consoleObj->select($manageCID);

$checkAccess1 = $member->hasAccess($consoleObj);

$addCID = $consoleObj->findConsoleIDByName("Add Games Played");
$consoleObj->select($addCID);

$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = $checkAccess1 || $checkAccess2;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($checkAccess) {
		

		
		if(is_array($_SESSION['btStatCache']) AND count($_SESSION['btStatCache']) > 0) {
			
			echo "
			
				<table align='left' border='0' cellspacing='2' cellpadding='2' width=\"90%\">
					<tr>
						<td class='formTitle'>Stat Name:</td>
						<td class='formTitle'>Stat Type:</td>
						<td class='formTitle'>Formula:</td>
						<td class='formTitle'>Actions:</td>
					</tr>
				";
				
			$counter = 0;
			$totalStats = count($_SESSION['btStatCache']);
			foreach($_SESSION['btStatCache'] as $key => $statInfo) {
				$statInfo = filterArray($statInfo);
				$counter++;
				
				$statType = "Input";
				$dispFormula = "<i>none</i>";
				if($statInfo['statType'] == "calculate") {
						$statType = "Auto-Calculated";
						
						$dispFirstStat = filterText($_SESSION['btStatCache'][$statInfo['firstStat']]['statName']);
						$dispSecondStat = filterText($_SESSION['btStatCache'][$statInfo['secondStat']]['statName']);
						
						$dispOp = "";
						switch($statInfo['calcOperation']) {
							case "add":
								$dispOp = " + ";
								break;
							case "sub":
								$dispOp = " - ";
								break;
							case "mul":
								$dispOp = " x ";
								break;
							case "div":
								$dispOp = " / ";
								break;
						}
						
						$dispFormula = $dispFirstStat.$dispOp.$dispSecondStat;
						
				}
				
				$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveStat('up', '".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' title='Move Up' width='24' height='24'></a>";
				if($counter == 1) {
					$dispUpArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
				}
				
				$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveStat('down', '".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' title='Move Down' width='24' height='24'></a>";
				if($totalStats == $counter) {
					$dispDownArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
				}
					
				echo "
					<tr>
						<td class='main'>".$statInfo['statName']."</td>
						<td class='main'>".$statType."</td>
						<td class='main'>".$dispFormula."</td>
						<td class='main'>
							".$dispUpArrow.$dispDownArrow."
							<a href='javascript:void(0)' onclick=\"editStat('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' title='Edit' width='24' height='24'></a>
							<a href='javascript:void(0)' onclick=\"deleteStat('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete' width='24' height='24'></a>
						</td>
					</tr>
				";
				
				
			}
			
			echo "
				</table>
			";

		}
		else {
			echo "<i>No Stats Added Yet!</i>";	
		}
		
		
	}
	
}




?>