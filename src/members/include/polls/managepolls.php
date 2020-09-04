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

include_once("../classes/access.php");
include_once("../classes/poll.php");

$cID = $_GET['cID'];

$pollObj = new Poll($mysqli);

if(isset($_GET['pID']) && $pollObj->select($_GET['pID'])) {
	define("SHOW_EDITPOLL", true);
	$pollInfo = $pollObj->get_info_filtered();
	include("include/edit.php");
}
else {
	$createPollCID = $consoleObj->findConsoleIDByName("Create a Poll");
	echo "
		<div class='formDiv' style='border: 0px; text-align: right; background: none'>
			&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$createPollCID."'>Create a Poll</a> &laquo;
		</div>
		
		<table class='formTable'>
			<tr>
				<td class='formTitle' style='width: 76%'>Poll Question:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
		<div id='pollList'>
	";
	
	define("SHOW_POLLLIST", true);
	include("include/polllist.php");
	
	echo "
		</div>
	";
	
}


?>