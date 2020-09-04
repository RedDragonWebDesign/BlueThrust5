<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	$donationLogForm = new Form();

	$maxYear = date("Y")+10;
	$maxDate = "new Date(".$maxYear.",12,31)";
	
	$dateObj = new DateTime();
	$dateObj->setTimestamp($_GET['start']);
	$dateObj->setTimezone(new DateTimeZone("UTC"));
	
	$defaultStartDate = $dateObj->format("M j, Y");

	$dateObj->setTimestamp($_GET['end']);
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
							   "dateFormate" => "M d, yy", 
							   "minDate" => "new Date(50, 1, 1)", 
							   "maxDate" => $maxDate, 
							   "yearRange" => "1950:".$maxYear, 
							   "altField" => "realStartDate",
							   "defaultDate" => $defaultStartDate),
					"value" => $_GET['start']*1000
				),
				"enddate" => array(
					"type" => "datepicker",
					"display_name" => "End Date",
					"attributes" => array("style" => "cursor: pointer", "id" => "jsEndDate", "class" => "textBox formInput"),
					"sortorder" => $i++,
					"options" => array("changeMonth" => "true", 
							   "changeYear" => "true", 
							   "dateFormate" => "M d, yy", 
							   "minDate" => "new Date(50, 1, 1)", 
							   "maxDate" => $maxDate, 
							   "yearRange" => "1950:".$maxYear, 
							   "altField" => "realEndDate",
							   "defaultDate" => $defaultEndDate),
					"value" => $_GET['end']*1000
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
				
				window.location = '".MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&campaignID=".$_GET['campaignID']."&p=log&start='+($('#realStartDate').val()/1000)+'&end='+($('#realEndDate').val()/1000);				
			
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

?>