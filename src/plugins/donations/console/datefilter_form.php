<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	$donationLogForm = new Form();

	$maxYear = date("Y")+10;
	$maxDate = "new Date(".$maxYear.",12,31)";
	
	$dateObj = new DateTime();
	$dateObj->setTimezone(new DateTimeZone("UTC"));
	
	$setStartValue = $_GET['start'];
	$setEndValue = $_GET['end'];
	
	if(is_numeric($_GET['start'])) {

		$dateObj->setTimestamp($_GET['start']);
				
	}
	else {
		$startDate = explode("-", $_GET['start']);
		$dateObj->setDate($startDate[2], $startDate[0], $startDate[1]);
		$setStartValue = $dateObj->getTimestamp();
	}
	
	$defaultStartDate = $dateObj->format("M j, Y");

	if(is_numeric($_GET['end'])) {
		$dateObj->setTimestamp($_GET['end']);
	}
	else {
		$endDate = explode("-", $_GET['end']);
		$dateObj->setDate($endDate[2], $endDate[0], $endDate[1]);
		$setEndValue = $dateObj->getTimestamp();
	}

	$defaultEndDate = $dateObj->format("M j, Y");
	
	
	$i=0;
	$arrComponents = array(
		"sectionLeft" => array(
			"type" => "section",
			"attributes" => array("style" => "float: left"),
			"sortorder" => $i++,
			"components" => array(
				"totaldonated" => array(
					"type" => "custom",
					"display_name" => "Total Donated",
					"sortorder" => $i++,
					"html" => "<div class='formInput'>".$campaignObj->formatAmount($totalDonated)."</div>"
				),
				"totaldonations" => array(
					"type" => "custom",
					"display_name" => "Total Donations",
					"sortorder" => $i++,
					"html" => "<div class='formInput'>".$totalDonations."</div>"
				)
			)
			
		),
		"sectionRight" => array(
			"type" => "section",
			"attributes" => array("style" => "float: right; margin-bottom: 10px"),
			"sortorder" => $i++,
			"components" => array(
				"startdate" => array(
					"type" => "datepicker",
					"display_name" => "Start Date",
					"attributes" => array("style" => "cursor: pointer", "id" => "jsStartDate", "class" => "textBox formInput"),
					"sortorder" => $i++,
					"options" => array("changeMonth" => "true", 
							   "changeYear" => "true", 
							   "dateFormat" => "M d, yy", 
							   "minDate" => "new Date(50, 1, 1)", 
							   "maxDate" => $maxDate, 
							   "yearRange" => "1950:".$maxYear, 
							   "altField" => "realStartDate",
							   "defaultDate" => $defaultStartDate),
					"value" => $setStartValue*1000
				),
				"enddate" => array(
					"type" => "datepicker",
					"display_name" => "End Date",
					"attributes" => array("style" => "cursor: pointer", "id" => "jsEndDate", "class" => "textBox formInput"),
					"sortorder" => $i++,
					"options" => array("changeMonth" => "true", 
							   "changeYear" => "true", 
							   "dateFormat" => "M d, yy", 
							   "minDate" => "new Date(50, 1, 1)", 
							   "maxDate" => $maxDate, 
							   "yearRange" => "1950:".$maxYear, 
							   "altField" => "realEndDate",
							   "defaultDate" => $defaultEndDate),
					"value" => $setEndValue*1000
				),
				"filter" => array(
					"type" => "custom",
					"html" => "<div style='float: right'><br><input type='button' class='submitButton' id='filterButton' value='Show'></div>",
					"sortorder" => $i++
				)
			)
			
		)
	);
	
	$filterButtonJS = "
		$(document).ready(function() {
			$('#filterButton').click(function() {
				
				window.location = '".MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&campaignID=".$_GET['campaignID']."&p=log&start='+($('#realStartDate').val())+'&end='+($('#realEndDate').val());				
			
			});
		});
	";
	
	
	
	
	$setupDonationFormArgs = array(
		"name" => "console-".$cID."-donationlog",
		"components" => $arrComponents,
		"description" => "Use the form below to filter the dates of donations.",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"wrapper" => array("<div class='formDiv' style='overflow: auto'>", "</div>"),
		"embedJS" => $filterButtonJS
	);
	
	$donationLogForm->buildForm($setupDonationFormArgs);
	
	$donationLogForm->show();