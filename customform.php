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


// Config File
$prevFolder = "";

include_once($prevFolder."_setup.php");
include_once($prevFolder."classes/customform.php");

// Classes needed for index.php


$customFormObj = new CustomForm($mysqli);

if(!$customFormObj->select($_GET['pID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
}


$customPageInfo = $customFormObj->get_info();

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
$PAGE_NAME = $customPageInfo['name']." - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($customPageInfo['name']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb($customPageInfo['name']);

include($prevFolder."include/breadcrumb.php");

$arrComponents = $customFormObj->getComponents();
$dispError = "";
$countErrors = 0;

if($_POST['submit']) {
	
	// Check for multi submissions
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."customform_submission WHERE ipaddress = '".$IP_ADDRESS."' ORDER BY submitdate DESC LIMIT 1");
	if($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		if((time()-$row['submitdate']) < 120) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> To prevent abuse you must wait 2 minutes before submitting again.<br>";			
		}
	}
	
	if($countErrors == 0) {
		
		$arrColumns = array("submitdate", "ipaddress", "customform_id");
		$arrValues = array(time(), $IP_ADDRESS, $customPageInfo['customform_id']);
		
		if($customFormObj->objSubmission->addNew($arrColumns, $arrValues)) {
			$submissionInfo = $customFormObj->objSubmission->get_info();
			foreach($arrComponents as $componentID) {
				$customFormObj->objComponent->select($componentID);
				$componentInfo = $customFormObj->objComponent->get_info_filtered();
				
				if($componentInfo['componenttype'] == "separator") {
					continue;	
				}
				
				
				$formComponentName = "customform_".$componentID;
				
				$arrSelectValues = $customFormObj->getSelectValues($componentID);
				
				// Check if required
				if($componentInfo['required'] == 1 && $componentInfo['componenttype'] != "multiselect" && trim($_POST[$formComponentName]) == "") {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> ".$componentInfo['name']." may not be blank.<br>";
				}
				elseif($componentInfo['required'] == 1 && $componentInfo['componenttype'] == "multiselect") {
					$countMultiSelect = 0;
					foreach($arrSelectValues as $selectValueID) {
						$multiSelectName = $formComponentName."_".$selectValueID;
						if($_POST[$multiSelectName] == 1) {
							$countMultiSelect++;
						}
					}
					
					if($countMultiSelect == 0) {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> ".$componentInfo['name']." may not be blank.<br>";
					}
				}
				
				// Check Select Value
				
				if($componentInfo['componenttype'] == "select" && !in_array($_POST[$formComponentName], $arrSelectValues)) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid value for ".$componentInfo['name'].".<br>";
				}
				
				
				if($countErrors == 0) {
					$arrColumns = array("submission_id", "component_id", "formvalue");
					if($componentInfo['componenttype'] == "multiselect") {
						foreach($arrSelectValues as $selectValueID) {
							$multiSelectName = $formComponentName."_".$selectValueID;
							$customFormObj->objSelectValue->select($selectValueID);
							$selectValue = $customFormObj->objSelectValue->get_info_filtered("componentvalue");
							if($_POST[$multiSelectName] == 1 && !$customFormObj->objFormValue->addNew($arrColumns, array($submissionInfo['submission_id'], $componentID, $selectValue))) {
								$countErrors++;
								$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save informtaion for ".$componentInfo['name'].".<br>";
							}
							
						}
					}
					elseif($componentInfo['componenttype'] == "select") {
						$customFormObj->objSelectValue->select($_POST[$formComponentName]);
						$selectValue = $customFormObj->objSelectValue->get_info_filtered("componentvalue");
						if(!$customFormObj->objFormValue->addNew($arrColumns, array($submissionInfo['submission_id'], $componentID, $selectValue))) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save informtaion for ".$componentInfo['name'].".<br>";
						}
						
					}
					elseif(($componentInfo['componenttype'] == "input" || $componentInfo['componenttype'] == "largeinput") && !$customFormObj->objFormValue->addNew($arrColumns, array($submissionInfo['submission_id'], $componentID, $_POST[$formComponentName]))) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save informtaion for ".$componentInfo['name'].".<br>";
					}
					
				}
				else {
					
					$mysqli->query("DELETE FROM ".$dbprefix."customform_values WHERE submission_id = '".$submissionInfo['submission_id']."'");
					$customFormObj->objSubmission->delete();
					
					break;	
				}

				
			}
		
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
		}
		
		
		
	}
	
	
	
	if($countErrors == 0) {
		
		if($customPageInfo['submitmessage'] == "") {
			$customPageInfo['submitmessage'] = "<p align='center'>Success!</p>";	
		}
		
		if($customPageInfo['submitlink'] == "") {
			$customPageInfo['submitlink'] = $MAIN_ROOT;	
		}

		
		if($customPageInfo['specialform'] == "") {
			echo "
			
				<div style='display: none' id='successBox'>
					".$customPageInfo['submitmessage']."
				</div>
				<script type='text/javascript'>
					popupDialog('".$customPageInfo['name']."', '".$customPageInfo['submitlink']."', 'successBox');
				</script>
			";
		}
		else {
			
			echo "
				<div style='display: none' id='successBox'>
					".$customPageInfo['submitmessage']."
				
					<form action='".$customPageInfo['submitlink']."' method='post'>
						";
	
					foreach($arrComponents as $value) {
						
						$tempName = "customform_".$value;
						echo "	
							<input type='hidden' name='".$tempName."' value='".$_POST[$tempName]."'>
						";
						
					}
								
					echo "
						<input type='submit' name='submit' id='btnSubmitCustomForm' style='display: none'>
					</form>
				</div>
				<script type='text/javascript'>
				$(document).ready(function() {
		
					$('#successBox').dialog({
						title: '".$customPageInfo['name']."',
						modal: true,
						zIndex: 99999,
						width: 400,
						resizable: false,
						show: 'scale',
						buttons: {
							'Ok': function() {
								$(this).dialog('close');
								//$('#btnSubmitCustomForm').click();
							}
						},
						beforeClose: function() {
							$('#btnSubmitCustomForm').click();
						}
						
					});
					$('.ui-dialog :button').blur();
					
					
				});
				</script>
			
			";
			
		}
		
		$member = new Member($mysqli);
		$member->selectAdmin();
		
		
		$consoleObj = new ConsoleOption($mysqli);
		$viewSubmissionsCID = $consoleObj->findConsoleIDByName("View Custom Form Submissions");
		$member->postNotification("There is a new submission for custom form: <b>".$customPageInfo['name']."</b><br><a href='".$MAIN_ROOT."members/console.php?cID=".$viewSubmissionsCID."'>View Form Submissions</a>");
		
		
	}
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
		
	}
	
}

