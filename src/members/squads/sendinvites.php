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
$('#breadCrumbTitle').html(\"Send Squad Invite\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Send Squad Invite\");
});
</script>
";


$dispError = "";
$countErrors = 0;
$squadRankList = $squadObj->getRankList();

if(count($squadRankList) < 2) {

	echo "
		<div style='display: none' id='errorBox'>
			<p align='center'>
				You must add a squad rank before inviting members to join!
			</p>
		</div>
	
		<script type='text/javascript'>
			popupDialog('Send Squad Invite', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'errorBox');
		</script>
	";



}
elseif($squadInfo['recruitingstatus'] == 0) {

	echo "
		<div style='display: none' id='errorBox'>
			<p align='center'>
				Recruiting is currently closed for your squad!  Set the recruiting status to \"Open\" on the Edit Squad Profile page to allow recruiting!
			</p>
		</div>
	
		<script type='text/javascript'>
			popupDialog('Send Squad Invite', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'errorBox');
		</script>
	";


}
else {

	$squadMemberList = $squadObj->getMemberList();
	$intFounderRankID = $squadObj->getFounderRankID();
	
	if($_POST['submit']) {
		
		$squadInvitesOutstanding = $squadObj->getOutstandingInvites();
		
		// Check Member
		
		if($_POST['newmemberid'] == "" && trim($_POST['newmember']) == "") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a member to invite!";	
		}
		elseif(($_POST['newmemberid'] != "" && !$member->select($_POST['newmemberid'])) || ($_POST['newmemberid'] == "" && trim($_POST['newmember']) != "" && !$member->select($_POST['newmember']))) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member!";	
		}
		else {
			$intNewMemberID = $member->get_info("member_id");	
		}
		
		if(in_array($intNewMemberID, $squadMemberList)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> This member is already in your squad!";
		}
		elseif(in_array($intNewMemberID, $squadInvitesOutstanding)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> This member was already sent an invitation!";
		}
		
		
		// Check Starting Rank
		
		if($squadObj->memberHasAccess($memberInfo['member_id'], "setrank")) {
			
			if(!$squadObj->objSquadRank->select($_POST['startingrank']) || $_POST['startingrank'] == $intFounderRankID) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid starting rank!";
			}

		}
		else {
			$startRankKey = max(array_keys($squadRankList));
			$_POST['startingrank'] = $squadRankList[$startRankKey];
		}
		
		
		
		if($countErrors == 0) {
			
			
			$arrColumns = array("squad_id", "sender_id", "receiver_id", "datesent", "message", "startingrank_id");
			$arrValues = array($squadInfo['squad_id'], $memberInfo['member_id'], $intNewMemberID, time(), $_POST['message'], $_POST['startingrank']);
			
			$squadInviteObj = new Basic($mysqli, "squadinvites", "squadinvite_id");
			
			if($squadInviteObj->addNew($arrColumns, $arrValues)) {
				
				$intViewSquadInvitesCID = $consoleObj->findConsoleIDByName("View Squad Invitations");
				
				$member->postNotification("You have received a squad invitation from <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>!<br><br><a href='".$MAIN_ROOT."members/console.php?cID=".$intViewSquadInvitesCID."'>Click Here</a> to view your Squad Invitations.");
				
				echo "
				
					<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Sent Squad Invitation to ".$member->getMemberLink()."!
					</p>
					</div>
				
					<script type='text/javascript'>
						popupDialog('Send Squad Invite', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
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
	
		$sqlMemberList = "('".implode("','", $squadMemberList)."')";
		
		$arrMembers = array();
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE member_id NOT IN ".$sqlMemberList." AND disabled = '0' ORDER BY username");
		while($row = $result->fetch_assoc()) {
	
			$arrMembers[] = array("id" => $row['member_id'], "value" => filterText($row['username']));
			
		}
		
		
		$dispSetRank = "";
		$setrankoptions = "";
		
		if($squadObj->memberHasAccess($memberInfo['member_id'], "setrank")) {
			$intFounderRankID = $squadObj->getFounderRankID();
	
			foreach($squadRankList as $squadRank) {

				if($squadRank != $intFounderRankID) {
					$squadObj->objSquadRank->select($squadRank);
					$squadRankInfo = $squadObj->objSquadRank->get_info_filtered();
					$setrankoptions .= "<option value='".$squadRankInfo['squadrank_id']."'>".$squadRankInfo['name']."</option>";
				}
						
			}
			
			$dispSetRank = "
	
			<tr>
				<td class='formLabel'>Starting Rank:</td>
				<td class='main'><select name='startingrank' class='textBox'>".$setrankoptions."</select></td>
			</tr>
			
			";
			
		}
		
		
		$arrJSONMembers = json_encode($arrMembers);
		
		echo "
			<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=SendInvites' method='post'>
				<div class='formDiv'>
				
			";
		
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to send squad invite because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
					Use the form below to send invitations to clan members so they can join your squad.  The member must accept the invitation in order to add them to your squad.
					<br><br>
					
					<table class='formTable'>
						<tr>
							<td class='formLabel'>Member:</td>
							<td class='main'><input type='text' name='newmember' value='".$_POST['newmember']."' id='newmember' class='textBox' style='width: 200px'></td>
						</tr>
						".$dispSetRank."
						<tr>
						<tr>
							<td class='formLabel' valign='top'>Message:</td>
							<td class='main'><textarea rows='5' cols='40' class='textBox' name='message'>".$_POST['message']."</textarea>
						</tr>
						<tr>
							<td class='main' colspan='2' align='center'><br>
								<input type='submit' name='submit' value='Send Invite' class='submitButton' style='width: 125px'>
							</td>
						</tr>
					</table>
				</div>
				<input type='hidden' name='newmemberid' id='newmemberid'>
			</form>
		
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
					
					var arrMemberList = ".$arrJSONMembers.";
				
					$('#newmember').autocomplete({
						source: arrMemberList,
						minLength: 3,
						select: function(event, ui) {
						
							$('#newmemberid').val(ui.item.id);
						
						}
						
					
					
					});
				
				});
			
			</script>
			
		";
	
		
	}

}


?>