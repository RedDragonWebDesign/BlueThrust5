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
include_once("../../../../classes/basicorder.php");
include_once("../../../../classes/forumboard.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Moderators");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$memberModObj = new Member($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");

$boardObj = new ForumBoard($mysqli);

// Check Login
$LOGIN_FAIL = true;

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	echo "
	
		<table class='formTable' style='margin-bottom: 20px'>
			<tr>
				<td class='formTitle' style='width: 50%'>Board:</td>
				<td class='formTitle' style='width: 35%'>Date Added:</td>
				<td class='formTitle' style='width: 15%'>Actions:</td>
			</tr>
	
	";
	
	if(isset($_POST['mID']) && $memberModObj->select($_POST['mID'])) {
		$memberModInfo = $memberModObj->get_info_filtered();
		if($_POST['action'] == "add") {
			
			if(substr($_POST['bID'], 0, 4) == "cat_") {
				$catID = str_replace("cat_", "", $_POST['bID']);
				if($categoryObj->select($catID)) {
					
					$arrBoards = $categoryObj->getAssociateIDs();
					foreach($arrBoards as $boardID) {
						$boardObj->select($boardID);
						$boardObj->addMod($memberModInfo['member_id']);
					}
					
				}

			}
			elseif(substr($_POST['bID'], 0, 6) == "board_") {
				
				$boardID = str_replace("board_", "", $_POST['bID']);
				if($boardObj->select($boardID)) {
					$boardObj->addMod($memberModInfo['member_id']);
				}
			}
			
		}
		elseif($_POST['action'] == "delete") {
			
			if($boardObj->select($_POST['bID'])) {
				$boardObj->removeMod($memberModInfo['member_id']);	
			}
		}
		
		
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_moderator WHERE member_id = '".$memberModInfo['member_id']."' ORDER BY dateadded DESC");
		while($row = $result->fetch_assoc()) {
			
			$boardObj->select($row['forumboard_id']);
			$boardInfo = $boardObj->get_info_filtered();
			
			echo "
				<tr>
					<td class='main'>".$boardInfo['name']."</td>
					<td class='main'>".getPreciseTime($row['dateadded'])."</td>
					<td class='main' align='center'><a href='javascript:void(0)' onclick=\"deleteMod('".$row['forumboard_id']."', '".$row['member_id']."')\" title='Remove'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' style='width: 24px; height: 24px'></a></td>
				</tr>					
			";
		
		}
		
		if($result->num_rows == 0) {

			echo "
				<tr>
					<td colspan='3' align='center'>
						<div class='shadedBox' style='width: 40%; margin: 20px auto'>
							<p class='main' align='center'>
								<i>".$memberModInfo['username']." is not a moderator for any board!</i>
							</p>
						</div>
					</td>
				</tr>
			";
			
		}
		
	}
	else {
		
		echo "
		
				<tr>
					<td colspan='3' align='center'>
						<div class='shadedBox' style='width: 40%; margin: 20px auto'>
							<p class='main' align='center'>
								<i>No member selected!</i>
							</p>
						</div>
					</td>
				</tr>
		";
	}
	
	
	
	
	
	echo "
		</table>
		
	";
	
}

?>