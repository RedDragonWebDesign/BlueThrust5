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

include_once($prevFolder."classes/game.php");
include_once($prevFolder."classes/tournament.php");

$cID = $_GET['cID'];
$dispError = "";
$countErrors = 0;

$tournamentObj = new Tournament($mysqli);
$gameObj = new Game($mysqli);

$arrTimezones = DateTimeZone::listIdentifiers();

if($_POST['submit']) {
	
	// Check tournament name
	
	if(trim($_POST['tournamentname']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a tourament name.<br>";
	}
	
	// Check Date
	
	if(is_numeric($_POST['startdate'])) {
		$_POST['startdate'] = $_POST['startdate']/1000;	
	}
	else {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid start date.<br>";
	}
	
	// Check Start Time
	
	$blnHourCheck = !is_numeric($_POST['starthour']) || $_POST['starthour'] < 1 || $_POST['starthour'] > 12;
	$blnMinuteCheck = !is_numeric($_POST['startminute']) || $_POST['startminute'] < 0 || $_POST['startminute'] > 59;
	$blnAMPMCheck = $_POST['startampm'] != "AM" && $_POST['startampm'] != "PM";
	
	if($blnHourCheck || $blnMinuteCheck || $blnAMPMCheck) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid start time.<br>";
	}
	
	
	// Format Date
	$formattedDate = "";
	if($countErrors == 0) {
		$tempHour = $_POST['starthour'];
		if($_POST['startampm'] == "AM" && $_POST['starthour'] == 12) {
			$tempHour = 0;
		}
		elseif($_POST['startampm'] == "PM" && $_POST['starthour'] != 12) {
			$tempHour += 12;
		}
		
		$tempTimezone = date_default_timezone_get();
		date_default_timezone_set("UTC");
		$tempDate = $_POST['startdate'];
		$tempYear = date("Y", $tempDate);
		$tempMonth = date("n", $tempDate);
		$tempDay = date("j", $tempDate);
		
		$formattedDate = mktime($tempHour, $_POST['startminute'], 0, $tempMonth, $tempDay, $tempYear);
		date_default_timezone_set($tempTimezone);
		
	}
	
	
	
	// Check Game
	$arrGamesPlayed = $gameObj->getGameList();
	if(!in_array($_POST['game'], $arrGamesPlayed)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid game.<br>";
	}
	
	
	/*	Check Seed Type
	 * 
	 * 	1 - Manual
	 * 	2 - Random
	 * 	3 - Pools
	 * 
	 */
	
	$arrSeedTypes = array(1,2,3);
	if(!in_array($_POST['seedtype'], $arrSeedTypes)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid seed type.<br>";
	}
	
	// Check Eliminations

	if($_POST['eliminations'] != 1 && $_POST['eliminations'] != 2) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid eliminations value.<br>";
	}
	
	// Check Teams/Players
	
	$arrTeamCount = array(4, 8, 16, 32, 64);
	if(!in_array($_POST['totalteams'], $arrTeamCount)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid Max Teams/Players value.<br>";
	}
	
	// Check Players per team
	
	if(!is_numeric($_POST['playersperteam']) || $_POST['playersperteam'] < 1 || $_POST['playersperteam'] > 16) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid Players Per Team value.<br>";
	}
	
	/* 
	 * Check Access
	 * 	
	 * 	1 - Clan Only
	 * 	2 - Multi-Clan
	 * 	3 - Everyone
	 * 
	 * Using the seed types array because they are same values as Access values 1,2 or 3
	 */
	
	if(!in_array($_POST['tournamentaccess'], $arrSeedTypes)) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid Access value.<br>";
	}
	
	
	// Check Password
	
	if($_POST['tournamentpassword'] != "" && $_POST['tournamentpassword'] != $_POST['tournamentpassword1']) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Your passwords did not match.<br>";
	}
	
	
	if($countErrors == 0) {
		$arrColumns = array("member_id", "gamesplayed_id", "name", "seedtype", "startdate", "eliminations", "playersperteam", "maxteams", "description", "password", "requirereplay", "access", "timezone");
		$arrValues = array($memberInfo['member_id'], $_POST['game'], $_POST['tournamentname'], $_POST['seedtype'], $formattedDate, $_POST['eliminations'], $_POST['playersperteam'], $_POST['totalteams'], $_POST['extrainfo'], md5($_POST['tournamentpassword']), $_POST['requirereplay'], $_POST['tournamentaccess'], $_POST['startimezone']);
		
		if($tournamentObj->addNew($arrColumns, $arrValues)) {
			
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Created New Tournament!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Create a Tournament', '".$MAIN_ROOT."members', 'successBox');
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
	}
	
}


