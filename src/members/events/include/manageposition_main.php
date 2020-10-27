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


if(!isset($member) || !isset($eventObj) || substr($_SERVER['PHP_SELF'], -strlen("manage.php")) != "manage.php") {

	include_once("../../../_setup.php");
	include_once("../../../classes/member.php");
	include_once("../../../classes/event.php");

	// Start Page

	$consoleObj = new ConsoleOption($mysqli);

	$cID = $consoleObj->findConsoleIDByName("Manage My Events");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();
	$consoleTitle = $consoleInfo['pagetitle'];

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$eventObj = new Event($mysqli);
	$memberInfo = $member->get_info();
	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $eventObj->select($_GET['eID']) && ($eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions") || $memberInfo['rank_id'] == 1)) {
		
		$eventInfo = $eventObj->get_info_filtered();
	}
	else {
		exit();
	}

}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($consoleObj->findConsoleIDByName("Manage My Events"));
	if(!$member->hasAccess($consoleObj) || !$eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions")) {
		exit();
	}
}

	
echo "
<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
	<tr><td colspan='5' class='dottedLine'></td></tr>
";

$counter = 0;
$x = 1;
$eventObj->objEventPosition->setCategoryKeyValue($eventInfo['event_id']);
$intHighestOrder = $eventObj->objEventPosition->getHighestSortNum();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."eventpositions WHERE event_id = '".$eventInfo['event_id']."' ORDER BY sortnum");
while($row = $result->fetch_assoc()) {


	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}

	if($x == 1) {
		$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"movePosition('up', '".$row['position_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
	}
	
	
	if($x == $intHighestOrder) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"movePosition('down', '".$row['position_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
	}


	echo "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=ManagePositions&posID=".$row['position_id']."&action=edit'>".filterText($row['name'])."</a></b></td>
		
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=ManagePositions&posID=".$row['position_id']."&action=edit''><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Position Information'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='javascript:void(0)' onclick=\"deletePosition('".$row['position_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Position'></a></td>
	</tr>
	";

	$x++;


}

echo "</table>";


if($result->num_rows == 0) {
	
	echo "
	
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
			<p class='main' align='center'>
				<i>There are currently no event positions!<br><br>Click <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$_GET['eID']."&pID=AddPosition'>here</a> to add a position.</i>
			</p>
		</div>
	
	";	
	
}


?>