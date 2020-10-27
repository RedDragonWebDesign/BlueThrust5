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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");
include_once("../../../../classes/profilecategory.php");
include_once("../../../../classes/profileoption.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$profileCatObj = new ProfileCategory($mysqli);
$profileObj = new ProfileOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
$consoleObj->select($cID);
$_GET['cID'] = $cID;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();
	$selectedOption = "";
	$addSQL = "";
	
	
	if($member->hasAccess($consoleObj) && $profileCatObj->select($_POST['catID'])) {

		
		
		if($_POST['oID'] != "" AND $profileObj->SELECT($_POST['oID'])) {
			$addSQL = " AND profileoption_id != '".$_POST['oID']."'";
		
			$profileOptionInfo = $profileObj->get_info_filtered();
		
			if($profileOptionInfo['profilecategory_id'] == $_POST['catID']) {
		
				$arrAssociates = $profileCatObj->getAssociateIDs("ORDER BY sortnum");
				$highestIndex = count($arrAssociates) - 1;
				$arrFlipped = array_flip($arrAssociates);
				if($highestIndex > 0) {
		
		
					if($arrFlipped[$_POST['oID']] == $highestIndex) {
						$temp = $highestIndex-1;
						$selectedOption = $arrAssociates[$temp];
					}
					else {
						$temp = $arrFlipped[$_POST['oID']]+1;
						$selectedConsole = $arrAssociates[$temp];
					}
		
				}
		
		
			}
		
		}
		
		
		
		
		$profileCatInfo = $profileCatObj->get_info_filtered();	
	
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."profileoptions WHERE profilecategory_id = '".$profileCatInfo['profilecategory_id']."'".$addSQL." ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {
			$strSelect = "";
			if($row['profileoption_id'] == $selectedOption) {
				$strSelect = "selected";
			}
			
			$dispOptions .= "<option value='".$row['profileoption_id']."' ".$strSelect.">".filterText($row['name'])."</option>";
			
		}
		
		
		if($result->num_rows == 0) {
			$dispOptions = "<option value='first'>(no other profile options)</option>";	
		}
		
		echo $dispOptions;
	}
	
	
	
}



?>