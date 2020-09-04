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


$cID = $_GET['cID'];
$regOptionsCID = $consoleObj->findConsoleIDByName("Registration Options");
$regMessage = "You must approve member applications before the applicant becomes a full member on the website.  In order to use member applications, you must have open registration.  You can change this setting on the <a href='".$MAIN_ROOT."members/console.php?cID=".$regOptionsCID."'>Registration Options</a> page.";

if($websiteInfo['memberapproval'] == 0) {
	$regMessage = "All member applications are currently set to be automatically accepted, however you can still view the applications.  You can change this setting on the <a href='".$MAIN_ROOT."members/console.php?cID=".$regOptionsCID."'>Registration Options</a> page.";
}

echo "

	<div class='formDiv'>
		Below is a listing of all member applications.<br><br>".$regMessage."
		
		<div class='loadingSpiral' id='loadingSpiral' style='display: none'>
			<p align='center' class='main'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
		
		<div id='memberApplications'>
		";
		
		include("include/memberapplist.php");

echo "
		</div>
		<br>
	</div>
	
	<div id='declineAppDiv' style='display: none'></div>
	<script type='text/javascript'>

		function acceptApp(intAppID) {
			$(document).ready(function() {
				
				$('#loadingSpiral').show();
				$('#memberApplications').fadeOut(250);
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/acceptmemberapp.php', { mAppID: intAppID }, function(data) {
					$('#memberApplications').html(data);
					$('#loadingSpiral').hide();
					$('#memberApplications').fadeIn(250);
				});
			});		
		}
		
		function removeApp(intAppID) {
			$(document).ready(function() {
				
				$('#loadingSpiral').show();
				$('#memberApplications').fadeOut(250);
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/removememberapp.php', { mAppID: intAppID }, function(data) {
					$('#memberApplications').html(data);
					$('#loadingSpiral').hide();
					$('#memberApplications').fadeIn(250);
				});
			});	
		}
		
		function declineApp(intAppID) {
			$(document).ready(function() {
				
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/declinememberapp.php', { mAppID: intAppID }, function(data) {
					$('#declineAppDiv').html(data);
				});
			});	
		
		}
	
	</script>
	
";


?>


