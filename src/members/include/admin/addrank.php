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
include_once($prevFolder."classes/rankcategory.php");
$cID = $_GET['cID'];


if(isset($_POST['submit']) && $_POST['submit']) {
	$countErrors = 0;
	
	
	// Check Rank Name
	$checkRankName = trim($_POST['rankname']);
	if($checkRankName == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank rank name.<br>";
	}
	
	// Check Rank Category
	$rankCatObj = new RankCategory($mysqli);
	if(!$rankCatObj->select($_POST['rankcat'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank category.<br>";
	}
	
	// Check Image Height
	
	if(!is_numeric($_POST['rankimageheight']) AND trim($_POST['rankimageheight']) != "") {
		$countErrors++;
		$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a numeric value.<br>";
	}
	elseif($_POST['rankimageheight'] <= 0 AND is_numeric($_POST['rankimageheight'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a value greater than 0.<br>";
	}
	
	if($_FILES['rankimagefile']['name'] == "" AND (trim($_POST['rankimageheight']) == "" OR $_POST['rankimageheight'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must supply an image height for images that aren't uploaded.<br>";
	}
	
	// Check Image Width
	
	if(!is_numeric($_POST['rankimagewidth']) AND trim($_POST['rankimagewidth']) != "") {
		$countErrors++;
		$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a numeric value.<br>";
	}
	elseif($_POST['rankimagewidth'] <= 0 AND is_numeric($_POST['rankimagewidth'])) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a value greater than 0.<br>";
	}
	
	if($_FILES['rankimagefile']['name'] == "" AND (trim($_POST['rankimagewidth']) == "" OR $_POST['rankimagewidth'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must supply an image width for images that aren't uploaded.<br>";
	}
	
	// Check Before/After and Rank
	
	$beforeAfterRankOK = false;
	$rankObj = new Rank($mysqli);
	
	if($_POST['rankorder'] != "first") {
		if(!$rankObj->select($_POST['rankorder'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order. (rank)<br>";
		}
		else {
			$beforeAfterRankInfo = $rankObj->get_info();
			$beforeAfterRankOK = true;
			
			// Check to see if we can get a new rank order number
			
			$intNewRankOrderNum = $rankObj->makeRoom($_POST['beforeafter']);
			
			if(!is_numeric($intNewRankOrderNum)) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order. (rank)<br>";
			}
			
		}
	
	}
	else {
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1'");
		$num_rows = $result->num_rows;
		
		if($num_rows != 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order.<br>";
		}
		else {
			$intNewRankOrderNum = 1;
		}
		
		
	}
	
	if($_POST['beforeafter'] != "after" AND $_POST['beforeafter'] != "before") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid rank order. (before/after)<br>";
	}
	
	

	
	
	// Check Max Rank to Promote
	
	if($_POST['promoterank'] != "0") {
		
		if($_POST['promoterank'] != "-1") {
			if(!$rankObj->select($_POST['promoterank'])) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid maximum promotion rank.<br>";
			}
			else {
				$promoteRankInfo = $rankObj->get_info();
				if($promoteRankInfo['ordernum'] > $intNewRankOrderNum) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You cannot allow a rank to promote higher than its rank order.<br>";
				}
			}
		}
		
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
	
	
	// If everything is ok, try uploading the image
	if($countErrors == 0) {
		// Check Rank Image File
		if($_FILES['rankimagefile']['name'] != "") {
			$uploadFile = new BTUpload($_FILES['rankimagefile'], "rank_", "../images/ranks/", array(".jpg",".png",".gif",".bmp"));
			
			if(!$uploadFile->uploadFile()) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload rank image file.  Please make sure the file extension is either .jpg, .png, .gif or .bmp<br>";
			}
			else {
				$rankImgURL = "images/ranks/".$uploadFile->getUploadedFileName();
			}

		}
		else {
			
			if(trim($_POST['rankimageurl']) == "") {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must include a rank image.<br>";
			}
			else {
				$rankImgURL = $_POST['rankimageurl'];
			}
		}
	}
	
	if($countErrors > 0) {
		
		$_POST = filterArray($_POST);
		
		$_POST['submit'] = false;
	}
	else {
		// EVERYTHING IS OK
		$newRank = new Rank($mysqli);
		$arrColumns = array("rankcategory_id", "name", "description", "imageurl", "ordernum", "autodays", "hiderank", "promotepower", "autodisable", "color", "imagewidth", "imageheight");
		$arrValues = array($_POST['rankcat'], $_POST['rankname'], $_POST['rankdesc'], $rankImgURL, $intNewRankOrderNum, ((isset($_POST['autodays'])) ? $_POST['autodays'] : 0), ((isset($_POST['hiderank'])) ? $_POST['hiderank'] : 0), ((isset($_POST['promoterank'])) ? $_POST['promoterank'] : 0), ((isset($_POST['autodisable'])) ? $_POST['autodisable'] : 0), $_POST['rankcolor'], $_POST['rankimagewidth'], $_POST['rankimageheight']);
		
		if($newRank->addNew($arrColumns, $arrValues)) {
			// Added Rank! Now give the rank its privileges
			
			$newRankInfo = $newRank->get_info_filtered();
			
			// If maximum rank is set to "(this rank)", set the promotepower to the new rank's rank_id
			if($_POST['promoterank'] == -1) {
				$newRank->update(array("promotepower"), array($newRankInfo['rank_id']));
			}
			
			$arrColumns = array("rank_id", "console_id");
			
			$privObj = new Basic($mysqli, "rank_privileges", "privilege_id");
			
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."console ORDER BY sortnum");
			$rankOptions = "";
			while($row = $result->fetch_assoc()) {
				
				$strPostVarName = "consoleid_".$row['console_id'];
				
				if(isset($_POST[$strPostVarName]) && $_POST[$strPostVarName] == 1) {
					$arrValues = array($newRankInfo['rank_id'], $row['console_id']);
					$privObj->addNew($arrColumns, $arrValues);
				}
			}
		
		$manageRanksCID = $consoleObj->findConsoleIDByName("Manage Ranks");
		echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Rank: <b>".$newRankInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add New Rank', '".$MAIN_ROOT."members/console.php?cID=".$manageRanksCID."', 'successBox');
			</script>
		";
		
		}
		else {
			$_POST['submit'] = false;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to add new rank.  Please try again.<br>";
		}
		
	}
}



if(!isset($_POST['submit']) || !$_POST['submit']) {
	$rankCategories = $mysqli->query("SELECT * FROM ".$dbprefix."rankcategory ORDER BY ordernum");
	
    $rankCatOptions = "";
	while($arrRankCat = $rankCategories->fetch_assoc()) {
		$rankCatName = filterText($arrRankCat['name']);
		$rankCatOptions .= "<option value='".$arrRankCat['rankcategory_id']."'>".$arrRankCat['name']."</option>";
	
	}
	
	$getRanks = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE rank_id != '1' ORDER BY ordernum");
	$rankOptions = "";
	while($arrRanks = $getRanks->fetch_assoc()) {
		$rankName = filterText($arrRanks['name']);
		$rankOptions .= "<option value='".$arrRanks['rank_id']."'>".$rankName."</option>";
	
	}
	
	$firstRankOption = "";
	if($rankOptions == "") {
		$firstRankOption = "<option value='1'>(first rank)</option>";
	}
	
	
	echo "
	
	<form action='console.php?cID=$cID' method='post' enctype='multipart/form-data'>
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
	
	<script type='text/javascript'>
		$(document).ready(function() {
			$('#rankcolor').miniColors({
				change: function(hex, rgb) { }
			});
		});
	</script>
		Fill out the form below to add a new ranking.<br><br>
		<b><u>NOTE:</u></b> When adding a Rank Image, if both the File and URL are filled out, the File will be used.
		
		
		<table class='formTable'>
			<tr>
				<td colspan='2' class='main'>
					<b>General Information</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
				</td>
			</tr>
			<tr>
				<td class='formLabel'>Rank Name:</td>
				<td class='main'><input type='text' name='rankname' value='".((isset($_POST['rankname'])) ? $_POST['rankname'] : "")."' class='textBox' style='width: 250px'></td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Rank Image:</td>
				<td class='main'>
					File:<br><input type='file' name='rankimagefile' class='textBox' style='width: 250px; border: 0px'><br>
					<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
					<p><b><i>OR</i></b></p>
					URL:<br><input type='text' name='rankimageurl' value='".((isset($_POST['rankimageurl'])) ? $_POST['rankimageurl'] : "")."' class='textBox' style='width: 250px'>
				</td>
			</tr>
			<tr>
				<td class='formLabel'>Image Width: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Width to the width that you would like the Rank Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
				<td class='main'>
					<input type='text' name='rankimagewidth' value='".((isset($_POST['rankimagewidth'])) ? $_POST['rankimagewidth'] : "")."' class='textBox' style='width: 40px'> <i>px</i>
				</td>
			</tr>
			<tr>
				<td class='formLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Rank Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
				<td class='main'>
					<input type='text' name='rankimageheight' value='".((isset($_POST['rankimageheight'])) ? $_POST['rankimageheight'] : "")."' class='textBox' style='width: 40px'> <i>px</i>
				</td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Description:</td>
				<td class='main'><textarea class='textBox' name='rankdesc' rows='5' cols='40'>".((isset($_POST['rankdesc'])) ? $_POST['rankdesc'] : "")."</textarea></td>
			</tr>
			<tr>
				<td class='formLabel'>Rank Category:</td>
				<td class='main'><select name='rankcat' class='textBox'>$rankCatOptions</select></td>
			</tr>
			<tr>
				<td class='formLabel' valign='top'>Rank Order:</td>
				<td class='main'><select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br><select name='rankorder' class='textBox'>$firstRankOption.$rankOptions</select></td>
			</tr>
			<tr>
				<td class='formLabel'>Color:</td>
				<td class='main'><input type='text' id='rankcolor' name='rankcolor' value='".((isset($_POST['rankcolor'])) ? $_POST['rankcolor'] : "")."' class='textBox' style='width: 70px'></td>
			</tr>
			<tr>
				<td class='formLabel'>Hide Rank:</td>
				<td class='main'><input type='checkbox' name='hiderank' value='1' class='textBox' onmouseover=\"showToolTip('If you hide a rank, it will also hide members of this rank.')\" onmouseout='hideToolTip()'></td>
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
				<td class='main'><select name='promoterank' class='textBox'><option value='0'>(Can't Promote)</option><option value='-1'>(this rank)</option>$rankOptions</select></td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br><br>
				<div style='padding-left: 3px; padding-right: 35px; padding-bottom: 15px'>Set the auto-days option if you want a member to be automatically promoted to this rank after a certain number of days in the clan.  Leave blank or 0 to disable this option.</div>
				</td>
			</tr>
			<tr>
				<td class='formLabel'><div style='padding-left: 3px'>Auto-Days:</div></td>
				<td class='main'><input type='text' class='textBox' name='autodays' value='".((isset($_POST['autodays'])) ? $_POST['autodays'] : "")."' style='width: 40px'></td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br><br>
				<div style='padding-left: 3px; padding-right: 35px; padding-bottom: 15px'>The auto-disable option allows you to create ranks for trial members.  Set the number of days you want a member to be this rank before being auto-disabled.  Leave blank or 0 to disable this option.</div>
				</td>
			</tr>
			<tr>
				<td class='formLabel'><div style='padding-left: 3px'>Auto-Disable:</div></td>
				<td class='main'><input type='text' class='textBox' name='autodisable' value='".((isset($_POST['autodisable'])) ? $_POST['autodisable'] : "")."' style='width: 40px'></td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br>
					<b>Console Options</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					<p align='center'>
						<div class='main' style='margin-left: 15px; overflow-y: auto; width: 75%; height: 300px'>
							";
							
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
							$consoleCatObj = new Basic($mysqli, "consolecategory", "consolecategory_id");
                            $consoleJSCode = "";
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
											
											echo "&nbsp;&nbsp;<input type='checkbox' name='consoleid_".$consoleOptionInfo['console_id']."' value='1'> ".$consoleOptionInfo['pagetitle']."<br>";
										
										}
										elseif($consoleOptionInfo['pagetitle'] == "-separator-") {
											$dispSeparator = "<div class='dashedLine' style='width: 250px; margin: 6px 1px; padding: 0px; float: left'></div>";
											echo "<div style='float: left'>&nbsp;&nbsp;<input type='checkbox' name='consoleid_".$consoleOptionInfo['console_id']."' value='1'>&nbsp;</div>".$dispSeparator;
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
					<input type='submit' name='submit' value='Add Rank' class='submitButton'>
				</td>
			</tr>
			
		</table>
		</div>
	
	</form>
	
	
	
	";

}



?>