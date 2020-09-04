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
	$awardMedalObj = new Basic($mysqli, "medals_members", "medalmember_id");
	
	$rankObj->select($rankInfo['promotepower']);
	$maxRankInfo = $rankObj->get_info_filtered();
	
	if($rankInfo['rank_id'] == 1) {
		$maxRankInfo['ordernum'] += 1;	
	}
	
	$arrRanks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$arrRanks[] = $row['rank_id'];
	}


	$sqlRanks = "('".implode("','", $arrRanks)."')";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.member_id != '".$memberInfo['member_id']."' ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
	
		$rankObj->select($row['rank_id']);
		$memberOptions[$row['member_id']] = $rankObj->get_info_filtered("name")." ".filterText($row['username']);
		
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."medals ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$medalOptions[$row['medal_id']] = filterText($row['name']);
	}
	
	echo "
		<div class='main' id='medalPopUp' style='display: none; position: relative'>
			<div class='loadingSpiral' id='loadingSpiral' style='position: relative'><p align='center'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br><br><i>Loading...</i></p></div>
			<div id='medalInfoDiv' style='position: relative'></div>
			
		</div>
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
				
			});
		</script>
	";

	$i = 1;
	$arrComponents = array(
		"member" => array(
			"type" => "select",
			"options" => $memberOptions,
			"attributes" => array("class" => "textBox formInput"),
			"db_name" => "member_id",
			"sortorder" => $i++,
			"display_name" => "Member",
			"validate" => array("RESTRICT_TO_OPTIONS", array("name" => "IS_SELECTABLE", "selectObj" => $member, "select_back" => "member_id"), array("name" => "NOT_EQUALS_VALUE", "value" => $memberInfo['member_id']))
		),
		"medal" => array(
			"type" => "select",
			"options" => $medalOptions,
			"attributes" => array("class" => "textBox formInput", "id" => "medalselect"),
			"db_name" => "medal_id",
			"sortorder" => $i++,
			"display_name" => "Medal",
			"validate" => array("RESTRICT_TO_OPTIONS"),
			"html" => "<div class='main formInput' style='display: none; padding-left: 10px' id='reshowDiv'><a href='javascript:void(0)' id='setShowTrue'>Show Medal Info</a></div>"
		),
		"reason" => array(
			"type" => "textarea",
			"attributes" => array("class" => "textBox formInput", "rows" => 3, "style" => "width: 35%"),
			"db_name" => "reason",
			"sortorder" => $i++,
			"display_name" => "Reason"
		),
		"submit" => array(
			"type" => "submit",
			"attributes" => array("class" => "submitButton formSubmitButton"),
			"value" => "Award Medal",
			"sortorder" => $i++
		)
	
	);

	if($_POST['submit']) {
		$member->select($_POST['member']);	
		$medalObj->select($_POST['medal']);
	}
	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"attributes" => array("id" => "formDiv", "action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"afterSave" => array("awardMedalSave"),
		"saveMessage" => "Successfully awarded ".$member->getMemberLink()." the medal <b>".$medalObj->get_info_filtered("name")."</b>!",
		"saveObject" => $awardMedalObj,
		"saveType" => "add",
		"saveAdditional" => array("dateawarded" => time()),
		"description" => "Use the form below to award a medal."
	);
	
	// After Save
	
	function awardMedalSave() {
		global $member, $medalObj, $memberInfo;
		$member->select($_POST['member_id']);
		$logMessage = $member->getMemberLink()." was awarded the ".$medalObj->get_info_filtered("name")." medal.<br><br><b>Reason:</b><br>".filterText($_POST['reason']);
			
		$member->postNotification("You were awarded the medal: <b>".$medalObj->get_info_filtered("name")."</b>");
			
		$member->select($memberInfo['member_id']);
		$member->logAction($logMessage);
	}

?>