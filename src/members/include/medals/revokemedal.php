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
	
	include_once("../classes/medal.php");
	
	$rankInfo = $memberRank->get_info_filtered();
	if($memberInfo['promotepower'] != 0) {
		$rankInfo['promotepower'] = $memberInfo['promotepower'];	
	}
	elseif($memberInfo['promotepower'] == -1) {
		$rankInfo['promotepower'] = 0;	
	}
	
	$cID = $_GET['cID'];
	
	$dispError = "";
	$countErrors = 0;
	if($memberInfo['rank_id'] == 1) {
	
		$maxOrderNum = $mysqli->query("SELECT MAX(ordernum) FROM ".$dbprefix."ranks WHERE rank_id != '1'");
		$arrMaxOrderNum = $maxOrderNum->fetch_array(MYSQLI_NUM);
	
		if($maxOrderNum->num_rows > 0) {
			$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE ordernum = '".$arrMaxOrderNum[0]."'");
			$row = $result->fetch_assoc();
			$rankInfo['promotepower'] = $row['rank_id'];
		}
	
	}
	
	$rankObj = new Rank($mysqli);
	$medalObj = new Medal($mysqli);
	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	if($rankInfo['rank_id'] == 1) {
		$maxRankInfo['ordernum'] += 1;
	}
	
	$arrRanks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum < '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$arrRanks[] = $row['rank_id'];
	}





	$sqlRanks = "('".implode("','", $arrRanks)."')";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.member_id != '".$memberInfo['member_id']."' ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
	
		$rankObj->select($row['rank_id']);
		$memberOptions[$row['member_id']] = $rankObj->get_info_filtered("name")." ".filterText($row['username']);
	
	}
	
	if($_POST['submit']) {
		$member->select($_POST['member']);			
		$arrMedals = $member->getMedalList();
		$medaloptions = array();
		foreach($arrMedals as $medalID) {
			
			$medalObj->select($medalID);
			$medalInfo = $medalObj->get_info_filtered();
			
			$medalOptions[$medalInfo['medal_id']] = $medalInfo['name'];
			
		}
		
		$medalObj->select($_POST['medal']);
	}
	
	
	$i = 1;
	$arrComponents = array(
		"member" => array(
			"type" => "select",
			"options" => $memberOptions,
			"attributes" => array("class" => "textBox formInput", "id" => "memberselect"),
			"sortorder" => $i++,
			"display_name" => "Member",
			"validate" => array("RESTRICT_TO_OPTIONS", array("name" => "IS_SELECTABLE", "selectObj" => $member, "select_back" => "member_id"), array("name" => "NOT_EQUALS_VALUE", "value" => $memberInfo['member_id']))
		),
		"medal" => array(
			"type" => "select",
			"options" => $medalOptions,
			"attributes" => array("class" => "textBox formInput", "id" => "medalselect"),
			"sortorder" => $i++,
			"display_name" => "Medal",
			"validate" => array("RESTRICT_TO_OPTIONS"),
			"html" => "<div class='main formInput' style='display: none; padding-left: 10px' id='reshowDiv'><a href='javascript:void(0)' id='setShowTrue'>Show Medal Info</a></div>"
		),
		"reason" => array(
			"type" => "textarea",
			"attributes" => array("class" => "textBox formInput", "rows" => 3, "style" => "width: 35%"),
			"sortorder" => $i++,
			"display_name" => "Reason"
		),
		"freezetime" => array(
			"display_name" => "Freeze Medal",
			"type" => "select",
			"tooltip" => "When revoking a medal that is auto-awarded based on number of days in the clan or number of recruits, the medal will be automatically re-awarded after being revoked.  Set this option to prevent the medal from being auto-awarded.",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox formInput"),
			"validate" => array("RESTRICT_TO_OPTIONS", "NUMBER_ONLY"),
			"options" => array(0 => "Don't Freeze", 1 => "1 day", 3=> "3 days", 7 => "7 days", 10 => "10 days", 14 => "14 days", 21 => "21 days", 30 => "30 days", 45 => "45 days", 60 => "60 days", 75 => "75 days", 90 => "90 days", 36500 => "Forever")
		
		),
		"submit" => array(
			"type" => "submit",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"value" => "Revoke Medal",
			"sortorder" => $i++
		)
	
	);
	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"attributes" => array("id" => "formDiv", "action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"afterSave" => array("revokeMedalSave"),
		"saveMessage" => "Successfully revoked the <b>".$medalObj->get_info_filtered("name")."</b> medal from ".$member->getMemberLink()."!",
		"description" => "Use the form below to revoke a medal.<br><br><b><u>NOTE:</u></b> If you revoke a medal that is awarded automatically after a certain number of days in the clan, it will be re-awarded to the member."
	);
	
	
	echo "		
		<script type='text/javascript'>
			$(document).ready(function() {
				var blnHidePreview = 0;
				var intFirst = 0;
				
				$('#setShowTrue').click(function() {
					blnHidePreview = 0;
					intFirst = 0;
					$('#medalselect').change();
					$('#reshowDiv').hide();	
				});				
				
				$('#memberselect').change(function() {
				
					var intMemberID = $('#memberselect').val();
					
					$('#medalselect').html('');
					
					
					$.post('".$MAIN_ROOT."members/include/medals/membermedals.php', { mID: intMemberID }, function(data) {
					
						$('#medalselect').html(data);
					
					});
				
				
				});
				
				
				$('#medalselect').change(function() {
					
					var intX = $('#formDiv').position().left+150+$('#formDiv').width();
					var intY = $('#formDiv').position().top+($('#formDiv').height()/2);
					
				
					$('#loadingSpiral').show();
					$('#medalInfoDiv').hide();
					$.post('".$MAIN_ROOT."members/include/medals/medalinfo.php', { medalID: $('#medalselect').val() }, function(data) {
						$('#medalInfoDiv').html(data);
						$('#medalInfoDiv').show();
						$('#loadingSpiral').hide();
						if(blnHidePreview == 0) {
							$('#medalPopUp').dialog({
								title: 'Medal Information',
								show: 'fade',
								zIndex: 99999,
								resizable: false,
								modal: false,
								width: 150,
								beforeClose: function(event, ui) {
									blnHidePreview = 1;
									$('#reshowDiv').show();
								}
							
							});
						}
						
					});
				
					if(intFirst == 0) { $('#medalPopUp').dialog({position: [intX,intY]}); intFirst = 1; }
					
				});
			
				
				$('#memberselect').change();
				
			});
		</script>
		
		
	";
	

	// After Save
	
	function revokeMedalSave() {
		global $mysqli, $member, $medalObj, $memberInfo, $formObj;
		$revokeMedalObj = new Basic($mysqli, "medals_members", "medalmember_id");
		$arrMemberMedals = $member->getMedalList(true);
		$memberMedalID = array_search($_POST['medal'], $arrMemberMedals);
		
		if($revokeMedalObj->select($memberMedalID) && $revokeMedalObj->delete()) {
			// Check if medal is frozen for member already
			
			$arrFrozenMembers = $medalObj->getFrozenMembersList();
			
			if(in_array($_POST['member'], $arrFrozenMembers)) {
			
				$frozenMedalID = array_search($_POST['member'], $arrFrozenMembers);
				$medalObj->objFrozenMedal->select($frozenMedalID);
				$medalObj->objFrozenMedal->delete();
				
			}
			
			$frozenMessage = "";
			if($medalObj->get_info("autodays") != 0 || $medalObj->get_info("autorecruits") != 0) {
				$freezeTime = (86400*$_POST['freezetime'])+time();
				$medalObj->objFrozenMedal->addNew(array("medal_id", "member_id", "freezetime"), array($_POST['medal'], $_POST['member'], $freezeTime));
				$dispDays = ($_POST['freezetime'] == 1) ? "day" : "days";
				$frozenMessage = "  The medal will not be awarded again for ".$_POST['freezetime']." ".$dispDays.".";
			}
			
			$logMessage = $member->getMemberLink()." was stripped of the ".$medalObj->get_info_filtered("name")." medal.".$frozenMessage."<br><br><b>Reason:</b><br>".filterText($_POST['reason']);
			
			$member->postNotification("You were stripped of the medal: <b>".$medalObj->get_info_filtered("name")."</b>");
			
			$member->select($memberInfo['member_id']);
			$member->logAction($logMessage);
			
		}
		else {
			$formObj->blnSaveResult = false;	
			$formObj->errors[] = "Unable to save information to the database.  Please contact the website administrator.";
		}

	}
	
?>