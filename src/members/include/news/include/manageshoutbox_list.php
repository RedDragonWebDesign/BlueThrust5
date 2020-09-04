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

if(!defined("SHOW_SHOUTBOXLIST")) {
	exit();
}


echo "
	<table class='formTable'  style='margin-top: 0px; border-spacing: 0px; table-layout: fixed'>
	";

$counter = 0;
$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");
$result = $mysqli->query("SELECT * FROM ".$dbprefix."news WHERE newstype = '3' ORDER BY dateposted DESC");
while($row = $result->fetch_assoc()) {
	$dispPoster = ($member->select($row['member_id'])) ? $member->getMemberLink() : "Unknown";
	
	if($counter == 0) {
		$addCSS = "";
		$counter = 1;
	}
	else {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	
	echo "
		<tr>
			<td class='pmInbox main solidLine".$addCSS."' style='padding-left: 0px' width=\"5%\"><input type='checkbox' value='".$row['news_id']."' class='textBox'></td>
			<td class='pmInbox main solidLine".$addCSS."' style='padding-left: 5px' width=\"30%\">".$dispPoster."</a></div></td>
			<td class='pmInbox main solidLine".$addCSS."' style='padding-left: 5px; overflow: hidden' width=\"35%\"><div style='width: 85%; white-space:nowrap; overflow: hidden; text-overflow: ellipsis'><a href='".$MAIN_ROOT."members/console.php?cID=".$manageNewsCID."&newsID=".$row['news_id']."'>".filterText($row['newspost'])."</a></div></td>
			<td class='pmInbox main solidLine".$addCSS."' style='padding-left: 5px' width=\"30%\">".getPreciseTime($row['dateposted'])."</td>
		</tr>
	";
	
}

if($result->num_rows == 0) {
	echo "	
		<tr>
			<td class='main' align='center' colspan='4'>
				<div class='shadedBox' style='margin-top: 20px; width: 45%; margin-left: auto; margin-right: auto'>
					<p align='center'><i>There are no shoutbox posts!</i></p>
				</div>
			</td>
		</tr>
	";
}

echo "</table>";
$member->select($memberInfo['member_id']);