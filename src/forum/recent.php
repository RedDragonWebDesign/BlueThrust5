<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

// Config File
$prevFolder = "../";

require_once($prevFolder."_setup.php");

$breadcrumbObj->setTitle("Recent Posts");
$breadcrumbObj->addCrumb("Home", MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", MAIN_ROOT."forum");
$breadcrumbObj->addCrumb("Recent Posts");

$PAGE_NAME = "Recent Forum Posts - ";

require_once(BASE_DIRECTORY."forum/templates/_header.php");


$NUM_PER_PAGE = $websiteInfo['forum_postsperpage'];
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;
	$NUM_PER_PAGE = $memberInfo['postsperpage'];
}

if($NUM_PER_PAGE == 0) {
	$NUM_PER_PAGE = 25;
}

$arrTopics = [];
$accessableTopicsSQL = "SELECT forumtopic_id, forumboard_id FROM ".$dbprefix."forum_topic";
$result = $mysqli->query($accessableTopicsSQL);
while($row = $result->fetch_assoc()) {
	$boardObj->select($row['forumboard_id']);
	if($boardObj->memberHasAccess($memberInfo)) {
		$arrTopics[] = $row['forumtopic_id'];
	}
}

$topicsFilterSQL = "('".implode("','", $arrTopics)."')";

$totalPostsSQL = $mysqli->query("SELECT COUNT(*) as totalPosts FROM ".$dbprefix."forum_post WHERE forumtopic_id IN ".$topicsFilterSQL." ORDER BY dateposted");
$totalPosts = $totalPostsSQL->fetch_assoc();
$totalPosts = $totalPosts['totalPosts'];

if(!isset($_GET['pID']) || !is_numeric($_GET['pID'])) {
	$intOffset = 0;
	$_GET['pID'] = 1;
}
else {
	$intOffset = $NUM_PER_PAGE*($_GET['pID']-1);
}


// Count Pages
$NUM_OF_PAGES = ceil($totalPosts/$NUM_PER_PAGE);

if($NUM_OF_PAGES == 0) {
	$NUM_OF_PAGES = 1;	
}


$pageSelector = new PageSelector();
$pageSelector->setPages($NUM_OF_PAGES);
$pageSelector->setCurrentPage($_GET['pID']);
$pageSelector->setLink(MAIN_ROOT."forum/recent.php?pID=");

echo "<div style='position: relative; overflow: auto'>";
$pageSelector->show();
echo "</div>";

if($NUM_OF_PAGES == 1) { echo "<br><br>"; }

$query = "SELECT * FROM ".$dbprefix."forum_post WHERE forumtopic_id IN ".$topicsFilterSQL." ORDER BY dateposted DESC LIMIT ".$intOffset.", ".$NUM_PER_PAGE;
$result = $mysqli->query($query);

$count = 0;
while($row = $result->fetch_assoc()) {
	$count++;
	$boardObj->objPost->select($row['forumpost_id']);
	$topicInfo = $boardObj->objPost->getTopicInfo(true);
	$boardObj->select($topicInfo['forumboard_id']);

	echo "<div class='largeFont' style='position:relative;'><b>".$boardObj->getLink(true)." - ".$boardObj->objPost->getLink(true)."</b></div>";
	$boardObj->objPost->show(true);
	
	if($count != $result->num_rows) {
		echo "<br><div class='dottedLine'></div><br>";
	}
}

echo "<div style='position: relative; overflow: auto'>";
$pageSelector->show();
echo "</div>";

if($result->num_rows == 0) {

	echo "
		
		<div class='shadedBox' style='width: 45%; margin-left: auto; margin-right: auto'>
			<p align='center' class='main'>
				<i>No Recent Posts!</i>
			</p>
		</div>
	
	";
	
}

require_once(BASE_DIRECTORY."forum/templates/_footer.php");
