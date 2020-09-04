<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	
	$signUpForm = $memberAppObj->objSignUpForm;
	$appComponentObj->defaultCounter = 0;
	$arrComponents = $appComponentObj->getDefaultInputCode();
	
	$i = $appComponentObj->defaultCounter;
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."app_components ORDER BY ordernum DESC");
	if($result->num_rows > 0) {
		
		$arrComponents['applicationquestions'] = array(
			"type" => "section",
			"options" => array("section_title" => "Application Questions"),
			"sortorder" => $i++
		);
		

		while($row = $result->fetch_assoc()) {
			$appComponentObj->select($row['appcomponent_id']);
			$arrAppCompInfo = filterArray($row);	
			$formInputName = "appcomponent_".$arrAppCompInfo['appcomponent_id'];
			
			$arrComponents[$formInputName] = array(
				"sortorder" => $i++,
				"attributes" => array("class" => "formInput textBox"),
				"display_name" => $arrAppCompInfo['name'],
				"tooltip" => $arrAppCompInfo['tooltip'],
				"type" => "text"
			);
			
			$arrComponents[$formInputName] = array_merge($arrComponents[$formInputName], $appComponentObj->getComponentInputCode());
						
		}
		
		
	}
	
	$arrComponents['submit'] = array(
		"type" => "submit",
		"sortorder" => $i++,
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"value" => "Sign Up"
	);
	
	
	$extraDesc = ($websiteInfo['memberapproval'] == 1) ? " After signing up, you must be approved by a member before becoming a full member on the website." : "";
	$setupSignupForm = array(
		"name" => "signup-form",
		"components" => $arrComponents,
		"description" => "Use the form below to sign up to join ".$websiteInfo['clanname'].".".$extraDesc,
		"attributes" => array("action" => $MAIN_ROOT."signup.php", "method" => "post"),
		"saveLink" => MAIN_ROOT,
		"saveMessage" => "You have successfully signed up to join ".$websiteInfo['clanname']."!",
		"saveMessageTitle" => "Sign Up - Confirmation"
	);
	

	
	$signUpForm->buildForm($setupSignupForm);
	

	
?>