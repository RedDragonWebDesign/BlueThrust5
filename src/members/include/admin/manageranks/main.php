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

if(!isset($prevFolder) || $prevFolder == "") {
	$prevFolder = "../../../../";	
}

include_once($prevFolder."_setup.php");

// Classes needed for console.php
include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/rankcategory.php");
include_once($prevFolder."classes/consoleoption.php");


$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {
		$cOptObj = new ConsoleOption($mysqli);
		if(!isset($_GET['cID'])) {
			$_GET['cID'] = 	$cOptObj->findConsoleIDByName("Manage Ranks");
		}

		$cOptObj->select($_GET['cID']);
		
		$intAddNewRankCID = $cOptObj->findConsoleIDByName("Add New Rank");

		$memberInfo = $member->get_info();

		if($member->hasAccess($cOptObj)) {

			echo "
			<script type='text/javascript'>
			
			$(document).ready(function() {
				$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Ranks\");
			});
			
			</script>
			";

			$cOptObj = new ConsoleOption($mysqli);
			$intAddNewRankCID = $cOptObj->findConsoleIDByName("Add New Rank");
			
			
			$x = 0;
			$counter = 0;
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' ORDER BY ordernum DESC");
			$dispRanks = "";
            while($row = $result->fetch_assoc()) {
				if($counter == 1) {
					$addCSS = " alternateBGColor";
					$counter = 0;
				}
				else {
					$addCSS = "";
					$counter = 1;
				}
			
				$dispRanks .= "
				<tr>
				<td class='dottedLine".$addCSS."' width=\"80%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&rID=".$row['rank_id']."&action=edit'>".$row['name']."</a></b></td>
				<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='console.php?cID=".$cID."&rID=".$row['rank_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' title='Edit Rank'></a></td>
				<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='javascript:void(0)' onclick=\"deleteRank('".$row['rank_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete Rank'></a></td>
				</tr>
				";
				
				$x++;
			}
			
		
			if($x == 0) {
				$dispRanks = "<tr><td colspan='3' align='center'><br><p class='main'><i>No ranks added yet!</i></p></td></tr>";	
			}
			
			echo "
			<div id='contentDiv'>
			
			<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
				<tr>
					<td class='main' colspan='2' align='right'>
						&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewRankCID."'>Add New Rank</a> &laquo;<br><br>
					</td>
				</tr>
				<tr>
					<td class='formTitle' width=\"80%\">Rank Name:</td>
						<td class='formTitle' width=\"20%\">Actions:</td>
				</tr>
			</table>
			<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
				<tr><td colspan='3' class='dottedLine'></td></tr>
				".$dispRanks."
			</table>
			
			</div><br><br><br>
			";

		}
	}
}

?>