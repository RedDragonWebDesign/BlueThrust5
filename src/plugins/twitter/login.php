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

include("twitter.php");
include_once($prevFolder."classes/member.php");


if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}

$twitterObj = new Twitter($mysqli);


if(!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier']) || $_GET['oauth_token'] != $_SESSION['btOauth_Token']) {

	// CONNECT
			
	$response = $twitterObj->getRequestToken($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if($response !== false) {
		parse_str($response, $arrOutput);	
		
		$_SESSION['btOauth_Token'] = $arrOutput['oauth_token'];
		$_SESSION['btOauth_Token_Secret'] = $arrOutput['oauth_token_secret'];
		
		
		header("Location: ".$twitterObj->authorizeURL."?oauth_token=".$arrOutput['oauth_token']);
		exit();
	}


}
elseif(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && $_GET['oauth_token'] == $_SESSION['btOauth_Token']) {
	// CALLBACK
	
	
	$twitterObj->oauthTokenSecret = $_SESSION['btOauth_Token_Secret'];
	$response = $twitterObj->getAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
	
	if($twitterObj->httpCode == 200) {
		parse_str($response, $oauthArray);

		if($twitterObj->authorizeLogin($oauthArray['oauth_token'], $oauthArray['oauth_token_secret'])) {
			$twitterInfo = $twitterObj->get_info();
			if($twitterInfo['allowlogin'] == 1) {
				// LOGGED IN!
				
				// Update Twitter Stats
				
				$twitterObj->oauthToken = $twitterObj->get_info("oauth_token");
				$twitterObj->oauthTokenSecret = $twitterObj->get_info("oauth_tokensecret");
				
				$twitterObj->reloadCacheInfo();				
				
				$memberObj = new Member($mysqli);
				$memberObj->select($twitterInfo['member_id']);
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
				$dispError = "You may not use twitter to log in to this account.  To change this setting, log in to your account regularly and change your Twitter Connect settings.<br><br>";
			}		
		}
		else {

			$dispError = "There is no user associated with this Twitter account.  You must connect your Twitter account while logged in before using this feature.";
			
		}
		
		
	}
	else {

		$dispError = "Unable to connect to Twitter!  Please <a href='".$MAIN_ROOT."plugins/twitter/login.php'>Try Again</a>.";
	}
	
	
}
else {
	$dispError = "You entered an incorrect username/password combination!";	
}



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