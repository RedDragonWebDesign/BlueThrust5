<?php

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}


$tournamentObj->select($tournamentObj->objMatch->get_info("tournament_id"));
$tournamentInfo = $tournamentObj->get_info_filtered();
$matchInfo = $tournamentObj->objMatch->get_info_filtered();

$arrTeam1 = $tournamentObj->getTeamPlayers($matchInfo['team1_id']);
$arrTeam2 = $tournamentObj->getTeamPlayers($matchInfo['team2_id']);

if(in_array($memberInfo['member_id'], $arrTeam1)) {
	$checkApprove = $matchInfo['team2approve'];
	$checkMyApprove = $matchInfo['team1approve'];
	$dispMyTeamApprove = "team1approve";
	$dispOpponentTeamApprove = "team2approve";
	$intMyTeamID = $matchInfo['team1_id'];
	$intOpponentTeamID = $matchInfo['team2_id'];
	$arrOpponent = $arrTeam2;
	$dispOpponent =  $tournamentObj->getPlayerName($matchInfo['team2_id']);
	$dispMatchReplay = $matchInfo['replayteam1url'];
	$dispReplayColumn = "replayteam1url";
}
elseif(in_array($memberInfo['member_id'], $arrTeam2)) {
	$checkApprove = $matchInfo['team1approve'];
	$checkMyApprove = $matchInfo['team2approve'];
	$dispMyTeamApprove = "team2approve";
	$dispOpponentTeamApprove = "team1approve";
	$intMyTeamID = $matchInfo['team2_id'];
	$intOpponentTeamID = $matchInfo['team1_id'];
	$arrOpponent = $arrTeam1;
	$dispOpponent =  $tournamentObj->getPlayerName($matchInfo['team1_id']);
	$dispMatchReplay = $matchInfo['replayteam1url'];
	$dispReplayColumn = "replayteam2url";
}
else {
	echo "
	<script type='text/javascript'>
	window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
	</script>
	";
	exit();
}


$dispTeam1 = $tournamentObj->getPlayerName($matchInfo['team1_id']);
$dispTeam2 = $tournamentObj->getPlayerName($matchInfo['team2_id']);


if($tournamentInfo['playersperteam'] == 1) {
	$dispTeamOrPlayer = "Player";
}
else {
	$dispTeamOrPlayer = "Team";
}


$dispApproved = "";
if($checkApprove == 1) {
	$dispApproved = "<br><br><b><u>NOTE:</u></b> ".$dispOpponent." has already submitted results for this match.  You can approve the submission by clicking the Approve button below or you can enter in different results.";
}
elseif($checkMyApprove == 1) {
	$dispApproved = "<br><br><b><u>NOTE:</u></b> You have already submitted results for this match.  The results will show as pending on the tournament profile page and bracket until ".$dispOpponent." or the tournament manager approves your submission.";
}



if($_POST['submit'] && !$_POST['approve']) {
	$arrColumns = array();
	$arrValues = array();

	// Check Winner
	$arrWinners = array(0,1,2);
	if(!in_array($_POST['matchwinner'], $arrWinners)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid match winner.<br>";
		$countErrors++;
	}

	if($_POST['matchwinner'] != 0) {
		$arrColumns[] = $dispMyTeamApprove;
		$arrValues[] = 1;
		$arrColumns[] = $dispOpponentTeamApprove;
		$arrValues[] = 0;
	}


	// Upload Replay

	if($_FILES['uploadfile']['name'] != "") {

		$uploadReplayObj = new BTUpload($_FILES['uploadfile'], "replay_", "../downloads/replays/", array(".zip"));

		if(!$uploadReplayObj->uploadFile()) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload the replay. Please make sure the file extension is .zip and that the file size is not too big.<br>";
		}
		else {
			$matchReplayURL = $MAIN_ROOT."downloads/replays/".$uploadReplayObj->getUploadedFileName();
		}
		
	}
	else {
		$matchReplayURL = $_POST['uploadurl'];
	}

	if($countErrors == 0) {
		$arrColumns[] = "outcome";
		$arrValues[] = $_POST['matchwinner'];
		$arrColumns[] = $dispReplayColumn;
		$arrValues[] = $matchReplayURL;
		$arrColumns[] = "team1score";
		$arrValues[] = $_POST['team1score'];
		$arrColumns[] = "team2score";
		$arrValues[] = $_POST['team2score'];
		

		
		if($tournamentObj->objMatch->update($arrColumns, $arrValues)) {
			
			echo "
			
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Updated Match</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Update Match', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
			</script>
			
			";
			
			foreach($arrOpponent as $value) {
				$tMemberObj->select($value);
				$tMemberObj->postNotification($member->getMemberLink()." has updated the match results for <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&mID=".$_GET['mID']."'>".$dispTeam1." vs. ".$dispTeam2."</a>");
			}

		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
		
	}


}
elseif(!$_POST['submit'] && $_POST['approve'] && $checkApprove == 1) {
	
	// Upload Replay
	
	if($_FILES['uploadfile']['name'] != "") {
	
		$uploadReplayObj = new BTUpload($_FILES['uploadfile'], "replay_", "../downloads/replays/", array(".zip"));
	
		if(!$uploadReplayObj->uploadFile()) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to upload the replay. Please make sure the file extension is .zip and that the file size is not too big.<br>";
		}
		else {
			$matchReplayURL = $MAIN_ROOT."downloads/replays/".$uploadReplayObj->getUploadedFileName();
		}
	
	}
	else {
		$matchReplayURL = $_POST['uploadurl'];
	}
	
	if($countErrors == 0) {
	
		$arrColumns[] = $dispReplayColumn;
		$arrValues[] = $matchReplayURL;
		$arrColumns[] = $dispMyTeamApprove;
		$arrValues[] = 1;
		
		if($tournamentObj->objMatch->update($arrColumns, $arrValues)) {
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Approved Match Results</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Approve Match', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
				</script>
			
			
			";
			
			foreach($arrOpponent as $value) {
				$tMemberObj->select($value);
				$tMemberObj->postNotification($member->getMemberLink()." has approved the match results for <a href='".$MAIN_ROOT."tournaments/view.php?tID=".$matchInfo['tournament_id']."'>".$dispTeam1." vs. ".$dispTeam2."</a>");
			}
			
			if($_POST['matchwinner'] == 1) {
				$matchWinner = $matchInfo['team1_id'];
			}
			else {
				$matchWinner = $matchInfo['team2_id'];
			}
			
			$nextMatchSpot = $tournamentObj->getNextMatchTeamSpot($matchWinner);
			
			$tournamentObj->objMatch->select($matchInfo['nextmatch_id']);
			
			
			$tournamentObj->objMatch->update(array($nextMatchSpot), array($matchWinner));
			
			
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
	}
	

}






