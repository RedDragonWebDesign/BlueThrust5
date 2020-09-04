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

	if(!defined("SHOW_SEARCHRESULTS")) {
		exit();	
	}

	/*
	 * Filter Keyword: keyword
	 * 0 = Search Entire Posts
	 * 1 = Search Titles Only
	 * 
	 * 
	 * Filter Username: searchuser
	 * 0 = Find Posts by User
	 * 1 = Find Topics started by User
	 * 
	 * Find Topics with: filtertopics, filtertopics_replies
	 * 0 = At Least
	 * 1 = At Most
	 * 
	 * Find Posts From: filterposts, filterposts_newold
	 * 0 = Any Date
	 * 1 = Last Login
	 * 2 = Yesterday
	 * 3 = A week ago
	 * 4 = 2 weeks ago
	 * 5 = 1 month ago
	 * 6 = 3 months ago
	 * 7 = 6 months ago
	 * 8 = year ago
	 * --
	 * 0 = and Newer
	 * 1 = and Older
	 * 
	 * Sort Results by: sortresults, sortresults_ascdesc
	 * 0 = Last post date
	 * 1 = Topic Title
	 * 2 = # of Replies
	 * 3 = # of Views
	 * 4 = Topic Start Date
	 * 5 = Forum
	 * 6 = Username
	 * 7 = Member Rank
	 * --
	 * 0 = Descending
	 * 1 = Ascending
	 * 
	 */
	
	$postTable = $dbprefix."forum_post";
	$topicTable = $dbprefix."forum_topic";
	$membersTable = $dbprefix."members";
	$ranksTable = $dbprefix."ranks";
	$filterResults = array();
	
	
	$hooksObj->run("search_results_init");
	
	
	// Filter By Keyword
	$filterKeyword = array();
	if(trim($_POST['keyword']) != "") {
		$_POST['keyword'] = str_replace("%", "\%", $_POST['keyword']);
		
		if($_POST['filterkeyword'] == 0) {
			$filterKeyword = array("message" => $_POST['keyword'], "title" => $_POST['keyword']);
			
			$filterResults[] = " (".$postTable.".message LIKE '%".$mysqli->real_escape_string($_POST['keyword'])."%' OR ".$postTable.".title LIKE '%".$mysqli->real_escape_string($_POST['keyword'])."%') ";
			
		}
		else {
			$filterResults[] = " ".$postTable.".title LIKE '%".$mysqli->real_escape_string($_POST['keyword'])."%' ";
			
		}		
		
	}
	
	// Filter By Username
	$filterByUsername = "";
	$memberIDList = array();
	if(trim($_POST['fakesearchuser']) != "") {
 		$_POST['fakesearchuser'] = str_replace("*", "%", $_POST['fakesearchuser']);
		
 		$memberList = $member->get_entries(array("username" => $_POST['fakesearchuser']), "username", true, array("username" => "Like"));
 		$memberIDList = array();
 		
 		foreach($memberList as $searchMemberInfo) {
 			$memberIDList[] = $searchMemberInfo['member_id'];	
 		}

 		$memberListSQL = "('".implode("','", $memberIDList)."')";
 		$filterResults[] = " ".$postTable.".member_id IN ".$memberListSQL;
 		
 		if($_POST['filterusername'] == 1) {
 			$topicList = array();
 			$result = $mysqli->query("SELECT DISTINCT ".$topicTable.".forumpost_id FROM ".$topicTable.", ".$postTable." WHERE ".$topicTable.".forumpost_id = ".$postTable.".forumpost_id AND ".$postTable.".member_id IN ".$memberListSQL);
 			while($row = $result->fetch_assoc()) {
 				$topicList[] = $row['forumpost_id'];
 			}
 			
 			$topicListSQL = "('".implode("','", $topicList)."')";
 			$filterResults[] = " ".$postTable.".forumpost_id IN ".$topicListSQL;
 		}
  		
	}
	
	// Filter By Reply Count
	$filterTopicGTLT = "<=";
	if($_POST['filtertopics'] == 0) {
		$filterTopicGTLT = ">=";
	}
	$filterResults[] = " ".$topicTable.".replies ".$filterTopicGTLT." ".$_POST['filtertopics_replies']." ";	
	
	
	//Filter By Date
	$oneDay = 24*60*60;
	$arrFilterDates = array("", $memberInfo['lastlogin'], (time()-$oneDay), (time()-$oneDay*7), (time()-$oneDay*14), (time()-$oneDay*30), (time()-$oneDay*90), (time()-$oneDay*180), (time()-$oneDay*365));
	
	
	if($arrFilterDates[$_POST['filterposts']] != "" || $arrFilterDates[$_POST['filterposts']] != 0) {
		
		$filterByDateGTLT = "<=";
		if($_POST['filterposts_newold'] == 0) {
			$filterByDateGTLT = ">=";	
		}
		
		$filterResults[] = " ".$postTable.".dateposted ".$filterByDateGTLT." '".$arrFilterDates[$_POST['filterposts']]."' ";
		
	}
	
	// Filter Board
	$arrFilterBoards = array();
	if(!in_array(0, $_POST['filterboards'])) {

		$arrFilterBoards = $_POST['filterboards'];
		
		if($_POST['include_subforums'] == 1) {
			foreach($_POST['filterboards'] as $value) {
				$boardObj->select($value);
				$arrFilterBoards = array_merge($arrFilterBoards, $boardObj->getAllSubForums());
			}
			
			$arrFilterBoards = array_unique($arrFilterBoards);
		}
	}
	
	// Filter by Topic
	
	if(isset($_GET['topic']) && $boardObj->objTopic->select($_GET['topic'])) {
		$filterResults[] = " ".$postTable.".forumtopic_id = '".$_GET['topic']."' ";
	}
	
	
	$orderBY = "";
	// Sort Results
	$arrOrderBy[0] = $postTable.".dateposted";
	$arrOrderBy[1] = ""; //
	$arrOrderBy[2] = $topicTable.".replies";
	$arrOrderBy[3] = $topicTable.".views";
	$arrOrderBy[4] = ""; //
	$arrOrderBy[5] = ""; //
	$arrOrderBy[6] = $membersTable.".username";
	$arrOrderBy[7] = $ranksTable.".ordernum";
	

	
	if($arrOrderBy[$_POST['sortresults']] != "" && $_POST['sortresults_ascdesc'] == 0) {
		$orderBY = " ORDER BY ".$arrOrderBy[$_POST['sortresults']]." DESC";
	}
	elseif($arrOrderBy[$_POST['sortresults']] != "" && $_POST['sortresults_ascdesc'] == 1) {
		$orderBY = " ORDER BY ".$arrOrderBy[$_POST['sortresults']]." ASC";
	}

	
	$filterResultsSQL = implode(" AND ", $filterResults);
	$arrCustomSort = array(1, 4, 5);
	$blnResort = false;
	$arrSearchResults = array();
	
	$hooksObj->run("search_results_query");
	
	$query = "SELECT DISTINCT ".$postTable.".* FROM ".$postTable.", ".$topicTable.", ".$membersTable.", ".$ranksTable." WHERE ".$ranksTable.".rank_id = ".$membersTable.".rank_id AND ".$membersTable.".member_id = ".$postTable.".member_id AND ".$filterResultsSQL.$orderBY;
	$result = $mysqli->query($query);
	$totalResults = $result->num_rows;	
	
	// Pages
	$numPerPage = LOGGED_IN ? $memberInfo['postsperpage'] : $websiteInfo['forum_postsperpage'];
	if($numPerPage == 0) {
		$numPerPage = $websiteInfo['forum_postsperpage'];	
	}
	
	$totalPages = ceil($totalResults/$numPerPage);
	

	if(!isset($_GET['page']) || !is_numeric($_GET['page']) || $totalPages < $_GET['page'] || $_GET['page'] < 1) {
		$sqlLimit = " LIMIT 0, ".$numPerPage;
		$_POST['page'] = 1;
		
	}
	else {
		$sqlLimit = " LIMIT ".($numPerPage*($_GET['page']-1)).", ".$numPerPage;	
	}
	
	
	$query = "SELECT DISTINCT ".$postTable.".* FROM ".$postTable.", ".$topicTable.", ".$membersTable.", ".$ranksTable." WHERE ".$ranksTable.".rank_id = ".$membersTable.".rank_id AND ".$membersTable.".member_id = ".$postTable.".member_id AND ".$filterResultsSQL.$orderBY.$sqlLimit;
	$result = $mysqli->query($query);

	while($row = $result->fetch_assoc()) {
		
		if(in_array($_POST['sortresults'], $arrCustomSort)) {
			// Requires additional sorting
			$blnResort = true;
			switch($_POST['sortresults']) {
				case 1:
					$boardObj->objTopic->select($row['forumtopic_id']);
					$boardObj->objPost->select($boardObj->objTopic->get_info("forumpost_id"));
					$arrSearchResults[$row['forumpost_id']] = strtolower($boardObj->objPost->get_info("title"));
					break;
				case 4:
					$boardObj->objTopic->select($row['forumtopic_id']);
					$boardObj->objPost->select($boardObj->objTopic->get_info("forumpost_id"));
					$arrSearchResults[$row['forumpost_id']] = $boardObj->objPost->get_info("dateposted");
					break;
				case 5:
					$boardObj->objTopic->select($row['forumtopic_id']);
					$boardObj->select($boardObj->objTopic->get_info("forumboard_id"));
					$arrSearchResults[$row['forumpost_id']] = strtolower($boardObj->get_info("name"));
					break;
			}
			
		}
		else {
			$arrSearchResults[] = $row['forumpost_id'];
		}
		
	}
	
	if($blnResort) {
		
		if($_POST['sortresults_ascdesc'] == 0) {
			arsort($arrSearchResults);
		}
		else {
			asort($arrSearchResults);	
		}
		
		$arrSearchResults = array_keys($arrSearchResults);
	}
	
	// Filter Out Based on Board
	if(count($arrFilterBoards) > 0) {
		foreach($arrSearchResults as $key => $value) {
			$boardObj->objPost->select($value);
			$boardObj->objTopic->select($boardObj->objPost->get_info("forumtopic_id"));
			$tempTopicInfo = $boardObj->objTopic->get_info();
			if(!in_array($tempTopicInfo['forumboard_id'], $arrFilterBoards)) {
				unset($arrSearchResults[$key]);
			}
			
		}
		
		$totalResults = count($arrSearchResults);
		$totalPages = ceil($totalResults/$numPerPage);
	}
	
	
	$intManagePostsCID = $consoleObj->findConsoleIDByName("Manage Forum Posts");
	$consoleObj->select($intManagePostsCID);
	$hooksObj->run("search_results_display");
	$searchCounter = ($_POST['page']-1)*$numPerPage+1;
	define("SHOW_FORUMPOST", true);
	
	$postVars = $_POST;
	unset($postVars['checkCSRF']);
	unset($postVars['submit']);
	unset($postVars['page']);
	$postVars['searchuser'] = 1;
	foreach($postVars as $key => $value) {
		if($value == "") {
			unset($postVars[$key]);
		}
		
		
		if($key == "fakesearchuser") {
			$postVars['searchuser'] = $value;		
		}
	}

	unset($postVars['fakesearchuser']);
	
	for($i = 1; $i<=$totalPages; $i++) {
		
		$dispSelected = ($i == $_GET['page']) ? " selected" : "";
		
		$pageOptions .= "<option value='".$i."'".$dispSelected.">".$i."</option>";	
	}
	
	
	$urlArgs = http_build_query($postVars);

	$prevPageLink = "";
	if($_POST['page'] > 1 && $totalPages > 1) {
		$prevPageLink = "<a href='".$MAIN_ROOT."forum/search.php?page=".($_POST['page']-1)."&".$urlArgs."'>&laquo; Previous</a>";
	}
	
	$nextPageLink = "";
	if($_POST['page'] < $totalPages) {
		$nextPageLink = "<a href='".$MAIN_ROOT."forum/search.php?page=".($_POST['page']+1)."&".$urlArgs."'>Next &raquo;</a>";
		if($prevPageLink != "") {
			$nextPageLink = " | ".$nextPageLink;	
		}
	}
	
	
	echo "
		<p align='right'>
			Total Results: ".number_format($totalResults,0)."
		</p>
	";
	
	if($totalPages > 1) {
		echo "<p class='main' align='right'>Page: <select id='pickPageTop' class='textBox'>".$pageOptions."</select> <input type='button' onclick=\"choosePage('pickPageTop')\" class='submitButton' value='GO' style='margin-left: 5px; padding: 3px 10px'><br><br>".$prevPageLink.$nextPageLink."</p>";
	}
	
	foreach($arrSearchResults as $postID) {
		
		$boardObj->objPost->select($postID);	
		$topicInfo = $boardObj->objPost->getTopicInfo(true);
		$boardObj->select($topicInfo['forumboard_id']);
		
		if($boardObj->memberIsMod($memberInfo['member_id']) || $member->hasAccess($consoleObj)) {
			$boardObj->objPost->blnManageable = true;
		}
		
		echo "<div class='forumPostContainer' style='position: relative; border: 0px; margin-bottom: 0px'><h3 style='overflow: hidden; white-space: nowrap; width: 90%; text-overflow: ellipsis; margin: 0px'><a href='".$MAIN_ROOT."forum/viewtopic.php?tID=".$topicInfo['forumtopic_id']."#".$postID."'>".$topicInfo['title']."</a></h3><div style='position: absolute; right: 0px' class='largeFont'><b>#".$searchCounter."</b></div></div>";
		$boardObj->objPost->show();
		
		echo "<div class='dottedLine' style='margin: auto; width: 98%'></div>";
		$searchCounter++;
	}
	
	if($totalPages > 1) {
		echo "<p class='main' align='right'>Page: <select id='pickPageBottom' class='textBox'>".$pageOptions."</select> <input type='button' onclick=\"choosePage('pickPageBottom')\" class='submitButton' value='GO' style='margin-left: 5px; padding: 3px 10px'><br><br>".$prevPageLink.$nextPageLink."</p>";
	}
	
	
	if($totalResults == 0) {
		
		echo "
		
			<div class='shadedBox' style='width: 50%; margin-left: auto; margin-right: auto'>
			
				<p align='center' class='main'>
					No results found!<br><br>
					<a href='".$MAIN_ROOT."forum/search.php'>Search Again</a>
				</p>
				
			</div>
		
		";
		
	}
	
	
	
	echo "
		
		<script type='text/javascript'>
		
			function choosePage(pickPageID) {
				$(document).ready(function() {
				
					var pickPage = '#'+pickPageID;
					
					window.location = '".$MAIN_ROOT."forum/search.php?page='+$(pickPage).val()+'&".$urlArgs."'
					
				});
			}
		</script>
	";
	
?>