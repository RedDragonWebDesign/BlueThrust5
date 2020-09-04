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

// Classes needed for index.php

$tournamentObj = new Tournament($mysqli);
$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


// Start Page
$PAGE_NAME = "Tournaments - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle("Tournaments");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Tournaments");

include($prevFolder."include/breadcrumb.php");
?>

		
<div style='margin: 0px auto; '>
<table class='formTable' style='margin-left: auto; margin-right: auto'>

	<tr>
		<td class='formTitle' width="30%">Tournament Name:</td>
		<td class='formTitle' width="25%">Manager:</td>
		<td class='formTitle' width="25%">Start Date:</td>
		<td class='formTitle' width="20%">Status:</td>
	</tr>
	
	<?php
		$memberObj = new Member($mysqli);
	
		$counter = 0;
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournaments ORDER BY startdate DESC");
		
		while($row = $result->fetch_assoc()) {
			
			
			$tournamentObj->select($row['tournament_id']);
			$memberObj->select($row['member_id']);
			$dispManager = $memberObj->getMemberLink();
			
			$dateTimeObj = new DateTime();
			$dateTimeObj->setTimestamp($row['startdate']);
			$includeTimezone = "";
			$dateTimeObj->setTimezone(new DateTimeZone("UTC"));
			$dispStartDate = $dateTimeObj->format("M j, Y g:i A");
			
			if($row['timezone'] != "") { 
				$dateTimeObj->setTimezone(new DateTimeZone($row['timezone']));
				$includeTimezone = " T"; 
			}
			
			$dispStartDate .= $dateTimeObj->format($includeTimezone);
			
			if($row['startdate'] < time() && $tournamentObj->getTournamentWinner() == 0) {
				$dispStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/bluedot.png' title='Started'>";	
			}
			elseif($row['startdate'] > time()) {
				$dispStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/orangedot.png' title='Forming'>";
			}
			elseif($row['startdate'] < time() && $tournamentObj->getTournamentWinner() != 0) {
				$dispStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/graydot.png' title='Finished'>";	
			}
			
			if($counter == 1) {
				$addCSS = " alternateBGColor";
				$counter = 0;
			}
			else {
				$addCSS = "";
				$counter = 1;
			}
			
			echo "
				<tr>
					<td class='main ".$addCSS."'><a href='".$MAIN_ROOT."tournaments/view.php?tID=".$row['tournament_id']."'>".filterText($row['name'])."</a></td>
					<td class='main ".$addCSS."'>".$dispManager."</td>
					<td class='main ".$addCSS."' align='center'>".$dispStartDate."</td>
					<td class='main ".$addCSS."' align='center'>".$dispStatus."</td>
				</tr>
			
			";
			
		}
		
		if($result->num_rows == 0) {
			

			echo "
				<tr>
					<td class='main' colspan='4'>
						<p align='center'>
							There are currently no tournaments created for this clan!
						</p>
					</td>
				</tr>
			";
			
		}
		?>
		
		<tr>
		<td class='main' colspan='4' valign='top'>
			<p align='center' style='margin-top: 50px'>
				<b>Total Tournaments:</b> <?php echo $result->num_rows; ?><br><br>
				<b>Key:</b> Started - <img src='<?php echo $MAIN_ROOT."themes/".$THEME; ?>/images/bluedot.png' style='vertical-align: middle'> | Forming - <img src='<?php echo $MAIN_ROOT."themes/".$THEME; ?>/images/orangedot.png' style='vertical-align: middle'> | Finished - <img src='<?php echo $MAIN_ROOT."themes/".$THEME; ?>/images/graydot.png' style='vertical-align: middle'>
			</p>
		</td>
	</tr>
</table>
</div>

<?php

include($prevFolder."themes/".$THEME."/_footer.php");


?>
			