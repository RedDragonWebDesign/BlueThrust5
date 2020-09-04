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


// Config File
$prevFolder = "../";

include($prevFolder."_setup.php");
include($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/tournament.php");



$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");
$tournamentObj = new Tournament($mysqli);
$member = new Member($mysqli);



if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}

if(!is_numeric($_GET['tID']) || !$tournamentObj->select($_GET['tID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");
}

$tournamentInfo = $tournamentObj->get_info_filtered();

if($tournamentInfo['seedtype'] == 3) {
	echo "
	<script type='text/javascript'>
		window.location = '".$MAIN_ROOT."tournaments/poolmatches.php?tID=".$_GET['tID']."';
	</script>
	";
	exit();
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $PAGE_NAME.$CLAN_NAME; ?></title>
		<link rel='stylesheet' type='text/css' href='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/style.css'>
		<link rel='stylesheet' type='text/css' href='<?php echo $MAIN_ROOT; ?>themes/btcs4.css.php'>
		<link rel='stylesheet' type='text/css' href='<?php echo $MAIN_ROOT; ?>js/css/jquery-ui-1.8.17.custom.css'>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/jquery-1.6.4.min.js'></script>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/jquery-ui-1.8.17.custom.min.js'></script>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/main.js'></script>
	</head>
	<body>
		<div id='toolTip'></div>
		<div id='toolTipWidth'></div>
		<div class='tournamentBracket'>
		
			<?php
			
			
				$roundNum = 1;
				$intLeft = 20;
				$intTop = 20;
				$intTopOriginal = $intTop;
				$intTeamSeparation = 18;
				$intSlotWidth = 200;
				$intSlotHeight = 20;
				$intMatchDistance = ($intSlotHeight*2)+$intTeamSeparation;

				$intWidthMakeUp = 6;	// Adjustment for CSS padding
				$intHeightMakeUp = 6;	// Adjustment for CSS padding
				$intMatchCount = 0;
				
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentmatch WHERE tournament_id = '".$tournamentInfo['tournament_id']."' ORDER BY round");
				while($row = $result->fetch_assoc()) {

					
					if($roundNum != $row['round']) {
						
						if($roundNum == 1) {
							echo "
								<div style='position: absolute; top: ".($intTop-$intMatchDistance)."px; width: 3px; height: ".$intSlotHeight."px'></div>
							";
						}
						
						$intMatchCount = 0;

						$intLeft += $intSlotWidth+($intTeamSeparation*8);
						$roundNum = $row['round'];
						$intTop = $intTopOriginal+($intSlotHeight*2)+$intTeamSeparation;
						
						
						
						switch($roundNum) {
							case 3:
								$intTop += ($intMatchDistance*2);
								break;
							case 4:
								$intTop += ($intMatchDistance*6);
								break;
							case 5:
								$intTop += ($intMatchDistance*14);
								break;
							case 6:
								$intTop += ($intMatchDistance*30);
								break;
							
						}
						
						
					}
					
					$intMatchCount++;
					
					
					for($i=1; $i<=2; $i++) {
					
						$teamIDColumn = "team".$i."_id";
						$teamID = $row[$teamIDColumn];
						
						$dispName = "Empty Spot";
						if($tournamentObj->objTeam->select($teamID)) {
							$teamInfo = $tournamentObj->objTeam->get_info_filtered();
							
							if($tournamentInfo['playersperteam'] == 1) {
								
								$teamPlayers = $tournamentObj->getTeamPlayers($teamID, true);
								
								if($tournamentObj->objPlayer->select($teamPlayers[0])) {
									
									if($member->select($tournamentObj->objPlayer->get_info("member_id"))) {
										$dispName = $member->getMemberLink();
									}
									else {
										$dispName = $tournamentObj->objPlayer->get_info_filtered("displayname");
									}
									
									
								}
								else {
									$dispName = "Bye";
								}
								
							}
							else {
								// Multi-Player Teams
								$dispPlayerList = "";
								$arrTeamPlayers = $tournamentObj->getTeamPlayers($teamInfo['tournamentteam_id'], true);
								foreach($arrTeamPlayers as $playerID) {
									$tournamentObj->objPlayer->select($playerID);

									$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
									if(is_numeric($playerInfo['member_id']) && $member->select($playerInfo['member_id'])) {
										
										$dispPlayerList .= "<b>&middot;</b> ".$member->getMemberLink()."<br>";
										
									}
									else {
										
										$dispPlayerList .= "<b>&middot;</b> ".$playerInfo['displayname']."<br>";
										
									}
									
									
								}
								
								if($dispPlayerList == "") {
									$dispPlayerList = "No Players on Team";
								}
									
							
								$dispName = "<span style='cursor: pointer' onmouseover=\"showToolTip('".addslashes($dispPlayerList)."')\" onmouseout='hideToolTip()'>".$teamInfo['name']."</span>";
								
								
								

							}
							
							$dispSeed = "#".$teamInfo['seed'];
							
						}
						else {
							$dispName = "Empty Spot";
							$dispSeed = "";
						}
						
						
						echo "
							<div class='bracket main' style='width: ".$intSlotWidth."px; height: ".$intSlotHeight."px; line-height: ".$intSlotHeight."px;  left: ".$intLeft."px; top: ".$intTop."px'>".$dispName."</div>
							<div class='tinyFont seed' style='line-height: ".$intSlotHeight."px; height: ".$intSlotHeight."px; left: ".($intLeft+$intSlotWidth+$intWidthMakeUp+3)."px; top: ".$intTop."px'>".$dispSeed."</div>
						";
						
						if($i == 1) {
							
							if($intMatchCount % 2 != 0) {
								$intBracketConnectorTop = $intTop+$intSlotHeight+($intTeamSeparation/2);
							}
							
								$intTop += $intTeamSeparation+$intSlotHeight;
							
						}
						
					}
					
					
					$intBracketConnectorLeft = $intLeft+$intSlotWidth+$intWidthMakeUp+50;
					
					switch($roundNum) {
						
						case 1:
							$intTop += $intSlotHeight+$intMatchDistance;
							$intBracketConnectorHeight = ($intSlotHeight*2)+$intMatchDistance+$intTeamSeparation;
							break;
						case 2:
							$intTop += $intSlotHeight+($intMatchDistance*3);
							$intBracketConnectorHeight = ($intSlotHeight*2)+($intMatchDistance*3)+$intTeamSeparation;
							break;
						case 3:
							$intTop += $intSlotHeight+($intMatchDistance*7);
							$intBracketConnectorHeight = ($intSlotHeight*2)+($intMatchDistance*7)+$intTeamSeparation;
							break;
						case 4:
							$intTop += $intSlotHeight+($intMatchDistance*15);
							$intBracketConnectorHeight = ($intSlotHeight*2)+($intMatchDistance*15)+$intTeamSeparation;
							break;
						case 5:
							$intTop += $intSlotHeight+($intMatchDistance*31);
							$intBracketConnectorHeight = ($intSlotHeight*2)+($intMatchDistance*31)+$intTeamSeparation;
							break;
						default:
							$intTop += $intSlotHeight+$intMatchDistance;
							$intBracketConnectorHeight = ($intSlotHeight*2)+$intMatchDistance+$intTeamSeparation;
					}
					
					
					if(($intMatchCount%2) == 0) {
						
						echo "
							<div class='bracketConnector' style='left: ".$intBracketConnectorLeft."px; top: ".$intBracketConnectorTop."px; width: 30px; height: ".$intBracketConnectorHeight."px'></div>
							<div class='bracketConnectorDash' style='left: ".($intBracketConnectorLeft+30)."px; top: ".($intBracketConnectorTop+($intBracketConnectorHeight/2))."px'></div>
						";
					}
					
					
					/*
					
					if($roundNum == 2) {
						$intTop += $intSlotHeight+($intMatchDistance*3);//(($intSlotHeight+$intHeightMakeUp)*2)+$intTeamSeparation;
						//$intTop += 152;
					}
					elseif($roundNum == 3) {
						$intTop+= $intSlotHeight+($intMatchDistance*7);
					}
					elseif($roundNum ==
					else {
						$intTop += $intSlotHeight+$intMatchDistance;
					}
					*/
					
				}
				
				
				$intLeft += $intSlotWidth+($intTeamSeparation*8);
				$intTop = $intTopOriginal+($intSlotHeight*2)+$intTeamSeparation+($intSlotHeight/2)+($intTeamSeparation/2);
				$intBracketConnectorLeft = $intLeft-(($intTeamSeparation*8)/2)-30;
				
				switch($roundNum) {
					case 3:
						$intTop += ($intMatchDistance*2);
						break;
					case 4:
						$intTop += ($intMatchDistance*6);
						break;
					case 5:
						$intTop += ($intMatchDistance*14);
						break;
					case 6:
						$intTop += ($intMatchDistance*30);
						break;
					
				}
				
				$tournamentWinner = $tournamentObj->getTournamentWinner();
				$dispWinner = "Empty Spot";
				$dispSeed = "";
				if($tournamentWinner !== false) {
					
					$tournamentObj->objTeam->select($tournamentWinner);
					
					
					$dispSeed = "#".$tournamentObj->objTeam->get_info("seed");
					
					if($tournamentInfo['playersperteam'] == 1) {
						
						$arrWinner = $tournamentObj->getTeamPlayers($tournamentWinner, true);
						
						$tournamentObj->objPlayer->select($arrWinner[0]);
						$winnerInfo = $tournamentObj->objPlayer->get_info_filtered();
						
						if($member->select($winnerInfo['member_id'])) {
							$dispWinner = $member->getMemberLink();
						}
						else {
							$dispWinner = $winnerInfo['displayname'];
						}
						
						
					}
					else {
						
						$teamInfo = $tournamentObj->objTeam->get_info_filtered();
						
						$dispPlayerList = "";
						$arrTeamPlayers = $tournamentObj->getTeamPlayers($teamInfo['tournamentteam_id'], true);
						foreach($arrTeamPlayers as $playerID) {
							$tournamentObj->objPlayer->select($playerID);

							$playerInfo = $tournamentObj->objPlayer->get_info_filtered();
							if(is_numeric($playerInfo['member_id']) && $member->select($playerInfo['member_id'])) {
								
								$dispPlayerList .= "<b>&middot;</b> ".$member->getMemberLink()."<br>";
								
							}
							else {
								
								$dispPlayerList .= "<b>&middot;</b> ".$playerInfo['displayname']."<br>";
								
							}
							
							
						}
						
						if($dispPlayerList == "") {
							$dispPlayerList = "No Players on Team";
						}
							
					
						$dispWinner = "<span style='cursor: pointer' onmouseover=\"showToolTip('".addslashes($dispPlayerList)."')\" onmouseout='hideToolTip()'>".$teamInfo['name']."</span>";
						
						
								
						
						//$dispWinner = $tournamentObj->objTeam->get_info_filtered("name");
						
					}
					
					
				}
				
				echo "
				
					<div class='bracket main' style='width: ".$intSlotWidth."px; height: ".$intSlotHeight."px; line-height: ".$intSlotHeight."px;  left: ".$intLeft."px; top: ".$intTop."px'>".$dispWinner."</div>
					<div style='position: absolute; width: 40px; height: ".$intSlotHeight."px; left: ".($intLeft+$intSlotWidth+$intWidthMakeUp)."px; top: ".$intTop."px'></div>
					
					<div class='tinyFont seed' style='line-height: ".$intSlotHeight."px; height: ".$intSlotHeight."px; left: ".($intLeft+$intSlotWidth+$intWidthMakeUp+3)."px; top: ".$intTop."px'>".$dispSeed."</div>
					
					<div class='bracketConnector' style='left: ".$intBracketConnectorLeft."px; top: ".($intBracketConnectorTop-($intTeamSeparation/2))."px; width: 30px; height: ".$intSlotHeight."px'></div>
					<div class='bracketConnectorDash' style='left: ".($intBracketConnectorLeft+30)."px; top: ".($intBracketConnectorTop-($intTeamSeparation/2)+($intSlotHeight/2))."px'></div>
				
				
				";
			
			
			?>
		
		</div>
	
	</body>
</html>