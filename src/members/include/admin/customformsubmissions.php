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


include_once("../classes/customform.php");

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

$customFormPageObj = new CustomForm($mysqli, "custompages", "custompage_id");



if(isset($_GET['cfID']) && $customFormPageObj->select($_GET['cfID'])) {
	
	
	echo "
	
		<div id='loadingSpiral' style='display: none'>
			<p align='center' class='main'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
	
		<div id='submissionsDiv'>
	
	";
	include("custompages/submissiondetail.php");
	echo "</div>";
	
	echo "

		<script type='text/javascript'>
	
		
			function deleteSubmission(intSubmissionID) {
			
				$('#loadingSpiral').show();
				$('#submissionsDiv').fadeOut(250);
			
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."members/include/admin/custompages/include/deletesubmission.php', { cfID: '".$_GET['cfID']."', subID: intSubmissionID }, function(data) {
					
						$('#submissionsDiv').html(data);
						$('#loadingSpiral').hide();
						$('#submissionsDiv').fadeIn(250);					
					
					});
				
				});
			}
		
		</script>
	
	";
	
	
}
else {
	
	
	include("custompages/main_submissions.php");
	
	
}


?>