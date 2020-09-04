<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	
	// Types of application components
	$typeOptions = array(
		"input" => "Input",
		"largeinput" => "Large-Input",
		"select" => "Select",
		"multiselect" => "Multi-Select",
		"captcha" => "Captcha",
		"captchaextra" => "Captcha - Extra Distortion",
		"profile" => "Profile Option"
	);
	
	
	// Selectable Component Options (when select or multi-select is selected)
	$acCounter = 0;
	$additionalComponents = array(
	
		"optionvalue" => array(
			"type" => "text",
			"attributes" => array("class" => "formInput textBox", "id" => "optionValue", "style" => "width: 30%"),
			"display_name" => "Option Value",
			"html" => "<div class='formInput formInputSideText' style='padding-top: 0px'><input type='button' class='submitButton' value='Add' id='addOptionValueBtn'></div>",
			"sortorder" => $acCounter++
		),
		"optionlist" => array(
			"type" => "custom",
			"html" => "<div id='optionValueList' class='main formInput' style='height: 75px; overflow: auto'></div>",
			"sortorder" => $acCounter++,
			"display_name" => "Option List"
		)
	
	);
	
	// Profile Option Components 
	
	$currentCat = "mainprofile";
	$profileSelectOptions = array(
		"mainprofile" => "Default Profile Options",
		"birthday" => "Birthday",
		"gamesplayed" => "Games Played",
		"maingame" => "Main Game",
		"recruiter" => "Recruiter"		
	);
	$profileCatOptions = array("mainprofile");
	
	$profileCatTable = $dbprefix."profilecategory";
	$profileOptionTable = $dbprefix."profileoptions";
	$query = "SELECT ".$profileCatTable.".name AS catName, ".$profileCatTable.".profilecategory_id, ".$profileOptionTable.".name, ".$profileOptionTable.".profileoption_id FROM ".$profileOptionTable.", ".$profileCatTable." WHERE ".$profileOptionTable.".profilecategory_id = ".$profileCatTable.".profilecategory_id ORDER BY ".$profileCatTable.".ordernum DESC, ".$profileOptionTable.".sortnum";
	$result = $mysqli->query($query);
	while($row = $result->fetch_assoc()) {
		$checkCat = "profilecat_".$row['profilecategory_id'];
		if($currentCat != $checkCat) {

			$profileSelectOptions[$checkCat] = filterText($row['catName']);
			$profileCatOptions[] = $checkCat;
			
		}

		
		$profileSelectOptions[$row['profileoption_id']] = filterText($row['name']);	
	}
	
	$profileComponents = array(
		"profileoption" => array(
			"type" => "select",
			"options" => $profileSelectOptions,
			"sortorder" => 1,
			"display_name" => "Select Option",
			"attributes" => array("class" => "formInput textBox", "id" => "profileOptionID"),
			"non_selectable_items" => $profileCatOptions
		)
	);
	
	$dispRequiredValue = (isset($appCompInfo)) ? $appCompInfo['required'] : "0";
	
	$i = 0;
	$addAppForm = new Form();
	$arrComponents = array(
	
		"name" => array(
			"display_name" => "Name",
			"type" => "text",
			"attributes" => array("class" => "textBox formInput", "id" => "componentName"),
			"sortorder" => $i++
		),
		"type" => array(
			"type" => "select",
			"options" => $typeOptions,
			"attributes" => array("class" => "textBox formInput", "id" => "componentType"),
			"sortorder" => $i++,
			"display_name" => "Type"
		),
		"required" => array(
			"type" => "checkbox",
			"options" => array(1 => ""),
			"attributes" => array("class" => "formInput", "id" => "componentRequiredCB"),
			"sortorder" => $i++,
			"display_name" => "Required",
			"html" => "<input type='hidden' id='componentRequired' value='".$dispRequiredValue."'><div id='captchaMessage' class='formInput formInputSideText tinyFont' style='padding-top: 0px; display: none'><i>Captcha's are automatically required.</i></div>"
		),
		"tooltip" => array(
			"type" => "textarea",
			"attributes" => array("class" => "textBox formInput", "id" => "componentTooltip"),
			"sortorder" => $i++,
			"display_name" => "Tooltip"
		),
		"morecomponents" => array(
			"type" => "section",
			"attributes" => array("id" => "moreComponentOptions", "style" => "display: none"),
			"components" => $additionalComponents,
			"options" => array("section_title" => "Selectable Options"),
			"sortorder" => $i++		
		),
		"profilecomponents" => array(
			"type" => "section",
			"attributes" => array("id" => "profileComponentOptions"),
			"components" => $profileComponents,
			"sortorder" => $i++,
			"options" => array("section_title" => "Profile Options")		
		)
	
	);
	
	
	$setupAppForm = array(
		"name" => "member-app-setup",
		"components" => $arrComponents,
		"wrapper" => ""
	
	);
	
	
	$addAppForm->buildForm($setupAppForm);
		
	echo "
	
		<script type='text/javascript'>
			
			$(document).ready(function() {
				
			
				$('#componentRequiredCB').click(function() {
					
					if($(this).is(':checked')) {
						$('#componentRequired').val('1');
					}
					else {
						$('#componentRequired').val('0');
					}
					
				});
			
				$('#componentType').change(function() {
				
					if($('#componentType').val() == 'select' || $('#componentType').val() == 'multiselect') {
						$('#moreComponentOptions').show();					
					}
					else {
						$('#moreComponentOptions').hide();
					}
					
					
					if($('#componentType').val() == 'captcha' || $('#componentType').val() == 'captchaextra') {
						$('#componentRequiredCB').attr('disabled', 'disabled');
						$('#captchaMessage').show();
					}
					else {
						$('#componentRequiredCB').attr('disabled', false);
						$('#captchaMessage').hide();
					}
					
					if($('#componentType').val() == 'profile') {
						$('#profileComponentOptions').show();
					}
					else {
						$('#profileComponentOptions').hide();
					}
				
				});
				
				$('#addOptionValueBtn').click(function() {
				
					$('#optionValueList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/membermanagement/include/appcomponentcache.php', { action: 'add', newOptionValue: $('#optionValue').val() }, function(data) {
						$('#optionValueList').html(data);
						$('#optionValue').val('');
						$('#optionValueList').fadeIn(250);
					});
				
				});
				
				
				
				$.post('".$MAIN_ROOT."members/include/membermanagement/include/appcomponentcache.php', { }, function(data) {
					$('#optionValueList').html(data);				
				});
			
				$('#componentType').change();
				
			});
		
			function deleteOptionValue(intValueKey) {
			
				$(document).ready(function() {
					$('#optionValueList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/membermanagement/include/appcomponentcache.php', { action: 'delete', deleteOptionKey: intValueKey }, function(data) {
						$('#optionValueList').html(data);
						$('#optionValue').val('');
						$('#optionValueList').fadeIn(250);
					});
				});
			
			}
			
			
		</script>
		
	";
?>