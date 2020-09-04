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



include_once("../classes/forumboard.php");

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

$boardObj = new ForumBoard($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");
	
$memberOptions = "<option value='select'>[SELECT]</option>";
$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.ordernum FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id != '1' AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id ORDER BY ".$dbprefix."ranks.ordernum DESC");
while($row = $result->fetch_assoc()) {

	$memberRank->select($row['rank_id']);
	$dispRankName = $memberRank->get_info_filtered("name");
	$memberOptions .= "<option value='".$row['member_id']."'>".$dispRankName." ".filterText($row['username'])."</option>";

}


$boardOptions = "<option value='select'>[SELECT]</option>";
$result = $mysqli->query("SELECT forumcategory_id FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	
	
	$categoryObj->select($row['forumcategory_id']);
	$arrBoards = $categoryObj->getAssociateIDs(" ORDER BY sortnum");
	$catInfo = $categoryObj->get_info_filtered();
	$boardOptions .= "<option value='cat_".$catInfo['forumcategory_id']."'>".$catInfo['name']."</option>";
	foreach($arrBoards as $boardID) {
		$boardObj->select($boardID);
		$boardInfo = $boardObj->get_info_filtered();
		$boardOptions .= "<option value='board_".$boardInfo['forumboard_id']."'>&nbsp;&nbsp;&nbsp;".$boardInfo['name']."</option>";
	
	}
	
}

echo "
	<div class='formDiv'>
		Use the form below to assign moderators to different boards in your forum.
		<table class='formTable'>
			<tr>
				<td class='formLabel'>Member:</td>
				<td class='main'><select id='memberModList' class='textBox'>".$memberOptions."</select></td>
			</tr>
			<tr>
				<td class='formLabel'>Board:</td>
				<td class='main'><select id='boardList' class='textBox'>".$boardOptions."</select></td>
			</tr>
			<tr>
				<td class='main' align='center' colspan='2'>
					<input type='button' class='submitButton' id='assignMod' style='width: 145px; margin: 20px auto' value='Assign as Moderator'>
				</td>
			</tr>
			<tr>
				<td class='main' colspan='2'>
					<div class='dottedLine' style='padding-bottom: 3px; margin-top: 10px'><b>Board List:</b></div>
				</td>
			</tr>
		</table>
		
		<div id='loadingSpiralPageList' style='display: none'>
			<p align='center' class='main'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
		
		<div id='moderatorDiv'>
			<table class='formTable'>
				<tr>
					<td class='formTitle' style='width: 50%'>Board:</td>
					<td class='formTitle' style='width: 35%'>Date Added:</td>
					<td class='formTitle' style='width: 15%'>Actions:</td>
				</tr>
				<tr>
					<td colspan='3' align='center'>
						<div class='shadedBox' style='width: 40%; margin: 20px auto'>
							<p class='main' align='center'>
								<i>No member selected!</i>
							</p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div id='boardError' style='display: none'>
		<p class='main' align='center'>
			You must select a board!
		</p>
	</div>
	
	<div id='memberError' style='display: none'>
		<p class='main' align='center'>
			You must select a member!
		</p>
	</div>
	
	<script type='text/javascript'>
	
		$(document).ready(function() {
		
			$('#memberModList').change(function() {
			
				$('#loadingSpiral').show();
				$('#moderatorDiv').fadeOut(250);

				$.post('".$MAIN_ROOT."members/include/forum/include/moderatorlist.php', { mID: $('#memberModList').val() }, function(data) {
				
					$('#moderatorDiv').html(data);
					$('#loadingSpiral').hide();
					$('#moderatorDiv').fadeIn(250);
				
				});
				
			
			});
			
			
			$('#assignMod').click(function() {
			
				if($('#boardList').val() != 'select' && $('#memberModList').val() != 'select') {
						$('#loadingSpiral').show();
						$('#moderatorDiv').fadeOut(250);
			
						$.post('".$MAIN_ROOT."members/include/forum/include/moderatorlist.php', { mID: $('#memberModList').val(), bID: $('#boardList').val(), action: 'add' }, function(data) {
						
							$('#moderatorDiv').html(data);
							$('#loadingSpiral').hide();
							$('#moderatorDiv').fadeIn(250);
						
						});
					}
					else if($('#memberModList').val() == 'select') {
					
						$('#memberError').dialog({
						
							title: 'Manage Moderators - Error',
							width: 400,
							modal: true,
							resizable: false,
							show: 'scale',
							zIndex: 999999,
							buttons: {
							
								'OK': function() {
								
									$(this).dialog('close');
								
								}
							
							}
						
						});
					
					}
					else {
					
						$('#boardError').dialog({
						
							title: 'Manage Moderators - Error',
							width: 400,
							modal: true,
							resizable: false,
							show: 'scale',
							zIndex: 999999,
							buttons: {
							
								'OK': function() {
								
									$(this).dialog('close');
								
								}
							
							}
						
						});
					
					}
			
			
			});
		
		});	
		
		
		function deleteMod(boardID, memberID) {
		
			$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#moderatorDiv').fadeOut(250);
	
				$.post('".$MAIN_ROOT."members/include/forum/include/moderatorlist.php', { mID: memberID, bID: boardID, action: 'delete' }, function(data) {
				
					$('#moderatorDiv').html(data);
					$('#loadingSpiral').hide();
					$('#moderatorDiv').fadeIn(250);
				
				});
			});

		}
	
	</script>
";


?>