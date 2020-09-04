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
$prevFolder = "";

include($prevFolder."_setup.php");

// Classes needed for index.php


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
$PAGE_NAME = "Members - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);
$rankObj = new Rank($mysqli);

// Disable Members for Inactivity
if($websiteInfo['maxdsl'] != 0) {
	$maxDSLTime = time() - ($websiteInfo['maxdsl']*86400);
	$time = time();
	$result = $mysqli->query("UPDATE ".$dbprefix."members SET disabled = '1', disableddate = '".$time."' WHERE disabled = '0' AND rank_id != '1' AND onia = '0' AND lastlogin <= '".$maxDSLTime."'");
	
	$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE disableddate = '".$time."'");
	while($row = $result->fetch_assoc()) {
		$arrLogColumns = array("member_id", "logdate", "message");
		$arrLogValues = array($row['member_id'], $time, "Disabled due to inactivity.");
		$logObj->addNew($arrLogColumns, $arrLogValues);
	}
}

// Disable members who fail to be promoted for auto-disable ranks
$arrRanks = array();
$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE autodisable != '0'");
while($row = $result->fetch_assoc()) {
	$arrRanks[] = $row['rank_id'];
}

$sqlRanks = "('".implode("','", $arrRanks)."')";

$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE rank_id IN ".$sqlRanks." AND onia = '0'");
while($row = $result->fetch_assoc()) {
	$member->select($row['member_id']);
	$memberListInfo = $member->get_info();
	$rankObj->select($row['rank_id']);
	$memRankListInfo = $rankObj->get_info();
	if((floor(time()/86400)-floor($memberListInfo['datejoined']/86400)) >= $memRankListInfo['autodisable']) {
		$member->update(array("disabled", "disableddate"), array(1, $time));
		$member->logAction("Disabled for failure to be promoted before ".$memRankListInfo['autodisable']." days.");
	}
}




$rankCatObj = new RankCategory($mysqli);

$gameObj = new Game($mysqli);

$breadcrumbObj->setTitle("Members");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Members");
include($prevFolder."include/breadcrumb.php");

