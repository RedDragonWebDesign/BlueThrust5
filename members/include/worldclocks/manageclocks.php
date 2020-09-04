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
	
	
	$objManageList = new btOrderManageList($clockObj);
	$objManageList->strMainListLink = BASE_DIRECTORY."members/include/worldclocks/main.php";

	
	if($_GET['clockID'] != "" && $clockObj->select($_GET['clockID']) && $_GET['action'] == "edit") {
		$clockInfo = $clockObj->get_info_filtered();
		include(BASE_DIRECTORY."members/include/worldclocks/edit.php");
	}
	elseif($_GET['action'] == "delete" && $clockObj->select($_POST['itemID'])) {
		$info = $clockObj->get_info_filtered();
		$objManageList->strDeleteName = $info['name'];
		$objManageList->strDeletePostVarID = "clockID";	
	}
	elseif($_GET['action'] != "move") {
		include($objManageList->strMainListLink);	
	}
		

?>