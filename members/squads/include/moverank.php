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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/squad.php");

$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$pID = "manageranks";
$squadObj = new Squad($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($member->get_info("member_id"), $pID)) {

	$squadInfo = $squadObj->get_info_filtered();
	$memberInfo = $member->get_info_filtered();

	if($squadObj->objSquadRank->select($_POST['rID'])) {
		
		$squadRankInfo = $squadObj->objSquadRank->get_info();
		
		$addTo = 1;
		if($_POST['rDir'] == "up") {
			$addTo = -1;
		}
		
		if($squadRankInfo['sortnum'] != 1 && ($squadRankInfo['sortnum']+$addTo) != 1) {


			$newSortNum = $squadRankInfo['sortnum']+$addTo;
			
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."squadranks WHERE squad_id = '".$squadRankInfo['squad_id']."' AND sortnum = '".$newSortNum."'");
			$row = $result->fetch_assoc();
			
			$newSortNumRankID = $row['squadrank_id'];
			
			$arrColumns = array("sortnum");
			$arrValues = array($newSortNum);
			
			$squadObj->objSquadRank->update($arrColumns, $arrValues);
			
			if($squadObj->objSquadRank->select($newSortNumRankID)) {
				$squadObj->objSquadRank->update($arrColumns, array($squadRankInfo['sortnum']));				
			}
			else {
				$squadObj->objSquadRank->select($squadRankInfo['squadrank_id']);
				$squadObj->objSquadRank->update($arrColumns, array($squadRankInfo['sortnum']));
			}
			
			
		}
		
		include("ranklist.php");
		
	}

}


?>