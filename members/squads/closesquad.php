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

	$manageAllSquadsCID = $consoleObj->findConsoleIDByName("Manage All Squads");
	$consoleAllSquads = new ConsoleOption($mysqli);
	$consoleAllSquads->select($manageAllSquadsCID);
	
	if(!$member->hasAccess($consoleObj) || ($squadInfo['member_id'] != $memberInfo['member_id'] && !$member->hasAccess($consoleAllSquads))) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Close Squad\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Close Squad\");
});
</script>
";


$dispError = "";
$countErrors = 0;


$squadMemberList = $squadObj->getMemberListSorted();


if($_POST['submitted']) {
	
	if($squadObj->delete()) {
		$dispMessage = "Successfully closed squad: <b>".$squadInfo['name']."</b>";
		$dispFounderName = $member->getMemberLink();
		
		foreach($squadMemberList as $memberID) {
			
			if($memberID != $squadInfo['member_id']) {
				$member->select($memberID);
				$member->postNotification($dispFounderName." has closed the squad: <b>".$squadInfo['name']."</b>!");
			}
		}
		
		
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
	popupDialog('Close Squad', '".$MAIN_ROOT."members', 'successBox');
	</script>
	
	";
	
}


if(!$_POST['submitted']) {
	
	echo "
	
		<form action='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=CloseSquad' method='post' id='closeSquadForm'>
			<div class='formDiv'>
				<p align='center' class='main' style='font-weight: bold'>
					ARE YOU SURE YOU WANT TO DELETE THE SQUAD: <span style='text-decoration: underline'>".strtoupper($squadInfo['name'])."</span>?
				</p>
				
				<p align='center' class='main' style='padding: 5px 20px'>
					All members will be removed from the squad. Squad ranks, news posts and shoutbox posts will be deleted.  You will not be able to recover any of the information after clicking the <b>Close Squad</b> button.
				</p>
				<br>
				<p align='center'>
					<input type='button' class='submitButton' id='closeSquadButton' value='Close Squad' style='width: 125px'><br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>Cancel</a>
				</p>
			</div>
			<input type='hidden' name='submitted' value='1'>
		</form>
		<div id='closeSquadConfirm' style='display: none'>
			<p align='center' class='main'>
				Are you really sure?!?
			</p>
		</div>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#closeSquadButton').click(function() {
				
					$('#closeSquadConfirm').dialog({
					
						title: 'Close Squad - Confirm',
						modal: true,
						show: 'scale',
						resizable: false,
						width: 400,
						zIndex: 9999,
						buttons: {
						
							'Yes': function() {
								$('#closeSquadForm').submit();
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