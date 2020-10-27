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

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$memberInfo = $member->get_info_filtered();
$newMemberObj = new Member($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Member Applications");
$consoleObj->select($cID);

$memberAppObj = new MemberApp($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $memberAppObj->select($_POST['mAppID']) && $memberAppObj->get_info("memberadded") == 1) {
	
	$memberAppUser = $memberAppObj->get_info_filtered("username");
	
	if(!$memberAppObj->delete()) {
		
		echo "
			<div id='memberAppMessage' style='display: none'>
				<p class='main' align='center'>
					Unable to remove member application!  Please contact the website administrator.
				</p>
			</div>
		
			<script type='text/javascript'>
		
				$(document).ready(function() {
				
					$('#memberAppMessage').dialog({
					
						title: 'Remove Member Application - Error',
						width: 400,
						modal: true,
						zIndex: 99999,
						show: 'scale',
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
	else {
		$member->logAction("Removed the member application for ".$memberAppUser.".");	
	}
	
	include("memberapplist.php");

}


?>