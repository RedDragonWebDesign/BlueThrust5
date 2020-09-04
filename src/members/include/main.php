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

include_once("../classes/members.php");
include_once("../classes/ranks.php");
include_once("../classes/consoleoption.php");

$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

$LOGIN_FAIL = true;

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {
		$LOGIN_FAIL = false;
		
		$memberInfo = $member->get_info();
		$memberRankID = $memberInfo['rank_id'];
		
		$memberRank = new Rank($mysqli);
		$memberRank->select($memberRankID);
		$rankPrivileges = $memberRank->get_privileges();
		
		$strPrivileges = implode(",", $rankPrivileges);
		
		$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."consolecategory ORDER BY ordernum");
		
		while($row = $result->fetch_assoc()) {
			$arrConsoleCats[] = $row['consolecategory_id'];
		}
		
		
		$arrFullySortedConsole = array();
		$consoleObj = new ConsoleOption($mysqli);
		foreach($rankPrivileges as $consoleoption) {
		
			$consoleObj->select($consoleoption);
			$consoleInfo = $consoleObj->get_info();
			
			$sortNum = array_search($consoleInfo['consolecategory_id'], $arrConsoleCats);
			
			$arrFullySortedConsole[$sortNum][] = $consoleoption;
		
		}
		$consoleCatObj = new basic($mysqli, "consolecategory", "consolecategory_id");
		
		foreach($arrConsoleCats as $key => $categoryID) {
			
			$consoleCatObj->select($categoryID);
			$consoleCatInfo = $consoleCatObj->get_info();
			echo "<b>".$consoleCatInfo['name']."</b>";
			echo "<br>";
			
			$arrConsoleOptions = $arrFullySortedConsole[$key];
			
				foreach($arrConsoleOptions as $consoleOptionID) {
			
					$consoleObj->select($consoleOptionID);
					$consoleInfo = $consoleObj->get_info();
					
					echo " - ".$consoleInfo['pagetitle']."<br>";
				}
					
		
		}
		
		
		
		
		
	}

}


if($LOGIN_FAIL) {
die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."index.php?p=Login';</script>");
}

?>