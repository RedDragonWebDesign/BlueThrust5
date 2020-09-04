<?php

include_once("../../_setup.php");
include_once("../../classes/member.php");

$member = new Member($mysqli);
$consoleObj = new ConsoleOption($mysqli);

$websiteSettingsCID = $consoleObj->findConsoleIDByName("Website Settings");
$consoleObj->select($websiteSettingsCID);

if(!isset($_SESSION['btUsername']) || !isset($_SESSION['btPassword']) || !$member->select($_SESSION['btUsername']) || ($member->select($_SESSION['btUsername']) && !$member->authorizeLogin($_SESSION['btPassword'])) || ($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword']) && !$member->hasAccess($consoleObj))) {
	header("HTTP/1.0 404 Not Found");
	exit();
}


$menuSQL = file_get_contents("savemenu.sql");

if($menuSQL !== false) {

	//$menuSQL = str_replace("INSERT INTO `", "INSERT INTO `".$dbprefix, $menuSQL);
	
	
	$emptyMenusSQL = "TRUNCATE `".$dbprefix."menuitem_customblock`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menuitem_custompage`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menuitem_image`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menuitem_link`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menuitem_shoutbox`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menu_category`;";
	$emptyMenusSQL .= "TRUNCATE `".$dbprefix."menu_item`;";
	
	
	$fullSQL = $emptyMenusSQL.$menuSQL;
	
	if($mysqli->multi_query($fullSQL)) {
	
	
		do {
			if($result = $mysqli->store_result()) {
				$result->free();
			}
		}
		while($mysqli->next_result());
		
		echo "1";
		
		
	}

}
else {
	echo "2";
}

?>

