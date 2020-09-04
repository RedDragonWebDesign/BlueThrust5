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
	include_once("../../../../classes/forumboard.php");
	
	// Start Page

	$consoleObj = new ConsoleOption($mysqli);

	$cID = $consoleObj->findConsoleIDByName("Manage Boards");
	$intAddBoardCID = $consoleObj->findConsoleIDByName("Add Board");
	$intEditCatCID = $consoleObj->findConsoleIDByName("Manage Forum Categories");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$boardObj = new ForumBoard($mysqli);

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
	$consoleObj->select($consoleObj->findConsoleIDByName("Manage Boards"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


function dispManageTable($arrBoards, $indent=0) {
	global $mysqli, $MAIN_ROOT, $THEME, $cID;
	
	$boardObj = new ForumBoard($mysqli);
	$counter = 0;
	$x = 0;
	foreach($arrBoards as $boardID) {
		$boardObj->select($boardID);
		$boardInfo = $boardObj->get_info_filtered();
		
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveBoard('up', '".$boardInfo['forumboard_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveBoard('down', '".$boardInfo['forumboard_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
		
		if($x == 0) {
			$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";	
		}
		
		
		if($boardObj->getHighestSortNum() == $boardInfo['sortnum']) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";
		}
		
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
				<td class='dottedLine main".$addCSS."' style='width: 76%; padding-left: 10px'>".str_repeat("&nbsp;&nbsp;", $indent)."<b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&bID=".$boardInfo['forumboard_id']."&action=edit'>".$boardInfo['name']."</a></b></td>
				<td class='dottedLine main".$addCSS."' style='width: 6%' align='center'>".$dispUpArrow."</td>
				<td class='dottedLine main".$addCSS."' style='width: 6%' align='center'>".$dispDownArrow."</td>
				<td class='dottedLine main".$addCSS."' style='width: 6%' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&bID=".$boardInfo['forumboard_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Board'></a></td>
				<td class='dottedLine main".$addCSS."' style='width: 6%' align='center'><a href='javascript:void(0)' onclick=\"deleteBoard('".$boardInfo['forumboard_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Board'></a></td>
			</tr>
		";
		
		$x++;
		
		$arrSubForums = $boardObj->getSubForums();
		if(count($arrSubForums) > 0) {
			dispManageTable($arrSubForums, ($indent+1));
		}
		
	}	
	
	
}



echo "
	<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
		<tr><td class='dottedLine' colspan='5'></td></tr>
";


$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_board WHERE subforum_id = '0'");
$totalBoards = $result->num_rows;

if($totalBoards > 0) {
	$result = $mysqli->query("SELECT forumcategory_id FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$categoryObj->select($row['forumcategory_id']);
		$arrBoards = $categoryObj->getAssociateIDs("AND subforum_id = '0' ORDER BY sortnum", true);
		
		$catInfo = $categoryObj->get_info_filtered();
		
		echo "
			<tr><td colspan='5' style='padding-top: 3px'></td></tr>
			<tr>
				<td class='dottedLine main' style='width: 76%'><b><u>".$catInfo['name']."</u></b></td>
				<td class='dottedLine main' style='width: 12%' colspan='2' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intAddBoardCID."&catID=".$catInfo['forumcategory_id']."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/add.png' width='24' height='24' title='Add Board to ".$catInfo['name']."'></a></td>
				<td class='dottedLine main' style='width: 12%' colspan='2' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intEditCatCID."&catID=".$catInfo['forumcategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' title='Edit Category' width='24' height='24'></a></td>
			</tr>
		
		";
		
		dispManageTable($arrBoards);
	}

}
echo "</table>";

if($totalBoards == 0) {
	
	echo "
	
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
		
			<p class='main' align='center'>
				<i>There are currently no boards in your forum!<br><br>Click <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddBoardCID."'>here</a> to add a board.</i>
			</p>
		
		</div>
	
	";
	
}


?>