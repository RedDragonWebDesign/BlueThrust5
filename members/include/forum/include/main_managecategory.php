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
	
	$cID = $consoleObj->findConsoleIDByName("Manage Forum Categories");
	$intAddCategoryCID = $consoleObj->findConsoleIDByName("Add Forum Category");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	
	$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
	$categoryObj->set_assocTableName("forum_board");
	$categoryObj->set_assocTableKey("forumboard_id");
	
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
	$consoleObj->select($consoleObj->findConsoleIDByName("Manage Forum Categories"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


echo "
	<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
		<tr><td class='dottedLine' colspan='5'></td></tr>
";

$counter = 0;
$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	
	$categoryObj->select($row['forumcategory_id']);
	$addCSS = "";
	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$counter = 1;	
	}
	
	$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveCat('up', '".$row['forumcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
	$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveCat('down', '".$row['forumcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
	
	if($categoryObj->getHighestOrderNum() == $row['ordernum']) {
		$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";
	}
	
	
	if($row['ordernum'] == 1) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";
	}
	
	
	echo "
	
		<tr>
			<td class='dottedLine main".$addCSS."' style='padding-left: 10px; width: 76%'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&catID=".$row['forumcategory_id']."&action=edit'>".filterText($row['name'])."</a></b></td>
			<td class='dottedLine main".$addCSS."' style='width: 6%'>".$dispUpArrow."</td>
			<td class='dottedLine main".$addCSS."' style='width: 6%'>".$dispDownArrow."</td>
			<td class='dottedLine main".$addCSS."' style='width: 6%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&catID=".$row['forumcategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Forum Category'></a></td>
			<td class='dottedLine main".$addCSS."' style='width: 6%'><a href='javascript:void(0)' onclick=\"deleteCat('".$row['forumcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Category'></a></td>
		</tr>
	
	
	";
}

echo "</table>";

if($result->num_rows == 0) {
	
	echo "
	
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
			<p class='main' align='center'>
				<i>There are currently no forum categories!<br><br>Click <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCategoryCID."'>here</a> to add a category.</i>
			</p>
		</div>
	
	";
	
}


?>