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

	$cID = $consoleObj->findConsoleIDByName("Manage Diplomacy Statuses");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();
	$consoleTitle = $consoleInfo['pagetitle'];

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
	$consoleObj->select($consoleObj->findConsoleIDByName("Manage Diplomacy Statuses"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


echo "
<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
	<tr><td colspan='5' class='dottedLine'></td></tr>
";

if(!isset($diplomacyStatusObj)) {
	$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");
}

$counter = 0;
$x = 1;
$intHighestOrder = $diplomacyStatusObj->getHighestOrderNum();

$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status ORDER BY ordernum DESC");
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
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveStatus('up', '".$row['diplomacystatus_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
	}
	
	
	if($x == $intHighestOrder) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveStatus('down', '".$row['diplomacystatus_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
	}


	echo "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&sID=".$row['diplomacystatus_id']."&action=edit'>".filterText($row['name'])."</a></b></td>
		
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&sID=".$row['diplomacystatus_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Status Information'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='javascript:void(0)' onclick=\"deleteStatus('".$row['diplomacystatus_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Status'></a></td>
	</tr>
	";

	$x++;


}

echo "</table>";



?>