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
include_once("../../../../classes/tournament.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Join a Tournament");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$tournamentObj = new Tournament($mysqli);

// Check Login

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $tournamentObj->select($_POST['tID'])) {

	$tournamentInfo = $tournamentObj->get_info_filtered();
	
	if($tournamentInfo['password'] != "") {
		
	
		echo "
			<div id='passwordCheckDialog' style='display: none'>
				<p class='main' align='center'>
					This tournament requires a password to join.  Enter the password below to join the tournament.<br><br>
					<input type='password' class='textBox' id='enterPass'>
				</p>
			</div>
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#passwordCheckDialog').dialog({
					
						title: 'Join a Tournament - Check Password',
						zIndex: 99999,
						modal: true,
						show: 'scale',
						width: 400,
						resizable: false,
						buttons: {
						
							'Join': function() {
							
								$(this).dialog('close');
								$('#tournamentPassword').val($('#enterPass').val());
								$('#btnSubmit').click();
								
							
							},
							'Cancel': function() {
							
								$(this).dialog('close');
								$('#loadingSpiral').hide();
								
							}
						
						
						}
					
					
					});
				
					
					
					
				});
			
			</script>
			
		";
	
		
		
	}
	else {
		
		echo "
		
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#btnSubmit').click();
				
				});
			
			</script>
		
		";
		
	}
	
	
}



?>