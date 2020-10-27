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

include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/rank.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Add Profile Option");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = $checkAccess1 || $checkAccess2;


if($member->authorizeLogin($_SESSION['btPassword'])) {

	
	$memberInfo = $member->get_info_filtered();
	
	if($checkAccess && isset($_SESSION['btProfileCache'][$_POST['editKey']])) {
		

		
		if($_POST['submit']) {
			
			
			if(trim($_POST['editValue']) != "") {
				
				if($_SESSION['btProfileCache'][$_POST['editKey']] != $_POST['editValue']) {
					$_SESSION['btProfileCacheRefresh'] = true;
				}
				
				$_SESSION['btProfileCache'][$_POST['editKey']] = $_POST['editValue'];
				
				
				include("view.php");
				
			
			}
			
			
		}
		
		if(!$_POST['submit']) {
			
			echo "
			
				<p align='center' class='main'>
					<b>Select Value:</b> <input type='text' id='editvalue' value='".$_SESSION['btProfileCache'][$_POST['editKey']]."' class='textBox'>
				</p>
			
			
			";
			
			
		}
		
		
		
	}
	
}


?>