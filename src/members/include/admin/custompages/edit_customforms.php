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
$customFormPageObj = new CustomForm($mysqli, "custompages", "custompage_id");

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


if(!$customFormPageObj->select($_GET['cfID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}


$customFormInfo = $customFormPageObj->get_info_filtered();
$cID = $_GET['cID'];


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Custom Form Pages</a> > ".$customFormInfo['name']."\");
});
</script>
";




$countErrors = 0;
$dispError = "";
if($_POST['submit']) {
	
	
	if(trim($_POST['pagename']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a page name for your custom page.<br>";
	}
	
	
	if($countErrors == 0) {
		
		$_POST['wysiwygHTML'] = str_replace("<?", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("?>", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("&lt;?", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("?&gt;", "", $_POST['wysiwygHTML']);
		
		$_POST['submitMessageHTML'] = str_replace("<?", "", $_POST['submitMessageHTML']);
		$_POST['submitMessageHTML'] = str_replace("?>", "", $_POST['submitMessageHTML']);
		$_POST['submitMessageHTML'] = str_replace("&lt;?", "", $_POST['submitMessageHTML']);
		$_POST['submitMessageHTML'] = str_replace("?&gt;", "", $_POST['submitMessageHTML']);
		
		$postResults = ($_POST['postresults'] == "yes") ? "yes" : "";
		
		if($customFormPageObj->update(array("name", "pageinfo", "submitmessage", "submitlink", "specialform"), array($_POST['pagename'], $_POST['wysiwygHTML'], $_POST['submitMessageHTML'], $_POST['submitlink'], $postResults)) && $customFormPageObj->addComponents($_SESSION['btFormComponent'])) {

			foreach($_SESSION['btDeleteFormComponent'] as $deleteKey) {
				if($customFormPageObj->objComponent->select($deleteKey)) {
					$checkFormID = $customFormPageObj->get_info("customform_id");
					if($checkFormID == $customFormInfo['customform_id']) {
						$customFormPageObj->objComponent->delete();
						$mysqli->query("DELETE FROM ".$dbprefix."customform_selectvalues WHERE component_id = '".$checkFormID."'");
						$mysqli->query("DELETE FROM ".$dbprefix."customform_values WHERE component_id = '".$checkFormID."'");
					}					
				}	
			}
			
			
			$intManageCustomPagesID = $consoleObj->findConsoleIDByName("Manage Custom Form Pages");
			$customPageInfo = $customFormPageObj->get_info_filtered();
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Edited Custom Page: <b>".$customPageInfo['name']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Edit Custom Form Page', '".$MAIN_ROOT."members/console.php?cID=".$intManageCustomPagesID."&cfID=".$customPageInfo['customform_id']."&action=edit', 'successBox');
				</script>
			";

			
			
		}
		else {
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to add custom page.  Please try again!<br>";
			$_POST['submit'] = false;
			$_POST['wysiwygHTML'] = addslashes($_POST['wysiwygHTML']);
			$_POST['submitMessageHTML'] = addslashes($_POST['submitMessageHTML']);
		}
		
	}
	else {
		$_POST['submit'] = false;
		$_POST['wysiwygHTML'] = addslashes($_POST['wysiwygHTML']);
		$_POST['submitMessageHTML'] = addslashes($_POST['submitMessageHTML']);
	}
	
	
}


if(!$_POST['submit']) {
	
	echo "
	<form action='console.php?cID=".$cID."&cfID=".$customFormInfo['customform_id']."&action=edit' method='post'>
	<div class='formDiv'>
	
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add custom page because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	else {
		
		$_SESSION['btFormComponent'] = array();
		$x = 0; // Form Component Counter
		$arrFormComponents = $customFormPageObj->getComponents();
		foreach($arrFormComponents as $formComponentID) {
			
			$customFormPageObj->objComponent->select($formComponentID);
			
			$tempComponentInfo = $customFormPageObj->objComponent->get_info_filtered();
			
			$_SESSION['btFormComponent'][$x]['name'] = $tempComponentInfo['name'];
			$_SESSION['btFormComponent'][$x]['type'] = $tempComponentInfo['componenttype'];
			$_SESSION['btFormComponent'][$x]['required'] = $tempComponentInfo['required'];
			$_SESSION['btFormComponent'][$x]['tooltip'] = $tempComponentInfo['tooltip'];
			$_SESSION['btFormComponent'][$x]['component_id'] = $tempComponentInfo['component_id'];
			
			
			if($tempComponentInfo['componenttype'] == "select" || $tempComponentInfo['componenttype'] == "multiselect") {
				$arrSelectValues = $customFormPageObj->getSelectValues($formComponentID);
				foreach($arrSelectValues as $selectValueID) {

					$customFormPageObj->objSelectValue->select($selectValueID);
					$selectValue = $customFormPageObj->objSelectValue->get_info_filtered("componentvalue");
					$_SESSION['btFormComponent'][$x]['cOptions'][] = $selectValue;
				
				}
			}
			
			$x++;
		}
		
		
		$_SESSION['btFormComponentCount'] = $x;
		$_SESSION['btDeleteFormComponent'] = array();
	}
	
	
	$postResultsYes = "";
	if($customFormInfo['specialform'] == "yes") {
		$postResultsYes = " selected";	
	}
	$addMenuItemCID = $consoleObj->findConsoleIDByName("Add Menu Item");
	
	echo "
		Use the form below to edit the selected custom form page.  Remember to hit the save button!
		<br><br>
		  In order to display a custom form page in the menu, go to the <a href='".$MAIN_ROOT."members/console.php?cID=".$addMenuItemCID."'>Add Menu Item</a> page.
		<br><br>
		<table class='formTable'>
			<tr>
				<td class='formLabel'>Page Name:</td>
				<td class='main'><input type='text' name='pagename' class='textBox' value='".$customFormInfo['name']."' style='width: 250px'></td>
			</tr>
			<tr>
				<td class='formLabel'>Page URL: <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the URL to use in your link to this custom page.  You cannot edit this field.')\" onmouseout='hideToolTip()'>(?)</a></td>
				<td class='main'><input type='text' class='textBox' onclick=\"$(this).select()\" value='".$MAIN_ROOT."customform.php?pID=".$customFormInfo['customform_id']."' style='width: 250px'></td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br>
					<b>Page Information</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					<div style='padding-left: 3px; padding-bottom: 15px'>
						Use the text editor below to format your custom page.  You may also use HTML by clicking the HTML button.
					</div>
				</td>
			</tr>
			<tr>
				<td colspan='2' style='padding-left: 10px' align='center'>
					<textarea id='tinymceTextArea' name='wysiwygHTML' rows='15' style='width: 80%'>".$customFormPageObj->get_info("pageinfo")."</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br>
					<b>Submission Information</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
				</td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Submit Message: <a href='javascript:void(0)' onmouseover=\"showToolTip('Enter a message to display when the form is submitted.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
				<td class='main' valign='top' style='padding-bottom: 20px'>
					<textarea id='tinymceSubmitMessage' name='submitMessageHTML' rows='3' style='width: 50%; margin-bottom: 20px'>".$customFormPageObj->get_info("submitmessage")."</textarea>
				</td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Submit Link: <a href='javascript:void(0)' onmouseover=\"showToolTip('Enter a URL to direct the user to once the form is submitted.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
				<td class='main' valign='top'>
					<input type='text' name='submitlink' class='textBox' value='".$customFormInfo['submitlink']."' style='width: 250px'>
				</td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Post Results: <a href='javascript:void(0)' onmouseover=\"showToolTip('Only used if a submit link is given.  Set to yes to post the form results to the submit link URL as well.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
				<td class='main' valign='top'>
					<select name='postresults' class='textBox'>
						<option value='no'>No</option><option value='yes'".$postResultsYes.">Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br><br>
					<b>Form Information</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>				
				</td>
			</tr>
			<tr>
				<td class='main' colspan='2' align='center'>
				
					<p align='center'>
						<input type='button' class='submitButton' id='btnAddComponent' value='Add Component' style='width: 150px'>
					</p>
				
					<table class='formTable' style='width: 90%; margin-top: 25px'>
						<tr>
							<td class='formTitle' style='width: 50%'>Component Name:</td>
							<td class='formTitle' style='width: 26%'>Type:</td>
							<td class='formTitle' style='width: 24%'>Actions:</td>
						</tr>
					</table>
					
					<div id='loadingSpiral' style='display: none'>
						<p align='center' class='main'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
					
					<div id='formComponentList'>
		
					

					
					";
					
	
					
	
	
					echo "
					
					</div>
					
					
					
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
				<br><br>
				<input type='submit' class='submitButton' name='submit' value='Save' style='width: 125px'>
				<br>
				</td>
			</tr>
		</table>
		</div>
		</form>
		
		
		<div id='componentDump' style='display: none'></div>

		<script type='text/javascript'>

			$('document').ready(function() {
				$('#tinymceTextArea').tinymce({
			
					script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
					theme: 'advanced',
					theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
					theme_advanced_resizing: true
				
				});
				
				$('#tinymceSubmitMessage').tinymce({
			
					script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
					theme: 'advanced',
					theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
					theme_advanced_resizing: true
				
				});
			
				
				$('#loadingSpiral').show();
				$('#formComponentList').hide();
				$.post('".$MAIN_ROOT."members/include/admin/custompages/include/componentcache.php', { }, function(data) {
				
					$('#formComponentList').html(data);
					$('#loadingSpiral').hide();
					$('#formComponentList').fadeIn(250);
				
				});
				
				
				
				$('#btnAddComponent').click(function() {
				
					
					$.post('".$MAIN_ROOT."members/include/admin/custompages/include/addcomponent.php', { }, function(data) {
					
						$('#componentDump').html(data);
						
						$('#componentDump').dialog({
				
							title: 'Add Custom Form Component',
							width: 450,
							modal: true,
							show: 'scale',
							resizable: false,
							zIndex: 99999,
							dialogClass: 'add-component-dialog',
							buttons: {
							
								'Add Component': function() {
								
									$(\".add-component-dialog .ui-button-text:contains('Add Component')\").text('Please Wait...');
									
									$.post('".$MAIN_ROOT."members/include/admin/custompages/include/addcomponent.php', { addComponent: 1, componentName: $('#componentName').val(), componentType: $('#componentType').val(), componentToolTip: $('#componentToolTip').val(), componentRequired: $('#componentRequired').val() }, function(data) {
										$('#componentDump').html(data);
										
									});
									$('.ui-dialog :button').blur();

								},
								'Cancel': function() {
								
									$(this).dialog('close');
								
								}
							
							
							}
						
						});
						
					
					});
				
				
				});
				
				
			});

	";
	
	if($dispError != "") {
		echo "
			$('#wysiwygDiv').html('".$_POST['wysiwygHTML']."');
			
		";
	}
	
	echo "

	
	
			function editComponent(intComponentIndex) {
				
				$(document).ready(function() {
				
				
				$.post('".$MAIN_ROOT."members/include/admin/custompages/include/editcomponent.php', { whichComponent: intComponentIndex }, function(data) {
					
					$('#componentDump').html(data);
				
					$('#componentDump').dialog({
					
						title: 'Edit Custom Form Component',
						width: 450,
						modal: true,
						show: 'scale',
						resizable: false,
						zIndex: 99999,
						dialogClass: 'add-component-dialog',
						buttons: {
						
							'Save': function() {
							
								$(\".add-component-dialog .ui-button-text:contains('Save')\").text('Please Wait...');
								
								
								$.post('".$MAIN_ROOT."members/include/admin/custompages/include/editcomponent.php', { whichComponent: intComponentIndex, editComponent: 1, componentName: $('#componentName').val(), componentType: $('#componentType').val(), componentToolTip: $('#componentToolTip').val(), componentRequired: $('#componentRequired').val() }, function(data) {
									$('#componentDump').html(data);
								});
								
								
								$('.ui-dialog :button').blur();

							},
							'Cancel': function() {
							
								$(this).dialog('close');
							
							}
						
						
						}
				
					
					});
				
					
					});
					
				});
	
			}
			
			
			
			function moveComponent(intKey, strDir) {
			
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#formComponentList').fadeOut(250);
				
					$.post('".$MAIN_ROOT."members/include/admin/custompages/include/movecomponent.php', { moveDir: strDir, whichComponent: intKey }, function(data) {
					
						$('#formComponentList').html(data);
						$('#loadingSpiral').hide();
						$('#formComponentList').fadeIn(250);
					
					});
				
				});
						
			}
			
			
			function deleteComponent(intKey) {
			
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#formComponentList').fadeOut(250);
				
					$.post('".$MAIN_ROOT."members/include/admin/custompages/include/deletecomponent.php', { whichComponent: intKey }, function(data) {
					
						$('#formComponentList').html(data);
						$('#loadingSpiral').hide();
						$('#formComponentList').fadeIn(250);
					
					});
				
				});
						
			}
			
	
		</script>
	";
	
	
	
	
}


?>