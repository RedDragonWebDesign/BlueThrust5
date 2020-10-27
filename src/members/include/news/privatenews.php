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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];

	include_once("../classes/news.php");
	$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");
	$postNewsCID = $consoleObj->findConsoleIDByName("Post News");

	
	$dispPostNews = "";
	$dispManageNews = "";
	
	
	if($consoleObj->select($postNewsCID) && $member->hasAccess($consoleObj)) {
	
		$dispPostNews = "&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$postNewsCID."'>Post News</a> &laquo; &nbsp; ";
	}
	
	if($consoleObj->select($manageNewsCID) && $member->hasAccess($consoleObj)) {
		$dispManageNews = "&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$manageNewsCID."'>Manage News</a> &laquo;";
	}
	
	$consoleObj->select($cID);
	$newsObj = new News($mysqli);
	echo "
	
		<p align='right' class='main' style='padding-right: 20px'>
			".$dispPostNews.$dispManageNews."
		</p>
	
	";

	$arrPosts = $newsObj->getPosts(2);
	
	if(count($arrPosts) > 0) {
		foreach($arrPosts as $post) {
			
			$newsObj->select($post['news_id']);
			$newsObj->show();			
			
		}
		
		$newsObj->displayPageSelector(2, MAIN_ROOT."members/console.php?cID=".$cID."&page=");
		
	}
	else {
		
		echo "
		
			<div class='shadedBox' style='width: 300px; margin-left: auto; margin-right: auto'>
				<p class='main' align='center'>
					<i>There are currently no private news posts!</i>
				</p>
			</div>
		
		";
		
	}


?>