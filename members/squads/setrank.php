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


	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "setrank")) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Set Member Rank\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Set Member Rank\");
});
</script>
";


$dispError = "";
$countErrors = 0;

$arrSquadRanks = $squadObj->getRankList();

if($_POST['submit']) {
	
	// Check Squad Member
	
	if(!$squadObj->objSquadMember->select($_POST['squadmember'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad member.<br>";
	}
	elseif($squadObj->objSquadMember->select($_POST['squadmember']) && $squadObj->objSquadMember->get_info("squad_id") != $squadInfo['squad_id']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad member.<br>";
	}
	elseif($squadObj->objSquadMember->get_info("member_id") == $squadInfo['member_id']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not change the founder's rank.<br>";
	}
	
	// Check Squad Rank
	
	if(!in_array($_POST['squadrank'], $arrSquadRanks)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank.<br>";
	}
	
	if($countErrors == 0) {
		
		$arrColumns = array("squadrank_id");
		$arrValues = array($_POST['squadrank']);
		
		// Squad Member Info
		$squadMemberInfo = $squadObj->objSquadMember->get_info_filtered();
		$squadObj->objSquadRank->select($squadMemberInfo['squadrank_id']); // Old Rank
		$squadMemberRankInfo = $squadObj->objSquadRank->get_info_filtered();
		
		
		$squadObj->objSquadRank->select($_POST['squadrank']); // New Rank
		$newRankInfo = $squadObj->objSquadRank->get_info_filtered();
		
		// Check if promotion or demotion
		
		$strAction = "changed";
		if($squadMemberRankInfo['sortnum'] > $newRankInfo['sortnum']) {
			$arrColumns[] = "lastpromotion";
			$arrValues[] = time();
			$strAction = "promoted";
		}
		elseif($squadMemberRankInfo['sortnum'] < $newRankInfo['sortnum']) {
			$arrColumns[] = "lastdemotion";
			$arrValues[] = time();
			$strAction = "demoted";
		}
		
		if($squadObj->objSquadMember->update($arrColumns, $arrValues)) {
			$member->select($squadMemberInfo['member_id']);
			$dispMemberLink = $member->getMemberLink();
			
			
			$member->postNotification("Your were ".$strAction." to the rank of <b>".$newRankInfo['name']."</b> in squad: <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>.");
			
			
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully ".$strAction." ".$dispMemberLink." to the rank of ".$newRankInfo['name']."!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Set Member Rank', '".$MAIN_ROOT."squads/profile.php?sID=".$_GET['sID']."', 'successBox');
			</script>
			
			";
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST['submit'] = false;
	}

	
}



if(!$_POST['submit']) {
	
	$arrSquadMembers = $squadObj->getMemberListSorted();
	$squadmemberoptions = "";
	foreach($arrSquadMembers as $memberID) {
		$member->select($memberID);
		if($memberID != $squadInfo['member_id']) {
			$squadmemberoptions .= "<option value='".$squadObj->getSquadMemberID($memberID)."'>".$member->get_info_filtered("username")."</option>";
		}
	}
	
	
	$squadrankoptions = "";
	foreach($arrSquadRanks as $squadRank) {
		$squadObj->objSquadRank->select($squadRank);
		if($squadRank != $squadObj->getFounderRankID()) {
			$squadrankoptions .= "<option value='".$squadRank."'>".$squadObj->objSquadRank->get_info_filtered("name")."</option>";
		}
	}
	
	echo "
		<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$squadInfo['squad_id']."&pID=SetRank' method='post'>
			<div class='formDiv'>
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to set member rank because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Use the form below to set a squad members' rank.<br><br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Squad Member:</td>
						<td class='main'><select name='squadmember' class='textBox'>".$squadmemberoptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>New Rank:</td>
						<td class='main'><select name='squadrank' class='textBox'>".$squadrankoptions."</select></td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br>
							<input type='submit' name='submit' value='Set Rank' class='submitButton' style='width: 100px'>
						</td>
					</tr>			
				</table>
			</div>
		</form>
	";
	
}