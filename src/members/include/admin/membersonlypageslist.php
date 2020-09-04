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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !isset($_GET['cID'])) {

	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");

	// Start Page

	$consoleObj = new ConsoleOption($mysqli);

	$cID = $consoleObj->findConsoleIDByName("Member's Only Pages");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		$memberInfo = $member->get_info();
	}
	else {
		exit();
	}

}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($consoleObj->findConsoleIDByName("Member's Only Pages"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}



$result = $mysqli->query("SELECT * FROM ".$dbprefix."membersonlypage ORDER BY pagename");

if($result->num_rows > 0) {
	echo "
	
		<table class='formTable' style='border-spacing: 0px; margin-bottom: 20px; margin-top: 2px'>
			<tr>
				<td class='formTitle' style='width: 40%; border-right: 0px'>Page Name:</td>
				<td class='formTitle' style='width: 40%; border-right: 0px'>Date Added:</td>
				<td class='formTitle' style='width: 20%'>Untag Page:</td>
			</tr>
		";
		
		$counter = 0;
		while($row = $result->fetch_assoc()) {
			
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
					<td class='main dottedLine".$addCSS."' style='padding-left: 5px; width: 40%'><a href='http://".$row['pageurl']."' target='_blank'>".filterText($row['pagename'])."</a></td>
					<td class='main dottedLine".$addCSS."' style='width: 40%' align='center'>".getPreciseTime($row['dateadded'])."</td>	
					<td class='main dottedLine".$addCSS."' style='width: 20%' align='center'><a href='javascript:void(0)' onclick=\"untagPage('".$row['page_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' title='Untag Page' width='24' height='24'></a></td>
				</tr>
			";
			
		}
	
	echo "
		</table>
	
	";

}
else {
	echo "
	
		<div class='shadedBox' style='margin: 20px auto; width: 40%'>
		
			<p align='center'>
		
				<i>There are currently no pages set to member's only!</i>
			
			</p>
			
		</div>
	
	";
}


?>