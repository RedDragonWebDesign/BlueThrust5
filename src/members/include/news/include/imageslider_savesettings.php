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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/imageslider.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$countErrors = 0;
	$dispErrors = "";
	$widthUnit = ($_POST['containerWidthUnit'] == 1) ? "px" : "%";
	$heightUnit = ($_POST['containerHeightUnit'] == 1) ? "px" : "%";
	$displayType = ($_POST['displayStyle'] == "slider") ? "slider" : "random";
	
	if(!is_numeric($_POST['containerWidth'])) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Display width must be a numeric value.<br>";
		$countErrors++;	
	}
	
	if(!is_numeric($_POST['containerHeight'])) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Display height must be a numeric value.<br>";
		$countErrors++;
	}
	
	if($countErrors == 0) {
		$arrColumns = array("hpimagetype", "hpimagewidth", "hpimageheight", "hpimagewidthunit", "hpimageheightunit");
		$arrValues = array($displayType, $_POST['containerWidth'], $_POST['containerHeight'], $widthUnit, $heightUnit);
		if($webInfoObj->multiUpdate($arrColumns, $arrValues)) {
			echo "
				
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#saveSuccess').fadeIn(200).delay(3000).fadeOut(200);
					});
				</script>
			";
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save the information to the database.<br>";
		}
	}
	
	if($countErrors > 0) {
		
		echo "
			
			<span id='errorMessages'>
				<strong>Unable to save image settings because the following errors occurred:</strong><br><br>
				".$dispError."
			</span>
			
			<script type='text/javascript'>
			
				$(document).ready(function() {
				
					$('#errorDiv').html($('#errorMessages').html());
					$('#errorDiv').show();
					$('html, body').animate({ scrollTop: 0 });
				
				});
			
			</script>
		
		";
	}
	
}


?>