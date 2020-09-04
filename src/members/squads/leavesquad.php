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
	$arrSquadMembers = $squadObj->getMemberList();

	if(!$member->hasAccess($consoleObj) || $squadInfo['member_id'] == $memberInfo['member_id'] || !in_array($memberInfo['member_id'], $arrSquadMembers)) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Leave Squad\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Leave Squad\");
});
</script>
";


$dispError = "";
$countErrors = 0;



if($_POST['submitted']) {
	
	
	$squadMemberID = $squadObj->getSquadMemberID($memberInfo['member_id']);
	$squadObj->objSquadMember->select($squadMemberID);
	
	if($squadObj->objSquadMember->delete()) {
		
		$dispMessage = "Successfully left squad: <b>".$squadInfo['name']."</b>";
		
	}
	else {
		
		$dispMessage = "Unabled to close squad!";
		
	}
	
	echo "
	
	<div style='display: none' id='successBox'>
	<p align='center'>
	".$dispMessage."
	</p>
	</div>
	
	<script type='text/javascript'>
	popupDialog('Leave Squad', '".$MAIN_ROOT."members', 'successBox');
	</script>
	
	";
	
}



if(!$_POST['submitted']) {
	
	echo "
		
	<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=LeaveSquad' method='post' id='leaveSquadForm'>
			<div class='formDiv'>
				<p align='center' class='main' style='font-weight: bold'>
					ARE YOU SURE YOU WANT TO LEAVE THE SQUAD: <span style='text-decoration: underline'>".strtoupper($squadInfo['name'])."</span>?
				</p>
				
				<p align='center' class='main' style='padding: 5px 20px'>
					You will need to either re-apply or get re-invited if you want to join the squad again after leaving.
				</p>
				<br>
				<p align='center'>
					<input type='button' class='submitButton' id='leaveSquadButton' value='Leave Squad' style='width: 125px'><br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>Cancel</a>
				</p>
			</div>
			<input type='hidden' name='submitted' value='1'>
		</form>
		<div id='leaveSquadConfirm' style='display: none'>
			<p align='center' class='main'>
				Are you really sure?!?
			</p>
		</div>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#leaveSquadButton').click(function() {
				
					$('#leaveSquadConfirm').dialog({
					
						title: 'Leave Squad - Confirm',
						modal: true,
						show: 'scale',
						resizable: false,
						width: 400,
						zIndex: 9999,
						buttons: {
						
							'Yes': function() {
								$('#leaveSquadForm').submit();
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