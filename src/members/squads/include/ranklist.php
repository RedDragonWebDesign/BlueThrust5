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


// Start Page
if(!isset($consoleObj)) { $consoleObj = new ConsoleOption($mysqli); }

if(!isset($cID)) { $cID = $consoleObj->findConsoleIDByName("View Your Squads"); $consoleObj->select($cID); }


if(!isset($consoleInfo)) { $consoleInfo = $consoleObj->get_info_filtered(); }



if(!isset($member)) { $member = new Member($mysqli); $member->select($_SESSION['btUsername']); }


$pID = "manageranks";

if(!isset($squadObj)) {
	$squadObj = new Squad($mysqli);
	$squadObj->select($_POST['sID']);
}

$arrSquadPrivileges = $squadObj->arrSquadPrivileges;

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $squadObj->memberHasAccess($member->get_info("member_id"), $pID)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	
	
		
	
	
	echo "
	
	<script type='text/javascript'>
	$(document).ready(function() {
	$('#breadCrumbTitle').html(\"Manage Ranks\");
	$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Manage Ranks\");
	});
	</script>
	";
	
	$intFounderRankID = $squadObj->getFounderRankID();
	$intHighestOrder = $squadObj->countRanks();
	$x = 1;
	$counter = 0;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."squadranks WHERE squad_id = '".$squadInfo['squad_id']."' ORDER BY sortnum");
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
	
		$dispDeleteButton = "<a href='javascript:void(0)' onclick=\"deleteRank('".$_GET['sID']."', '".$row['squadrank_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Delete Rank'></a>";
		
		if($row['squadrank_id'] == $intFounderRankID) {
			$dispDeleteButton = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";	
		}
		
		if($x == 1 || $x == 2) {
			$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
		}
		else {
			$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveRank('up', '".$squadInfo['squad_id']."', '".$row['squadrank_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
		}
	
		if($x == $intHighestOrder || $x == 1) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
		}
		elseif($x != 1) {
			$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveRank('down', '".$squadInfo['squad_id']."', '".$row['squadrank_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
		}
	
	
		$dispRanks .= "
		<tr>
		<td class='dottedLine".$addCSS."' width=\"80%\">&nbsp;&nbsp;<span class='main'><b><a href='managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks&rID=".$row['squadrank_id']."'>".$row['name']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks&rID=".$row['squadrank_id']."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' title='Edit Rank'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\">".$dispDeleteButton."</td>
		</tr>
		";
	
		$x++;
	}
	
	
	if($x == 0) {
		$dispRanks = "<tr><td colspan='3' align='center'><br><p class='main'><i>No ranks added yet!</i></p></td></tr>";
	}
	
	echo "
	
	
	
	<table class='formTable' style='border-spacing: 1px'>
	<tr>
	<td class='main' colspan='2' align='right'>
	&raquo; <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=AddRank'>Add New Rank</a> &laquo;<br><br>
	</td>
	</tr>
	<tr>
	<td class='formTitle' width=\"76%\">Rank Name:</td>
	<td class='formTitle' width=\"24%\">Actions:</td>
	</tr>
	</table>
	<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
	<tr><td colspan='5' class='dottedLine'></td></tr>
	".$dispRanks."
	</table>
	
	<br><br><br>
	
	";


}
?>