if(!$_POST['submit']) {
	
	$gameoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
	
		$gameoptions .= "<option value='".$row['gamesplayed_id']."'>".filterText($row['name'])."</option>";
	
	}
	
	if($result->num_rows == 0) {
		$gameoptions = "<option value='0'>No Games Added!</option>";
	}
	
	$strSeedExplaination = "<span style=\'text-decoration:underline; font-weight: bold\'>Manual:</span> Seeds go in numeric order as you add players to the tournament.<br><br><span style=\'text-decoration:underline; font-weight: bold\'>Random:</span> Seeds are randomly set to players as you add them to the tournament.<br><br><span style=\'text-decoration:underline; font-weight: bold\'>Pools:</span> Teams/Players are separated into groups before the main tournament starts.  Each team/player plays one another in their group.  Seeds are determined by the win/loss record within that group.<br><br>With each seed option, you will have the ability to change the first round matches.  The matches will be set up with the top seed facing the lowest seed, second top seed facing the second lowest seed, and so on.";
	
	echo "
		
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to create tournament because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	foreach($arrTimezones as $timeZone) {
		
		$tz = new DateTimeZone($timeZone);
		$dispOffset = ((($tz->getOffset(new DateTime("now", $tz)))/60)/60);
		$dispSign = ($dispOffset < 0) ? "" : "+";
		$timezoneoptions .= "<option value='".$timeZone."'>".str_replace("_", " ", $timeZone)." (UTC".$dispSign.$dispOffset.")</option>";
	}
	
	
	echo "
				Use the form below to create a tournament.
				<table class='formTable'>
					<tr>
						<td colspan='2' class='main'>
							<b>General Information</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Tournament Name:</td>
						<td class='main'><input type='text' class='textBox' name='tournamentname' value='".$_POST['tournamentname']."' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Start Date:</td>
						<td class='main'><input type='text' class='textBox' id='startdate' readonly='readonly'></td>
					</tr>
					<tr>
						<td class='formLabel'>Start Time:</td>
						<td class='main'>
							<select name='starthour' class='textBox'><option value='12'>12</option>";
								for($i=1;$i<=11;$i++) { echo "<option value='".$i."'>".$i."</option>"; }
	echo "
							</select>
							<select name='startminute' class='textBox'>"; 
								for($i=0;$i<=59;$i++) { if($i<10) { $dispI = "0".$i; } else { $dispI = $i; } echo "<option value='".$dispI."'>".$dispI."</option>"; }
	echo "
							</select>
							<select name='startampm' class='textBox'>
								<option value='AM'>AM</option><option value='PM'>PM</option>
							</select>
							<select name='startimezone' class='textBox'>
								<option value=''>[Use Default]</option>
								".$timezoneoptions."
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Game:</td>
						<td class='main'>
							<select name='game' class='textBox'>".$gameoptions."</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Require Replay:</td>
						<td class='main'><input type='checkbox' class='textBox' value='1' style='border: 0px' name='requirereplay'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Extra Info:</td>
						<td class='main'>
							<textarea class='textBox' rows='5' cols='35' name='extrainfo'>".$_POST['extrainfo']."</textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Tournament Structure</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Seed Type: <a href='javascript(0)' onmouseover=\"showToolTip('".$strSeedExplaination."')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'>
							<select name='seedtype' class='textBox'>
								<option value='1'>Manual</option><option value='2'>Random</option><option value='3'>Pools</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Eliminations:</td>
						<td class='main'>
							<select class='textBox' name='eliminations'>
								<option value='1'>Single Elimination</option>
								<!-- <option value='2'>Double Elimination</option> -->
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Max Teams/Players:</td>
						<td class='main'>
							<select name='totalteams' class='textBox'>
								<option value='4'>4</option>
								<option value='8'>8</option>
								<option value='16'>16</option>
								<option value='32'>32</option>
								<option value='64'>64</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Players Per Team:</td>
						<td class='main'>
							<select name='playersperteam' class='textBox'>
								"; for($i=1;$i<=16;$i++) { echo "<option value='".$i."'>".$i."</option>"; }
					echo "
							</select>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Tournament Access</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
							<div style='padding-left: 3px; padding-right: 45px; padding-bottom: 15px'><span style='font-weight: bold; text-decoration: underline'>NOTE:</span> If you choose to create a multi-clan tournament, you must supply a password in order to auto-update brackets across clan websites.  Otherwise, a password is optional.</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Access:</td>
						<td class='main'>
							<select class='textBox' name='tournamentaccess'>
								<option value='1'>Clan Only</option>
								<option value='2'>Multi-Clan</option>
								<option value='3'>Everyone</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Password:</td>
						<td class='main'><input type='password' name='tournamentpassword' id='tournamentpassword' class='textBox' style='width: 125px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Re-type Password:</td>
						<td class='main'><input type='password' name='tournamentpassword1' id='tournamentpassword1' class='textBox' style='width: 125px'><span id='checkPassword' style='padding-left: 5px'></span></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br><br>
							<input type='submit' name='submit' value='Create Tournament' class='submitButton' style='width: 150px'>
						</td>
					</tr>					
				</table>
			
			</div>
			<input type='hidden' name='startdate' id='realstartdate'>
		</form>
		
		
		<script type='text/javascript'>
			
			$(document).ready(function() {
			
				$('#startdate').datepicker({
				
				";
					$dispMonth = date("n")-1;
					echo "
					changeMonth: true,
					changeYear: true,
					dateFormat: 'M d, yy',
					minDate: new Date(".date("Y").", ".$dispMonth.", ".date("j")."),
					altField: '#realstartdate',
					altFormat: '@'
				
				});
				
				
				$('#tournamentpassword1').keyup(function() {
					
					if($('#tournamentpassword').val() != \"\") {
					
						if($('#tournamentpassword1').val() == $('#tournamentpassword').val()) {
							$('#checkPassword').toggleClass('successFont', true);
							$('#checkPassword').toggleClass('failedFont', false);
							$('#checkPassword').html('ok!');
						}
						else {
							$('#checkPassword').toggleClass('successFont', false);
							$('#checkPassword').toggleClass('failedFont', true);
							$('#checkPassword').html('error!');
						}
					
					}
					else {
						$('#checkPassword').html('');
					}
				
				});
			
			});
		
		</script>
	";
	
}


?>