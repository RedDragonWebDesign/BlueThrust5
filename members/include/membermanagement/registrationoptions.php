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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$rankInfo = $memberRank->get_info_filtered();
$cID = $_GET['cID'];



if($_POST['submit']) {
	
	$setRegistration = 1;
	$setMemberApproval = 0;
	if($_POST['registrationstatus'] != 1) {
		$setRegistration = 0;
		if($_POST['memberapproval'] == 1) {
			$setMemberApproval = 1;	
		}
	}
	
	$updateColumns = array("memberregistration", "memberapproval");
	$updateValues = array($setRegistration, $setMemberApproval);
	
	
	
	if($webInfoObj->multiUpdate($updateColumns, $updateValues)) {
		
		$member->logAction("Modified website registration options.");
		
		echo "
		<div style='display: none' id='successBox'>
			<p align='center' class='main'>
				Successfully Saved Registration Options!
			</p>
		</div>
		
		<script type='text/javascript'>
			popupDialog('Registration Options', '".$MAIN_ROOT."members', 'successBox');
		</script>
		
		";
		
		
	}
	else {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		$_POST['submit'] = false;		
	}
	
	
	
}


if(!$_POST['submit']) {
	
	$selectOpen = "";
	$checkApproval = "";
	if($websiteInfo['memberregistration'] != 1) {
		$selectOpen = " selected";
		if($websiteInfo['memberapproval'] == 1) {
			$checkApproval = " checked";	
		}
	}
	
	
	echo "
	
		<div class='formDiv'>
		";
		if($dispError != "") {
			echo "
				<div class='errorDiv'>
					<strong>Unable to save registration options because the following errors occurred:</strong><br><br>
					$dispError
				</div>
			";
		}
		
		echo "
		
			Use the form below to manage the member registration options for the clan.  New members are automatically given the lowest rank in the clan.
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Registration Status: <a href='javascript:void(0)' onmouseover=\"showToolTip('Open Registration allows members to sign up on their own.  Closed Registration requires a member of the clan to add new members.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<select name='registrationstatus' id='registrationStatus' class='textBox'>
								<option value='1'>Closed</option>
								<option value='0'".$selectOpen.">Open</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Membership Approval: <a href='javascript:void(0)' onmouseover=\"showToolTip('Only used when open registration is selected.  If checked, members who sign up must be approved before becoming a member.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<input type='checkbox' id='memberApproval' name='memberapproval' value='1' class='textBox' style='border: 0px'".$checkApproval.">
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br><br>
							<input type='submit' name='submit' value='Save' class='submitButton'><br><br>
						</td>
					</tr>
				</table>
			
			</form>
		
		</div>
	
		<script type='text/javascript'>
		
			$(document).ready(function() {
				$('#registrationStatus').change(function() {
					if($('#registrationStatus').val() == '1') {
						$('#memberApproval').attr('disabled', true);
						$('#memberApproval').attr('checked', false);
					}
					else {
						$('#memberApproval').attr('disabled', false);					
					}
				});
				
				
				$('#registrationStatus').change();
			});
		
		</script>
		
	";
	
}


