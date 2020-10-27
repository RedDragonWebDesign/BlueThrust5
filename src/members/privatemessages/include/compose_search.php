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
include_once("../../../classes/consoleoption.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$memberInfo = $member->get_info_filtered();
	$searchTerm = $mysqli->real_escape_string($_GET['term']);
	$pmSessionID = $_GET['pmsessionid'];
	
	$filterMembers = "('')";
	$checkFilterList = implode("", $_SESSION['btComposeList'][$pmSessionID]['member']);
	if(is_numeric($checkFilterList)) {
		$filterMembers = "('".implode("','", $_SESSION['btComposeList'][$pmSessionID]['member'])."')";
	}
	
	$rankObj = new Rank($mysqli);
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.name FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."members.username LIKE '".$searchTerm."%' AND ".$dbprefix."members.member_id NOT IN ".$filterMembers." ORDER BY ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
	
		$rankObj->select($row['rank_id']);
		$displayName = $rankObj->get_info_filtered("name")." ".filterText($row['username']);
		$arrComposeList[] = array("id" => "member_".$row['member_id'], "value" => $displayName);
	
	}
	
	$arrQuery['rankcategory']['query'] = "SELECT * FROM ".$dbprefix."rankcategory WHERE name LIKE '".$searchTerm."%' AND rankcategory_id NOT IN ";
	$arrQuery['rankcategory']['orderby'] = " ORDER BY ordernum DESC";
	$arrQuery['rankcategory']['id'] = "rankcategory_id";
	$arrQuery['rankcategory']['append'] = " - Category";
	
	$arrQuery['rank']['query'] = "SELECT * FROM ".$dbprefix."ranks WHERE name LIKE '".$searchTerm."%' AND rank_id != '1' AND rank_id NOT IN ";
	$arrQuery['rank']['orderby'] = " ORDER BY ordernum DESC";
	$arrQuery['rank']['id'] = "rank_id";
	$arrQuery['rank']['append'] = " - Rank";
	
	$arrQuery['squad']['query'] = "SELECT ".$dbprefix."squads.* FROM ".$dbprefix."squads, ".$dbprefix."squads_members WHERE ".$dbprefix."squads.squad_id = ".$dbprefix."squads_members.squad_id AND ".$dbprefix."squads_members.member_id = '".$memberInfo['member_id']."' AND ".$dbprefix."squads.name LIKE '".$searchTerm."%' AND ".$dbprefix."squads.squad_id NOT IN ";
	$arrQuery['squad']['orderby'] = " ORDER BY ".$dbprefix."squads.name DESC";
	$arrQuery['squad']['id'] = "squad_id";
	$arrQuery['squad']['append'] = " Members";
	
	$arrQuery['tournament']['query'] = "SELECT * FROM ".$dbprefix."tournaments WHERE member_id = '".$memberInfo['member_id']."' AND name LIKE '".$searchTerm."%' AND tournament_id NOT IN ";
	$arrQuery['tournament']['orderby'] = " ORDER BY startdate DESC";
	$arrQuery['tournament']['id'] = "tournament_id";
	$arrQuery['tournament']['append'] = " Players";
	
	
	foreach($arrQuery as $key => $arr) {
		
		$filterList = "('')";
		$checkFilterList = implode("", $_SESSION['btComposeList'][$pmSessionID][$key]);
		if(is_numeric($checkFilterList)) {
			$filterList = "('".implode("','", $_SESSION['btComposeList'][$pmSessionID][$key])."')";
		}
		
		$sessionPrefix = $key."_";
		
		
		$arr['query'] .= $filterList;
		
		//echo $arr['query'].$arr['orderby'];
		$result = $mysqli->query($arr['query'].$arr['orderby']);
		while($row = $result->fetch_assoc()) {
			
			$arrComposeList[] = array("id" => $sessionPrefix.$row[$arr['id']], "value" => filterText($row['name']).$arr['append']);
			
		}

	}
	
	
	echo json_encode($arrComposeList);
	
}


?>