?>
<div id='tiltPhoneImg' style='display: none'><img src='<?php echo $MAIN_ROOT; ?>images/tiltphone.png'><p align='center'>need more space<br>tilt your phone!</p></div>
<table class='formTable' id='membersPageTable'>
<?php
$maxDSLIntervals = floor($websiteInfo['maxdsl']/3);
$arrCountDSL[1] = 0;
$arrCountDSL[2] = 0;
$arrCountDSL[3] = 0;
$arrMemberCountCat = array();
$arrGameCount = array();
$arrGamesPlayed = array();
$result = $mysqli->query("SELECT rankcategory_id FROM ".$dbprefix."rankcategory WHERE hidecat = '0' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	
	$rankCatObj->select($row['rankcategory_id']);
	$rankCatObj->refreshImageSize();
	$rankCatInfo = $rankCatObj->get_info_filtered();
	
	$arrRanks = $rankCatObj->getAssociateIDs(" ORDER BY ordernum DESC");
	
	if($websiteInfo['maxdsl'] == 0) {
		$tableCols = 4;	
	}
	else {
		$tableCols = 5;
	}
	
	
	if($rankCatInfo['useimage'] == 1 && $rankCatInfo['imageurl'] != "") {
		echo "
		<tr>
			<td class='main' align='center' colspan='".$tableCols."'><img src='".$rankCatInfo['imageurl']."' width='".$rankCatInfo['imagewidth']."' height='".$rankCatInfo['imageheight']."' onmouseover=\"showToolTip('<b>".$rankCatInfo['name']."</b><br>".$rankCatInfo['description']."')\" onmouseout='hideToolTip()'></td>
		</tr>
		";
	}
	else {
	
		$dispCatDesc = "";
		if($rankCatInfo['description'] != "") {
			$dispCatDesc = " style='cursor: pointer' onmouseover=\"showToolTip('<b>".$rankCatInfo['name']."</b><br>".$rankCatInfo['description']."')\" onmouseout='hideToolTip()'";
		}
	
		echo "
		<tr>
			<td class='largeFont' style='font-weight: bold' align='center' colspan='".$tableCols."'><span".$dispCatDesc.">".$rankCatInfo['name']."</span></td>
		</tr>
		";
	}
	
	
	echo "
		<tr>
			<td class='formTitle'>Rank:</td>
			<td class='formTitle'>Username:</td>
			<td class='formTitle'>Main Game:</td>
			";
			
			if($tableCols == 5) { echo "<td class='formTitle'><span onmouseover=\"showToolTip('Days Since Last Login')\" onmouseout='hideToolTip()' style='cursor: help'>DSL:</span></td>"; }
			
			echo "
			<td class='formTitle'>Status:</td>
		</tr>
	";
	
	$sqlRanks = "('".implode("','", $arrRanks)."')";
	
	$sqlHideInactive = "";
	if($websiteInfo['hideinactive'] == 1) {
		$sqlHideInactive = " AND ".$dbprefix."members.onia = '0'";
	}
	
	$query = "SELECT ".$dbprefix."members.member_id FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id=".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."ranks.hiderank = '0'".$sqlHideInactive." ORDER BY ".$dbprefix."ranks.ordernum DESC";
	$result2 = $mysqli->query($query);
	$arrMemberCountCat[$row['rankcategory_id']] = $result2->num_rows;
	while($arrMembers = $result2->fetch_assoc()) {
		$member->select($arrMembers['member_id']);
		$memberListInfo = $member->get_info_filtered();
		$rankObj->select($memberListInfo['rank_id']);
		$rankObj->refreshImageSize();
		$rankListInfo = $rankObj->get_info_filtered();
		
		
	
		$dispMainGame = "Not Set";
		if($gameObj->select($memberListInfo['maingame_id'])) {
			$gameObj->refreshImageSize();
			$gameInfo = $gameObj->get_info_filtered();
			$arrGameCount[] = $gameInfo['gamesplayed_id'];
			$dispMainGame = "<div class='memberPageImage'><img src='".$gameInfo['imageurl']."' width='".$gameInfo['imagewidth']."' height='".$gameInfo['imageheight']."' onmouseover=\"showToolTip('".$gameInfo['name']."')\" onmouseout='hideToolTip()'></div>";
		}
		else {
			$arrGameCount[] = "NotSet";	
		}
		
		
		$arrGamesPlayed = array_merge($arrGamesPlayed, $member->gamesPlayed());
		
		$dispDSL = floor((time()-$memberListInfo['lastlogin'])/86400);
		if($memberListInfo['onia'] == 1) {
			$dispDSL = "IA";	
		}
		
		
		if(is_numeric($dispDSL) && $dispDSL >= 0 && $dispDSL <= $maxDSLIntervals) {
			$arrCountDSL[1]++;	
		}
		elseif(is_numeric($dispDSL) && $dispDSL > $maxDSLIntervals && $dispDSL <= ($maxDSLIntervals*2)) {
			$arrCountDSL[2]++;
		}
		elseif(is_numeric($dispDSL)) {
			$arrCountDSL[3]++;
		}

		
		
		
		if($memberListInfo['loggedin'] == 1 && (time()-$memberListInfo['lastseen']) < 600) {
			$dispStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/onlinedot.png' onmouseover=\"showToolTip('".$memberListInfo['username']." is Online!')\" onmouseout='hideToolTip()'>";	
		}
		else {
			$dispStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/offlinedot.png'>";
			
			if($memberListInfo['loggedin'] == 1) {
				$member->update(array("loggedin"), array(0));
			}
			
		}
		
		echo "
			<tr>
				<td class='main' align='center'><div class='memberPageImage'><img src='".$rankListInfo['imageurl']."' width='".$rankListInfo['imagewidth']."' height='".$rankListInfo['imageheight']."'></div></td>
				<td class='main'>".$member->getMemberLink()."</td>
				<td class='main' align='center'>".$dispMainGame."</td>
				";
				if($tableCols == 5) { echo "<td class='main' align='center'>".$dispDSL."</td>"; }
				echo "
				<td class='main' align='center'><div class='memberPageImage'>".$dispStatus."</div></td>
			</tr>
		";
		
	}
	
	echo "<tr><td colspan='5'><br></td></tr>";
	
}

$totalMembers = array_sum($arrMemberCountCat);


?>
</table>
<div style='margin: 20px auto'>

	<table class='formTable' id='membersPageTable' style='width: 85%; margin-left: auto; margin-right: auto'>
		<tr>
			<td colspan='3' class='main dottedLine' align='center'><b>Total Members:</b> <?php echo $totalMembers; ?></td>
		</tr>
		<?php
			foreach($arrMemberCountCat as $key=>$value) {
				$rankCatObj->select($key);
				$rankCatInfo = $rankCatObj->get_info_filtered();
				
				$totalBars = round(($value/$totalMembers)*100);
				//$dispBars = "|".str_repeat("|", $totalBars);
				$dispBars = "<div class='solidBox' style='position: reltaive; padding: 0px; margin: 0px; width: 90%'><div class='tinyFont' style='width: ".$totalBars."%; background-color: ".$rankCatInfo['color']."'>&nbsp;</div></div>";
				
				echo "
					<tr>
						<td class='main' style='font-weight: bold; width: 40%'>Total ".$rankCatInfo['name'].":</td>
						<td class='main' style='font-weight: bold; width: 20%'>".$value." - ".(round($value/$totalMembers,2)*100)."%</td>
						<td class='main' style='width: 40%'>".$dispBars."</td>
					</tr>
				
				";
				
			}
		?>
	</table>
	

