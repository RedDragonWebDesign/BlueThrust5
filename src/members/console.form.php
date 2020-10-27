<?php

	if(!defined("LOGGED_IN") || !LOGGED_IN) { die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."'</script>"); }
	
	
		
	$hooksObj->run("console_forms");
	$formObj->buildForm($setupFormArgs);
	
	
	if($_POST['submit']) {
		
		if($formObj->save()) {
			
			$formObj->saveMessageTitle = $consoleInfo['pagetitle'];
			
			$formObj->showSuccessDialog();
			
		}
		

		if(count($formObj->errors) > 0) {
			$_POST = filterArray($_POST);
			if($formObj->prefillValues) {
				$formObj->prefillPostedValues();
			}
			$_POST['submit'] = false;		
		}
		
		
	}
	
	
	if(!$_POST['submit']) {
		$formObj->show();	
	}
	
	
	
?>