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
include_once("../../../../classes/basicsort.php");
include_once("../../../../classes/forumboard.php");
include_once("../../../../classes/rankcategory.php");



// Start Page

$consoleObj = new ConsoleOption($mysqli);

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Add Board");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);

$cID = $consoleObj->findConsoleIDByName("Manage Boards");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);

$boardObj = new ForumBoard($mysqli);

$rankCatObj = new RankCategory($mysqli);
$rankObj = new Rank($mysqli);


if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {
	
	// Set Access
	if(isset($_POST['accessInfo'])) {
		$arrAccessOptions = array(1,2);
		$accessInfo = json_decode($_POST['accessInfo'], true);
		
		foreach($accessInfo as $checkBoxName => $accessType) {
			
			$rankID = str_replace("rankaccess_", "", $checkBoxName);
			
			if(in_array($accessType, $arrAccessOptions) && $rankObj->select($rankID)) {	
			
				$_SESSION['btRankAccessCache'][$checkBoxName] = $accessType;
				
			}
			elseif($accessType == 0 && $rankObj->select($rankID)) {

				$_SESSION['btRankAccessCache'][$checkBoxName] = 0;
				unset($_SESSION['btRankAccessCache'][$checkBoxName]);
				
			}
			
		}
		
	}
	

	// Display List
	
	$rankoptions = "";
	$result1 = $mysqli->query("SELECT rankcategory_id FROM ".$dbprefix."rankcategory ORDER BY ordernum DESC");
	while($row = $result1->fetch_assoc()) {

		$rankCatObj->select($row['rankcategory_id']);
		$arrRanks = $rankCatObj->getRanks();
		$rankCatName = $rankCatObj->get_info_filtered("name");
		
		if(count($arrRanks) > 0) {
			$rankoptions .= "<b><u>".$rankCatName."</u></b> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('rankcat_".$row['rankcategory_id']."', 1)\">Check All</a> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('rankcat_".$row['rankcategory_id']."', 0)\">Uncheck All</a><br>";
			$rankoptions .= "<div id='rankcat_".$row['rankcategory_id']."'>";
			foreach($arrRanks as $rankID) {
				
				$dispRankAccess = "";
				if($_SESSION['btRankAccessCache']["rankaccess_".$rankID] == 1) {
					$dispRankAccess = " - <span class='allowText' style='font-style: italic'>Read-Only</span>";
				}
				elseif($_SESSION['btRankAccessCache']["rankaccess_".$rankID] == 2) {
					$dispRankAccess = " - <span class='pendingFont' style='font-style: italic'>Full Access</span>";
				}
				
				$rankObj->select($rankID);
				$rankName = $rankObj->get_info_filtered("name");
				$rankoptions .= "<input type='checkbox' name='rankaccess_".$rankID."' value='1' data-rankaccess='1'> ".$rankName.$dispRankAccess."<br>";
				$rankCounter++;
			}
			
			$rankoptions .= "</div><br>";
		}
		
	}
	
	echo $rankoptions;
	
}


?>