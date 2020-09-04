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


	if(!defined("SHOW_BANLIST")) {
	
	
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/rank.php");

	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("IP Banning");
	$consoleObj->select($cID);
	
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	
		if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
			$memberInfo = $member->get_info_filtered();		
		}
		else {
			exit();	
		}
	
	}
		
?>

<table class='formTable' style='margin-top: 0px; border-spacing: 0px; width: 80%'>
<?php 
	$counter = 0;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ipban ORDER BY exptime");
	while($row = $result->fetch_assoc()) {

		$row = filterArray($row);
		if($counter == 1) {
			$addCSS = " alternateBGColor";
			$counter = 0;
		}
		else {
			$counter = 1;
			$addCSS = "";
		}
		
		$dispExpireTime = ($row['exptime'] == 0) ? "Never" : date("D M j, Y g:i a T", $row['exptime']);
		
		echo "
			<tr>
				<td class='main manageList".$addCSS."' align='center' style='width: 40%'>".$row['ipaddress']."</td>
				<td class='main manageList".$addCSS."' align='center' style='width: 45%'>".$dispExpireTime."</td>
				<td class='main manageList".$addCSS."' align='center' style='width: 15%'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' data-deleteip='".$row['ipaddress']."' style='cursor: pointer'></td>
			</tr>
		";
		
	}
	
	if($result->num_rows == 0) {
		echo "
			<div class='shadedBox' style='width: 45%; margin: 20px auto'>
				<p class='main' align='center'>
					<i>There are currently no IP bans!</i>
				</p>
			</div>
		";
	}
?>
</table>