<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2013
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

include($prevFolder."_setup.php");
include_once("classes/member.php");
$siteDomain = $_SERVER['SERVER_NAME'];
$dispError = "";
$countErrors = 0;
if(isset($_POST['countErrors'])) {
$countErrors = $_POST['countErrors'];
}


// Classes needed for index.php

$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}

// Start Page
$PAGE_NAME = 'Forgot Password'." - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

if(LOGGED_IN) {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You are logged in and cannot use this feature. Please use the change password in the console.<br>";
}

if(count($_GET) > 0 && !isset($_GET['stage'])) {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Stage data not properly defined.<br>";
}
elseif(!isset($_GET['stage'])) {
$stage = 'start';
}
else {
$stage = $_GET['stage'];
}

if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
    $dispHTTP = "http://";
   }
   else {
    $dispHTTP = "https://";
   }
$url=$dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$forgotPassObj = new Basic($mysqli, "forgotpass", "rqid");
$memberObj = new Member($mysqli);

$breadcrumbObj->setTitle("Forgot Password");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Forgot Password");
include($prevFolder."include/breadcrumb.php");
?>

<?php
if ($stage == "start"  && $countErrors == 0) {
echo "
<form action='forgotpassword.php?stage=send' method='post'>
	<input type='hidden' name='validator' value='20473833234' />
	<div class='formDiv'>
		This form will help you in resetting a forgotten password. It will send an email to your accounts registered email address. In that email will be a link you must click. The link will bring you back here and allow you to set a new password.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Username:</td>
					<td class='main'><input type='text' class='textBox' name='username'></td>
				</tr>
				<tr>
					<td class='formLabel'>Account Email:</td>
					<td class='main'><input type='text' class='textBox' name='email'></td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'><br><input type='submit' class='submitButton' value='Request Change'></td>
				</tr>
		</table>
	</div>
</form>
";
}
elseif ($stage == "send"  && $countErrors == 0) {
if(isset($_POST['validator'])) {
if($_POST['validator'] != '20473833234') {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Validator Entry Not Correct. Most likely due to an invalid form submission.<br>";
}
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Validator Entry Not Existant. Most likely due to an invalid form submission.<br>";
}
$username = $_POST['username'];
$email = $_POST['email'];
$changekey = sha1(rand(-100000, 100000) . rand(-100000, 100000) . $username);
$time = time();
if($memberObj->select($username)) {
if($memberObj->get_info("email") == $email) {
$emailvalid = true;
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Email Address Not Valid.<br>";
}
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Username Not Valid.<br>";
}
if($countErrors == 0) {
$arrayCol = array('username', 'email', 'changekey', 'timeofrq');
$arrayVal = array($username, $email, $changekey, $time);
$forgotPassObj->addNew($arrayCol, $arrayVal);

$subject = 'Your Forgotten Password Request - ' . $CLAN_NAME;

$message = "
<html>
<body>
Hello,<br>
You've requested a change in your password on the clan website.<br>
<br>
Please click the following link to continue and follow the instructions on the page it opens:<br>
--------------------------------------------<br>
<a href='$url?stage=validate&changekey=$changekey'>$url?stage=validate&changekey=$changekey</a><br>
<br>
Thanks!
";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: '.$CLAN_NAME.' <no-reply@'.$siteDomain.'>' . "\r\n";

mail($email, $subject, $message, $headers);
echo "
<div class='formDiv'>
Your request has been successfully submitted. Please check your email for the link and further instructions.
</div>
";
}
}
elseif ($stage == "validate"  && $countErrors == 0) {
$changekey = $mysqli->real_escape_string($_GET['changekey']);
$forgotPassObj->set_tableKey("changekey");
if($forgotPassObj->select($changekey, false)) {
$dataArr = $forgotPassObj->get_info();
$rqid = $dataArr['rqid'];
$username = $dataArr['username'];
$email = $dataArr['email'];
$timeofrq = $dataArr['timeofrq'];
$timeofrqcon = date('l jS \of F Y h:i:s A', $timeofrq);
echo "
<form action='forgotpassword.php?stage=set' method='post'>
<input type='hidden' name='changekey' value='$changekey' />
<div class='formDiv'>
<strong>Validated!</strong><br>Please type in your new password.<br><br>
<strong>Data:</strong><br>
Request ID: $rqid<br>
Username: $username<br>
Email: $email<br>
Time of Request: $timeofrqcon Server Time<br>
<table class='formTable'>
<tr>
<td class='formLabel'>New Password:</td>
<td class='main'><input type='password' class='textBox' name='newpass' id='newpassword'></td>
</tr>
<tr>
<td class='formLabel'>Confirm New Password:</td>
<td class='main'><input type='password' class='textBox' name='connewpass' id='newpassword1'><span id='checkPassword' style='padding-left: 5px'></span></td>
</tr>
<tr>
<td class='main' colspan='2' align='center'><input type='submit' class='submitButton' style='width: 125px' value='Submit Change'></td>
</tr>
</table>
</div>
</form>

<script type='text/javascript'>
   
   $(document).ready(function() {
   
    $('#newpassword1').keyup(function() {
     
     if($('#newpassword').val() != '') {
     
      if($('#newpassword1').val() == $('#newpassword').val()) {
       $('#checkPassword').toggleClass('successFont', true);
       $('#checkPassword').toggleClass('failedFont', false);
       $('#checkPassword').html('Passwords Match! OK!');
      }
      else {
       $('#checkPassword').toggleClass('successFont', false);
       $('#checkPassword').toggleClass('failedFont', true);
       $('#checkPassword').html('Passwords Do Not Match!');
      }
     
     }
     else {
      $('#checkPassword').html('');
     }
    
    });
   
   });
  
  </script>
";
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Not a Valid Changekey.<br>";
}
}
elseif ($stage == "set" && isset($_POST['newpass']) && isset($_POST['changekey'])  && $countErrors == 0) {
$newpass = $_POST['newpass'];
$newpasscon = $_POST['connewpass'];
$changekey = $_POST['changekey'];
if($newpass != $newpasscon) {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The two passwords did not match. Please click the back button in your browser and try again.<br>";
}

if($countErrors == 0) {
$forgotPassObj->set_tableKey("changekey");
if($forgotPassObj->select($changekey, false)) {
$dataArr = $forgotPassObj->get_info();
$username = $dataArr['username'];
$email = $dataArr['email'];
if($memberObj->select($username)) {
if($memberObj->get_info("email") == $email) {
$emailvalid = true;
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> POST Validation Failed. Email Validation Error.<br>";
}
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> POST Validation Failed. Username Validation Error.<br>";
}
}
else {
$countErrors++;
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> POST Validation Failed. Changekey Validation Error.<br>";
}
if($emailvalid == true && $countErrors == 0) {
$memberObj->set_password($newpass);
$forgotPassObj->delete();
echo "
<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 40%'>
	<p class'main' align='center'>
		<b>Password Successfully Changed!</b> You can now log in to the site with your new password!
	</p>
</div>
";
}
}
}
elseif ($countErrors == 0) {
$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Not a proper setup definition.<br>";
}
if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to recover password because the following errors occurred:</strong><br><br>
		$dispError
		<br>
		</div>
		";
	}
?>

<?php include($prevFolder."themes/".$THEME."/_footer.php"); ?>