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

	include(BASE_DIRECTORY."members/include/membermanagement/include/appcomponent_form.php");
	
	if($_POST['saveComponent']) {
		
		
		// Check Component Name
			
		if(trim($_POST['newComponentName']) == "") {
			$addAppForm->errors[] = "You can't have a blank component name.<br>";
		}
		
		if(!in_array($_POST['newComponentType'], array_keys($typeOptions))) {
			$addAppForm->errors[] = "You selected an invalid component type.<br>";
		}
		
		
		
	if(count($addAppForm->errors) == 0) {
			
			
			if($appComponentObj->getHighestOrderNum() == "") {
				$componentOrderNum = $appComponentObj->validateOrder("first", "before");	
			}
			else {
				$appComponentObj->selectByOrder(1);
				$componentOrderNum = $appComponentObj->makeRoom("after");
			}
			
			if($_POST['newComponentRequired'] != 0) {
				$_POST['newComponentRequired'] = 1;
			}
			
			$arrColumns = array("name", "componenttype", "ordernum", "required", "tooltip");
			$arrValues = array($_POST['newComponentName'], $_POST['newComponentType'], $componentOrderNum, $_POST['newComponentRequired'], $_POST['newComponentTooltip']);
			
			if($appComponentObj->addNew($arrColumns, $arrValues)) {
				
				if($_POST['newComponentType'] == "select" || $_POST['newComponentType'] == "multiselect") {
					$appComponentSelectOptionObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
					$newComponentID = $appComponentObj->get_info("appcomponent_id");
					foreach($_SESSION['btAppComponent']['cOptions'] as $optionValue) {
						$appComponentSelectOptionObj->addNew(array("appcomponent_id", "componentvalue"), array($newComponentID, $optionValue));
					}
					
				}
				elseif($_POST['newComponentType'] == "profile") {
					$appComponentSelectOptionObj = new Basic($mysqli, "app_selectvalues", "appselectvalue_id");
					$newComponentID = $appComponentObj->get_info("appcomponent_id");
					$appComponentSelectOptionObj->addNew(array("appcomponent_id", "componentvalue"), array($newComponentID, $_POST['profileOptionID']));
				}
				
				$member->logAction("Added a new member application component.");
				echo "
					<div id='addAppComponentSuccess' style='display: none'>
						<p class='main' align='center'>
							New Member Application Component Added!<br><br>
							Click OK to continue modifying the member application.
						</p>
					</div>
					
					<script type='text/javascript'>
						$(document).ready(function() {
							
							$('#addAppComponentSuccess').dialog({
							
								title: 'Add Application Component',
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
				$addAppForm->errors[] = "nable to save information to the database.  Please contact the website administrator.";
			}
			
		}
		
		
		if(count($addAppForm->errors) == 0) {
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
					
						$('#addAppComponentFormDialog').hide();
					
					});
				</script>
			";
		}
		
		
		
	}
	
	if(!$_POST['saveComponent']) {
		$_SESSION['btAppComponent']['cOptions'] = array();
	}
	
		
	
	echo "<div id='addAppComponentFormDialog'>";
	
	$addAppForm->show();
	
	echo "</div>";
	
	
}


?>