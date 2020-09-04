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


echo "
	<div class='formDiv'>
		Below is a listing of all diplomacy requests.
	
		
		<div id='diplomacyRequests'>
	";
		
		include("include/diplomacyrequests.php");

	echo "
		</div>
		
	</div>
	<div id='declineRequestResponse' style='display: none'></div>
	<script type='text/javascript'>
	
		function declineRequest(intRequestID) {
			$(document).ready(function() {
		
				$.post('".$MAIN_ROOT."members/include/diplomacy/include/declinerequest.php', { reqID: intRequestID }, function(data) {
					$('#declineRequestResponse').html(data);				
				});
			
			});
		}
	
	</script>
";


?>

