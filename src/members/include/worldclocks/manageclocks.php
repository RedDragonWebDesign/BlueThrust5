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


	if (!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
		exit();
	} else {
		$memberInfo = $member->get_info_filtered();
		$consoleObj->select($_GET['cID']);
		if (!$member->hasAccess($consoleObj)) {
			exit();
		}

	}


	$objManageList = new btOrderManageList($clockObj);
	$objManageList->strMainListLink = BASE_DIRECTORY . "members/include/worldclocks/main.php";


	if (isset($_GET['clockID']) && $_GET['clockID'] != "" && $clockObj->select($_GET['clockID']) && isset($_GET['action']) && $_GET['action'] == "edit") {
		$clockInfo = $clockObj->get_info_filtered();
		require_once(BASE_DIRECTORY . "members/include/worldclocks/edit.php");
	} elseif (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_POST['itemID']) && $clockObj->select($_POST['itemID'])) {
		$info = $clockObj->get_info_filtered();
		$objManageList->strDeleteName = $info['name'];
		$objManageList->strDeletePostVarID = "clockID";
	} elseif (!isset($_GET['action']) || $_GET['action'] != "move") {
		require_once($objManageList->strMainListLink);
	}