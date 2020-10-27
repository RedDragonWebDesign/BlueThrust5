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

$prevFolder = "../../../../";
include_once($prevFolder."_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Member Application");
$consoleObj->select($cID);

$appComponentObj = new BasicOrder($mysqli, "app_components", "appcomponent_id");


if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	
	
	if($appComponentObj->select($_POST['appCompID'])) {
		
		$appCompInfo = $appComponentObj->get_info_filtered();
		$appComponentObj->set_assocTableName("app_selectvalues");
		$appComponentObj->set_assocTableKey("appselectvalue_id");

		include(BASE_DIRECTORY."members/include/membermanagement/include/appcomponent_form.php");
		if($_POST['saveComponent']) {
			
			
			// Check Component Name
			
			if(trim($_POST['saveComponentName']) == "") {
				$addAppForm->errors[] = "You can't have a blank component name.<br>";
			}
			
			if(!in_array($_POST['saveComponentType'], array_keys($typeOptions))) {
				$addAppForm->errors[] .= "You selected an invalid component type.<br>";
			}
			
			
			
			if(count($addAppForm->errors) == 0) {
				
				if($_POST['saveComponentRequired'] != 0) {
					$_POST['saveComponentRequired'] = 1;
				}
				
				$arrColumns = array("name", "componenttype", "required", "tooltip");
				$arrValues = array($_POST['saveComponentName'], $_POST['saveComponentType'], $_POST['saveComponentRequired'], $_POST['saveComponentTooltip']);
				

				if($appComponentObj->update($arrColumns, $arrValues)) {
					if($appCompInfo['componenttype'] == "select" || $appCompInfo['componenttype'] == "multiselect" || $appCompInfo['componenttype'] == "profile") {
						$mysqli->query("DELETE FROM ".$dbprefix."app_selectvalues WHERE appcomponent_id = '".$appCompInfo['appcomponent_id']."'");
					}
					
					
					if($_POST['saveComponentType'] == "select" || $_POST['saveComponentType'] == "multiselect") {
						$appComponentSelectOptionObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
						foreach($_SESSION['btAppComponent']['cOptions'] as $optionValue) {
							$appComponentSelectOptionObj->addNew(array("appcomponent_id", "componentvalue"), array($appCompInfo['appcomponent_id'], $optionValue));
						}
					}
					elseif($_POST['saveComponentType'] == "profile") {
						$appComponentSelectOptionObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
						$appComponentSelectOptionObj->addNew(array("appcomponent_id", "componentvalue"), array($appCompInfo['appcomponent_id'], $_POST['profileOptionID']));		
					}
					
					
					$member->logAction("Modified the member application.");
					
					echo "
					
						<div id='editAppComponentSuccess' style='display: none'>
							<p class='main' align='center'>
								Member Application Component Saved!<br><br>
								Click OK to continue modifying the member application.
							</p>
						</div>
						
						<script type='text/javascript'>
							$(document).ready(function() {
								
								$('#editAppComponentSuccess').dialog({
								
									title: 'Edit Application Component',
									modal: true,
									zIndex: 99999,
									show: 'scale',
									width: 450,
									resizable: false,
									buttons: {
										'OK': function() {
										
											$('#loadingSpiral').show();
											$('#appComponentList').fadeOut(250);
											$.post('".$MAIN_ROOT."members/include/membermanagement/include/appcomponentlist.php', { }, function(data) {
												$('#appComponentList').html(data);
												$('#loadingSpiral').hide();
												$('#appComponentList').fadeIn(250);				
											});
										
										
											$(this).dialog('close');
										}
									}
								
								});
								
								$('#appComponentForm').dialog('close');
								
							
							});
						</script>
					
					
					";
					
				}
				else {
					$addAppForm->errors[] = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.";
				}
				
				
			}
			
			
			if(count($addAppForm->errors) > 0) {
				$_POST['saveComponent'] = false;
			}
			
			
			
		}
		
		
		if(!$_POST['saveComponent']) {
		
			
			
			if(($appCompInfo['componenttype'] == "select" || $appCompInfo['componenttype'] == "multiselect") && $countErrors == 0) {
				$appSelectOptionObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
				$arrSelectValues = $appComponentObj->getAssociateIDs();
			
				$tempArr = array();
				foreach($arrSelectValues as $selectValueID) {
					
					$appSelectOptionObj->select($selectValueID);
					$appSelectValue = $appSelectOptionObj->get_info_filtered("componentvalue");
					
					$tempArr[$selectValueID] = $appSelectValue;
				}
				
				asort($tempArr);
	
				$_SESSION['btAppComponent']['cOptions'] = $tempArr;
				
			}
			elseif($countErrors == 0) {
				$_SESSION['btAppComponent']['cOptions'] = array();
			}
			
			
		
		
		}
				
		
	}
	else {
		echo "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#appComponentForm').dialog('close');
				});
			</script>
		";
	}
	
	
	$addAppForm->components['name']['value'] = $appCompInfo['name'];
	$addAppForm->components['type']['value'] = $appCompInfo['componenttype'];
	$addAppForm->components['required']['value'] = $appCompInfo['required'];
	$addAppForm->components['tooltip']['value'] = $appCompInfo['tooltip'];
	
	if($appCompInfo['componenttype'] == "profile") {
		
		$appSelectValueID = $appComponentObj->getAssociateIDs();
		$appSelectValueObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
		$appSelectValueObj->select($appSelectValueID[0]);
		
		$addAppForm->components['profilecomponents']['components']['profileoption']['value'] = $appSelectValueObj->get_info("componentvalue");
	}
	
	echo "<div id='addAppComponentFormDialog'>";
	
	$addAppForm->show();
	
	echo "</div>";
}



?>