</div>
<div style='margin: 20px auto'>

	<table class='formTable' id='membersPageTable' style='width: 85%; margin-left: auto; margin-right: auto'>
		<tr>
			<td colspan='3' class='main dottedLine' align='center'><b>- Game Statistics -</b></td>
		</tr>
		<?php
			$arrGames = $gameObj->getGameList();
		
			$arrTotalGamesPlayed = array_count_values($arrGamesPlayed);
			foreach($arrGames as $value) {
				$gameObj->select($value);
				$gameInfo = $gameObj->get_info_filtered();
				
				if($arrTotalGamesPlayed[$value] == "") {
					$arrTotalGamesPlayed[$value] = 0;
				}
				
				$totalBars = round(($arrTotalGamesPlayed[$value]/$totalMembers))*100;
				$dispBars = "<div class='solidBox' style='position: reltaive; padding: 0px; margin: 0px; width: 90%'><div class='tinyFont alternateBGColor' style='width: ".$totalBars."%'>&nbsp;</div></div>";
				
				echo "
					<tr>
						<td class='main' style='font-weight: bold; width: 40%'>Total ".$gameInfo['name']." Players:</td>
						<td class='main' style='font-weight: bold; width: 20%'>".$arrTotalGamesPlayed[$value]." - ".(round($arrTotalGamesPlayed[$value]/$totalMembers,2)*100)."%</td>
						<td class='main' style='letter-spacing: -4px; width: 40%'>".$dispBars."</td>
					</tr>
				
				";
				
			}
		?>
	</table>
	

</div>
<div style='margin: 20px auto'>

<?php 
if($websiteInfo['maxdsl'] != 0) {
	
	echo "
	<table class='formTable' id='membersPageTable' style='width: 85%; margin-left: auto; margin-right: auto'>
		<tr>
			<td colspan='3' class='main dottedLine' align='center'><b>- Activity Statistics -</b></td>
		</tr>
		";
			for($i=1;$i<=3;$i++) {
				
				
				if($i == 1) {
					$dispTitle = "Low DSL";
					$dispColor = $websiteInfo['lowdsl'];
					$highEndDSL = $maxDSLIntervals;
					$lowEndDSL = 0;
					$extraDSLMessage = "These members are very safe from being disabled due to inactivity.";
				}
				elseif($i == 2) {
					$dispTitle = "Medium DSL";	
					$dispColor = $websiteInfo['meddsl'];
					$highEndDSL = $maxDSLIntervals*2;
					$lowEndDSL =  $maxDSLIntervals+1;
					$extraDSLMessage = "These members are somewhat safe from being disabled but should log in soon.";
				}
				else {
					$dispTitle = "High DSL";	
					$dispColor = $websiteInfo['highdsl'];
					$highEndDSL = $websiteInfo['maxdsl']-1;
					$lowEndDSL =  1+($maxDSLIntervals*2);
					$extraDSLMessage = "These members are in danger of being disabled due to inactivity.";
				}
				
				$totalBars = round(($arrCountDSL[$i]/$totalMembers)*100);
				$dispBars = "<div class='solidBox' style='position: reltaive; padding: 0px; margin: 0px; width: 90%'><div class='tinyFont' style='width: ".$totalBars."%; background-color: ".$dispColor."'>&nbsp;</div></div>";
				
				
				echo "
					<tr>
						<td class='main' style='font-weight: bold; width: 40%'>".$dispTitle." <span onmouseover=\"showToolTip('Members who logged in within the last ".$lowEndDSL." to ".$highEndDSL." days. ".$extraDSLMessage."')\" onmouseout='hideToolTip()' style='cursor: help'>(?)</span>:</td>
						<td class='main' style='font-weight: bold; width: 20%'>".$arrCountDSL[$i]." - ".(round($arrCountDSL[$i]/$totalMembers,2)*100)."%</td>
						<td class='main' style='letter-spacing: -4px; width: 40%'>".$dispBars."</td>
					</tr>
				
				";
			}
			echo "
				<tr>
					<td colspan='3' align='center' class='main'><br><br>
						If a member gets to <b><span style='color: ".$websiteInfo['highdsl']."'>".$websiteInfo['maxdsl']."</span></b> DSL, they will be disabled from the clan website.
					</td>
				</tr>
			
		
	</table>
	";
}
?>

</div>
<?php include($prevFolder."themes/".$THEME."/_footer.php"); ?>