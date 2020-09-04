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

if($tournamentInfo['seedtype'] != 3 && !$tournamentObj->checkForPools()) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."tournaments/bracket.php?tID=".$_GET['tID']."';
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
		<link rel='stylesheet' type='text/css' href='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/btcs4.css'>
		<link rel='stylesheet' type='text/css' href='<?php echo $MAIN_ROOT; ?>js/css/jquery-ui-1.8.17.custom.css'>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/jquery-1.6.4.min.js'></script>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/jquery-ui-1.8.17.custom.min.js'></script>
		<script type='text/javascript' src='<?php echo $MAIN_ROOT; ?>js/main.js'></script>
	</head>
	<body>
		<div id='toolTip'></div>
		
			<p align='center' class='largeFont'>
				<b><?php echo $tournamentInfo['name']; ?></b>
			</p>
		
			<div style='float: left; width: 600px; padding-bottom: 25px'>
		
		<?php
		
			$arrPools = $tournamentObj->getPoolList();
			$dispPoolLetter = "A";
			foreach($arrPools as $poolID) {

				$tournamentObj->objTournamentPool->select($poolID);
				$arrTeamsInPool = $tournamentObj->objTournamentPool->getTeamsInPool();
				
				echo "<p class='main' align='left'><b><u>Pool ".$dispPoolLetter.":</u></b></p>";
				$counter = 0;
				
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentpools_teams WHERE pool_id = '".$poolID."'");
				while($row = $result->fetch_assoc()) {
					
					$dispTeamOne = $tournamentObj->getPlayerName($row['team1_id']);
					$dispTeamTwo = $tournamentObj->getPlayerName($row['team2_id']);
					
					if($dispTeamOne == "") {
						$dispTeamOne = "<i>Empty Spot</i>";	
					}
					
					if($dispTeamTwo == "") {
						$dispTeamTwo = "<i>Empty Spot</i>";	
					}
					
					if($row['winner'] == 1) {
						$dispTeamOne = "<span class='successFont' style='font-weight: bold'>".$dispTeamOne."</span>";	
					}
					elseif($row['winner'] == 2) {
						$dispTeamTwo = "<span class='successFont' style='font-weight: bold'>".$dispTeamTwo."</span>";
					}
					
					echo "
						<div class='dottedBox main' style='width: 280px; float: left'>
							<div class='shadedBox main' style='position: relative; border-width: 0px; text-align: left'>
								".$dispTeamOne."
								
								<div style='position: absolute; top: 5px; right: 15px'><span style='cursor: help' onmouseover=\"showToolTip('Score')\" onmouseout='hideToolTip()'>".$row['team1score']."</span></div>
								
							</div>
							<div class='shadedBox main' style='position: relative; border-width: 0px; margin-top: 3px; text-align: left'>
								".$dispTeamTwo."
								
								<div style='position: absolute; top: 5px; right: 15px'><span style='cursor: help' onmouseover=\"showToolTip('Score')\" onmouseout='hideToolTip()'>".$row['team2score']."</span></div>
							</div>
						</div>
					";
					
					$counter++;
					if($counter == 2) {
						$counter = 0;
						echo "<div style='clear: both'></div>";	
					}
				}
				
				
				$dispPoolLetter++;
			}
		
		
		?>
		
		</div>
	</body>
</html>