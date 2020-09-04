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
include_once("../../../../classes/basic.php");
include_once("../../../../classes/rank.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();


$cID = $consoleObj->findConsoleIDByName("View Member Applications");
$consoleObj->select($cID);

$memberAppObj = new MemberApp($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $memberAppObj->select($_POST['mAppID'])) {
	
	$arrMemAppInfo = $memberAppObj->get_info_filtered();
	
	if($memberAppObj->addMember()) {
		
		$newMemberInfo = $memberAppObj->getNewMemberInfo();
		$dispNewMember = $newMemberInfo['username'];
		
		$member->logAction("Accepted ".$dispNewMember."'s member application.");
		
		if($newMemberInfo['recruiter'] == 0) {
			$memberAppObj->setRecruiter($memberInfo['member_id']);			
		}
		
		
		echo "
			<div id='memAppMessage'>
				<p class='main' align='center'>
					".$dispNewMember." was successfully added to the website!
				</p>
			</div>
		";
		
	}
	else {
		echo "
			<div id='memAppMessage'>
				<p class='main' align='center'>
					Unable to accept ".$dispNewMember."'s application!  Please contact the website administrator.
				</p>
			</div>
		";
	}
	
	
	echo "
		
		<script type='text/javascript'>
			$(document).ready(function() {
			
				$('#memAppMessage').dialog({
				
					title: 'Accept Member Application',
					modal: true,
					zIndex: 99999,
					show: 'scale',
					width: 400,
					resizable: false,
					buttons: {
						'OK': function() {
							$(this).dialog('close');
						}
						
					}
				
				});
			
			});		
		</script>
	";
	
	
}

include("memberapplist.php");

?>