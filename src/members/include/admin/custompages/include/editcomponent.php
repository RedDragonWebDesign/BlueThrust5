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


include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/customform.php");



$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Add Custom Form Page");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);
$cID = $consoleObj->findConsoleIDByName("Manage Custom Form Pages");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);


$customFormObj = new CustomForm($mysqli);
$appComponentObj = $customFormObj->objComponent;

$componentIndex = $_POST['whichComponent'];



if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2) && is_numeric($componentIndex)) {
	$countErrors == 0;
	$dispError = "";
	
	
	if($_POST['editComponent']) {
		
		
		$arrTypes = array("input", "largeinput", "select", "multiselect", "separator");
		
		// Check Name
		
		if(trim($_POST['componentName']) == "") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Component name may not be blank.<br>";
		}
		
		// Check Component Type
		
		if(!in_array($_POST['componentType'], $arrTypes)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid component type.<br>";
		}
		
		
		$intRequired = 1;
		if($_POST['componentRequired'] != 1) {
			$intRequired = 0;
		}
		
		if($countErrors == 0) {
		
			$_SESSION['btFormComponent'][$componentIndex]['name'] = $_POST['componentName'];
			$_SESSION['btFormComponent'][$componentIndex]['type'] = $_POST['componentType'];
			$_SESSION['btFormComponent'][$componentIndex]['required'] = $_POST['componentRequired'];
			$_SESSION['btFormComponent'][$componentIndex]['tooltip'] = $_POST['componentToolTip'];
		
			
			$_SESSION['btFormComponent'][$componentIndex]['cOptions'] = $_SESSION['btFormComponentTempSelectValues'];
			
		
			echo "
				<script type='text/javascript'>
		
					$(document).ready(function() {
				
						$('#formComponentList').fadeOut(250);
						$('#loadingSpiral').show();
						$.post('".$MAIN_ROOT."members/include/admin/custompages/include/componentcache.php', { }, function(data) {
					
							$('#formComponentList').html(data);
							$('#loadingSpiral').hide();
							$('#formComponentList').fadeIn(250);
						
						});
				
						$('#componentDump').dialog('close');
				
					});
		
				</script>
			";
		
		}
		
		if($countErrors > 0) {
			$_POST = filterArray($_POST);
			$_POST['editComponent'] = false;
		
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
				
						$(\".add-component-dialog .ui-button-text:contains('Please Wait...')\").text('Edit Component');
				
					});
				</script>
			";
		
			
			$arrComponentInfo['type'] = $_POST['componentType'];
			$arrComponentInfo['name'] = $_POST['componentName'];
			$arrComponentInfo['required'] = $_POST['componentRequired'];
			$arrComponentInfo['tooltip'] = $_POST['componentToolTip'];
			
			
		}
		
		
	}
	
	
	if(!$_POST['editComponent']) {
	
		
		if($dispError != "") {
			
			
			
		}
		else {
		
			$_SESSION['btFormComponentTempSelectValues'] = $_SESSION['btFormComponent'][$componentIndex]['cOptions'];
			$arrComponentInfo = filterArray($_SESSION['btFormComponent'][$componentIndex]);
		}
		
		$arrSelectedType = array();
		$checkRequired = "";
		switch($arrComponentInfo['type']) {
			case "largeinput":
				$arrSelectedType['largeinput'] = " selected";
				break;
			case "select":
				$arrSelectedType['select'] = " selected";
				break;
			case "multiselect":
				$arrSelectedType['multiselect'] = " selected";
				break;
			case "separator":
				$arrSelectedType['separator'] = " selected";
				break;
		
		}
		
		if($arrComponentInfo['required'] == 1) {
			$checkRequired = " checked";
		}
	
		
		echo "
		
				<div id='addComponentForm'>
					<table class='formTable' style='width: 90%'>
						<tr>
							<td class='main' style='width: 25%'><b>Name:</b></td>
							<td class='main' style='width: 75%'><input type='text' class='textBox' value='".$arrComponentInfo['name']."' id='componentName'></td>
						</tr>
						<tr>
							<td class='main' style='width: 25%'><b>Type:</b></td>
							<td class='main' style='width: 75%'>
								<select id='componentType' class='textBox'>
									<option value='input'>Input</option>
									<option value='largeinput'".$arrSelectedType['largeinput'].">Large-Input</option>
									<option value='select'".$arrSelectedType['select'].">Select</option>
									<option value='multiselect'".$arrSelectedType['multiselect'].">Multi-Select</option>
									<option value='separator'".$arrSelectedType['separator'].">Separator</option>
								</select>
								<span id='separatorInfo' style='display: none'>
									<a href='javascript:void(0)' onmouseover=\"showToolTip('Enter text into the tooltip box to add a description that will be displayed below the separator.')\" onmouseout='hideToolTip()'><b>(?)</b></a>
								</span>
							</td>
						</tr>
						<tr>
							<td class='main' style='width: 25%' valign='top'><b>Tooltip:</b></td>
							<td class='main' style='width: 75%'>
								<textarea id='componentToolTip' class='textBox' style='width: 200px; height: 40px'>".$arrComponentInfo['tooltip']."</textarea>
							</td>
						</tr>
						<tr>
							<td class='main' style='width: 25%'><b>Required:</b></td>
							<td class='main' style='width: 75%'><input type='checkbox' id='componentRequiredFake'".$checkRequired."><input type='hidden' value='".$arrComponentInfo['required']."' id='componentRequired'></td>
						</tr>
				
					</table>
					
					<div id='addComponentSelectInputForm' style='display: none'>
						
						<table class='formTable' style='width: 90%'>
							<tr>
								<td class='main dottedLine' colspan='2'>
									<b>Selectable Options</b>
								</td>
							</tr>
							<tr>
								<td class='main' style='width: 25%'><b>Option Value:</b></td>
								<td class='main' style='width: 75%'><input type='text' id='selectValue' class='textBox'> <input type='button' id='btnAddSelectValue' class='submitButton' style='width: 40px' value='Add'></td>
							</tr>
							<tr>
								<td class='main' style='width: 25%' valign='top'><b>Option List:</b></td>
								<td class='main' style='width: 75%'>
									<div id='selectValueList'>
									
									
									
									</div>
								</td>
							</tr>
						</table>
						
					</div>
				</div>
				<script type='text/javascript'>
				
					$(document).ready(function() {
					
					
						$('#btnAddSelectValue').click(function() {
							$.post('".$MAIN_ROOT."members/include/admin/custompages/include/selectvaluecache.php', { action: 'add', optionValue: $('#selectValue').val(), whichValue: '".$componentIndex."' }, function(data) {
							
								$('#selectValueList').html(data);
							
								$('#selectValue').val('');
								$('#selectValue').focus();
							});
						});
					
					
						
						$('#componentType').change(function() {
						
							
							if($('#componentType').val() == 'select' || $('#componentType').val() == 'multiselect') {
								$('#addComponentSelectInputForm').show();
							}
							else {
								$('#addComponentSelectInputForm').hide();
							}
							
							if($('#componentType').val() == 'separator') {
								$('#separatorInfo').show();
							}
							else {
								$('#separatorInfo').hide();
							}
							
						});
						
						$('#componentRequiredFake').click(function() {
							if($('#componentRequiredFake').is(':checked')) {
								$('#componentRequired').val('1');				
							}
							else {
								$('#componentRequired').val('0');
							}
						});
						
						
						$('#componentType').change();
						
						$.post('".$MAIN_ROOT."members/include/admin/custompages/include/selectvaluecache.php', { whichValue: '".$componentIndex."' }, function(data) {
							
								$('#selectValueList').html(data);
							
							});
					
					});
					
					
					function deleteSelectValue(intKey) {
					
						$(document).ready(function() {
							$('#selectValueList').fadeOut(250);
							$.post('".$MAIN_ROOT."members/include/admin/custompages/include/selectvaluecache.php', { action: 'delete', whichValue: '".$componentIndex."', deleteKey: intKey }, function(data) {
								$('#selectValueList').html(data);
								$('#selectValueList').fadeIn(250);
							});
						
						});
					
					}
				
				</script>
		
		";
	
	
	
	}
	
}


?>