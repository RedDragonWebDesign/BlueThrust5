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


if(!$gameObj->select($_GET['gID'])) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
		</script>
	";
	exit();
}


$gameInfo = $gameObj->get_info_filtered();


echo "

<script type='text/javascript'>
	$(document).ready(function() {
		$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Games Played</a> > ".$gameInfo['name']."\");
	});
</script>
";



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
	
	if($_FILES['gameimagefile']['name'] == "" AND trim($_POST['gameimageurl']) == "" AND $gameObj->getLocalImageURL() === false AND (trim($_POST['gameimageheight']) == "" OR $_POST['gameimageheight'] <= 0)) {
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
	
	
	if($_FILES['gameimagefile']['name'] == "" AND trim($_POST['gameimageurl']) == "" AND $gameObj->getLocalImageURL() === false AND (trim($_POST['gameimagewidth']) == "" OR $_POST['gameimagewidth'] <= 0)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must supply an image width for images that aren't uploaded.<br>";
	}
	
	
	
	
	// Check Display Order
	
	$intGameOrderNum = $gameObj->validateOrder($_POST['gameorder'], $_POST['beforeafter'], true, $gameObj->get_info("ordernum"));
	
	
	if($intGameOrderNum === false) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid category order.<br>";
	}
	
	
	
	if($countErrors == 0) {
		// No Errors! Check game image, if it needs to be uploaded, try uploading.
		
		$gameImageURL = "";
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
	
	}
	
	// Check if there are still no errors after uploading image
	
	if($countErrors == 0) {
		$gameObj->select($gameInfo['gamesplayed_id']);
		
		// update column names
		$updateColumns = array("name", "imagewidth", "imageheight", "ordernum");
		
		// update column values
		$updateValues = array($_POST['gamename'], $_POST['gameimagewidth'], $_POST['gameimageheight'], $intGameOrderNum);
		
		// Check if need to update image
		if($gameImageURL != "") {
			$updateColumns[] = "imageurl";
			$updateValues[] = $gameImageURL;
		}
		
		if($gameObj->update($updateColumns, $updateValues)) {
			
			// Resort order if needed
			if($resortOrder) {
				$gameObj->resortOrder();	
			}
			
			// Updated Game Info ---> now update game stats
			$gameInfo = $gameObj->get_info_filtered(); // Make sure we have the most up to date game info.
			
			$updateGameStatCol = array("name", "stattype", "calcop", "decimalspots", "ordernum", "hidestat", "gamesplayed_id", "textinput"); //, 
			
			// First update/add name and stat type
			foreach($_SESSION['btStatCache'] as $key => $statInfo) {
				
				
				$updateGameStatsVal = array($statInfo['statName'], $statInfo['statType'], $statInfo['calcOperation'], $statInfo['rounding'], $key, $statInfo['hideStat'], $gameInfo['gamesplayed_id'], $statInfo['textInput']);
				
				
				if($statInfo['gamestatsID'] != "" AND $gameStatsObj->select($statInfo['gamestatsID'])) {
				// Updating already added stats
					$checkSave = $gameStatsObj->update($updateGameStatCol, $updateGameStatsVal);
				}
				else {
				// Adding new stats
					$checkSave = $gameStatsObj->addNew($updateGameStatCol, $updateGameStatsVal);
				}
				
				
				
				if($checkSave) {
					$arrSavedStats[] = $gameStatsObj->get_info_filtered();
				}
				else {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> ".filterText($statInfo['statName'])."<br>";
				}
				
			}
			
			// Now update other stat information
			$updateGameStatCol = array("firststat_id", "secondstat_id");
			
			/*
			 * 	1. Make sure that all of the game stats were successfully inserted into the db
			*   2. For each stat that was an auto-calculated stat, we need to update the firststat and secondstat IDs
			*   3. We can identify the correct $arrSavedStat index by accessing the stat order which is stored in
			*     $_SESSION[btStatCache][key][firstStat] and $_SESSION[btStatCache][key][secondStat]
			*/
			
			
			if($countErrors == 0) {
				foreach($arrSavedStats as $key => $statInfo) {
					if($statInfo['stattype'] == "calculate") {
				
						$intFirstStatOrder = $_SESSION['btStatCache'][$key]['firstStat'];
						$intFirstStatID = $arrSavedStats[$intFirstStatOrder]['gamestats_id'];
				
						$intSecondStatOrder = $_SESSION['btStatCache'][$key]['secondStat'];
						$intSecondStatID = $arrSavedStats[$intSecondStatOrder]['gamestats_id'];
				
						$arrValues = array($intFirstStatID, $intSecondStatID);
				
						$gameStatsObj->select($statInfo['gamestats_id']);
						$gameStatsObj->update($updateGameStatCol, $arrValues);
					}
				}
			}
			else {
				$showErrorMessage = "<br><br>However, the following stats were unable to be saved:<br><br>".$dispError;
			}
			
			
			//$newGameInfo = $gameObj->get_info_filtered();

			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Game: <b>".$gameInfo['name']."</b>!".$showErrorMessage."
			</p>
			</div>
			
			<script type='text/javascript'>
			popupDialog('Manage Games Played', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			";
			
			
	
		}
		else {
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save game information.  Please try again.<br>";
			$_POST['submit'] = false;
		}
		
	}
	else {
		$_POST['submit'] = false;	
	}

}


