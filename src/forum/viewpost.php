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

$prevFolder = "../";

include($prevFolder."_setup.php");

$breadcrumbObj->setTitle("View Post");
$breadcrumbObj->addCrumb("Home", MAIN_ROOT);
$breadcrumbObj->addCrumb("Forum", MAIN_ROOT."forum");
$breadcrumbObj->addCrumb("View Post");

$PAGE_NAME = "View Post - ";

include(BASE_DIRECTORY."forum/templates/_header.php");


if($boardObj->objPost->select($_GET['post'])) {
	$topicInfo = $boardObj->objPost->getTopicInfo(true);
	$boardObj->select($topicInfo['forumboard_id']);
	
	echo "
		<div class='largeFont' style='padding-top: 20px; position:relative;'>
			<b>".$boardObj->getLink(true)." - ".$boardObj->objPost->getLink(true)."</b>
		</div>
	";
	
	$boardObj->objPost->show(true);
}
else {
	echo "
		<div class='shadedBox' style='width: 45%; margin-left: auto; margin-right: auto; margin-top: 20px'>
			<p align='center'>
				<i>Forum Post Not Found!</i>
			</p>
		</div>
	";
}

include(BASE_DIRECTORY."forum/templates/_footer.php");