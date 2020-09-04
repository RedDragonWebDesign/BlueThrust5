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
include_once("../../../../classes/consolecategory.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleCatObj = new ConsoleCategory($mysqli);
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($cID);

$counter = 0;
if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $consoleCatObj->select($_POST['catID'])) {
		
		$addSQL = "";
		$selectedConsole = "";
		if($_POST['cnID'] != "" && $consoleObj->SELECT($_POST['cnID'])) {
			$addSQL = " AND console_id != '".$_POST['cnID']."'";
			
			$consoleInfo = $consoleObj->get_info_filtered();
			
			if($consoleInfo['consolecategory_id'] == $_POST['catID']) {
				
				$arrBeforeAfter = $consoleObj->findBeforeAfter();
				$selectedConsole = $arrBeforeAfter[0];
			}
			
		}
		
		define('MEMBERRANK_ID', $memberInfo['rank_id']);
		$consoleCatInfo = $consoleCatObj->get_info_filtered();
		
		
		
		$arrConsoles = $consoleCatObj->getAssociateIDs();
		$sqlConsoles = "('".implode("','", $arrConsoles)."')";
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."console WHERE console_id IN ".$sqlConsoles.$addSQL." ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {
			$strSelect = "";
			if($row['console_id'] == $selectedConsole) {
				$strSelect = "selected";	
			}
			echo "<option value='".$row['console_id']."' ".$strSelect.">".filterText($row['pagetitle'])."</option>";
			$counter++;
		}
		
		
	}
	
	
}

if($counter == 0) {
	echo "<option value='first'>(no other options in category)</option>";
}


?>