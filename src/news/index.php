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


$prevFolder = "../";
require_once($prevFolder."_setup.php");


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if ($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if (time() < $ipbanInfo['exptime'] or $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}
}

$newsObj = new News($mysqli);

// Start Page
$PAGE_NAME = "News - ";
require_once($prevFolder."themes/".$THEME."/_header.php");


$breadcrumbObj->setTitle("News");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("News");
require_once($prevFolder."include/breadcrumb.php");


$totalPages = $newsObj->calcPages();

$arrPosts = $newsObj->getPosts();

if (count($arrPosts) > 0) {
	foreach ($arrPosts as $post) {
		$newsObj->select($post['news_id']);
		$newsObj->show();
	}

	$newsObj->displayPageSelector();
}
else {
	echo "

	<div class='shadedBox' style='width: 300px; margin-top: 50px; margin-bottom: 25px; margin-left: auto; margin-right: auto'>
		<p class='main' align='center'>
			<i>There are currently no news posts!</i>
		</p>
	</div>

	";
}


require_once($prevFolder."themes/".$THEME."/_footer.php");
