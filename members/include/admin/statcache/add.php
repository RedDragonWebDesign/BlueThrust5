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
include_once("../../../../classes/rank.php");



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$manageCID = $consoleObj->findConsoleIDByName("Manage Games Played");
$consoleObj->select($manageCID);

$checkAccess1 = $member->hasAccess($consoleObj);

$addCID = $consoleObj->findConsoleIDByName("Add Games Played");
$consoleObj->select($addCID);

$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = $checkAccess1 || $checkAccess2;

if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	
	$memberInfo = $member->get_info_filtered();
	
	if($checkAccess) {
		
		
		if($_POST['submit']) {
			
			$countErrors = 0;
			
			// Check Stat Name
			if(trim($_POST['statName'] == "")) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>middot;</b> You must enter a stat name.<br>";
			}
			
			// Check Stat type
			if($_POST['statType'] == "calculate") {
				if(count($_SESSION['btStatCache']) < 2) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp<b>&middot;</b> You must have at least two input stats before having a calculated stat.<br>";
				}
				else {
					// Check the for two calculated stats and the math operation
					
					
					// Check operation
					$possibleOps = array("add", "sub", "mul", "div");
					if(!in_array($_POST['calcOperation'], $possibleOps)) {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You selected an invalid operation. - ".$_POST['calcOperation']."<br>";
					}
					
					//Check First Stat
					if(trim($_SESSION['btStatCache'][$_POST['firstStat']]['statName']) == "") {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You selected an invalid first calculation statistic.";	
					}
					
					
					//Check Second Stat
					if(trim($_SESSION['btStatCache'][$_POST['secondStat']]['statName']) == "") {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You selected an invalid second calculation statistic.";
					}
					
					
					
					
				}
			}
			elseif($_POST['statType'] != "inputnum" AND $_POST['statType'] != "inputtext") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You selected an invalid stat type.<br>";	
			}
			
			
			// Check Rounding
			if($_POST['rounding'] != "" AND !is_numeric($_POST['rounding'])) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You may only enter a number for rounding.<br>";
			}
			
			if($_POST['rounding'] < 0) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You may not enter a negative value for rounding.<br>";	
			}
			
			if($_POST['hideStat'] != 1) {
				$_POST['hideStat'] = 0;
			}
			
			
			if($countErrors == 0) {
			
				$_POST = filterArray($_POST);
				
				if($_POST['statType'] == "inputnum" OR $_POST['statType'] == "inputtext") {
					$_POST['firstStat'] = "";
					$_POST['secondStat'] = "";
					$_POST['calcOperation'] = "";					
				}
				
				$intInputText = 0;
				if($_POST['statType'] == "inputtext") {
					$intInputText = 1;
					$_POST['statType'] = "input";
				}
				elseif($_POST['statType'] == "inputnum") {
					$_POST['statType'] = "input";
				}
				
				
				$_SESSION['btStatCache'][] = array(
				
						'statName' => $_POST['statName'],
						'statType' => $_POST['statType'],
						'calcOperation' => $_POST['calcOperation'],
						'firstStat' => $_POST['firstStat'],
						'secondStat' => $_POST['secondStat'],
						'rounding' => $_POST['rounding'],
						'hideStat' => $_POST['hideStat'],
						'textInput' => $intInputText
				
				);
			
				echo "
					<script type='text/javascript'>
						$(document).ready(function() {
						
							$('#loadingSpiral').show();
							$('#statList').hide();
							$.post('".$MAIN_ROOT."members/include/admin/statcache/view.php', { }, function(data) {
								$('#statList').html(data);
								$('#statList').fadeOut(400);
								$('#loadingSpiral').hide();
								$('#statList').fadeIn(400);
							});
						
						
							$('#addNewStatForm').dialog('close');
							
							
						});
					</script>
				";
			
			
			}
			else {
				$_POST['submit'] = false;
					
			}
			
		}
		
		
		if(!$_POST['submit']) {
			
			$statOptions = "<option value='inputnum'>Input (Number)</option><option value='inputtext'>Input (Text)</option>";
			$onChange = "";
			if(is_array($_SESSION['btStatCache'])) {				
				if(count($_SESSION['btStatCache']) > 1) {
					
					$countStats = 0;
					foreach($_SESSION['btStatCache'] as $key => $statInfo) {
						if($statInfo['textInput'] == 0) {
							$statIDOptions .= "<option value='".$key."'>".filterText($statInfo['statName'])."</option>";
							$countStats++;
						}
					}
					
					
					if($countStats > 1) {
						$statOptions .= "<option value='calculate'>Auto-Calculate</option>";
					}
					
					$onChange = "onchange='changeStatsForm()'";
				}
			}

			
			
			
			
			if($dispError != "") {
				echo "
				<div class='errorDiv' style='width: 400px'>
				<strong>Unable to add new stat because the following errors occurred:</strong><br><br>
				$dispError
				</div>
				";
			}
			
			echo "
				<table align='center' border='0' cellspacing='2' cellpadding='2' width='400'>
					<tr>
						<td class='formLabel'>Stat Name:</td>
						<td class='main'><input type='text' id='gpStatName' class='textBox' style='width: 150px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Stat Type:</td>
						<td class='main'>
							<select id='gpStatType' class='textBox' ".$onChange.">
								".$statOptions."
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Hide Stat:</td>
						<td class='main'><input type='checkbox' id='gpHideStat' class='textBox' value='1' onmouseover=\"showToolTip('Hide Stats to create more complex auto-calculated stats... Or if you just don\'t want this stat to be shown on the site.')\" onmouseout='hideToolTip()'></td>
					</tr>
					<tr>
						<td class='formLabel'><div id='inputNumericRoundingTitle'>Rounding:</div></td>
						<td class='main'><div id='inputNumericRoundingTxtBox'><input type='text' id='gpRoundingInputNumeric' class='textBox' style='width: 30px' value='2'> decimals</div></td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
							<div id='gpFormFormula' style='display: none'>
								<br>
								<b>Formula</b>
								<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'><div style='display: none; padding-left: 5px' id='gpFormFirstStat'>First Stat:</div></td>
						<td class='main'><div style='display: none' id='gpFirstStatIDInput'><select id='gpFirstStatID' class='textBox'>".$statIDOptions."</select></div></td>
					</tr>
					<tr>
						<td class='formLabel'><div style='display: none; padding-left: 5px' id='gpFormCalcOp'>Operation:</div></td>
						<td class='main'><div style='display: none' id='gpCalcOpInput'><select id='gpCalcOp' class='textBox'><option value='add'>Plus</option><option value='sub'>Minus</option><option value='mul'>Multiplied By</option><option value='div'>Divided By</option></select></div></td>
					</tr>
					<tr>
						<td class='formLabel'><div style='display: none; padding-left: 5px' id='gpFormSecondStat'>Second Stat:</div></td>
						<td class='main'><div style='display: none' id='gpSecondStatIDInput'><select id='gpSecondStatID' class='textBox'>".$statIDOptions."</select></div></td>
					</tr>
					<tr>
						<td class='formLabel'><div style='display: none; padding-left: 5px' id='gpFormRounding'>Rounding:</div></td>
						<td class='main'><div style='display: none' id='gpRoundingInput'><input type='text' id='gpRounding' class='textBox' style='width: 30px' value='2'> decimals</div></td>
					</tr>
				</table>
				
				
				<script type='text/javascript'>
					function changeStatsForm() {
					
						$(document).ready(function() {
							
							if($('#gpStatType').val() == 'calculate') {
								
								$('#gpFormFormula').show();
							
								$('#gpFormFirstStat').show();
								$('#gpFirstStatIDInput').show();
								$('#gpFirstStatID').show();
								
								$('#gpFormCalcOp').show();
								$('#gpCalcOpInput').show();
								$('#gpCalOp').show();
								
								$('#gpFormSecondStat').show();
								$('#gpSecondStatIDInput').show();
								$('#gpSecondStatID').show();
								
								$('#gpFormRounding').show();
								$('#gpRoundingInput').show();
								
							}
							else {
							
								$('#gpFormFormula').hide();
							
								$('#gpFormFirstStat').hide();
								$('#gpFirstStatIDInput').hide();
								$('#gpFirstStatID').hide();
								
								$('#gpFormCalcOp').hide();
								$('#gpCalcOpInput').hide();
								$('#gpCalOp').hide();
								
								$('#gpFormSecondStat').hide();
								$('#gpSecondStatIDInput').hide();
								$('#gpSecondStatID').hide();
								
								$('#gpFormRounding').hide();
								$('#gpRoundingInput').hide();
								
							}
							
							
							
							
							if($('#gpStatType').val() == \"inputnum\") {
								$('#inputNumericRoundingTitle').show();
								$('#inputNumericRoundingTxtBox').show();
							}
							else {
								$('#inputNumericRoundingTitle').hide();
								$('#inputNumericRoundingTxtBox').hide();							
							}
							
							
						
						});

					}
				</script>
				
			";
		}
		
		
		
	}
	else {
		header("HTTP/1.0 404 Not Found");
	}
	
	
	
}
else {
	header("HTTP/1.0 404 Not Found");
}






?>