if(!$_POST['submit']) {


	$localImageURL = $gameObj->getLocalImageURL();
	
	if($gameInfo['imagewidth'] == 0 AND $localImageURL !== false) {
		$gameImageSize = getimagesize($prevFolder.$localImageURL);
		
		$gameInfo['imagewidth'] = $gameImageSize[0];
	}
	
	if($gameInfo['imageheight'] == 0 AND $localImageURL !== false) {
		$gameImageSize = getimagesize($prevFolder.$localImageURL);
	
		$gameInfo['imageheight'] = $gameImageSize[1];
	}
	
	$popupWidth = $gameInfo['imagewidth']+50;
	echo "
	<script type='text/javascript'>
	
	
	function showGameImage() {
		
		$(document).ready(function() {
			$('#popupGameImage').dialog({
				title: 'View Game Image',
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
	
	echo "
	<div style='display: none' id='popupGameImage'><p align='center'><img src='".$gameInfo['imageurl']."' width='".$gameInfo['imagewidth']."' height='".$gameInfo['imageheight']."'></div>
	<form action='console.php?cID=".$cID."&gID=".$_GET['gID']."&action=edit' method='post' enctype='multipart/form-data'>
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
	
	
	$afterSelected = "";
	$intGameBeforeAfter = "";
	
	$intNextGameOrder = $gameInfo['ordernum']-1;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed WHERE ordernum = '".$intNextGameOrder."'");
	if($result->num_rows == 1) {
		$beforeGameInfo = $result->fetch_assoc();
		$intGameBeforeAfter = $beforeGameInfo['gamesplayed_id'];
	}
	else {
		// Editing First Game Need to select "After" option
		$intGameAfter = $gameInfo['ordernum']+1;
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed WHERE ordernum = '".$intGameAfter."'");
		if($result->num_rows == 1) {
			$afterGameInfo = $result->fetch_assoc();
			$intGameBeforeAfter = $afterGameInfo['gamesplayed_id'];
			$afterSelected = " selected";
		}
	}
	$counter = 0;
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed WHERE gamesplayed_id != '".$gameInfo['gamesplayed_id']."' ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$counter++;
		$gameName = filterText($row['name']);
		if($row['gamesplayed_id'] == $intGameBeforeAfter) {
			$gameOrderOptions .= "<option value='".$row['gamesplayed_id']."' selected>".$gameName."</option>";
		}
		else {
			$gameOrderOptions .= "<option value='".$row['gamesplayed_id']."'>".$gameName."</option>";
		}
		
	}
	
	if($counter == 0) {
		$gameOrderOptions = "<option value='first'>(no other games)</option>";
	}
	
	
	// Set btStatCache
	$_SESSION['btStatCache'] = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamestats WHERE gamesplayed_id = '".$gameInfo['gamesplayed_id']."' ORDER BY ordernum");
	while($row = $result->fetch_assoc()) {
		$row = filterArray($row);
		
		$intFirstStatID = "";
		$intSecondStatID = "";
		
		if($row['stattype'] == "calculate") {
			$gameStatsObj->select($row['firststat_id']);
			$intFirstStatID = $gameStatsObj->get_info_filtered("ordernum");
			
			$gameStatsObj->select($row['secondstat_id']);
			$intSecondStatID = $gameStatsObj->get_info_filtered("ordernum");
		}
		
		$_SESSION['btStatCache'][] = array(
					'statName' => $row['name'],
					'statType' => $row['stattype'],
					'calcOperation' => $row['calcop'],
					'firstStat' => $intFirstStatID,
					'secondStat' => $intSecondStatID,
					'rounding' => $row['decimalspots'],
					'hideStat' => $row['hidestat'],
					'textInput' => $row['textinput'],
					'gamestatsID' => $row['gamestats_id']
				
				);
		
	}
	
	
	
	echo "
		Use the form below to modify the selected game.<br><br>
		<b><u>NOTE:</u></b> When adding a Game Image, if both the File and URL are filled out, the File will be used.  If you don't want to change an already uploaded image, leave both the File and URL blank.
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
					<td class='main'><input type='text' name='gamename' value='".$gameInfo['name']."' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Game Image:</td>
					<td class='main'>
						<i>Current Image: <a href='javascript:void(0)' onclick='showGameImage()'>View Game Image</a></i><br>
						File:<br><input type='file' name='gameimagefile' class='textBox' style='width: 250px; border: 0px'><br>
						<span style='font-size: 10px'>File Types: .jpg, .gif, .png, .bmp | <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						<p><b><i>OR</i></b></p>
						URL:<br><input type='text' name='gameimageurl' value='".$_POST['gameimageurl']."' class='textBox' style='width: 250px'>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Image Width: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Width to the width that you would like the Game Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<input type='text' name='gameimagewidth' value='".$gameInfo['imagewidth']."' class='textBox' style='width: 40px'> <i>px</i>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Image Height: <a href='javascript:void(0)' onmouseover=\"showToolTip('Set the Image Height to the height that you would like the Game Image to be displayed on your website.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<input type='text' name='gameimageheight' value='".$gameInfo['imageheight']."' class='textBox' style='width: 40px'> <i>px</i>
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Display Order: <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the order that the game will be displayed on the side menu bar.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after' ".$afterSelected.">After</option></select><br>
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
						<input type='button' value='Add New Stat' onclick='showAddNewStat()' class='submitButton' style='width: 125px'>
						<div id='loadingSpiral' class='loadingSpiral'>
							<p align='center'>
								<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'><br>
						<div id='statList'></div>
					</td>
				<tr>
					<td class='main' align='center' colspan='2'><br><br><br>
						<input type='submit' name='submit' value='Edit Game' class='submitButton' style='width: 125px'>
					</td>
				</tr>
			</table>
		
		</div>
	</form>
		
		<div id='addNewStatForm' style='display: none'></div>
		
		
		
	<script type='text/javascript'>
		
		
		$(document).ready(function() {
		
			$('#loadingSpiral').show();
			$('#statList').hide();
			$.post('".$MAIN_ROOT."members/include/admin/statcache/view.php', { }, function(data) {
				$('#statList').html(data);
				$('#statList').fadeOut(400);
				$('#loadingSpiral').hide();
				$('#statList').fadeIn(400);
			});
		
		});
	
		
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
			
			$('#addNewStatForm').html(\"<p align='center'>Are you sure you want to permanently delete this stat?</p>\");
			$('#addNewStatForm').dialog({
				title: 'Manage Games Played',
				width: 400,
				modal: true,
				resizable: false,
				zIndex: 9999,
				show: 'scale',
				buttons: {
					'Yes': function() {
						$.post('".$MAIN_ROOT."members/include/admin/statcache/delete.php', { sID: intStatID }, function(data) {
			
							$('#addNewStatForm').html(data);
							$('#addNewStatForm').dialog('close');
			
						});
					
					},
					'Cancel': function() {
						$(this).dialog('close');
					}
				
				}
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