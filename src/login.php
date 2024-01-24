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

$fail = false;

// Config File
$prevFolder = "";
require_once("_setup.php");

// Classes needed for login.php
require_once("classes/member.php");

// Start Page

require_once("themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle("Log In");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Log In");

// If form submitted, process form
if ( ! empty($_POST['submit']) ) {
	$login_username = $_POST['user'];
	$login_password = $_POST['pass'];
	$fail = true;

	$checkMember = new Member($mysqli);

	$checkMember->select($login_username);
	$memberInfo = $checkMember->get_info();

	$usernameExists = ($memberInfo['username'] ?? '') != "";

	if ( $usernameExists ) {

		$passwordMatches = $checkMember->authorizeLogin($login_password, 1);

		if ( $passwordMatches ) {
			$_SESSION['btUsername'] = $memberInfo['username'];
			$_SESSION['btPassword'] = $memberInfo['password'];
			$_SESSION['btRememberMe'] = $_POST['rememberme'] ?? '';


			$memberInfo = $checkMember->get_info();

			$newLastLogin = time();
			$newTimesLoggedIn = $memberInfo['timesloggedin']+1;
			$newIP = $_SERVER['REMOTE_ADDR'];

			$checkMember->update(
				["lastlogin", "timesloggedin", "ipaddress", "loggedin"],
				[$newLastLogin, $newTimesLoggedIn, $newIP, 1]
			);

			$checkMember->autoPromote();

			$fail = false;
			echo "
				<script type='text/javascript'>
					window.location = 'members/';
				</script>
			";
		}

	}

	if ( $fail ) {
		$_POST['submit'] = false;
	}
}


if ( empty($_POST['submit']) && ! constant("LOGGED_IN")) {

	if ( $fail ) {
		$errorMessage = "You entered an incorrect username/password combination!";
	}
	else {
		$errorMessage = "You must be logged in to view this page!";
	}

require_once($prevFolder."include/breadcrumb.php");
echo "


	<div class='shadedBox' style='width: 40%; margin-bottom: 20px; margin-top: 50px; margin-left: auto; margin-right: auto;'>
		<p class='main' align='center'>
			$errorMessage
		</p>
	</div>
	
	<div class='shadedBox' style='width: 40%; margin-bottom: 50px; margin-top: 20px; margin-left: auto; margin-right: auto;'>
		<p class='main' align='center'>
			<form action='".$MAIN_ROOT."login.php' method='post' style='margin-left: auto; margin-right: auto'>
				<table class='formTable' style='width: auto; margin-left: auto; margin-right: auto'>
					<tr>
						<td class='main' style='width: 45%'>Username:</td>
						<td class='main'><input type='text' class='textBox' name='user'></td>
					</tr>
					<tr>
						<td class='main'>Password:</td>
						<td class='main'><input type='password' class='textBox' name='pass'></td>
					</tr>
					<tr>
						<td class='main'>Remember Me:</td>
						<td class='main'><input type='checkbox' name='rememberme' value='1' checked></td>
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


";

}
elseif (constant("LOGGED_IN")) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members/console.php'
		</script>
	";
}

require_once("themes/".$THEME."/_footer.php");