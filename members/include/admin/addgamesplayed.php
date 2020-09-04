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
include_once($prevFolder."classes/game.php");
$cID = $_GET['cID'];

$gameObj = new Game($mysqli);

if($_POST['submit']) {
	
	// Check Game Name
	$checkGameName = trim($_POST['gamename']);
	if($checkGameName == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not enter a blank game name.<br>";
	}
	
	
	// Check Image Height
	
	if(!is_numeric($_POST['gameimageheight']) AND trim($_POST['gameimageheight']) != "") {
		$countErrors++;
		$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a numeric value.<br>";
	}
	elseif(is_numeric($_POST['gameimageheight']) AND $_POST['gameimageheight'] <= 0) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Height must be a value greater than 0.<br>";
	}
	
	if($_FILES['gameimagefile']['name'] == "" AND (trim($_POST['gameimageheight']) == "" OR $_POST['gameimageheight'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must supply an image height for images that aren't uploaded.<br>";
	}
	
	// Check Image Width
	
	if(!is_numeric($_POST['gameimagewidth']) AND trim($_POST['gameimagewidth']) != "") {
		$countErrors++;
		$dispError .="&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a numeric value.<br>";
	}
	elseif(is_numeric($_POST['gameimagewidth']) AND $_POST['gameimagewidth'] <= 0) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The Image Width must be a value greater than 0.<br>";
	}
	
	
	if($_FILES['gameimagefile']['name'] == "" AND (trim($_POST['gameimagewidth']) == "" OR $_POST['gameimagewidth'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must supply an image width for images that aren't uploaded.<br>";
	}
	
	
	
	
	// Check Display Order
	
	if($_POST['beforeafter'] != "before" AND $_POST['beforeafter'] != "after") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected an invalid display order. (before/after)<br>";
	}
	elseif($_POST['gameorder'] == "first") {
		
		// Check if this is really the first game being added
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed ORDER BY ordernum DESC");
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected an invalid display order.<br>";	
		}
		else {
			$intGameOrderNum = 1;
		}
		
	}
	elseif($_POST['gameorder'] != "first") {
		
		// Check if its a real game selected
		if(!$gameObj->select($_POST['gameorder'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected an invalid display order. (game position)<br>";
		}
		else {
			// Game was selected make some room for the new game and get a new ordernum
			$intGameOrderNum = $gameObj->makeRoom($_POST['beforeafter']);
			
			if(!is_numeric($intGameOrderNum)) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The selected an invalid display order. (game position)<br>";
			}

		}
		
	}
	
	
	
	if($countErrors == 0) {
	// No Errors! Check game image, if it needs to be uploaded, try uploading.

		if($_FILES['gameimagefile']['name'] != "") {
			
			$btUploadObj = new BTUpload($_FILES['gameimagefile'], "game_", "../images/gamesplayed/", array(".jpg", ".png", ".bmp", ".gif"));
					
			if(!$btUploadObj->uploadFile()) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload games image file.  Please make sure the file extension is either .jpg, .png, .gif or .bmp<br>";				
			}
			else {
				$gameImageURL = "images/gamesplayed/".$btUploadObj->getUploadedFileName();
			}
			
		}
		elseif(trim($_POST['gameimageurl']) != "") {
			$gameImageURL = $_POST['gameimageurl'];
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must include an image for the game.<br>";	
		}
		
	}
	
	
	
	if($countErrors == 0) {
	// No errors after adding the image. Add game to database.
	
		$newGame = new Basic($mysqli, "gamesplayed", "gamesplayed_id");
		
		$arrColumns = array("name", "imageurl", "imagewidth", "imageheight", "ordernum");
		$arrValues = array($_POST['gamename'], $gameImageURL, $_POST['gameimagewidth'], $_POST['gameimageheight'], $intGameOrderNum);
		
		
		if($newGame->addNew($arrColumns, $arrValues)) {
			$newGameInfo = $newGame->get_info_filtered();
			
			// Try adding stats
			$showErrorMessage = "";
			$newStat = new Basic($mysqli, "gamestats", "gamestats_id");
			$arrColumns = array("name", "stattype", "ordernum", "decimalspots", "gamesplayed_id", "hidestat", "textinput");
			$arrSavedStats = array();
			
			// First insert all stats so we can get their actual database ids
			// After we add them, save the info array to a separate array
			foreach($_SESSION['btStatCache'] as $key => $statInfo) {
				
				$arrValues = array($statInfo['statName'], $statInfo['statType'], $key, $statInfo['rounding'], $newGameInfo['gamesplayed_id'], $statInfo['hideStat'], $statInfo['textInput']);
				
				if(!$newStat->addNew($arrColumns, $arrValues)) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> ".filterText($statInfo['statName'])."<br>";
				}
				else {
					$arrSavedStats[] = $newStat->get_info_filtered();
				}
			}
			
			/*
			 * 	1. Make sure that all of the game stats were successfully inserted into the db
			 *  2. For each stat that was an auto-calculated stat, we need to update the firststat and secondstat IDs
			 *  3. We can identify the correct $arrSavedStat index by accessing the stat order which is stored in
			 *     $_SESSION[btStatCache][key][firstStat] and $_SESSION[btStatCache][key][secondStat]
			 */
			
			if($countErrors == 0) {
				$arrColumns = array("firststat_id", "secondstat_id", "calcop");
				foreach($arrSavedStats as $key => $statInfo) {
					if($statInfo['stattype'] == "calculate") {

						$intFirstStatOrder = $_SESSION['btStatCache'][$key]['firstStat'];
						$intFirstStatID = $arrSavedStats[$intFirstStatOrder]['gamestats_id'];
						
						$intSecondStatOrder = $_SESSION['btStatCache'][$key]['secondStat'];
						$intSecondStatID = $arrSavedStats[$intSecondStatOrder]['gamestats_id'];
						
						$calcOp = $_SESSION['btStatCache'][$key]['calcOperation'];
						
						$arrValues = array($intFirstStatID, $intSecondStatID, $calcOp);
						
						$newStat->select($statInfo['gamestats_id']);
						$newStat->update($arrColumns, $arrValues);
					}
				}
				
				
			}
			else {
				$showErrorMessage = "<br><br>However, the following stats were unable to be saved:<br><br>".$dispError;	
			}
			
			
			
			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Added New Game: <b>".$newGameInfo['name']."</b>!".$showErrorMessage."
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Add New Game', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
			
		}
		else {
			
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to add new game.  Please try again.<br>";
			$_POST['submit'] = false;
			
		}
		
	}
	else {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
	
}


