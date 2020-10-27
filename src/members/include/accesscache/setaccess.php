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
	
	include("../../../_setup.php");
	include_once("../../../classes/member.php");
	include_once("../../../classes/rank.php");
	include_once("../../../classes/access.php");

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$rankObj = new Rank($mysqli);
	
	$accessObj = new Access($mysqli);
	
	if($member->authorizeLogin($_SESSION['btPassword']) && isset($_POST['cacheID']) && isset($_POST['accessType']) && isset($_POST['accessInfo'])) {

		$accessObj->cacheID = $_POST['cacheID'];
		$accessInfo = json_decode($_POST['accessInfo'], true);

		
		if($_POST['accessType'] == "rank") {
			$objSelector = $rankObj;
			$sessionPrefix = "rankaccess_";
			$sessionName = "btAccessCache";
		}
		else {
			$objSelector = $member;
			$sessionName = "btMemberAccess";
		}
		
		
	
		foreach($accessInfo as $checkBoxName => $accessTypeValue) {
			
			$selectorID = ($_POST['accessType'] == "rank") ? str_replace($sessionPrefix, "", $checkBoxName) : $checkBoxName;
			
			if($accessTypeValue == 0 && $objSelector->select($selectorID)) {
				$_SESSION[$sessionName][$_POST['cacheID']][$checkBoxName] = 0;
				unset($_SESSION[$sessionName][$_POST['cacheID']][$checkBoxName]);
			}
			elseif(is_numeric($accessTypeValue) && $objSelector->select($selectorID)) {
				$_SESSION[$sessionName][$_POST['cacheID']][$checkBoxName] = $accessTypeValue;				
			}
			
		}
		
		
		define("SHOW_ACCESSCACHE", true);
		include("viewcache.php");
	}


?>