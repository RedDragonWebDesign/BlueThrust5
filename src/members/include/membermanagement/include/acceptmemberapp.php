<?php


/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("../../../../_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();


$cID = $consoleObj->findConsoleIDByName("View Member Applications");
$consoleObj->select($cID);

$memberAppObj = new MemberApp($mysqli);


if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $memberAppObj->select($_POST['mAppID'])) {
	$arrMemAppInfo = $memberAppObj->get_info_filtered();


	require_once(BASE_DIRECTORY."members/include/membermanagement/include/memberapp_setrank.php");

	$newRankID = 2;
	$setRankOptions = memberAppSetRank();
	if (count($setRankOptions) > 0) {
		$allowedRanks = array_keys($setRankOptions['setrank']['options']);
		if (in_array($_POST['newRank'], $allowedRanks)) {
			$newRankID = $_POST['newRank'];
		}
	}

	if ($memberAppObj->addMember($newRankID)) {
		$newMemberInfo = $memberAppObj->getNewMemberInfo();
		$dispNewMember = $newMemberInfo['username'];

		$member->logAction("Accepted ".$dispNewMember."'s member application.");

		if ($newMemberInfo['recruiter'] == 0) {
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

require_once("memberapplist.php");
