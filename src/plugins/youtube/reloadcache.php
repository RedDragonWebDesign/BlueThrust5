<?php

	/*
	 * BlueThrust Clan Scripts
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
	require_once("../../_setup.php");
	require_once("youtube.php");

	$ytObj = new Youtube($mysqli);
	$arrReturn = array();
	if (isset($_POST['yID']) && is_numeric($_POST['yID']) && $ytObj->select($_POST['yID'])) {

		$ytInfo = $ytObj->get_info_filtered();

		if ((time()-$ytInfo['lastupdate']) > 1800) {
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