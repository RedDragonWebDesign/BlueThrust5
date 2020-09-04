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

if(!isset($member) || !isset($squadObj) || substr($_SERVER['PHP_SELF'], -strlen("managesquad.php")) != "managesquad.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$squadObj->select($sID);


	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "addrank")) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Add Rank\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Add Rank\");
});
</script>
";


$dispError = "";
$countErrors = 0;
if($_POST['submit']) {
	
	// Check Rank Name
	
	if(trim($_POST['rankname'] == "")) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank rank name.<br>";
	}
	
	
	// Check Display Order
	
	$arrRankList = $squadObj->getRankList();
	$intFounderRankID = $squadObj->getFounderRankID();
	
	
	$blnCheckOrder1 = $_POST['rankorder'] == "first" && count($arrRankList) > 1;
	$blnCheckOrder2 = $_POST['rankorder'] == $intFounderRankID;
	$blnCheckOrder3 = $_POST['rankorder'] != "first" && !$squadObj->objSquadRank->select($_POST['rankorder']);
	$blnCheckOrder4 = $_POST['beforeafter'] != "before" && $_POST['beforeafter'] != "after";
	
	
	if($blnCheckOrder1 || $blnCheckOrder2 || $blnCheckOrder3 || $blnCheckOrder4) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid rank order.<br>";
	}
	elseif($_POST['rankorder'] == "first" && count($arrRankList) == 1) {
		$intNewOrderNum = 2;	
	}
	elseif($_POST['rankorder'] != "first" && $squadObj->objSquadRank->select($_POST['rankorder'])) {

		$intNewOrderNum = $squadObj->objSquadRank->makeRoom($_POST['beforeafter']);
		
		if($intNewOrderNum === false) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid rank order.<br>";
		}
		
	}

	
	// Filter Rank Privileges
	$arrRankPrivileges = $squadObj->arrSquadPrivileges;
	foreach($arrRankPrivileges as $squadPriv) {
		if($_POST[$squadPriv] != 1) {
			$_POST[$squadPriv] = 0;
		}
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("squad_id", "name", "sortnum", "postnews", "managenews", "postshoutbox", "manageshoutbox", "addrank", "manageranks", "editprofile", "sendinvites", "acceptapps", "setrank", "removemember");
		$arrValues = array($_GET['sID'], $_POST['rankname'], $intNewOrderNum, $_POST['postnews'], $_POST['managenews'], $_POST['postshoutbox'], $_POST['manageshoutbox'], $_POST['addrank'], $_POST['manageranks'], $_POST['editprofile'], $_POST['sendinvites'], $_POST['acceptapps'], $_POST['setrank'], $_POST['removemember']);
		
		if($squadObj->objSquadRank->addNew($arrColumns, $arrValues)) {
		
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Squad Rank!
				</p>
			</div>
		
			<script type='text/javascript'>
				popupDialog('Add Squad Rank', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
		
			";
		
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
}


if(!$_POST['submit']) {
	
	$countRanks = 0;
	$rankoptions = "";
	$arrSquadRanks = $squadObj->getRankList();
	$intFounderRankID = $squadObj->getFounderRankID();
	
	foreach($arrSquadRanks as $squadRankID) {
		
		if($squadRankID != $intFounderRankID) {
		
			$countRanks++;
			$squadObj->objSquadRank->select($squadRankID);
			$dispRankName = $squadObj->objSquadRank->get_info_filtered("name");
			$rankoptions .= "<option value='".$squadRankID."'>".$dispRankName."</option>";
			
		}
		
	}
	
	
	if($countRanks == 0) {
		$rankoptions = "<option value='first'>(first rank)</option>";
	}
	
	$arrSquadOptions = $squadObj->arrSquadPrivileges;
	$arrSquadOptionsDispName = array("Post News", "Manage News", "Post in Shoutbox", "Manage Shoutbox Posts", "Add Rank", "Manage Ranks", "Set Member Rank", "Edit Squad Profile", "Send Squad Invites", "View Applications", "Remove Member");
	$arrSquadOptionDescriptions = array("", "", "", "", "", "", "", "Edit Squad Information, squad name, recruiting status, etc.", "Send invitations for new members to join.", "Review and Accept/Decline new member applications.", "");
	
	foreach($arrSquadOptions as $key=>$squadOption) {
		
		$showTip = "";
		
		if($arrSquadOptionDescriptions[$key] != "") {
			$showTip = "<a href='javascript:void(0)' onmouseover=\"showToolTip('".$arrSquadOptionDescriptions[$key]."')\" onmouseout='hideToolTip()'><b>(?)</b></a>";
		}
		
		$dispRankPrivileges .= "<li><input type='checkbox' class='textBox' value='1' name='".$squadOption."' id='".$squadOption."'> <label for='".$squadOption."' style='cursor: pointer'>".$arrSquadOptionsDispName[$key]." ".$showTip."</label></li>";
		
		
		
	}
	
	echo "
		<form action='managesquad.php?sID=".$_GET['sID']."&pID=AddRank' method='post'>
			<div class='formDiv'>
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add squad because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
			
	echo "
				Use the form below to add a new squad rank.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Rank Name:</td>
						<td class='main'><input type='text' name='rankname' value='".$_POST['rankname']."' class='textBox' style='width: 200px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order: <a href='javascript:void(0)' onmouseover=\"showToolTip('Squad members will be shown on the squad profile page in the order of their rank\'s display order.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='rankorder' class='textBox'>".$rankoptions."</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Rank Privileges:</td>
						<td class='main' valign='top'>
							<ul style='list-style-type: none; padding-left: 0px'>
							
								".$dispRankPrivileges."
							
							
							</ul>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Add Rank' class='submitButton' style='width: 100px'>
						</td>
					</tr>
							
				</table>
				
			</div>
		</form>
	
	";
	
	
}