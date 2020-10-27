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

	
	include(BASE_DIRECTORY."members/include/worldclocks/clock_form.php");
	
	
	
?>