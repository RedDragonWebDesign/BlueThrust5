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

// Config File
$prevFolder = "../../";
include($prevFolder."_setup.php");

// Classes needed for login.php

include("youtube.php");
include_once($prevFolder."classes/member.php");


if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}

$ytObj = new Youtube($mysqli);

$dispError = array();
$countErrors = 0;

// Start Page
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > Log In";
include($prevFolder."themes/".$THEME."/_header.php");

if(constant("LOGGED_IN")) {
	
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members'
		</script>
	";
	exit();
	
}

if(isset($_GET['code']) && $_GET['state'] == $_SESSION['btYoutubeNonce'] && !isset($_GET['error'])) {
	
	$arrURLInfo = parse_url($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		
	$response = $ytObj->getAccessToken($_GET['code'], $arrURLInfo['scheme']."://".$arrURLInfo['host'].$arrURLInfo['path']);
	
	if(isset($response['access_token'])) {
		
		$ytObj->accessToken = $response['access_token'];
		$ytObj->refreshToken = ($response['refresh_token'] != "") ? $response['refresh_token'] : 1;
		$channelInfo = $ytObj->getChannelInfo();

		
		$channelID = $channelInfo['items'][0]['id'];
		
		if($ytObj->authorizeLogin($channelID)) {
			$ytInfo = $ytObj->get_info();
			
			$memberObj = new Member($mysqli);
			$memberObj->select($ytInfo['member_id']);
			$memberInfo = $memberObj->get_info();
			
			$_SESSION['btUsername'] = $memberInfo['username'];
			$_SESSION['btPassword'] = $memberInfo['password'];
			
			$newLastLogin = time();
			$newTimesLoggedIn = $memberInfo['timesloggedin']+1;
			$newIP = $_SERVER['REMOTE_ADDR'];
			
			$memberObj->update(array("lastlogin", "timesloggedin", "ipaddress", "loggedin"), array($newLastLogin, $newTimesLoggedIn, $newIP, 1));
			
			$memberObj->autoPromote();			
			
			echo "
				<script type='text/javascript'>
					window.location = '".$MAIN_ROOT."members';
				</script>
			";
			exit();
		}
		else {
			$dispError = "There is no user associated with this Youtube account.  You must connect your Youtube account while logged in before using this feature.";				
		}
		
	}
	else {
		$dispError = "Unable to validate your Youtube account, please log in regularly through the website.";
	}
	
	
}
elseif(isset($_GET['error'])) {
	$dispError = "Unable to validate your Youtube account, please log in regularly through the website.";
}
elseif(!isset($_GET['error']) && !isset($_GET['code'])) {
			
	$loginLink = $ytObj->getConnectLink($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$_SESSION['btYoutubeNonce'] = $ytObj->tokenNonce;
	
	echo "
	
		<p class='main'>Redirecting to Youtube...</p>
		
		<script type='text/javascript'>
			window.location = '".$loginLink."';
		</script>
	
	";
	
	exit();
}




echo "
	<div class='breadCrumbTitle'>LOG IN</div>
	<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
		$dispBreadCrumb
	</div>
	
	<div style='text-align: center'>
	<div class='shadedBox' style='width: 50%; margin-bottom: 20px; margin-top: 50px; margin-left: auto; margin-right: auto;'>
		<p class='main' align='center'>
			".$dispError."
		</p>
	</div>
	
	<div class='shadedBox' style='width: 40%; margin-bottom: 50px; margin-top: 20px; margin-left: auto; margin-right: auto;'>
		<p class='main' align='center'>
			<form action='".$MAIN_ROOT."login.php' method='post'>
				<table class='formTable' style='width: 100%'>
					<tr>
						<td class='main'>Username:</td>
						<td class='main'><input type='text' class='textBox' name='user'></td>
					</tr>
					<tr>
						<td class='main'>Password:</td>
						<td class='main'><input type='password' class='textBox' name='pass'></td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br>
							<input type='submit' name='submit' value='Log In' class='submitButton'>
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>
	</div>
";

include("themes/".$THEME."/_footer.php");


?>