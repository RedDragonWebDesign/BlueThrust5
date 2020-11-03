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

// Config File
$prevFolder = "";

require_once($prevFolder."_setup.php");

// Start Page
$PAGE_NAME = "Sign Up - ";
require_once($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);
$rankObj = new Rank($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$memberAppObj = new MemberApp($mysqli);

$appComponentObj = $memberAppObj->objAppComponent;
$appSelectValueObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
$profileOptionObj = new ProfileOption($mysqli);

if( $websiteInfo['memberregistration'] == 1 ) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."'
		</script>
	";
	exit();
}

$breadcrumbObj->setTitle("Sign Up");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Sign Up");
require_once($prevFolder."include/breadcrumb.php");

require_once(BASE_DIRECTORY."include/signup_form.php");

if ( ! empty($_POST['submit']) ) {
	$additionalSuccessInfo = "<br><br>You must wait to be approved by a member to become a full member on the website.";
	
	if($memberAppObj->save() && $websiteInfo['memberapproval'] == 0) {
		$memberAppObj->addMember();
		$additionalSuccessInfo = "<br><br>You may now log in to your account.";
	}
	
	if ( ! empty($_POST['submit']) ) {
		$signUpForm->saveMessage = "<span class='main'>".$signUpForm->saveMessage.$additionalSuccessInfo."</span>";
		
		$signUpForm->showSuccessDialog();
	}
}

if ( empty($_POST['submit']) ) {

	$signUpForm->show();
	
	
	echo "
	<script type='text/javascript'>
	
		$(document).ready(function() {
		
			$(\"a[data-refresh='1']\").click(function() {
						
				var imgDivID = '#'+$(this).attr('data-image');
				
				$(imgDivID).fadeOut(250);
				
				
				$.post('".$MAIN_ROOT."images/captcha.php?display=1&appCompID='+$(this).attr('data-appid'), { }, function(data) {
					$(imgDivID).html(data);
					$(imgDivID).fadeIn(250);
				});
				
			});
		
		});
	
	</script>
	
	";

} ?>

<?php require_once($prevFolder."themes/".$THEME."/_footer.php");