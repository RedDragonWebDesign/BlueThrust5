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


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$rankObj = new Rank($mysqli);

if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	$blnDispNone = true;
	if(($_POST['action'] == "promote" || $_POST['action'] == "demote") && $member->select($_POST['mID'])) {

		$rankObj->select($member->get_info("rank_id"));
		
		if($_POST['action'] == "promote") {
			$nextRank = $rankObj->get_info("ordernum")+1;
		}
		else {
			$nextRank = $rankObj->get_info("ordernum")-1;
		}
		
		if($nextRank != 1 && $rankObj->selectByOrder($nextRank)) {
			$blnDispNone = false;
			echo $rankObj->get_info_filtered("name");			
		}
		
	}
	
	
	if($blnDispNone) {
		echo "None";	
	}
	
	
}
else {
	echo "Error";	
}


?>