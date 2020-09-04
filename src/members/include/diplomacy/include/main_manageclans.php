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
	
	$cID = $consoleObj->findConsoleIDByName("Diplomacy: Manage Clans");
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
	$consoleObj->select($consoleObj->findConsoleIDByName("Diplomacy: Manage Clans"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

echo "
<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
	<tr><td colspan='5' class='dottedLine'></td></tr>
";

$counter = 0;
$x = 1;
$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy ORDER BY clanname");
while($row = $result->fetch_assoc()) {

	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}
	
	
	echo "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"80%\">&nbsp;&nbsp;<span class='main'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&dID=".$row['diplomacy_id']."&action=edit'>".filterText($row['clanname'])."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&dID=".$row['diplomacy_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Clan Information'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='javascript:void(0)' onclick=\"deleteClan('".$row['diplomacy_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Clan'></a></td>
	</tr>
	";
	
	$x++;


}

echo "</table>";

?>