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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/consolecategory.php");
include_once($prevFolder."classes/rankcategory.php");

$cID = $_GET['cID'];
$rankCatObj = new RankCategory($mysqli);
$consoleCatObj = new ConsoleCategory($mysqli);

$failbanObj = new Basic($mysqli, "failban", "failban_id");
$intMaxAttempts = 3;

if($_POST['submit']) {

	$countErrors = 0;
	
	// Check Page Title
	
	if(trim($_POST['pagetitle']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must give the console option a page title.<br>";
	}
	
	// Check Console Category
	
	if(!$consoleCatObj->select($_POST['consolecat'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid console category.<br>";
	}
	else {
		
		$arrConsoleIDs = $consoleCatObj->getAssociateIDs();

		if($_POST['consoleorder'] == "first" && count($arrConsoleIDs) > 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		}
		elseif(!in_array($_POST['consoleorder'], $arrConsoleIDs) && $_POST['consoleorder'] != "first") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order. (console option/console cat)<br>";
		}
		elseif(!$consoleObj->select($_POST['consoleorder']) && $_POST['consoleorder'] != "first") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order. (console option)<br>";
		}
		elseif($_POST['consoleorder'] == "first") {
			$intNewSortNum = 1;	
		}
		else {
			// Check Before/After Then Make Room
			
			if($_POST['consolebeforeafter'] == "before" OR $_POST['consolebeforeafter'] == "after") {
				$intNewSortNum = $consoleObj->makeRoom($_POST['consolebeforeafter']);
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order. (before/after)<br>";
			}			
		}
			
			
	}
	
	// Check Security Code
	
	if(constant('ADMIN_KEY') != $_POST['checkadmin']) {
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."failban WHERE ipaddress = '".$IP_ADDRESS."' AND pagename = 'addconsoleoption'");
		$countFails = $result->num_rows;
		$adminKeyFails = $intMaxAttempts-$countFails;
		
		$failbanObj->addNew(array("ipaddress", "pagename"), array($IP_ADDRESS, "addconsoleoption"));
		
		if($adminKeyFails <= 0) {
			$ipbanObj->set_tableKey("ipban_id");
			$ipbanObj->addNew(array("ipaddress"), array($IP_ADDRESS));
			
			
			$banMessage = "You have been permanently banned!  If you are the true website admin, you will be able to unban yourself.  If not... GTFO!";	
			echo "
			<div id='acoBan' style='display: none'><p align='center'>".$banMessage."</p></div>
			<script type='text/javascript'>
				$(document).ready(function() {
				
					$('#acoBan').dialog({
						title: 'Banned!',
						modal: true,
						resizable: false,
						width: 400,
						zIndex: 9999,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}	
						},
						beforeClose: function() {
							window.location = '".$MAIN_ROOT."banned.php';
						}
					
					});
					$('.ui-dialog :button').blur();
				});
			</script>
			
			";
			
			
		}
		
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You entered an invalid admin key.  Please check the config file for the correct admin key.  You have ".$adminKeyFails." more trys before being IP Banned. ".$IP_ADDRESS."<br>";
		
		
	
	}
	
	if($_FILES['consolefile'] == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must upload a file for the console option.<br>";		
	}
	
	if($countErrors == 0) {
		// No Errors Try uploading Console File
		
		$newFileName = strtolower(str_replace(" ","",$_POST['pagetitle']))."_";
		
		$btUpload = new BTUpload($_FILES['consolefile'], $newFileName, "include/customconsole/", array(".php"));
		
		if($btUpload->uploadFile()) {
			
			$consoleFileURL = "customconsole/".$btUpload->getUploadedFileName();
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload console option.  Please make sure the filesize is not too big and filetype is php.<br>";	
		}
		
		
	}
	
	if($_POST['hideoption'] != 1) {
		$_POST['hideoption'] = 0;
	}
	
	
	if($countErrors == 0) {
		// Still no errors after Uploading ---> Add to DB
		
		$arrColumns = array("consolecategory_id", "pagetitle", "filename", "sortnum", "hide");
		$arrValues = array($_POST['consolecat'], $_POST['pagetitle'], $consoleFileURL, $intNewSortNum, $_POST['hideoption']);
		
		
		if($consoleObj->addNew($arrColumns, $arrValues)) {
			
			// Added new Console Option, now add permissions
			$consolePrivObj = new Basic($mysqli, "rank_privileges", "privilege_id");
			
			$newConsoleInfo = $consoleObj->get_info_filtered();
			$arrColumns = array("rank_id", "console_id");
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1'");
			while($row = $result->fetch_assoc()) {
				$checkBoxName = "rankaccess_".$row['rank_id'];
				
				if($_POST[$checkBoxName] == 1) {
					$arrValues = array($row['rank_id'], $newConsoleInfo['console_id']);
					$consolePrivObj->addNew($arrColumns, $arrValues);
				}
			}
			
			
			$memberConsoleObj = new Basic($mysqli, "console_members", "privilege_id");
			$arrColumns = array("member_id", "console_id", "allowdeny");
			foreach($_SESSION['btAccessRules'] as $memAccessInfo) {
				if($memAccessInfo['accessRule'] == "allow") {
					$intAllowDeny = 1;
				}
				else {
					$intAllowDeny = 0;	
				}
				
				if($member->select($memAccessInfo['mID'])) {
					$memberConsoleObj->addNew($arrColumns, array($memAccessInfo['mID'], $newConsoleInfo['console_id'], $intAllowDeny));
				}
				
			}
			
			
			$consolePrivObj->addNew(array("rank_id", "console_id"), array("1", $newConsoleInfo['console_id']));
			
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Added New Console Option: <b>".$newConsoleInfo['pagetitle']."</b>!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Add New Console Option', '".$MAIN_ROOT."members/console.php', 'successBox');
			</script>
			";
			
		}
		
		
	}
	else {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}

}