if(!$_POST['submit']) {
	echo "<div class='formDiv'>";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to submit form because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo $customPageInfo['pageinfo'];
	
	echo "
	
		<form action='".$MAIN_ROOT."customform.php?pID=".$_GET['pID']."' method='post'>
			<table class='formTable'>
			";
			
			foreach($arrComponents as $componentID) {
				
				$customFormObj->objComponent->select($componentID);
				$componentInfo = $customFormObj->objComponent->get_info_filtered();
				$dispInput = "";
				$componentFormName = "customform_".$componentID;
				switch($componentInfo['componenttype']) {
					case "largeinput":
						$dispInput = "<textarea name='customform_".$componentID."' class='textBox' rows='4' style='width: 250px'>".$_POST[$componentFormName]."</textarea>";
						break;
					case "select":
						$selectoptions = "";
						$arrSelectValues = $customFormObj->getSelectValues($componentID);
						foreach($arrSelectValues as $selectValueID) {
							$customFormObj->objSelectValue->select($selectValueID);
							$selectValue = $customFormObj->objSelectValue->get_info_filtered("componentvalue");
							$selectoptions .= "<option value='".$selectValueID."'>".$selectValue."</option>";
						}
					
						$dispInput = "<select name='".$componentFormName."' class='textBox'>".$selectoptions."</select>";
						break;
					case "multiselect":
						$selectoptions = "";
						$arrSelectValues = $customFormObj->getSelectValues($componentID);
						foreach($arrSelectValues as $selectValueID) {
							$customFormObj->objSelectValue->select($selectValueID);
							$selectValue = $customFormObj->objSelectValue->get_info_filtered("componentvalue");
							$dispInput .= "<input type='checkbox' value='1' name='".$componentFormName."_".$selectValueID."'> ".$selectValue."<br>";
						}
						break;
					case "input":
						
						$dispInput = "<input type='text' value='".$_POST[$componentFormName]."' name='".$componentFormName."' class='textBox' style='width: 150px'>";			
				}
				
				$dispRequired = "";
				if($componentInfo['required'] == 1) {
					$dispRequired = "<span class='failFont' title='Required' style='cursor: default'>*</span>";	
				}
				
				$dispToolTip = "";
				if($componentInfo['tooltip'] != "") {
					$dispToolTip = "<div style='display: none' id='tooltip_".$componentID."'>".nl2br($componentInfo['tooltip'])."</div> <a href='javascript:void(0)' onmouseover=\"showToolTip($('#tooltip_".$componentID."').html())\" onmouseout='hideToolTip()'><b>(?)</b></a>";				
				}
				
				
				if($componentInfo['componenttype'] != "separator") {
					echo "
						<tr>
							<td class='formLabel' valign='top'>".$componentInfo['name'].": ".$dispRequired.$dispToolTip."</td>
							<td class='main' valign='top'>".$dispInput."</td>
						</tr>
					
					";
				}
				else {
					echo "
						<tr>
							<td colspan='2' class='main'><br>
								<b>".$componentInfo['name']."</b>
								<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
								<div style='padding-left: 3px; padding-bottom: 15px'>
									".nl2br($componentInfo['tooltip'])."
								</div>
							</td>
						</tr>
					";
				}
				
	
			}
	
			echo "
				<tr>
					<td class='main' align='center' colspan='2'><br><br>
					
						<input type='submit' name='submit' class='submitButton' value='Submit'>
					
					</td>
				</tr>
			</table>
		</form>
	";
	
	
	echo "</div>";

}
include($prevFolder."themes/".$THEME."/_footer.php"); 



?>