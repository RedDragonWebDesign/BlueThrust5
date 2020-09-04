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

include("facebook.php");
include_once($prevFolder."classes/member.php");


if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}

$fbObj = new Facebook($mysqli);

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




if(isset($_GET['code'])) {
	
	// Check if a member is connected	
	
	$fbObj->tokenNonce = $_SESSION['btFacebookNonce'];

	$arrURLInfo = parse_url($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	
	$arrAccessToken = $fbObj->getAccessToken($_GET['code'], $_GET['state'], $arrURLInfo['scheme']."://".$arrURLInfo['host'].$arrURLInfo['path']);
	
	$_SESSION['btFBAccessToken'] = $arrAccessToken['access_token'];
	
	if($fbObj->checkAccessToken()) {
		$fbInfo = $fbObj->getFBInfo();
		
		// Save in DB
		$arrColumns = array("name", "lastupdate");
		$arrValues = array($fbInfo['name'], time());
		
		if($fbObj->authorizeLogin($fbInfo['id'])) {
			$fbInfo = $fbObj->get_info();
			
			$memberObj = new Member($mysqli);
			$memberObj->select($fbInfo['member_id']);
			$memberInfo = $memberObj->get_info();
			
			$_SESSION['btUsername'] = $memberInfo['username'];
			$_SESSION['btPassword'] = $memberInfo['password'];
			$_SESSION['btRememberMe'] = $_POST['rememberme'];
			
			$newLastLogin = time();
			$newTimesLoggedIn = $memberInfo['timesloggedin']+1;
			$newIP = $_SERVER['REMOTE_ADDR'];
			
			$memberObj->update(array("lastlogin", "timesloggedin", "ipaddress", "loggedin"), array($newLastLogin, $newTimesLoggedIn, $newIP, 1));
			
			$memberObj->autoPromote();
			
			echo "
				<script type='text/javascript'>
					window.location = '".$MAIN_ROOT."index.php';
				</script>
			";
			
			exit();
		
			
			
		}
		else {
			$dispError = "There is no user associated with this Facebook account.  You must connect your Facebook account while logged in before using this feature.";	
		}
		
	}
	else {
		
		$dispError = "Unable to validate your Facebook account, please log in regularly through the website.";
		
	}
	
	
}
elseif(isset($_GET['error_reason'])) {
	$dispError = "There is no user associated with this Facebook account.  You must connect your Facebook account while logged in before using this feature.";
}


if(!isset($_GET['code']) || $dispError == "") {
	// Login through facebook

	$loginURL = $fbObj->getFBConnectLink($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$_SESSION['btFacebookNonce'] = $fbObj->tokenNonce;
	
	echo "
		<p>Redirecting to Facebook...</p>
	
		<script type='text/javascript'>
			window.location = '".$loginURL."';
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
	<div class='shadedBox' style='width: 40%; margin-bottom: 20px; margin-top: 50px; margin-left: auto; margin-right: auto;'>
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