if(!$_POST['submit']) {
	$_SESSION['btAccessRules'] = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory WHERE adminoption != '1' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$consoleCatOptions .= "<option value='".$row['consolecategory_id']."'>".filterText($row['name'])."</option>";		
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory ORDER BY ordernum DESC");
	$counter = 1;
	while($row = $result->fetch_assoc()) {	
		$arrRankCats[] = $row['rankcategory_id'];
	}
	
	foreach($arrRankCats as $rankCat) {
		$rankCatObj->select($rankCat);
		$arrRanks = $rankCatObj->getAssociateIDs();
		if(count($arrRanks) > 0) {
			$sqlRanks = "('".implode("','", $arrRanks)."')";
			$rankOptions .= "<span style='font-weight: bold; text-decoration: underline'>".$rankCatObj->get_info_filtered("name")."</span> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('ranksection_".$rankCat."', 1)\">Check All</a> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('ranksection_".$rankCat."', 0)\">Uncheck All</a><br>";
			$rankOptions .= "<div id='ranksection_".$rankCat."'>";
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' AND rank_id IN ".$sqlRanks." ORDER BY ordernum DESC");
			while($row = $result->fetch_assoc()) {
				$rankOptions .= "<input type='checkbox' name='rankaccess_".$row['rank_id']."' value='1' class='textBox'> ".filterText($row['name'])."<br>";
				$counter++;
			}
			$rankOptions .= "</div><br>";
		}
		
	}
	
	
	
	$rankOptionsHeight = $counter*20;
	
	if($rankOptionsHeight > 300) { $rankOptionsHeight = 300; }
	
	
	$memberOptions = "<option value='select'>[SELECT]</option>";
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.ordernum FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id != '1' AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."members.disabled = '0' ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$memberRank->select($row['rank_id']);
		$dispRankName = $memberRank->get_info_filtered("name");
		$memberOptions .= "<option value='".$row['member_id']."'>".$dispRankName." ".filterText($row['username'])."</option>";
		
	}
	
	$manageRanksCID = $consoleObj->findConsoleIDByName("Manage Ranks");
	echo "
		<form action='console.php?cID=$cID' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
			
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new console option because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
				Fill out the form below to add a console option.<br><br>
				<table class='formTable'>
					<tr>
						<td colspan='2' class='main'>
							<b>General Information</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Page Title:</td>
						<td class='main'><input type='text' name='pagetitle' class='textBox' value='".$_POST['pagetitle']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>File:</td>
						<td class='main'>
							<input type='file' name='consolefile' class='textBox' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>File Type: .php | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Console Category:</td>
						<td class='main'><select name='consolecat' id='consolecat' class='textBox' onchange='refreshConsoleOptions()'>".$consoleCatOptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:  <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the order that the console option will be displayed in the console menu.  You can add/remove separator\'s on the Manage Console Options Page.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<select name='consolebeforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='consoleorder' class='textBox' id='consoleorder'></select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Hide:</td>
						<td class='main'><input type='checkbox' name='hideoption' class='textBox' value='1'></td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Security</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
							<div style='padding-left: 3px; padding-bottom: 15px'>
								For security purposes, please enter the admin key that is set in the config file.
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Admin Key:  <a href='javascript:void(0)' onmouseover=\"showToolTip('For extra security, please enter the admin key that is set in the config file.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><input type='password' class='textBox' style='width: 100px' name='checkadmin'></td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Rank Access</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
							<div style='padding-left: 3px; padding-bottom: 15px'>
								Use this section to set which ranks are allowed to use this console option.
							</div>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2'>
						<div class='main' style='margin-left: 15px; overflow-y: auto; width: 90%; height: ".$rankOptionsHeight."px'>
							".$rankOptions."
						</div>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Member Access</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
							<div style='padding-left: 3px; padding-bottom: 15px'>
								Use this section to set whether a specific member can or cannot use this console option.  Clicking the Allow or Deny buttons will not change the page.
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Member:</td>
						<td class='main'><select id='acoMemberList' class='textBox'>".$memberOptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel'>Access:</td>
						<td class='main'><input type='button' class='submitButton' value='Allow' onclick=\"addMemberAccess('allow')\"> <input type='button' class='submitButton' value='Deny' onclick=\"addMemberAccess('deny')\"></td>
					</tr>
					<tr>
						<td class='main' colspan='2'><br><br>
							<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>
							<div id='acoMemberAccess'>
								
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
					<tr>
						<td class='main' align='center' colspan='2'><br><br><br>
							<input type='submit' name='submit' value='Add Console Option' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		<script type='text/javascript'>
			function refreshConsoleOptions() {
			
				$(document).ready(function() {
				
					intCID = $('#consolecat').val();
					
					$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/consolelist.php', { catID: intCID }, function(data) {
						
						$('#consoleorder').html(data);

					});
				});
			}
			
			function addMemberAccess(strAccess) {
			
				$(document).ready(function() {
					var intMemberID = $('#acoMemberList').val();
					$('#loadingSpiral').show();
					$('#acoMemberAccess').hide();
					
					$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/cache/add.php', { mID: intMemberID, accessrule: strAccess }, function(data) {
					
						$('#loadingSpiral').hide();
						$('#acoMemberAccess').html(data);				
						$('#acoMemberAccess').fadeIn(400);
						$('#acoMemberList').val('[SELECT]');
					});
				});
			}
			
			
			function deleteAccessRule(intKey) {
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#acoMemberAccess').hide();
					$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/cache/delete.php', { kID: intKey }, function(data) {
					
						$('#loadingSpiral').hide();
						$('#acoMemberAccess').html(data);				
						$('#acoMemberAccess').fadeIn(400);
						
					});
				
				});
			}
			
			refreshConsoleOptions();
		</script>
	";
}

?>