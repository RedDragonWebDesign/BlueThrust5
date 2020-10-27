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

	$prevFolder = "../../";
	include("../../_setup.php");
	include("youtube.php");
	
	$ytObj = new Youtube($mysqli);
	$arrReturn = array();
	if(isset($_POST['yID']) && is_numeric($_POST['yID']) && $ytObj->select($_POST['yID'])) {

		$ytInfo = $ytObj->get_info_filtered();

		if((time()-$ytInfo['lastupdate']) > 1800) {
			$ytObj->reloadCache();
			
			$arrReturn['result'] = "success";
			$arrReturn['html'] = $ytObj->dispSubscribeButton();
			$arrReturn['time'] = getPreciseTime(time());

		}
		else {
			$arrReturn['result'] = "error";
			$arrReturn['message'] = "Reload Limit Reached";	
		}
		
	}
	else {
		$arrReturn['result'] = "error";
		$arrReturn['message'] = "Invalid yID";
	}
	
	
	echo json_encode($arrReturn);

?>