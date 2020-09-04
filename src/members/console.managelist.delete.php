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

include("../_setup.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $_GET['cID'];
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$_SERVER['PHP_SELF'] = "console.php";
$_GET['action'] = "delete";

if(substr($consoleInfo['filename'], 0, strlen("../")) != "../") {
	$requireFile = BASE_DIRECTORY."members/include/".$consoleInfo['filename'];
}
else {
	$requireFile = $consoleInfo['filename'];	
}

require($requireFile);
if(!isset($objManageList)) {
	exit();	
}


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	define("LOGGED_IN", true);
	
	if($member->hasAccess($consoleObj)) {

		if(!$objManageList->blnConfirmDelete || (isset($_POST['confirm']) && $objManageList->blnConfirmDelete)) {
			
			$objManageList->delete();
			
			include($objManageList->strMainListLink);
			include(BASE_DIRECTORY."members/console.managelist.list.php");
			
		}
		else {
			echo "
				<p class='main' align='center'>
					Are you sure you want to delete <b>".$objManageList->strDeleteName."</b>?
				</p>
			";
		}
		
	}
	
	
}

?>