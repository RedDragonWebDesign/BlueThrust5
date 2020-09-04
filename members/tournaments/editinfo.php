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


if(!isset($member) || !isset($tournamentObj) || substr($_SERVER['PHP_SELF'], -strlen("managetournament.php")) != "managetournament.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$tournamentObj->select($tID);


	if(!$member->hasAccess($consoleObj)) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Edit Tournament Info\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Edit Tournament Info\");
});
</script>
";


$dispError = "";
$countErrors = 0;

include_once("../../classes/game.php");

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

	$arrSeedTypes = array(1,2,3);
	
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
		$arrColumns = array("gamesplayed_id", "name", "startdate", "eliminations", "playersperteam", "maxteams", "description", "requirereplay", "access", "timezone");
		$arrValues = array($_POST['game'], $_POST['tournamentname'], $formattedDate, $_POST['eliminations'], $_POST['playersperteam'], $_POST['totalteams'], $_POST['extrainfo'], $_POST['requirereplay'], $_POST['tournamentaccess'], $_POST['startimezone']);
		
		if($_POST['tournamentpassword'] != "") {
			$arrColumns[] = "password";
			$arrValues[] = md5($_POST['tournamentpassword']);			
		}
		elseif($_POST['tournamentpassword'] == "" && $_POST['removepass'] == 1) {
			$arrColumns[] = "password";
			$arrValues[] = "";
		}
		
		
		if($tournamentObj->update($arrColumns, $arrValues)) {

			echo "
			<div style='display: none' id='successBox'>
			<p align='center'>
			Successfully Edited Tournament Info!
			</p>
			</div>

			<script type='text/javascript'>
			popupDialog('Edit Tournament Info', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
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

		$selectGame = "";
		if($tournamentInfo['gamesplayed_id'] == $row['gamesplayed_id']) {
			$selectGame = " selected";	
		}
		
		$gameoptions .= "<option value='".$row['gamesplayed_id']."'".$selectGame.">".filterText($row['name'])."</option>";
		

	}

	if($result->num_rows == 0) {
		$gameoptions = "<option value='0'>No Games Added!</option>";
	}

		
	$dateTimeObj = new DateTime();
	$dateTimeObj->setTimestamp($tournamentInfo['startdate']);
	
	

	$dateTimeObj->setTimezone(new DateTimeZone("UTC"));
	$dispDate = $dateTimeObj->format("M j, Y");
	$dispHour = $dateTimeObj->format("g");
	$dispMinute = $dateTimeObj->format("i");
	$dispAMPM = $dateTimeObj->format("A");
	
	
	echo "

	<form action='".$MAIN_ROOT."members/tournaments/managetournament.php?tID=".$tID."&pID=EditTournamentInfo' method='post'>
	<div class='formDiv'>
	";

	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit tournament info because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}

	foreach($arrTimezones as $timeZone) {
		
		$tz = new DateTimeZone($timeZone);
		$dispOffset = ((($tz->getOffset(new DateTime("now", $tz)))/60)/60);
		$dispSign = ($dispOffset < 0) ? "" : "+";

		$dispSelected = ($tournamentInfo['timezone'] == $timeZone) ? " selected" : "";
		
		$timezoneoptions .= "<option value='".$timeZone."'".$dispSelected.">".str_replace("_", " ", $timeZone)." (UTC".$dispSign.$dispOffset.")</option>";
	
	}
	
	echo "
		Use the form below to edit tournament info.
			<table class='formTable'>
				<tr>
					<td colspan='2' class='main'>
						<b>General Information</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>Tournament Name:</td>
					<td class='main'><input type='text' class='textBox' name='tournamentname' value='".$tournamentInfo['name']."' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Start Date:</td>
					<td class='main'><input type='text' class='textBox' id='startdate' value='".$dispDate."' readonly='readonly'></td>
				</tr>
				<tr>
					<td class='formLabel'>Start Time:</td>
					<td class='main'>
						<select name='starthour' class='textBox'><option value='12'>12</option>
	";
	for($i=1;$i<=11;$i++) {
		
		$selectHour = "";
		if($i == $dispHour) {
			$selectHour = " selected";
		}
		
		echo "<option value='".$i."'".$selectHour.">".$i."</option>";
	}
	
	echo "
		</select>
			<select name='startminute' class='textBox'>
	";
	for($i=0;$i<=59;$i++) {
		if($i<10) {
			$dispI = "0".$i;
		} else { 
			$dispI = $i;
		} 
		
		$selectMinute = "";
		if($dispI == $dispMinute) {
			$selectMinute = " selected";
		}
		
		echo "<option value='".$dispI."'".$selectMinute.">".$dispI."</option>";
	}
	
	$selectPM = "";
	if($dispAMPM == "PM") {
		$selectPM = " selected";	
	}
	
	$checkRequireReplay = "";
	if($tournamentInfo['requirereplay'] == 1) {
		$checkRequireReplay = " checked";
	}
	
	echo "
	</select>
							<select name='startampm' class='textBox'>
								<option value='AM'>AM</option><option value='PM'".$selectPM.">PM</option>
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
						<td class='main'><input type='checkbox' class='textBox' value='1' style='border: 0px' name='requirereplay'".$checkRequireReplay."></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Extra Info:</td>
						<td class='main'>
							<textarea class='textBox' rows='5' cols='35' name='extrainfo'>".$tournamentInfo['description']."</textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Tournament Structure</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
							<div style='padding-left: 3px; padding-right: 45px; padding-bottom: 15px'><span style='font-weight: bold; text-decoration: underline'>NOTE:</span> If you change the amount of players/teams in the tournament, all matches will be reset.  You may also need to re-add some players or teams.</div>
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
							";
	
							$arrMaxTeams = array(4, 8, 16, 32, 64);
							foreach($arrMaxTeams as $maxTeams) {
								
								$selectMaxTeams = "";
								if($tournamentInfo['maxteams'] == $maxTeams) {
									$selectMaxTeams = " selected";	
								}
								
								echo "
									<option value='".$maxTeams."'".$selectMaxTeams.">".$maxTeams."</option>
								";
								
								
							}
							
							echo ";
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Players Per Team:</td>
						<td class='main'>
							<select name='playersperteam' class='textBox'>
	
	
	";
							
	for($i=1;$i<=16;$i++) {
		$dispSelected = "";
		if($tournamentInfo['playersperteam'] == $i) {
			$dispSelected = " selected";	
		}
		
		echo "<option value='".$i."'".$dispSelected.">".$i."</option>";
	}
	
	echo "
		</select>
		
		
						</td>
					</tr>
					<tr>
						<td colspan='2' class='main'><br>
							<b>Tournament Access</b>
							<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Access:</td>
						<td class='main'>
							<select class='textBox' name='tournamentaccess'>
							";
							
							$arrTournamentAccess = array(1 => "Clan Only", 2 => "Multi-Clan", 3 => "Everyone");
							foreach($arrTournamentAccess as $key=>$accessType) {
								
								$selectAccess = "";
								if($tournamentInfo['access'] == $key) {
									$selectAccess = " selected";
								}
								
								echo "<option value='".$key."'".$selectAccess.">".$accessType."</option>";
								
							}

							echo "
							</select>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Password: <a href='javascript:void(0)' onmouseover=\"showToolTip('If you don\'t want to change the current password, leave both password inputs blank.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><input type='password' name='tournamentpassword' id='tournamentpassword' class='textBox' style='width: 125px'></td>
					</tr>
					<tr>
						<td class='formLabel'>Re-type Password:</td>
						<td class='main'><input type='password' name='tournamentpassword1' id='tournamentpassword1' class='textBox' style='width: 125px'><span id='checkPassword' style='padding-left: 5px'></span></td>
					</tr>
					
					";
					
					if($tournamentInfo['password'] != "") {
						
						echo "
						
							<tr>
								<td class='formLabel'>Remove Password: <a href='javascript:void(0)' onmouseover=\"showToolTip('This tournament currently has a password in order for members to join.  Mark the check box to remove the password.')\" onmouseout='hideToolTip()'><b>(?)</b></a></td>
								<td class='main'><input type='checkbox' name='removepass' value='1'></td>
							</tr>
						
						
						";
						
					}							
							
							
					echo "
					<tr>
						<td class='main' colspan='2' align='center'><br><br>
							<input type='submit' name='submit' value='Update Tournament' class='submitButton' style='width: 150px'>
						</td>
					</tr>					
				</table>
			
			</div>
			<input type='hidden' value='".($tournamentInfo['startdate']*1000)."' name='startdate' id='realstartdate'>
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
					altFormat: '@',
					defaultDate: '".$dispDate."'
				
				});
				$('#startDate').datepicker('setDate', '".$dispDate."');
				
				
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