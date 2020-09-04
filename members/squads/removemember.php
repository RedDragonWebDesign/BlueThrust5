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


	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "removemember")) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Remove Squad Member\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Remove Squad Member\");
});
</script>
";


$dispError = "";
$countErrors = 0;

$squadMemberList = $squadObj->getMemberListSorted();

if($_POST['submitted']) {
	
	// Check the Member
	
	if(!in_array($_POST['squadmember'], $squadMemberList)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad member1.<br>";
	}
	
	$intSquadMemberID = $squadObj->getSquadMemberID($_POST['squadmember']);
	
	if(!$squadObj->objSquadMember->select($intSquadMemberID)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad member2.<br>";
	}
	elseif($squadObj->objSquadMember->get_info("squad_id") != $squadInfo['squad_id']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid squad member3.<br>";
	}

	
	if($squadInfo['member_id'] == $_POST['squadmember']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not remove the squad founder from the squad.<br>";
	}
	
	
	if($countErrors == 0) {
		
		if($squadObj->objSquadMember->delete()) {
			
			$member->select($_POST['squadmember']);
			$member->postNotification("You were removed from the squad squad: <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>.");
			
			
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully removed member from squad!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Remove Squad Member', '".$MAIN_ROOT."squads/profile.php?sID=".$_GET['sID']."', 'successBox');
			</script>
			
			";
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	if($countErrors > 0) {
		$_POST['submitted'] = false;	
	}
	
	
	
}

if(!$_POST['submitted']) {
	
	
	foreach($squadMemberList as $memberID) {
		
		$member->select($memberID);
		if($squadInfo['member_id'] != $memberID) {
			$squadmemberoptions .= "<option value='".$memberID."'>".$member->get_info_filtered("username")."</option>";
		}
	}
	
	echo "
		<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=RemoveMember' method='post' id='removeMemberForm'>
			<div class='formDiv'>
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to remove member because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Use the form below to remove a member from the squad.
			
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Squad Member:</td>
						<td class='main'><select name='squadmember' id='squadmember' class='textBox'>".$squadmemberoptions."</select></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='button' class='submitButton' value='Remove Member' id='removeMemberButton' style='width: 125px'>
						</td>
					</tr>
				</table>
			
			</div>
			<input type='hidden' value='1' name='submitted'>
		</form>
		<div id='confirmRemoveMember' style='display: none'>
			<p align='center' class='main'>Are you sure you want to remove <span style='font-weight: bold' id='dispSquadMember'></span> from the squad?</p>
		</div>
		<script type='text/javascript'>
		
				
			$(document).ready(function() {
				
			
				$('#removeMemberButton').click(function() {
				
					$('#dispSquadMember').html($('#squadmember option:selected').text());
				
				
					$('#confirmRemoveMember').dialog({
					
						title: 'Remove Member - Confirm',
						modal: true,
						width: 400,
						zIndex: 9999,
						resizable: false,
						show: 'scale',
						buttons: {
						
							'Yes': function() {
								$(this).dialog('close');
								$('#removeMemberForm').submit();							
							},
							'No': function() {
								$(this).dialog('close');
							}
						
						
						}
					
					
					});
				
				});
				

				
			});

		
		</script>
	";
	
	
}


?>