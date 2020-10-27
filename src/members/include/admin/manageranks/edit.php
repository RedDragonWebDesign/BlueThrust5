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

include_once($prevFolder."_setup.php");

// Classes needed for console.php
include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/rankcategory.php");
include_once($prevFolder."classes/consoleoption.php");

$cOptObj = new ConsoleOption($mysqli);
$cID = $cOptObj->findConsoleIDByName("Manage Ranks");
$cOptObj->select($cID);

$member = new Member($mysqli);

$checkMember = $member->select($_SESSION['btUsername']);

if($checkMember) {

	if($member->authorizeLogin($_SESSION['btPassword'])) {

		$memberInfo = $member->get_info();
		
		if($member->hasAccess($cOptObj)) {
			
			
			
			$rank = new Rank($mysqli);
			if($rank->select($_GET['rID'])) {
				$rankInfo = $rank->get_info_filtered();
				echo "
				
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Ranks</a> > ".$rankInfo['name']."\");
					});
				</script>
				";
				
				
				if(isset($_POST['submit']) && $_POST['submit']) {
					
					$countErrors = 0;
					
					// Check Rank Name
					$checkRankName = trim($_POST['rankname']);
					if($checkRankName == "") {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank rank name.<br>";
					}
					
					// Check Rank Category
					$rankCatObj = new Basic($mysqli, "rankcategory", "rankcategory_id");
					if(!$rankCatObj->select($_POST['rankcat'])) {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank category.<br>";
					}
					
					// Check Image Height
					
					if(!is_numeric($_POST['rankimageheight']) OR trim($_POST['rankimageheight']) == "") {
						$countErrors++;
						$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a numeric value.<br>";
					}
					else {
						if($_POST['rankimageheight'] <= 0) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a value greater than 0.<br>";
						}
					}
					
					// Check Image Width
					
					if(!is_numeric($_POST['rankimagewidth']) OR trim($_POST['rankimagewidth']) == "") {
						$countErrors++;
						$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a numeric value.<br>";
					}
					else {
						if($_POST['rankimagewidth'] <= 0) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a value greater than 0.<br>";
						}
					}
					
					
					// Check Rank Category
					$rankCatObj = new Basic($mysqli, "rankcategory", "rankcategory_id");
					if(!$rankCatObj->select($_POST['rankcat'])) {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank category.<br>";
					}
					
					// Check Auto Days
					
					if($_POST['autodays'] != "") {
						if(!is_numeric($_POST['autodays']) OR (is_numeric($_POST['autodays']) AND $_POST['autodays'] < 0)) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Auto days must either be blank or a positive numeric value.<br>";
						}
					}
					
					
					if($_POST['autodisable'] != "") {
						if(!is_numeric($_POST['autodisable']) OR (is_numeric($_POST['autodisable']) AND $_POST['autodisable'] < 0)) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Auto disable must either be blank or a positive numeric value.<br>";
						}
					}
					
					// Check Rank Order and Promote Power
					
					$checkRankObj = new Rank($mysqli);
					
					if($_POST['rankorder'] == $rankInfo['rank_id']) {
						// Hack attempt
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order. (possible hack attempt?)<br>";
					}
					
					
					
					//Check if rank selected for rank order is an actual rank
					if($checkRankObj->select($_POST['rankorder'])) {
						$checkRankInfo = $checkRankObj->get_info();
						if($_POST['beforeafter'] == "before") {			
							$intTempRankOrder = $checkRankInfo['ordernum']+1;
						}
						else {
							$intTempRankOrder = $checkRankInfo['ordernum']-1;
						}
						
						
						// If the rank order is the same do nothing keep it the same
						// If its not the same make room for the new order and then resort ordernum
						if($intTempRankOrder == $rankInfo['ordernum']) {
							$intNewRankOrderNum = $rankInfo['ordernum'];
							$resortRanks = false;
						}
						else {
							$intNewRankOrderNum = $checkRankObj->makeRoom($_POST['beforeafter']);
							$resortRanks = true;
						}
						
						if(!is_numeric($intNewRankOrderNum)) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order. (rank)<br>";
						}
					}
					else {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order.<br>";
					}
					
					
					
					// Check rank promote power
					
					if($checkRankObj->select($_POST['promoterank'])) {
						
						$checkRankInfo = $checkRankObj->get_info();
						
						if($checkRankInfo['ordernum'] > $intNewRankOrderNum) {
							$countErrors++;
							$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You cannot set a rank to promote higher than its rank order.<br>";
						}
						
					}
					elseif($_POST['promoterank'] == "none") {
						$_POST['promoterank'] = 0;
					}
					else {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid maximum promotion rank.<br>";
					}
					
					
					
					// No Errors, check if there is a new image then save
					if($countErrors == 0) {
					//	$updateRankImage = false;
						$arrUpdateValues = array($_POST['rankname'], $_POST['rankcat'], $_POST['rankdesc'], $_POST['rankimagewidth'], $_POST['rankimageheight'], $intNewRankOrderNum, ((isset($_POST['autodays'])) ? $_POST['autodays'] : 0), ((isset($_POST['hiderank'])) ? $_POST['hiderank'] : 0), ((isset($_POST['promoterank'])) ? $_POST['promoterank'] : 0), ((isset($_POST['autodisable'])) ? $_POST['autodisable'] : 0), $_POST['rankcolor']);
						$arrUpdateColumns = array("name", "rankcategory_id", "description", "imagewidth", "imageheight", "ordernum", "autodays", "hiderank", "promotepower", "autodisable", "color");
						
						// Check for new rank image
						if($_FILES['rankimagefile']['name'] != "") {
							$uploadFile = new BTUpload($_FILES['rankimagefile'], "rank_", "../images/ranks/", array(".jpg",".png",".gif",".bmp"));
				
							if(!$uploadFile->uploadFile()) {
								$countErrors++;
								$dispError .= "<b>&middot;</b> Unable to upload rank image file.  Please make sure the file extension is either .jpg, .png, .gif or .bmp<br>";
							}
							else {
								$rankImgURL = "images/ranks/".$uploadFile->getUploadedFileName();
								$arrUpdateValues[] = $rankImgURL;
								$arrUpdateColumns[] = "imageurl";
							}
							
							
							
						}
						elseif($_POST['rankimageurl'] != "") {
							$arrUpdateValues[] = $_POST['rankimageurl'];
							$arrUpdateColumns[] = "imageurl";
						}
						
						
						if($countErrors == 0) {
							// No errors after checking/uploading new rank image
							
							$rank->select($_GET['rID']);
							$rank->update($arrUpdateColumns, $arrUpdateValues);
							
							if($resortRanks) {
								$rank->resortOrder();	
							}
							
							$rankInfo = $rank->get_info_filtered();
							
							
							// Update privileges
							
							$result = $mysqli->query("DELETE FROM ".$dbprefix."rank_privileges WHERE rank_id = '".$rankInfo['rank_id']."'");
							if($result) {
								$arrColumns = array("rank_id", "console_id");
								
								$privObj = new Basic($mysqli, "rank_privileges", "privilege_id");
								
								$result = $mysqli->query("SELECT * FROM ".$dbprefix."console ORDER BY sortnum");
								$rankOptions = "";
								while($row = $result->fetch_assoc()) {
								
									$strPostVarName = "consoleid_".$row['console_id'];
								
									if(isset($_POST[$strPostVarName]) && $_POST[$strPostVarName] == 1) {
										$arrValues = array($rankInfo['rank_id'], $row['console_id']);
										$privObj->addNew($arrColumns, $arrValues);
									}
								}

							}

							
							
							
							echo "
							<div style='display: none' id='successBox'>
							<p align='center'>
							Successfully Edited Rank: <b>".$rankInfo['name']."</b>!
							</p>
							</div>
							
							<script type='text/javascript'>
							popupDialog('Manage Ranks', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
							</script>
							";
							
						}
						else {
							$_POST['submit'] = false;
						}
						
					}
					else {
						$_POST['submit'] = false;	
					}
					
				}
				
				if(!isset($_POST['submit']) || !$_POST['submit']) {
				
					
					
					$localImageURL = $rank->getLocalImageURL();
					
					if($rankInfo['imagewidth'] == 0 AND $localImageURL !== false) {
						$rankImageSize = getimagesize($prevFolder.$localImageURL);
					
						$rankInfo['imagewidth'] = $rankImageSize[0];
					}
					
					if($rankInfo['imageheight'] == 0 AND $localImageURL !== false) {
						$rankImageSize = getimagesize($prevFolder.$localImageURL);
					
						$rankInfo['imageheight'] = $rankImageSize[1];
					}
					
					
					
					$popupWidth = $rankInfo['imagewidth']+50;
					echo "
					<script type='text/javascript'>
					
					
					function showRankImage() {
						
						$(document).ready(function() {
							$('#popupRankImage').dialog({
								title: 'View Rank Image',
								modal: true,
								zIndex: 99999,
								width: ".$popupWidth.",
								resizable: false,
								show: \"fade\",
								buttons: {
									\"Ok\": function() {
										$(this).dialog(\"close\");
									}
								}
							});
						});
						$('.ui-dialog :button').blur();
					}
					
					</script>
					";
					
					$result = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory ORDER BY ordernum");
                    $rankCatOptions = "";
					while($row = $result->fetch_assoc()) {
						$rankCatName = filterText($row['name']);
						if($rankInfo['rankcategory_id'] == $row['rankcategory_id']) {
							$rankCatOptions .= "<option value='".$row['rankcategory_id']."' selected>".$rankCatName."</option>";
						}
						else {
							$rankCatOptions .= "<option value='".$row['rankcategory_id']."'>".$rankCatName."</option>";
						}
					}
					
					$afterSelected = "";
					$intRankBeforeAfter = "";
	
					$intNextRankOrder = $rankInfo['ordernum']-1;
					$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum = '".$intNextRankOrder."' AND rank_id != '1'");
					if($result->num_rows == 1) {
						$beforeRankInfo = $result->fetch_assoc();
						$intRankBeforeAfter = $beforeRankInfo['rank_id'];
					}
					else {
						// Editing First Rank Need to select "After" option
						$intRankAfter = $rankInfo['ordernum']+1;
						$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum = '".$intRankAfter."' AND rank_id != '1'");
						if($result->num_rows == 1) {
							$afterRankInfo = $result->fetch_assoc();
							$intRankBeforeAfter = $afterRankInfo['rank_id'];
							$afterSelected = " selected";
						}
					}
					$counter = 0;
					$rankOrderOptions = "";
                    $promotePowerOptions = "";
					$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' AND rank_id != '".$rankInfo['rank_id']."' ORDER BY ordernum DESC");
					while($row = $result->fetch_assoc()) {
						$counter++;
						$rankName = filterText($row['name']);
						if($row['rank_id'] == $intRankBeforeAfter) {
							$rankOrderOptions .= "<option value='".$row['rank_id']."' selected>".$rankName."</option>";
						}
						else {
							$rankOrderOptions .= "<option value='".$row['rank_id']."'>".$rankName."</option>";
						}
						
					}
	
					if($counter == 0) {
						$rankOrderOptions = "<option value='first'>(no other ranks)</option>";
					}
					
					$checkHideRank = "";
					if($rankInfo['hiderank'] == 1) {
						$checkHideRank = " checked";
					}
					
					
					
					if($rankInfo['promotepower'] == 0) {
						$promotePowerOptions .= "<option value='none' selected>(Can't Promote)</option>";
					}
					else {
						$promotePowerOptions .= "<option value='none'>(Can't Promote)</option>";
					}
					
					$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' ORDER BY ordernum DESC");
					while($row = $result->fetch_assoc()) {
						if($rankInfo['promotepower'] == $row['rank_id']) {
							$promotePowerOptions .= "<option value='".$row['rank_id']."' selected>".filterText($row['name'])."</option>";
						}
						else {
							$promotePowerOptions .= "<option value='".$row['rank_id']."'>".filterText($row['name'])."</option>";
						}
					}
					
					
					
					echo "
						<script type='text/javascript'>
							$(document).ready(function() {
								$('#rankcolor').miniColors({
									change: function(hex, rgb) { }
								});
							});
						</script>
						<div style='display: none' id='popupRankImage'><p align='center'><img src='".$rankInfo['imageurl']."' width='".$rankInfo['imagewidth']."' height='".$rankInfo['imageheight']."'></div>
						
						<div class='formDiv'>
						
						
						";
						
					if(isset($dispError) && $dispError != "") {
						echo "
						<div class='errorDiv'>
						<strong>Unable to add new rank because the following errors occurred:</strong><br><br>
						$dispError
						</div>
						";
					}
						
					echo "
							Use the form below to modify the selected rank.<br><br>
							<b><u>NOTE:</u></b> When changing the Rank Image, if both the File and URL are filled out, the File will be used.  If you don't want to change an already uploaded image, leave both the File and URL blank.
						
							<form action='console.php?cID=".$cID."&rID=".$_GET['rID']."&action=edit' method='post' enctype='multipart/form-data'>
								<table class='formTable'>
									<tr>
										<td class='main' colspan='2'>
											<b>General Information</b>
											<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Rank Name:</td>
										<td class='main'><input type='text' name='rankname' value=\"".$rankInfo['name']."\" class='textBox' style='width: 250px'></td>
									</tr>
									<tr>
										<td class='formLabel' valign='top'>Rank Image:</td>
										<td class='main'>
											<i>Current Image: <a href='javascript:void(0)' onclick='showRankImage()'>View Rank Image</a></i><br>
											File:<br>
											<input type='file' name='rankimagefile' class='textBox' style='border: 0px; width: 250px'><br>
											<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
											<p><b><i>OR</i></b></p>
											URL:<br>
											<input type='text' name='rankimageurl' class='textBox' style='width: 250px'>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Image Width: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Width to the width that you would like the Rank Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
										<td class='main'>
											<input type='text' name='rankimagewidth' value='".$rankInfo['imagewidth']."' class='textBox' style='width: 40px'> <i>px</i>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Rank Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
										<td class='main'>
											<input type='text' name='rankimageheight' value='".$rankInfo['imageheight']."' class='textBox' style='width: 40px'> <i>px</i>
										</td>
									</tr>
									<tr>
										<td class='formLabel' valign='top'>Description:</td>
										<td class='main'>
											<textarea rows='5' cols='40' class='textBox' name='rankdesc'>".$rankInfo['description']."</textarea>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Rank Category:</td>
										<td class='main'>
											<select name='rankcat' class='textBox'>".$rankCatOptions."</select>
										</td>
									</tr>
									<tr>
										<td class='formLabel' valign='top'>Rank Order:</td>
										<td class='main'>
											<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$afterSelected.">After</option></select><br>
											<select name='rankorder' class='textBox'>".$rankOrderOptions."</select>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Rank Color:</td>
										<td class='main'>
											<input type='text' name='rankcolor' id='rankcolor' value='".$rankInfo['color']."' class='textBox' style='width: 100px'>
										</td>
									</tr>
									<tr>
										<td class='formLabel'>Hide Rank:</td>
										<td class='main'>
											<input type='checkbox' name='hiderank' class='textBox' value='1'".$checkHideRank." onmouseover=\"showToolTip('If you hide a rank, it will also hide members of this rank.')\" onmouseout='hideToolTip()'>
										</td>
									</tr>
									<tr>
										<td colspan='2' class='main'><br>
											<b>Promotion Options</b>
											<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
											<div style='padding-left: 3px; padding-bottom: 15px'>This option determines the maximum rank that a member can promote to, award/revoke medals, disable/undisable members.</div>
										</td>
									</tr>
									<tr>
										<td class='formLabel'><div style='padding-left: 3px'>Maximum Rank:</div></td>
										<td class='main'><select name='promoterank' class='textBox'>".$promotePowerOptions."</select></td>
									</tr>
									<tr>
										<td colspan='2' class='main'><br><br>
											<div style='padding-left: 3px; padding-right: 35px; padding-bottom: 15px'>Set the auto-days option if you want a member to be automatically promoted to this rank after a certain number of days in the clan.  Leave blank or 0 to disable this option.</div>
										</td>
									</tr>
									<tr>
										<td class='formLabel'><div style='padding-left: 3px'>Auto-Days:</div></td>
										<td class='main'><input type='text' class='textBox' name='autodays' value='".$rankInfo['autodays']."' style='width: 40px'></td>
									</tr>
									<tr>
										<td colspan='2' class='main'><br><br>
										<div style='padding-left: 3px; padding-right: 35px; padding-bottom: 15px'>The auto-disable option allows you to create ranks for trial members.  Set the number of days you want a member to be this rank before being auto-disabled.  Leave blank or 0 to disable this option.</div>
										</td>
									</tr>
									<tr>
										<td class='formLabel'><div style='padding-left: 3px'>Auto-Disable:</div></td>
										<td class='main'><input type='text' class='textBox' name='autodisable' value='".$rankInfo['autodisable']."' style='width: 40px'></td>
									</tr>
									<tr>
										<td colspan='2' class='main'><br>
											<b>Console Options</b>
											<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
											<p align='center'>
												<div class='main' style='margin-left: 15px; overflow-y: auto; width: 75%; height: 300px'>
													";
					
													$consoleObj = new ConsoleOption($mysqli);
													$consoleCategories = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory ORDER BY ordernum DESC");
													while($arrConsoleCats = $consoleCategories->fetch_assoc()) {
														$tempNum = $arrConsoleCats['consolecategory_id'];
														$arrFormatOptions[$tempNum] = array();
													}
													
													$consoleOptions = $mysqli->query("SELECT * FROM ".$dbprefix."console ORDER BY sortnum");
													$rankOptions = "";
													while($arrConsoleOptions = $consoleOptions->fetch_assoc()) {
														$tempCat = $arrConsoleOptions['consolecategory_id'];
														$arrFormatOptions[$tempCat][] = $arrConsoleOptions['console_id'];
													}
													
													$countConsoleCats = 0;
                                                    $consoleJSCode = "";
													$consoleCatObj = new Basic($mysqli, "consolecategory", "consolecategory_id");
													foreach($arrFormatOptions as $key=>$arrOptions) {
														$consoleCatObj->select($key);
														$consoleCatInfo = $consoleCatObj->get_info();
														
														if(count($arrOptions) > 0) {
															$countConsoleCats++;
															echo "<br>
																<u><b>".$consoleCatInfo['name']."</b></u> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('category".$countConsoleCats."', 1)\">Check All</a> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('category".$countConsoleCats."', 0)\">Uncheck All</a><br>
																<div id='category".$countConsoleCats."'>
															";
															
															foreach($arrOptions as $consoleOption) {
																$consoleObj->select($consoleOption);
																$consoleOptionInfo = $consoleObj->get_info();
																
																$consoleJSCode .= "arrConsoleIDs[".$consoleOptionInfo['console_id']."] = $('#consoleid_".$consoleOptionInfo['console_id']."').attr('checked'); 
									";
																
																if($consoleOptionInfo['pagetitle'] != "-separator-") {
																	
																	if($consoleObj->hasAccess($rankInfo['rank_id'])) {
																		$dispSelected = " checked";								
																	}
																	else {
																		$dispSelected = "";
																	}
																	
																	echo "&nbsp;&nbsp;<input type='checkbox' name='consoleid_".$consoleOptionInfo['console_id']."' value='1'".$dispSelected."> ".$consoleOptionInfo['pagetitle']."<br>";
																}
																elseif($consoleOptionInfo['pagetitle'] == "-separator-") {
																	
																	if($consoleObj->hasAccess($rankInfo['rank_id'])) {
																		$dispSelected = " checked";
																	}
																	else {
																		$dispSelected = "";
																	}
																	
																	$dispSeparator = "<div class='dashedLine' style='width: 250px; margin: 6px 1px; padding: 0px; float: left'></div>";
																	echo "<div style='float: left'>&nbsp;&nbsp;<input type='checkbox' name='consoleid_".$consoleOptionInfo['console_id']."' value='1'".$dispSelected.">&nbsp;</div>".$dispSeparator;
																	echo "<div style='clear: both'></div>";
																}
															
															}
															
															echo "</div>";
														
														}
													
													}
													
													echo "
												</div>
											</p>
										</td>
									</tr>
									<tr>
										<td colspan='2' align='center'><br>
											<input type='submit' name='submit' value='Edit Rank' class='submitButton'>
										</td>
									</tr>
							
								</table>
							</form>
						</div>
					";
				}
				
				
			}
			
			
		}
		else { echo "no"; }
		
	} else { echo "no1"; }
}
else { echo "no2"; }


?>