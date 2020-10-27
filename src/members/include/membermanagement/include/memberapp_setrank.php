<?php

if(!defined("MAIN_ROOT")) { exit(); }

function memberAppSetRank() {
	global $mysqli, $member, $memberInfo, $dbprefix, $i, $memberAppInfo;
	
	$setRankOptions = array();
	
	
	$memberRank = new Rank($mysqli);
	$memberRank->select($memberInfo['rank_id']);
	
	$setRankConsole = new ConsoleOption($mysqli);
	$setRankCID = $setRankConsole->findConsoleIDByName("Set Member's Rank");
	$setRankConsole->select($setRankCID);
	
	if($memberAppInfo['memberadded'] == 0 && $member->hasAccess($setRankConsole)) {
		
		$rankInfo = $memberRank->get_info_filtered();
		if($memberInfo['promotepower'] != 0) {
			$rankInfo['promotepower'] = $memberInfo['promotepower'];	
		}
		elseif($memberInfo['promotepower'] == -1) {
			$rankInfo['promotepower'] = 0;	
		}
		
		if($memberInfo['rank_id'] == 1) {
			
			$maxOrderNum = $mysqli->query("SELECT MAX(ordernum) FROM ".$dbprefix."ranks WHERE rank_id != '1'");
			$arrMaxOrderNum = $maxOrderNum->fetch_array(MYSQLI_NUM);
			
			if($maxOrderNum->num_rows > 0) {
				$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE ordernum = '".$arrMaxOrderNum[0]."'");
				$row = $result->fetch_assoc();
				$rankInfo['promotepower'] = $row['rank_id'];
			}
			
		}
		
		$rankObj = new Rank($mysqli);
		
		$rankObj->select($rankInfo['promotepower']);
		$maxRankInfo = $rankObj->get_info_filtered();
		
		if($rankInfo['rank_id'] == 1) {
			$maxRankInfo['ordernum'] += 1;
		}
	
		
		$arrRanks = array();
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum");
		while($row = $result->fetch_assoc()) {
			$rankOptions[$row['ordernum']] = $row['name'];
		}
		
		
		$setRankOptions = array(
			"setrank" => array(
				"type" => "select",
				"display_name" => "Set Rank",
				"attributes" => array("class" => "formInput textBox", "id" => "newRankID_".$memberAppInfo['memberapp_id']),
				"options" => $rankOptions,
				"sortorder" => $i++
			)		
		);
	
	}

	return $setRankOptions;
}