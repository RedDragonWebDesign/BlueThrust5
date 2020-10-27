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


if($_GET['rID'] == "") {
	echo "
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='deleteMessage' style='display: none'></div>
		<div id='contentDiv'>
	";
	include("include/ranklist.php");
	echo "
		</div>
		
	<script type='text/javascript'>
	
		function moveRank(moveDir, squadID, rankID) {
			
			$(document).ready(function() {
			
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
			
				$.post('".$MAIN_ROOT."members/squads/include/moverank.php', { sID: squadID, rDir: moveDir, rID: rankID }, function(data) {
				
					$('#contentDiv').html(data);
					$('#loadingSpiral').hide();
					$('#contentDiv').fadeIn(400);
					
				});
			
			});
		
		}
	
		
		function deleteRank(squadID, rankID) {
		
			$(document).ready(function() {
			
			
			
				$.post('".$MAIN_ROOT."members/squads/include/deleterank.php', { sID: squadID, rID: rankID }, function(data) {
								
					
					
					$('#deleteMessage').dialog({
						
						title: 'Manage Squad Ranks - Delete',
						modal: true,
						zIndex: 9999,
						resizable: false,
						show: 'scale',
						width: 400,
						buttons: {
							'Yes': function() {
								
								$('#loadingSpiral').show();
								$('#contentDiv').hide();
								$.post('".$MAIN_ROOT."members/squads/include/deleterank.php', { sID: squadID, rID: rankID, confirm: 1 }, function(data1) {
									
									$('#contentDiv').html(data1);
									$('#loadingSpiral').hide();
									$('#contentDiv').fadeIn(400);
							
								});
								$(this).dialog('close');
							
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
					});
					
					$('#deleteMessage').html(data);
				
				});
			
			});
		
		
		}
	
	</script>
	";
	
}
elseif($_GET['rID'] != "" && $squadObj->objSquadRank->select($_GET['rID']) && $squadObj->objSquadRank->get_info("squad_id") == $squadInfo['squad_id']) {
	$dispError = "";
	$countErrors = 0;
	
	$squadRankInfo = $squadObj->objSquadRank->get_info_filtered();
	
	echo "
	
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#breadCrumbTitle').html(\"Manage Ranks\");
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks'><b>".$squadInfo['name'].":</b> Manage Ranks</a> > ".$squadRankInfo['name']."\");
		});
	</script>
	";
	
	
	
	if($_POST['submit']) {
		
		// Check Rank Name
		
		if(trim($_POST['rankname'] == "")) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank rank name.<br>";
		}
		
		// Check Display Order
		
		$arrRankList = $squadObj->getRankList();
		$intFounderRankID = $squadObj->getFounderRankID();
		
		if($intFounderRankID != $squadRankInfo['squadrank_id']) {
			$blnCheckOrder1 = $_POST['rankorder'] == "first" && count($arrRankList) > 2;
			$blnCheckOrder2 = $_POST['rankorder'] == $intFounderRankID;
			$blnCheckOrder3 = $_POST['rankorder'] != "first" && !$squadObj->objSquadRank->select($_POST['rankorder']);
			$blnCheckOrder4 = $_POST['beforeafter'] != "before" && $_POST['beforeafter'] != "after";
			
			
			if($blnCheckOrder1 || $blnCheckOrder2 || $blnCheckOrder3 || $blnCheckOrder4) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid rank order.<br>";
			}
			elseif($_POST['rankorder'] == "first" && count($arrRankList) == 2) {
				$intNewOrderNum = 2;
			}
			elseif($_POST['rankorder'] != "first" && $squadObj->objSquadRank->select($_POST['rankorder'])) {
			
				$intNewOrderNum = $squadObj->objSquadRank->makeRoom($_POST['beforeafter']);
			
				if($intNewOrderNum === false) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You have selected an invalid rank order.<br>";
				}
			
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
		
			if($intFounderRankID == $squadRankInfo['squadrank_id']) {
				$arrColumns = array("name");
				$arrValues = array($_POST['rankname']);
			}
			else {
				$arrColumns = array("name", "sortnum", "postnews", "managenews", "postshoutbox", "manageshoutbox", "addrank", "manageranks", "editprofile", "sendinvites", "acceptapps", "setrank", "removemember");
				$arrValues = array($_POST['rankname'], $intNewOrderNum, $_POST['postnews'], $_POST['managenews'], $_POST['postshoutbox'], $_POST['manageshoutbox'], $_POST['addrank'], $_POST['manageranks'], $_POST['editprofile'], $_POST['sendinvites'], $_POST['acceptapps'], $_POST['setrank'], $_POST['removemember']);
			}
			$squadObj->objSquadRank->select($squadRankInfo['squadrank_id']);
			if($squadObj->objSquadRank->update($arrColumns, $arrValues)) {
		
				echo "
				<div style='display: none' id='successBox'>
				<p align='center'>
				Successfully Edited Squad Rank!
				</p>
				</div>
		
				<script type='text/javascript'>
				popupDialog('Edit Squad Rank', '".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks', 'successBox');
				</script>
		
				";
				$squadObj->objSquadRank->resortOrder();
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
		
		$strDisplayOrderToolTip = "Squad members will be shown on the squad profile page in the order of their rank\'s display order.";
		$strRankPrivToolTip = "";
		if($intFounderRankID == $squadRankInfo['squadrank_id']) {
			$strDisplayOrderToolTip = "You may not edit the founder\'s rank display order.  It will always be listed first.";
			$strRankPrivToolTip = " <a href='javascript:void(0)' onmouseover=\"showToolTip('You may not edit the founder\'s rank privileges.')\" onmouseout='hideToolTip()'><b>(?)</b></a>";
		}
		
		
		// Figure out Before/After Rank Display Order
		$intHighestSortNum = $squadObj->countRanks();
		$selectAfter = "";
		if($squadRankInfo['sortnum'] == $intHighestSortNum) {
			$selectAfter = "selected";

			if($squadObj->objSquadRank->select($arrSquadRanks[$intHighestSortNum-2])) {
				$selectRank = $arrSquadRanks[$intHighestSortNum-2];
			}
			
		}
		else {
			
			if($squadObj->objSquadRank->select($arrSquadRanks[$squadRankInfo['sortnum']])) {
				$selectRank = $arrSquadRanks[$squadRankInfo['sortnum']];
			}
			
		}
		
		foreach($arrSquadRanks as $squadRankID) {
		
			if($squadRankID != $intFounderRankID && $squadRankID != $squadRankInfo['squadrank_id']) {
				$dispSelected = "";
				if($selectRank == $squadRankID) {
					$dispSelected = "selected";	
				}
				
				$countRanks++;
				$squadObj->objSquadRank->select($squadRankID);
				$dispRankName = $squadObj->objSquadRank->get_info_filtered("name");
				$rankoptions .= "<option value='".$squadRankID."' ".$dispSelected.">".$dispRankName."</option>";
		
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
			
			$dispChecked = "";
			if($squadRankInfo['squadrank_id'] == $intFounderRankID) {
				$dispChecked = "disabled='disabled' checked";
			}
			elseif($squadRankInfo[$squadOption] == 1) {
				$dispChecked = "checked";	
			}
			
			if($arrSquadOptionDescriptions[$key] != "") {
				$showTip = "<a href='javascript:void(0)' onmouseover=\"showToolTip('".$arrSquadOptionDescriptions[$key]."')\" onmouseout='hideToolTip()'><b>(?)</b></a>";
			}
		
			$dispRankPrivileges .= "<li><input type='checkbox' class='textBox' value='1' name='".$squadOption."' id='".$squadOption."' ".$dispChecked."> <label for='".$squadOption."' style='cursor: pointer'>".$arrSquadOptionsDispName[$key]." ".$showTip."</label></li>";
		
		
		
		}		
		
		echo "
		
		<form action='managesquad.php?sID=".$_GET['sID']."&pID=ManageRanks&rID=".$_GET['rID']."' method='post'>
			<div class='formDiv'>
			
			";
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to edit squad rank because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
				Use the form below to edit the selected squad rank.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Rank Name:</td>
						<td class='main'><input type='text' name='rankname' value='".$squadRankInfo['name']."' class='textBox' style='width: 200px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order: <a href='javascript:void(0)' onmouseover=\"showToolTip('".$strDisplayOrderToolTip."')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
						<td class='main'>
						";
		
						if($squadRankInfo['squadrank_id'] != $intFounderRankID) {
							echo "
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$selectAfter.">After</option></select><br>
							<select name='rankorder' class='textBox'>".$rankoptions."</select>
							";
						}
						else {
							echo "<span style='font-weight: bold; font-style: italic'>Founder Rank</span>";
						}
						
							echo "
						</td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Rank Privileges:".$strRankPrivToolTip."</td>
						<td class='main' valign='top'>
							<ul style='list-style-type: none; padding-left: 0px'>
							
								".$dispRankPrivileges."
							
							
							</ul>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Edit Rank' class='submitButton' style='width: 100px'>
						</td>
					</tr>
							
				</table>
				
			</div>
		</form>
		
		
		";
		
		
		
		
		
		
	}
	
	
	
	
	
	
	
}


?>
