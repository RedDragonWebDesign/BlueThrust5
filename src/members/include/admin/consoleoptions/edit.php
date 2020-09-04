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

if(!$consoleObj->select($_GET['cnID'])) {
	echo "
	<script type='text/javascript'>
		window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
	</script>
	";
	exit();
}



$failbanObj = new Basic($mysqli, "failban", "failban_id");
$intMaxAttempts = 3;

$consoleInfo = $consoleObj->get_info_filtered();

echo "

	<script type='text/javascript'>
		$(document).ready(function() {
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Console Options</a> > ".$consoleInfo['pagetitle']."\");
		});
	</script>
	
";



if($_POST['submit']) {
	
	
	$countErrors = 0;
	
	// Check Page Title
	
	if($consoleInfo['defaultconsole'] == 1 || $consoleInfo['sep'] == 1) { 
		$_POST['pagetitle'] = $consoleInfo['pagetitle']; 
		$_FILES['consolefile'] = "";
	}
	
	
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

		$blnOrderCheck1 = $_POST['consoleorder'] == "first" && count($arrConsoleIDs) > 1 && $consoleInfo['consolecategory_id'] == $_POST['consolecat'];
		$blnOrderCheck2 = !in_array($_POST['consoleorder'], $arrConsoleIDs) && $_POST['consoleorder'] != "first";
		$blnOrderCheck3 = !$consoleObj->select($_POST['consoleorder']) && $_POST['consoleorder'] != "first";
		$blnOrderCheck4 = $_POST['consoleorder'] == "first" && $_POST['consolecat'] != $consoleInfo['consolecategory_id'] && count($arrConsoleIDs) > 0;
		
		
		
		
		if($blnOrderCheck1 || $blnOrderCheck2 || $blnOrderCheck3 || $blnOrderCheck4) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		}
		else {
			// Check Before/After Then Make Room
			
			if($_POST['consolebeforeafter'] == "before" OR $_POST['consolebeforeafter'] == "after") {
				
				
				$consoleOrderInfo = $consoleObj->get_info();
				
				
				$intNewSortNum = $consoleObj->makeRoom($_POST['consolebeforeafter']);
				
				
				
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order. (before/after)<br>";
			}			
		}
			
			
	}
	
	// Check Security Code
	
	if($ADMIN_KEY != $_POST['checkadmin']) {
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."failban WHERE ipaddress = '".$IP_ADDRESS."' AND pagename = 'editconsoleoption'");
		$countFails = $result->num_rows;
		$adminKeyFails = $intMaxAttempts-$countFails;
		
		$failbanObj->addNew(array("ipaddress", "pagename"), array($IP_ADDRESS, "editconsoleoption"));
		
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

	$consoleFileURL = "";
	if($countErrors == 0 && $_FILES['consolefile']['name'] != "") {
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
		// Still no errors after Uploading ---> Update DB
		$consoleObj->select($_GET['cnID']);
		$arrColumns = array("consolecategory_id", "pagetitle", "sortnum", "hide");
		$arrValues = array($_POST['consolecat'], $_POST['pagetitle'], $intNewSortNum, $_POST['hideoption']);
		
		
		if($consoleFileURL != "") {
			$arrColumns[] = "filename";
			$arrValues[] = $consoleFileURL;
		}
		
		
		if($consoleObj->update($arrColumns, $arrValues)) {
			$consoleObj->resortOrder();
			$newConsoleInfo = $consoleObj->get_info_filtered();
			// Added new Console Option, now add permissions
			$consolePrivObj = new Basic($mysqli, "rank_privileges", "privilege_id");
			$mysqli->query("DELETE FROM ".$dbprefix."rank_privileges WHERE console_id = '".$newConsoleInfo['console_id']."'");
			$mysqli->query("OPTIMIZE TABLE ".$dbprefix."rank_privileges");
			$mysqli->query("DELETE FROM ".$dbprefix."console_members WHERE console_id = '".$newConsoleInfo['console_id']."'");
			$mysqli->query("OPTIMIZE TABLE ".$dbprefix."console_members");
			
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
			$consoleObj->resortOrder();
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Console Option: <b>".$newConsoleInfo['pagetitle']."</b>!
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Edit Console Option', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
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
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."console_members WHERE console_id = '".$consoleInfo['console_id']."'");
	while($row = $result->fetch_assoc()) {
		
		if($row['allowdeny'] == 0) {
			$strAllowDeny = "deny";	
		}
		else {
			$strAllowDeny = "allow";	
		}
		
		$_SESSION['btAccessRules'][] = array(
					'mID' => $row['member_id'],
					'accessRule' => $strAllowDeny
				);
	}
	
	
	$consoleCatObj->select($consoleInfo['consolecategory_id']);
	$arrConsoles = $consoleCatObj->getAssociateIDs("ORDER BY sortnum");
	$highestIndex = count($arrConsoles)-1;
	
	$afterSelected = "";
	if($arrConsoles[$highestIndex] == $consoleInfo['console_id']) {
		$afterSelected = "selected";	
	}
	
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$dispSelected = "";
		if($consoleInfo['consolecategory_id'] == $row['consolecategory_id']) {
			$dispSelected = "selected";	
		}
		
		$consoleCatOptions .= "<option value='".$row['consolecategory_id']."' ".$dispSelected.">".filterText($row['name'])."</option>";		
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
				
				$dispChecked = "";
				if($consoleObj->hasAccess($row['rank_id'])) {
					$dispChecked = "checked";
				}
				
				$rankOptions .= "<input type='checkbox' name='rankaccess_".$row['rank_id']."' value='1' class='textBox' ".$dispChecked."> ".filterText($row['name'])."<br>";
				$counter++;
			}
			$rankOptions .= "</div><br>";
		}
		
	}
	
	
	
	$rankOptionsHeight = $counter*20;
	
	if($rankOptionsHeight > 300) { $rankOptionsHeight = 300; }
	
	
	$memberOptions = "<option value='select'>[SELECT]</option>";
	$result = $mysqli->query("SELECT ".$dbprefix."members.*, ".$dbprefix."ranks.ordernum FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id != '1' AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
	while($row = $result->fetch_assoc()) {
		
		$memberRank->select($row['rank_id']);
		$dispRankName = $memberRank->get_info_filtered("name");
		$memberOptions .= "<option value='".$row['member_id']."'>".$dispRankName." ".filterText($row['username'])."</option>";
		
	}
	
	$manageRanksCID = $consoleObj->findConsoleIDByName("Manage Ranks");
	echo "
		<form action='console.php?cID=".$cID."&cnID=".$_GET['cnID']."&action=edit' method='post' enctype='multipart/form-data'>
			<div class='formDiv'>
			
		";
	
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit console option because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	if($consoleInfo['defaultconsole'] == 0 AND $consoleInfo['sep'] == 0) {
		$dispPageTitle = "<input type='text' name='pagetitle' class='textBox' value='".$consoleInfo['pagetitle']."' style='width: 250px'>";
		$dispPageTitleHelp = "";
		$dispFileHelp = "";
		$dispFileOpen = "<input type='file' name='consolefile' class='textBox' style='width: 250px; border: 0px'><br>
							<span style='font-size: 10px'>File Type: .php | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>";
		
	}
	elseif($consoleInfo['sep'] == 1) {
		$dispPageTitle = "<b>".$consoleInfo['pagetitle']."</b>";
		$dispPageTitleHelp = "";
		
		$dispFileOpen = "<span style='font-style: italic; font-weight: bold'>N/A</span>";
		$dispFileHelp = "";
		
	}
	else {
		$dispPageTitle = "<b>".$consoleInfo['pagetitle']."</b>";
		$dispPageTitleHelp = " <a href='javascript:void(0)' onmouseover=\"showToolTip('You cannot change the name of default console options through the admin section.')\" onmouseout='hideToolTip()'>(?)</a>";
	
		$dispFileOpen = "<span style='font-style: italic; font-weight: bold'>Saved</span>";
		$dispFileHelp = "<a href='javascript:void(0)' onmouseover=\"showToolTip('You cannot change the file of default console options through the admin section.')\" onmouseout='hideToolTip()'>(?)</a>";
	}
	
	$dispCheckedHide = "";
	if($consoleInfo['hide'] == 1) {
		$dispCheckedHide = " checked";	
	}
	echo "
				Fill out the form below to add a console option.<br><br>
				<span style='font-weight: bold; text-decoration: underline'>NOTE:</span> You do not have to re-upload the console file if you don't want to change it. 
				<table class='formTable'>
					<tr>
						<td colspan='2' class='main'>
							<b>General Information</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Page Title: ".$dispPageTitleHelp."</td>
						<td class='main'>".$dispPageTitle."</td>
					</tr>
					<tr>
						<td class='formLabel'>File: ".$dispFileHelp."</td>
						<td class='main'>
							".$dispFileOpen."
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Console Category:</td>
						<td class='main'><select name='consolecat' id='consolecat' class='textBox' onchange='refreshConsoleOptions()'>".$consoleCatOptions."</select></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:  <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the order that the console option will be displayed in the console menu.  You can add/remove separator\'s on the Manage Console Options Page.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<select name='consolebeforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
							<select name='consoleorder' class='textBox' id='consoleorder'></select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Hide:</td>
						<td class='main'><input type='checkbox' value='1' name='hideoption' class='textBox'".$dispCheckedHide."></td>
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
							<input type='submit' name='submit' value='Edit Console Option' class='submitButton' style='width: 150px'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		<script type='text/javascript'>
			$(document).ready(function() {
			
			
				$('#loadingSpiral').show();
				$('#acoMemberAccess').hide();
					
			
				$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/cache/view.php', { }, function(data) {
				
					$('#loadingSpiral').hide();
					$('#acoMemberAccess').html(data);				
					$('#acoMemberAccess').fadeIn(400);
				
				});
			
			
			});
		
		
		
			function refreshConsoleOptions() {
			
				$(document).ready(function() {
				
					intCID = $('#consolecat').val();
					intCNID = '".$_GET['cnID']."';
					
					$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/consolelist.php', { catID: intCID, cnID: intCNID }, function(data) {
						
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