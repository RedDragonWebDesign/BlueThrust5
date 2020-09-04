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

include_once("../classes/access.php");
include_once("../classes/poll.php");

$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;



$pollObj = new Poll($mysqli);
$accessObj = $pollObj->objAccess;


if(isset($_POST['accessCacheID'])) {
	$accessObj->cacheID = $_POST['accessCacheID'];
}

$_SESSION['btAccessCacheTables'][$accessObj->cacheID] = json_encode($accessObj->arrAccessTables);
$_SESSION['btAccessCacheTypes'][$accessObj->cacheID] = json_encode($accessObj->arrAccessTypes);

$arrPostSelected = array();

if($_POST['submit']) {
	
	// Check Question
	if(trim($_POST['pollquestion']) == "") {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> Your poll question may not be blank.<br>";
	}
	
	// Check Access Types
	$arrCheckAccessTypes = array("members", "memberslimited", "public");
	if(!in_array($_POST['accesstype'], $arrCheckAccessTypes)) {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> You selected an invalid access type.<br>";		
	}
	

	// Check Result Visibility
	$arrCheckVisTypes = array("open", "votedonly", "pollend", "never");
	if(!in_array($_POST['resultvisibility'], $arrCheckVisTypes)) {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> You selected an invalid result visibility type.<br>";		
	}
	
	// Check Max Votes
	
	if($_POST['maxvotes'] != "" && (!is_numeric($_POST['maxvotes']) || $_POST['maxvotes'] < 0)) {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> Max votes per user must be a value greater than zero.<br>";
	}
	
	// Check Poll End
	
	if($_POST['enddate'] != "forever" && $_POST['enddate'] != "choose") {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> You selected an invalid poll end date.<br>";
	}
	elseif($_POST['enddate'] == "choose" && (!is_numeric($_POST['realenddate']) || $_POST['realenddate'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp&nbsp;&nbsp;<b>&middot;</b> You selected an invalid poll end date.<br>";
	}
	
	
	
	if($countErrors == 0) {
		
		$setEndDate = 0;
		if($_POST['enddate'] == "choose") {
			$setEndDate = $_POST['realenddate']/1000;
			$tempYear = date("Y", $setEndDate);
			$tempMonth = date("n", $setEndDate);
			$tempDay = date("j", $setEndDate);
			$tempHour = $_POST['endhour'];
			if($_POST['endAMPM'] == "PM") {
				$tempHour += 12;	
			}
			
			$setEndDate = mktime($tempHour, $_POST['endminute'], 0, $tempMonth, $tempDay, $tempYear);	
		}
		
		
		$_POST['multivote'] = ($_POST['multivote'] != 1) ? 0 : 1;
		$_POST['displayvoters'] = ($_POST['displayvoters'] != 1) ? 0 : 1;
		
		$arrColumns = array("member_id", "question", "accesstype", "multivote", "displayvoters", "resultvisibility", "maxvotes", "pollend", "dateposted");
		$arrValues = array($memberInfo['member_id'], $_POST['pollquestion'], $_POST['accesstype'], $_POST['multivote'], $_POST['displayvoters'], $_POST['resultvisibility'], $_POST['maxvotes'], $setEndDate, time());
		
		if($pollObj->addNew($arrColumns, $arrValues)) {
			$pollObj->cacheID = $_POST['pollCacheID'];
			$pollObj->savePollOptions();
			
			if($_POST['accesstype'] == "memberslimited") {
				$accessObj->cacheID = $_POST['accessCacheID'];
				$accessObj->arrAccessFor = array("keyName" => "poll_id", "keyValue" => $pollObj->get_info("poll_id"));
				$accessObj->saveAccess();
			}
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully created a new poll!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Create a Poll', '".$MAIN_ROOT."members', 'successBox');
				</script>			
			";
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
	}
	
	
	if($countErrors > 0) {
		
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
		
		$arrPostSelected['accesstype']['limited'] = ($_POST['accesstype'] == "memberslimited") ? " selected" : "";
		$arrPostSelected['accesstype']['public'] = ($_POST['accesstype'] == "public") ? " selected" : "";
		
		$arrPostSelected['multivote'] = ($_POST['multivote'] == 1) ? " checked" : "";
		$arrPostSelected['displayvoters'] = ($_POST['displayvoters'] == 1) ? " checked" : "";
		
		
		$arrPostSelected['resultvisibility']['votedonly'] = ($_POST['resultvisibility'] == "votedonly") ? " selected" : "";
		$arrPostSelected['resultvisibility']['pollend'] = ($_POST['resultvisibility'] == "pollend") ? " selected" : "";
		$arrPostSelected['resultvisibility']['never'] = ($_POST['resultvisibility'] == "never") ? " selected" : "";
		
		$arrPostSelected['pollend']['choose'] = ($_POST['enddate'] == "choose") ? " selected" : "";
		$arrPostSelected['pollend']['forever'] = ($_POST['enddate'] == "forever") ? " selected" : "";
		
		$arrPostSelected['endAMPM'] = ($_POST['endAMPM'] == "PM") ? " selected" : "";
		
	}
	
	
}


$addMenuItemCID = $consoleObj->findConsoleIDByName("Add Menu Item");
if(!$_POST['submit']) {	
	
	echo "
	
		<div class='formDiv'>
		
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to create poll because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
		$pollOptionCacheID = $_POST['pollCacheID'];
	}
	else {
		$pollOptionCacheID = md5(time().uniqid());
		$_SESSION['btPollOptionCache'][$pollOptionCacheID] = array();		
	}
	
	
	
	$hourOptions = "";
	for($i=12; $i>=1; $i--) {
		$tempNum = str_pad($i, 2, "0", STR_PAD_LEFT);
		$dispSelected = "";
		if(isset($_POST['endhour']) && $_POST['endhour'] == $i) {
			$dispSelected = " selected";
		}
		
		$hourOptions .= "<option value='".$i."'".$dispSelected.">".$tempNum."</option>";
	}
	
	$minuteOptions = "";
	for($i=0; $i<=59; $i++) {
		$tempNum = str_pad($i, 2, "0", STR_PAD_LEFT);
		
		$dispSelected = "";
		if(isset($_POST['endminute']) && $_POST['endminute'] == $i) {
			$dispSelected = " selected";	
		}
		
		$minuteOptions .= "<option value='".$i."'".$dispSelected.">".$tempNum."</option>";	
	}

	
	
	echo "
		
			<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			Use the form below to add a poll.  You can display polls in menus by going to the <a href='".$MAIN_ROOT."members/console.php?cID=".$addMenuItemCID."'>Add Menu Item</a> page.		
			<table class='formTable'>
				<tr>
					<td class='formLabel' valign='top'>Question:</td>
					<td class='main'><textarea name='pollquestion' class='textBox' style='width: 60%; height: 30px'>".$_POST['pollquestion']."</textarea></td>
				</tr>
				<tr>
					<td class='formLabel'>Access:</td>
					<td class='main'>
						<select name='accesstype' id='accessType' class='textBox'>
							<option value='members'>Members Only</option>
							<option value='memberslimited'".$arrPostSelected['accesstype']['limited'].">Limited Members</option>
							<option value='public'".$arrPostSelected['accesstype']['public'].">Public</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Multi-Vote: <a href='javascript:void(0)' onmouseover=\"showToolTip('Select the checkbox to allow users to vote on more than one option at a time.')\" onmouseout=\"hideToolTip()\">(?)</a></td>
					<td class='main'>
						<input type='checkbox' name='multivote' value='1'".$arrPostSelected['multivote'].">
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Display Voters: <a href='javascript:void(0)' onmouseover=\"showToolTip('Select the checkbox to show who voted for which option.')\" onmouseout=\"hideToolTip()\">(?)</a></td>
					<td class='main'><input type='checkbox' name='displayvoters' value='1'".$arrPostSelected['displayvoters']."></td>
				</tr>
				<tr>
					<td class='formLabel'>Result Visibility:</td>
					<td class='main'>
						<select name='resultvisibility' class='textBox'>
							<option value='open'>Show Always</option>
							<option value='votedonly'".$arrPostSelected['resultvisibility']['votedonly'].">Show only after voted</option>
							<option value='pollend'".$arrPostSelected['resultvisibility']['pollend'].">Show only when poll ends</option>
							<option value='never'".$arrPostSelected['resultvisibility']['never'].">Don't reveal results</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Max votes per user: <a href='javascript:void(0)' onmouseover=\"showToolTip('Leave blank or 0 for unlimited votes.')\" onmouseout=\"hideToolTip()\">(?)</a></td>
					<td class='main'>
						<input type='text' name='maxvotes' value='".$_POST['maxvotes']."' class='textBox' style='width: 10%'>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Run poll until:</td>
					<td class='main'>
						<select name='enddate' id='endDatePick' class='textBox'>
							<option value=''>Select</option>
							<option value='choose'".$arrPostSelected['pollend']['choose'].">Choose Date</option>
							<option value='forever'".$arrPostSelected['pollend']['forever'].">Forever</option>
						</select>
						<div id='pickEndDateDiv' style='display: none'>
							<br>
							<input type='text' id='jqEndDate' name='fakeenddate' value='".$_POST['fakeenddate']."' class='textBox' readonly='readonly' style='cursor: pointer'>
							<p>
							<select class='textBox' name='endhour'>".$hourOptions."</select> : 
							<select class='textBox' name='endminute'>".$minuteOptions."</select>
							<select class='textBox' name='endAMPM'><option value='AM'>AM</option><option value='PM'".$arrPostSelected['endAMPM'].">PM</option></select>
							</p>
							<input type='hidden' name='realenddate' id='realEndDate'>
						</div>
					</td>
				</tr>
			</table>
			
			<div id='accessDiv' style='margin-bottom: 50px'>
				<div class='main' style='margin: 20px auto; width: 95%'>
					<div class='dottedLine'><b>Rank Access:</b></div>
					<p style='margin: 3px 0px;padding-left: 3px'>Use this section to set which ranks are allowed to access this poll.  If no access rules are set for a particular rank, they will have no access to this poll.</p>
				</div>
	
					";
					
					$accessObj->rankAccessDiv = "rankAccessList";
					$accessObj->dispSetRankAccess();
	
				echo "
				
					<div class='main' style='margin: 20px auto; width: 95%'>
						<div class='dottedLine'><b>Member Access:</b></div>
						<p style='margin: 3px 0px;padding-left: 3px'>Use this section to set whether a specific member is allowed to access this poll.</p>
					</div>
				";
				
					$accessObj->memberAccessDiv = "memberAccessList";
					$accessObj->dispSetMemberAccess();
				
			echo "	
			</div>
			
			<div class='dottedLine main' style='margin-top: 20px; width: 95%; margin-left: auto; margin-right: auto'>
				<b>Poll Options:</b>
			</div>
			<p align='center'>
				<input type='button' class='submitButton' id='btnAddOption' value='Add Option'><br>
			</p>
			<br>
			
			<table class='formTable' style='width: 75%'>
				<tr>
					<td class='formTitle' style='width: 50%'>Option Value:</td>
					<td class='formTitle' style='width: 14%'>Color:</td>
					<td class='formTitle' style='width: 36%'>Actions:</td>
				</tr>
			</table>
			
			<div id='loadingSpiral' class='loadingSpiral'>
				<p align='center'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
				</p>
			</div>
			
			<div id='pollOptions' style='margin-bottom: 40px'>
			
				<p align='center' class='main'>
					<i>No options added yet!</i>
				</p>
				
			</div>
			
			<p align='center'>
				<input type='submit' name='submit' value='Create Poll' class='submitButton'>
			</p>
			
		</div>
		<input type='hidden' name='accessCacheID' value='".$accessObj->cacheID."'>
		<input type='hidden' name='pollCacheID' value='".$pollOptionCacheID."'>
		</form>
		<div id='addModifyOptionDiv' style='display: none'></div>
		<script type='text/javascript'>
		
		
			$(document).ready(function() {
			
				
				$('#accessType').change(function() {
				
					if($(this).val() == \"memberslimited\") {
						$('#accessDiv').show();
					}
					else {
						$('#accessDiv').hide();
					}
				
				});
				$('#accessType').change();
			
				$('#btnAddOption').click(function() {
				
					$.post('".$MAIN_ROOT."members/include/polls/include/addoption.php', { cacheID: '".$pollOptionCacheID."' }, function(data) {
						$('#addModifyOptionDiv').html(data);
	
						$('#addModifyOptionDiv').dialog({
							title: 'Add Poll Option',
							width: 400,
							show: 'scale',
							modal: true,
							zIndex: 99999,
							resizable: false,
							buttons: {
								'Add': function() {
									
									$.post('".$MAIN_ROOT."members/include/polls/include/addoption.php', { cacheID: '".$pollOptionCacheID."', submit: 'add', optionValue: $('#optionValue').val(), optionColor: $('#optionColor').val(), optionOrder: $('#optionOrder').val(), optionOrderBeforeAfter: $('#optionOrderBeforeAfter').val() }, function(data) {
									
										postData = JSON.parse(data);

										if(postData['result'] == \"success\") {
											reloadOptionCache();
											$('#addModifyOptionDiv').dialog('close');
										}
										else {
										
											var errorHTML = \"<strong>Unable to add new poll option due to the following errors:</strong><ul>\";
											for(var i in postData['errors']) {
												errorHTML += \"<li>\"+postData['errors'][i]+\"</li>\";
											}
											errorHTML += \"</ul>\";
											
											$('#dialogErrors').html(errorHTML);
											$('#dialogErrors').show();
											
										}
									
									});
									
								
								
								},
								'Cancel': function() {
									$(this).dialog('close');
								}
							
							}
						
						});
						
					});
				
				});
			
			
				$('#jqEndDate').datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'M d, yy',
					minDate: new Date(".date("Y").", ".(date("n")-1).", ".date("d")."),
					maxDate: new Date(".(date("Y")+20).",12,31),
					yearRange: '".date("Y").":".(date("Y")+20)."',
					altField: '#realEndDate',
					altFormat: '@'
				});
				
	
				
				$('#endDatePick').change(function() {
					console.log($('#realEndDate').val());
					if($(this).val() == \"choose\") {
						$('#pickEndDateDiv').show();
					}
					else {
						$('#pickEndDateDiv').hide();
					}
				
				});
				$('#endDatePick').change();
				
				
				function reloadOptionCache() {
				
					$('#loadingSpiral').show();
					$('#pollOptions').hide();
					$.post('".$MAIN_ROOT."members/include/polls/include/optioncache.php', { cacheID: '".$pollOptionCacheID."' }, function(data) {
						$('#loadingSpiral').hide();
						$('#pollOptions').html(data);
						
						$('#pollOptions').fadeIn(250);
					
					});
				
				}
				
				reloadOptionCache();
				
			});
			
		</script>		
	";
	
}


?>