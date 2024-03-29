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

if (!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
} else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

$arrComponents = [
	"currentpassword" => [
		"display_name" => "Current Password",
		"type" => "password",
		"sortorder" => 1,
		"attributes" => ["class" => "textBox formInput"],
		"validate" => ["NOT_BLANK", "changePasswordChecks"]
	],
	"newpassword" => [
		"display_name" => "New Password",
		"type" => "password",
		"sortorder" => 2,
		"attributes" => ["class" => "textBox formInput", "id" => "newpassword"],
		"validate" => ["NOT_BLANK", ["name" => "EQUALS_VALUE", "value" => $_POST['newpassword1']], ["name" => "CHECK_LENGTH", "min_length" => 4]]
	],
	"newpassword1" => [
		"display_name" => "Re-type New Password",
		"type" => "custom",
		"html" => "<input type='password' id='newpassword1' name='newpassword1' class='textBox formInput'><span id='checkPassword' class='formInput' style='padding-left: 5px'></span>",
		"sortorder" => 3,
		"attributes" => ["class" => "textBox formInput", "id" => "newpassword1"],
		"validate" => ["NOT_BLANK"]
	],
	"submit" => [
		"type" => "submit",
		"sortorder" => 4,
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"value" => "Change Password"
	]

];

$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"afterSave" => ["savePassword"],
	"saveMessage" => "Successfully changed password!",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"description" => "Use the form below to change your password."
];


$formObj->prefillValues = false;


echo "
		<script type='text/javascript'>
			
			$(document).ready(function() {
			
				$('#newpassword1').keyup(function() {
					
					if($('#newpassword').val() != \"\") {
					
						if($('#newpassword1').val() == $('#newpassword').val()) {
							$('#checkPassword').toggleClass('successFont', true);
							$('#checkPassword').toggleClass('failedFont', false);
							$('#checkPassword').html('ok!');
						}
						else {
							$('#checkPassword').toggleClass('successFont', false);
							$('#checkPassword').toggleClass('failedFont', true);
							$('#checkPassword').html('error!');
						}
					
					}
					else {
						$('#checkPassword').html('');
					}
				
				});
			
			});
		
		</script>	
	";


// Change Password Check

function changePasswordChecks() {
	global $formObj, $member;
	if (!$member->authorizeLogin($_POST['currentpassword'], 1)) {
		$formObj->errors[] = "You entered an incorrect current password.";
	}
}


// Custom Save Function

function savePassword() {
	global $member;
	$member->set_password($_POST['newpassword']);
	$_SESSION['btPassword'] = $member->get_info("password");
}