if(!$_POST['submit']) {
	$_SESSION['btStatCache'] = array();
	echo "
	<form action='console.php?cID=$cID' method='post' enctype='multipart/form-data'>
		<div class='formDiv'>
	";
	

	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new game because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}

	
	// Get games already added
	$counter = 0;
	$gameOrderOptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$counter++;
		$dispName = filterText($row['name']);
		$gameOrderOptions .= "<option value='".$row['gamesplayed_id']."'>".$dispName."</option>";
		
	}
	
	if($counter == 0) {
		$gameOrderOptions = "<option value='first'>(first game)</option>";	
	}
	
	echo "
			Fill out the form below to add a game.<br><br>
			<span style='text-decoration: underline; font-weight: bold'>NOTE:</span> When adding a Game Image, if both the File and URL are filled out, the File will be used.
			<br><br>
			<table class='formTable'>
				<tr>
					<td colspan='2' class='main'>
						<b>General Information</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Game Name:</td>
					<td class='main'><input type='text' name='gamename' value='".$_POST['gamename']."' class='textBox' style='width: 250px'></td>	
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Game Image:</td>
					<td class='main'>
						File:<br><input type='file' name='gameimagefile' class='textBox' style='width: 250px; border: 0px'><br>
						<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						<p><b><i>OR</i></b></p>
						URL:<br><input type='text' name='gameimageurl' value='".$_POST['gameimageurl']."' class='textBox' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Image Width: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Width to the width that you would like the Game Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<input type='text' name='gameimagewidth' value='".$_POST['gameimagewidth']."' class='textBox' style='width: 40px'> <i>px</i>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Game Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<input type='text' name='gameimageheight' value='".$_POST['gameimageheight']."' class='textBox' style='width: 40px'> <i>px</i>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Display Order: <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the order that the game will be displayed on the side menu bar.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
						<select name='gameorder' class='textBox'>".$gameOrderOptions."</select>
					</td>	
				</tr>
				<tr>
					<td colspan='2' class='main'><br>
						<b>Game Statistics Information</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
						<div style='padding-left: 3px; padding-bottom: 15px'>
							In this section you can add new game statistics for the game you are adding.  You can add auto-calculated stats after adding two input stats.  The highest stat will be the default stat used to rank members on the top players page.
						</div>
					</td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'>
						<input type='button' value='Add New Stat' onclick='showAddNewStat()' class='submitButton'>
						<div id='loadingSpiral' class='loadingSpiral'>
							<p align='center'>
								<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
							</p>
						</div>
						
					</td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'><br>
						<div id='statList'><i>No Stats Added Yet!</i></div>
					</td>
				<tr>
					<td class='main' align='center' colspan='2'><br><br><br>
						<input type='submit' name='submit' value='Add New Game' class='submitButton'>
					</td>
				</tr>
			</table>
			
		</div>
	</form>
	
	<div id='addNewStatForm' style='display: none'></div>
	
	
	
	<script type='text/javascript'>
		function showAddNewStat() {
		
			$(document).ready(function() {
				
				$('#loadingSpiral').show();
				$.post('".$MAIN_ROOT."members/include/admin/statcache/add.php', { }, function(data) {
					$('#addNewStatForm').html(data);
					
					$('#addNewStatForm').dialog({
						title: 'Add New Stat',
						modal: true,
						resizable: false,
						width: 450,
						show: 'scale',
						zIndex: 99999,
						buttons: {
							'Add Stat': function() {
								
								var strStatName = $('#gpStatName').val();
								var strStatType = $('#gpStatType').val();
								var intCalcOp = $('#gpCalcOp').val();
								var intFirstStatID = $('#gpFirstStatID').val();
								var intSecondStatID = $('#gpSecondStatID').val();
								var intRounding;
								
								if(strStatType == \"inputnum\") {
									intRounding = $('#gpRoundingInputNumeric').val();
								}
								else {
									intRounding = $('#gpRounding').val();
								}
								
								var intHideStat = 0;
								if($('#gpHideStat').is(':checked')) {
									intHideStat = 1;
								}	
								
								$.post('".$MAIN_ROOT."members/include/admin/statcache/add.php', { submit: 1, statName: strStatName, statType: strStatType, calcOperation: intCalcOp, firstStat: intFirstStatID, secondStat: intSecondStatID, rounding: intRounding, hideStat: intHideStat }, function(data1) {
									$('#addNewStatForm').html(data1);
								});
							},
							'Cancel': function() {
								$(this).dialog('close');							
							}
						}
					});
					$('#loadingSpiral').hide();
				});
			
			});
		
		}
		
		
		function deleteStat(intStatID) {
			$.post('".$MAIN_ROOT."members/include/admin/statcache/delete.php', { sID: intStatID }, function(data) {
			
				$('#addNewStatForm').html(data);
			
			});
		
		}
		
		function moveStat(strDirection, intStatID) {
			$.post('".$MAIN_ROOT."members/include/admin/statcache/move.php', { statDir: strDirection, sID: intStatID }, function(data) {
				$('#addNewStatForm').html(data);
			});
		}
		
		function editStat(intStatID) {
			$.post('".$MAIN_ROOT."members/include/admin/statcache/edit.php', { sID: intStatID }, function(data) {
			
				$('#addNewStatForm').html(data);
				
				$('#addNewStatForm').dialog('destroy');
				
				$('#addNewStatForm').dialog({
					title: 'Edit Stat',
					modal: true,
					resizable: false,
					width: 450,
					show: 'scale',
					zIndex: 99999,
					buttons: {
						'Save': function() {
						
							var strStatName = $('#gpStatName').val();
							var strStatType = $('#gpStatType').val();
							var intCalcOp = $('#gpCalcOp').val();
							var intFirstStatID = $('#gpFirstStatID').val();
							var intSecondStatID = $('#gpSecondStatID').val();
							var intRounding;
								
							if(strStatType == \"inputnum\") {
								intRounding = $('#gpRoundingInputNumeric').val();
							}
							else {
								intRounding = $('#gpRounding').val();
							}
							
							var intHideStat = 0;
							if($('#gpHideStat').is(':checked')) {
								intHideStat = 1;
							}							
							
							$.post('".$MAIN_ROOT."members/include/admin/statcache/edit.php', { submit: 1, sID: intStatID, statName: strStatName, statType: strStatType, calcOperation: intCalcOp, firstStat: intFirstStatID, secondStat: intSecondStatID, rounding: intRounding, hideStat: intHideStat }, function(data1) {
								$('#addNewStatForm').html(data1);
							});
						
						
						},
						'Cancel': function() {
							$(this).dialog('close');
						}
					
					}
				});
				
				
			
			});
		}
		
	</script>
	";


}


?>