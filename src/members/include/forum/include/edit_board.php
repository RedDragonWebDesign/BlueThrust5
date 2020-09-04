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

include_once("../classes/rankcategory.php");

$cID = $_GET['cID'];

$boardInfo = $boardObj->get_info_filtered();

$rankCatObj = new RankCategory($mysqli);
$rankObj = new Rank($mysqli);
$tempMemObj = new Member($mysqli);

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > ".$boardInfo['name']."\");
});
</script>
";


if($_POST['submit']) {
	
	// Check Board Name
	
	if(trim($_POST['boardname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Board name may not be blank.<br>";
		$countErrors++;
	}
	
	// Check Category
	
	if(!$categoryObj->select($_POST['forumcat'])) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid forum category.<br>";
		$countErrors++;
	}
	
	
	// Check Subforum
	
	if($_POST['subforum'] == 1 && $boardObj->select($_POST['subforumboard'])) {
		$setSubForum = $_POST['subforumboard'];
	}
	else {
		$setSubForum = 0;
	}
	
	
	$boardObj->select($_GET['bID']);
	$boardObj->setSubForumID($setSubForum);
	
	// Check Order
	
	$boardObj->setCategoryKeyValue($categoryObj->get_info("forumcategory_id"));
	$intNewOrderSpot = $boardObj->validateOrder($_POST['displayorder'], $_POST['beforeafter'], true, $boardInfo['sortnum']);
	
	if($intNewOrderSpot === false) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		$countErrors++;
	}
	
	
	// Forum Access
	
	if($_POST['accesstype'] != 1) {
		$_POST['accesstype'] = 0;
		$arrRanks = array();
		$arrMembers = array();
	}
	else {
	
		$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE rank_id != '1'");
		while($row = $result->fetch_assoc()) {
	
			$checkboxName = "rankaccess_".$row['rank_id'];
			if($_SESSION['btRankAccessCache'][$checkboxName] == "1") {
				$arrRanks[$row['rank_id']] = 1;
			}
			elseif($_SESSION['btRankAccessCache'][$checkboxName] == "2") {
				$arrRanks[$row['rank_id']] = 0;
			}
	
		}
	
		foreach($_SESSION['btMemberAccessCache'] as $memID => $accessRule) {
	
			if($accessRule != "" && $tempMemObj->select($memID)) {
				$arrMembers[$memID] = $accessRule;
			}
	
		}
	
	
	}
	
	if($countErrors == 0) {
		
		$arrColumns = array("forumcategory_id", "name", "description", "sortnum", "accesstype", "subforum_id");
		$arrValues = array($_POST['forumcat'], $_POST['boardname'], $_POST['boarddesc'], $intNewOrderSpot, $_POST['accesstype'], $setSubForum);
		$boardObj->select($boardInfo['forumboard_id']);
		if($boardObj->update($arrColumns, $arrValues) && $boardObj->secureBoard($arrRanks, $arrMembers)) {
			$boardInfo = $boardObj->get_info_filtered();
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Edited Board: <b>".$boardInfo['name']."</b>!
					</p>
				</div>
		
			<script type='text/javascript'>
				popupDialog('Edit Board', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			";
		
			$boardObj->resortOrder();
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
		
	}
	
	
	if($countErrors > 0) {

		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
		
	}
	
	
	
}


if(!$_POST['submit']) {
	
	$_SESSION['btMemberAccessCache'] = $boardObj->getMemberAccessRules();
	$rankAccessRules = $boardObj->getRankAccessRules();

	if($dispError == "") {
		$_SESSION['btRankAccessCache'] = array();
		foreach($rankAccessRules as $rankID => $accessValue) {
			$keyName = "rankaccess_".$rankID;
			
			$setAccessValue = ($accessValue == 0) ? 2 : 1;
			
			$_SESSION['btRankAccessCache'][$keyName] = $setAccessValue;
		}
	}
	
	$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks ORDER BY ordernum DESC");
	$rankCounter = $result->num_rows;
	
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$selectCat = "";
		if($row['forumcategory_id'] == $boardInfo['forumcategory_id']) {
			$selectCat = " selected";	
		}
		$catoptions .= "<option value='".$row['forumcategory_id']."'".$selectCat.">".filterText($row['name'])."</option>";		
	}
	
	$arrBeforeAfter = $boardObj->findBeforeAfter();
	
	$selectAfter = "";
	if($arrBeforeAfter[1] == "after") {
		$selectAfter = " selected";		
	}
	
	$selectLimited = "";
	if($boardInfo['accesstype'] == 1) {
		$selectLimited = " selected";
	}
	
	$blnIsSubforum = 0;
	$checkSubForumBox = "";
	if($boardInfo['subforum_id'] != 0) {
		$blnIsSubforum = 1;
		$checkSubForumBox = " checked";
	}
	
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&bID=".$_GET['bID']."&action=edit' method='post'>
		<div class='formDiv'>
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit board because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
			Use the form below to edit the selected board.
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Board Name:</td>
					<td class='main'><input type='text' value='".$boardInfo['name']."' name='boardname' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Description:</td>
					<td class='main'><textarea name='boarddesc' class='textBox' style='width: 250px; height: 85px'>".$boardInfo['description']."</textarea></td>
				</tr>
				<tr>
					<td class='formLabel'>Category:</td>
					<td class='main'><select id='forumcat' name='forumcat' class='textBox'>".$catoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel'>Sub-Forum: <input type='checkbox' name='subforum' value='1' id='isSubForum'".$checkSubForumBox."></td>
					<td class='main'>
						<select name='subforumboard' id='subForumBoard' class='textBox'></select>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Display Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$selectAfter.">After</option></select><br>
						<select name='displayorder' id='displayorder' class='textBox'></select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Access Type:</td>
					<td class='main'><select id='accesstype' name='accesstype' class='textBox'><option value='0'>All Members</option><option value='1'".$selectLimited.">Limited</option></td>
				</tr>
			</table>
			
			<div id='accessTypeDiv' style='display: none'>
				<table class='formTable'>
					<tr>
						<td class='main' colspan='2'>
							<div class='dottedLine' style='width: 90%; padding-bottom: 3px'><b>Rank Access:</b></div>
							<div style='width: 90%; padding-left: 3px; margin-bottom: 15px'>Use this section to set which ranks are allowed to access this board.</div>
						</td>
					</tr>
					";
	
	
	$rankOptionsHeight = $rankCounter*20;
	
	if($rankOptionsHeight > 300) {
		$rankOptionsHeight = 300;
	}
	
	
	
	$memberOptions = "<option value='select'>[SELECT]</option>";
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.ordernum FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id != '1' AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
	
		$memberRank->select($row['rank_id']);
		$dispRankName = $memberRank->get_info_filtered("name");
		$memberOptions .= "<option value='".$row['member_id']."'>".$dispRankName." ".filterText($row['username'])."</option>";
	
	}
	
		echo "
					<tr>
						<td class='main' colspan='2'>
							<div id='loadingSpiralRankAccess' class='loadingSpiral'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>
							<div id='rankOptionsDiv' style='width: 90%; margin-left: 15px; overflow-y: auto; height: ".$rankOptionsHeight."px'>
														
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>With Selected:</td>
						<td class='main'>
							<select id='selectRankAccess' class='textBox'><option value='0'>No Access</option><option value='1'>Read-Only Access</option><option value='2'>Full Access</option></select>
							<input type='button' id='setRankAccess' value='Set' class='submitButton'>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
							<div class='dottedLine' style='width: 90%; padding-bottom: 3px; margin-top: 20px'><b>Member Access:</b></div>
							<div style='width: 90%; padding-left: 3px; margin-bottom: 15px'>Use this section to set whether a specific member can or cannot access this board. Clicking the Allow or Deny buttons will not change the page. </div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select id='accessMemberList' class='textBox'>".$memberOptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>Access:</td>
						<td class='main'>
							<select id='selectMemberAccess' class='textBox'><option value='0'>No Access</option><option value='2'>Read-Only Access</option><option value='1'>Full Access</option></select> <input type='button' id='btnSetMemberAccess' class='submitButton' value='Set'>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2'><br><br>
							<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>
							<div id='boardMemberAccess'>
								
								<table align='left' border='0' cellspacing='2' cellpadding='2' width=\"90%\">
									<tr>
										<td class='formTitle' width=\"60%\">Member:</td>
										<td class='formTitle' width=\"20%\">Access:</td>
										<td class='formTitle' width=\"20%\">Actions:</td>
									</tr>
									<tr>
										<td class='main' colspan='3'>
											<p align='center' style='padding-top: 10px'><i>No special member access rules set!</i></p>
										</td>
									</tr>
								</table>
								
							</div>
						</td>
					</tr>
					
				</table>
			</div>
			
			<div style='text-align: center; margin: 20px auto'>
				<input type='submit' name='submit' value='Edit Board' class='submitButton' style='width: 100px'>
			</div>
			
			
		</div>
		</form>
	
		<script type='text/javascript'>
	
			$(document).ready(function() {
				var intSubForumClick = ".$blnIsSubforum.";
				";
		if($blnIsSubforum == 0) {
			echo "$('#subForumBoard').attr('disabled', 'disabled');";
		}
		echo "
			
				$('#isSubForum').click(function() {	
				
					if(intSubForumClick == 0) {
						$('#subForumBoard').attr('disabled', false);
						intSubForumClick = 1;
						
						$('#subForumBoard').change();
						
					}
					else {
						$('#subForumBoard').attr('disabled', 'disabled');
						intSubForumClick = 0;
						
						$('#forumcat').change();
						
					}			
				
				});
				
				$('#subForumBoard').change(function() {
				
					$.post('".$MAIN_ROOT."members/include/forum/include/subforumlist.php', { subforum: $('#subForumBoard').val(), bID: '".$boardInfo['forumboard_id']."' }, function(data) {
						
						$('#displayorder').html(data);
					
					});
				
				});
			
			
				$('#forumcat').change(function() {
					$.post('".$MAIN_ROOT."members/include/forum/include/boardlist.php', { catID: $('#forumcat').val(), bID: '".$boardInfo['forumboard_id']."' }, function(data) {
						
						$('#subForumBoard').html(data);
					
						if(intSubForumClick == 0) {
							$('#displayorder').html(data);
						}
						else {
							$('#subForumBoard').change();
						}
					
					});
				});
				
				
				$('#loadingSpiral').show();
				$('#boardMemberAccess').hide();
				
				$.post('".$MAIN_ROOT."members/include/forum/include/boardaccesscache.php', { }, function(data) {
				
					$('#loadingSpiral').hide();
					$('#boardMemberAccess').html(data);				
					$('#boardMemberAccess').fadeIn(250);

				});
				
				$('#accesstype').change(function() {
				
					if($(this).val() == 0) {
						$('#accessTypeDiv').hide();
					}
					else {
						$('#accessTypeDiv').show();
					}
				
				});
				
			
				$('#accesstype').change();
				$('#forumcat').change();
				
				
				$('#loadingSpiralRankAccess').show();
				$('#rankOptionsDiv').hide();
				
				$.post('".$MAIN_ROOT."members/include/forum/include/rankaccesscache.php', { }, function(data) {
				
					$('#loadingSpiralRankAccess').hide();
					$('#rankOptionsDiv').html(data);				
					$('#rankOptionsDiv').fadeIn(250);

				});
				
				
				";
		
			if($dispError != "") {
				echo "
					$('#loadingSpiral').show();
					$('#boardMemberAccess').hide();
					
					$.post('".$MAIN_ROOT."members/include/forum/include/boardaccesscache.php', { }, function(data) {
					
						$('#loadingSpiral').hide();
						$('#boardMemberAccess').html(data);				
						$('#boardMemberAccess').fadeIn(250);
	
					});
				";
			}		
		
				
		echo "
				$('#setRankAccess').click(function() {
					var intAccessCount = 0;
					var objRankAccess = {};
					$(\"input[data-rankaccess='1']\").each(function(index) {
						if($(this).is(':checked') && ($('#selectRankAccess').val() == '0' || $('#selectRankAccess').val() == '1' || $('#selectRankAccess').val() == '2')) {
							objRankAccess[$(this).attr('name')] = $('#selectRankAccess').val();
						}
					});
					
					var jsonRankAccess = JSON.stringify(objRankAccess);
					
					$('#loadingSpiralRankAccess').show();
					$('#rankOptionsDiv').hide();
					
					$.post('".$MAIN_ROOT."members/include/forum/include/rankaccesscache.php', { accessInfo: jsonRankAccess }, function(data) {
					
						$('#loadingSpiralRankAccess').hide();
						$('#rankOptionsDiv').html(data);				
						$('#rankOptionsDiv').fadeIn(250);
					
					});
					
				});
				
				$('#btnSetMemberAccess').click(function() {
				
					addMemberAccess($('#selectMemberAccess').val());
				
				});		
		
		
			});
			
			
			function addMemberAccess(strAccess) {
			
				$(document).ready(function() {
					var intMemberID = $('#accessMemberList').val();
					$('#loadingSpiral').show();
					$('#boardMemberAccess').hide();
					
					$.post('".$MAIN_ROOT."members/include/forum/include/boardaccesscache.php', { mID: intMemberID, accessRule: strAccess, action: 'add' }, function(data) {
					
						$('#loadingSpiral').hide();
						$('#boardMemberAccess').html(data);				
						$('#boardMemberAccess').fadeIn(400);
						$('#accessMemberList').val('[SELECT]');
					});
				});
			}
			
			
			function deleteAccessRule(intKey) {
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#boardMemberAccess').hide();
					$.post('".$MAIN_ROOT."members/include/forum/include/boardaccesscache.php', { mID: intKey, action: 'delete' }, function(data) {
					
						$('#loadingSpiral').hide();
						$('#boardMemberAccess').html(data);				
						$('#boardMemberAccess').fadeIn(400);
						
					});
				
				});
			}
			
			
			
		</script>
	
	";
	
}