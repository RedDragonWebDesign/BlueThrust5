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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/imageslider.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$result = $mysqli->query("SELECT * FROM ".$dbprefix."imageslider ORDER BY ordernum DESC");
	
	echo "
		<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-bottom: 20px'>
			<tr><td class='dottedLine' colspan='5'></td></tr>
		";
	
	$intHighestOrderNum = $imageSliderObj->getHighestOrderNum();
	$counter = 1;
	while($row = $result->fetch_assoc()) {
		
		$dispUpArrow = ($counter == 1) ? "<img src='".$MAIN_ROOT."images/transparent.png' class='manageListActionButton'>" : "<a href='javascript:void(0)' onclick=\"moveImg('".$row['imageslider_id']."', 'up')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton'></a>";
		$dispDownArrow = ($counter == $intHighestOrderNum) ? "<img src='".$MAIN_ROOT."images/transparent.png' class='manageListActionButton'>" : "<a href='javascript:void(0)' onclick=\"moveImg('".$row['imageslider_id']."', 'down')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton'></a>";
		
		echo "
			<tr>
				<td class='main manageList dottedLine' style='width: 76%; font-weight: bold'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&imgID=".$row['imageslider_id']."&action=edit'>".parseBBCode(filterText($row['name']))."</a></td>
				<td class='main manageList dottedLine' style='width: 6%'>".$dispUpArrow."</td>
				<td class='main manageList dottedLine' style='width: 6%'>".$dispDownArrow."</td>
				<td class='main manageList dottedLine' style='width: 6%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&imgID=".$row['imageslider_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton'></a></td>
				<td class='main manageList dottedLine' style='width: 6%'><a href='javascript:void(0)' onclick=\"deleteImg('".$row['imageslider_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton'></a></td>
			</tr>
		";
		
		$counter++;
	}
	
	echo "</table>";
	
	if($result->num_rows == 0) {
		echo "

			<div class='shadedBox' style='width: 50%; margin: 20px auto'>
				<p class='main' align='center'>
					No images added yet!
				</p>
			</div>
		";
	}
}

?>