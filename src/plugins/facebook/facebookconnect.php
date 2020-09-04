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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}


include_once("../plugins/facebook/facebook.php");

$fbObj = new Facebook($mysqli);
$blnCheckForFacebook = $fbObj->hasFacebook($memberInfo['member_id']);

if($blnCheckForFacebook) {
	$fbInfo = $fbObj->get_info_filtered();
	$fbID = $fbInfo['fbconnect_id'];
	if((time()-$fbInfo['lastupdate']) > 1800) {

		$fbObj->accessToken = $fbInfo['access_token'];
		$fbInfo = $fbObj->getFBInfo();
		
		if($fbInfo == "") {
			// User revoked access through Facebook, refresh page and re-ask for access
			
			$fbObj->delete();
			
			echo "
			
				<script type='text/javascript'>
				
					window.location = '".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."';
				
				</script>
			
			";
			
			exit();
		}
		
		$arrColumns = array("name", "lastupdate");
		$arrValues = array($fbInfo['name'], time());
		
		$fbObj->select($fbID);
		$fbObj->update($arrColumns, $arrValues);
		$fbInfo = $fbObj->get_info_filtered();
		
	}
	
	echo "
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='connectedDiv'>
			<div class='formDiv'>
				<table class='formTable'>
					<tr>
						<td>
							<div class='main dottedLine' style='margin-bottom: 20px; padding-bottom: 3px'>
								<b>Connected:</b>
							</div>
						</td>
					</tr>
					<tr>
						<td>
						
							<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%; overflow: auto'>
	
								<div style='float: left; margin: 3px; padding: 0px' class='solidBox'><img src='".$fbObj->getProfilePic("normal")."'></div>
								<div style='float: left; padding-left: 8px; padding-top: 30px'>
									<span class='breadCrumbTitle' style='padding: 0px'><a href='https://www.facebook.com/profile.php?id=".$fbInfo['facebook_id']."'>".$fbInfo['name']."</a></span>
								</div>
							</div>
							<div style='font-style: italic; text-align: center; margin-top: 3px; margin-left: auto; margin-right: auto; position: relative' class='main'>
								Last updated ".getPreciseTime($fbInfo['lastupdate'])."
								<p class='largeFont' style='font-style: normal; font-weight: bold' align='center'>
									<a style='cursor: pointer' id='btnDisconnect'>DISCONNECT ACCOUNT</a>
								</p>
							</div>
						
						</td>
					</tr>
				</table>		
			</div>
		</div>
		<div id='disconnectDiv' style='display: none'>
			<p class='main' align='center'>
				Are you sure you want to disconnect your Facebook account?
			</p>
		</div>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#btnDisconnect').click(function() {
					
					$('#disconnectDiv').dialog({
						title: 'Disconnect Facebook',
						modal: true,
						zIndex: 99999,
						width: 400,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {
								$('#connectedDiv').fadeOut(250);
								$('#loadingSpiral').show();
								$.post('".$MAIN_ROOT."plugins/facebook/disconnect.php', { }, function(data) {
								
									$('#connectedDiv').html(data);
									$('#loadingSpiral').hide();
									$('#connectedDiv').fadeIn(250);
								
								});
								
								
								$(this).dialog('close');
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
						
					});
					$('.ui-dialog :button').blur();
				
				});
			
			});
		
		</script>
	
	";
	
}
elseif(!$blnCheckForFacebook && isset($_GET['code'])) {
	
	$fbObj->tokenNonce = $_SESSION['btFacebookNonce'];

	$arrURLInfo = parse_url($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	
	$arrAccessToken = $fbObj->getAccessToken($_GET['code'], $_GET['state'], $arrURLInfo['scheme']."://".$arrURLInfo['host'].$arrURLInfo['path']."?cID=".$_GET['cID']);
	
	$_SESSION['btFBAccessToken'] = $arrAccessToken['access_token'];
	
	if($fbObj->checkAccessToken()) {
		$fbInfo = $fbObj->getFBInfo();
		
		// Save in DB
		$arrColumns = array("facebook_id", "member_id", "name", "access_token", "lastupdate");
		$arrValues = array($fbInfo['id'], $memberInfo['member_id'], $fbInfo['name'], $arrAccessToken['access_token'], time());
		
		$fbObj->addNew($arrColumns, $arrValues);
		
		echo "
			
			<script type='text/javascript'>
				window.location = '".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."';
			</script>
		
		";
	}
	else {
		
		echo "
		
			<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%'>
				<p class='main' align='center'>
					Unable to connect account!  Please Try Again.<br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."'>Retry</a>
				</p>
			</div>
		
		";
		
	}
	
}
elseif(!$blnCheckForFacebook && isset($_GET['error_reason'])) {
	echo "
	
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members';
		</script>
	";
}
elseif(!$blnCheckForFacebook) {
	
	$loginURL = $fbObj->getFBConnectLink($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$_SESSION['btFacebookNonce'] = $fbObj->tokenNonce;
	
	echo "
		<p>Redirecting to Facebook...</p>
	
		<script type='text/javascript'>
			window.location = '".$loginURL."';
		</script>
	";
	
}

?>