echo "
<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&mID=".$_GET['mID']."' method='post' enctype='multipart/form-data'>
<div class='formDiv'>

";

if($dispError != "") {
	echo "
	<div class='errorDiv'>
	<strong>Unable to edit match because the following errors occurred:</strong><br><br>
	$dispError
	</div>
	";
}


	echo "
			Use the form below to edit the match details.
			".$dispApproved."
			<table class='formTable'>
				<tr>
					<td colspan='2' class='main'>
						<b>Match Up</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>".$dispTeamOrPlayer." 1:</td>
					<td class='main'>".$dispTeam1."</td>
				</tr>
				<tr>
					<td class='formLabel'>".$dispTeamOrPlayer." 2:</td>
					<td class='main'>".$dispTeam2."</td>
				</tr>
				<tr>
					<td colspan='2' class='main'><br>
						<b>Match Outcome</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
					</td>
				</tr>
				<tr>
					<td class='formLabel'>".$dispTeamOrPlayer." 1 Score:</td>
					<td class='main'><input type='text' name='team1score' value='".$matchInfo['team1score']."' class='textBox' style='width: 40px'></td>
				</tr>
				<tr>
					<td class='formLabel'>".$dispTeamOrPlayer." 2 Score:</td>
					<td class='main'><input type='text' name='team2score' value='".$matchInfo['team2score']."' class='textBox' style='width: 40px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Match Winner:</td>
					<td class='main'><select id='matchWinner' name='matchwinner' class='textBox'><option value='0'>None Yet</option><option value='1'>".$dispTeam1."</option><option value='2'>".$dispTeam2."</option></select></td>
				</tr>
				<tr>
					<td colspan='2' class='main'><br>
						<b>Upload Replay</b>
						<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
						";
	
				if($checkApprove == 1) {
					echo "<p style='margin: 2px; padding-left: 5px'>* Clicking the Approve Results button will also upload your replay.</p><br>";	
				}
	
	echo "
					</td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Match Replay:</td>
					<td class='main' valign='top'>
						File:<br>
						<input type='file' class='textBox' style='width: 250px; border: 0px' name='uploadfile'><br>
						<span style='font-size: 10px'>&nbsp;&nbsp;&nbsp;<b>&middot;</b> File Type: .zip<br>&nbsp;&nbsp;&nbsp;<b>&middot;</b> <a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></span>
						<br><br>
						<b><i>OR</i></b>
						<br><br>
						URL:<br>
						<input type='text' class='textBox' style='width: 250px' value='".$dispMatchReplay."' name='uploadurl'>
					</td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'><br>";
				
					if($checkApprove == 1) {
						
						echo "<input type='submit' name='approve' value='Approve Results' class='submitButton' style='width: 125px'><br><br>";
						
					}
	
				echo "
					
						<input type='submit' name='submit' value='Update Results' class='submitButton' style='width: 125px'>
					<br>
					</td>
				</tr>
			</table>
		
		
		</div>
	</form>
	
		<script type='text/javascript'>
	
			$(document).ready(function() {
			
				$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> ".$dispTeam1." vs. ".$dispTeam2."\");
			
			
				$('#consoleTopBackButton').attr('href', '".$MAIN_ROOT."members/console.php?cID=".$cID."');
				$('#consoleBottomBackButton').attr('href', '".$MAIN_ROOT."members/console.php?cID=".$cID."');

				
				$('#matchWinner').val('".$matchInfo['outcome']."');
				
				
			});
		
		</script>
		
	";



?>