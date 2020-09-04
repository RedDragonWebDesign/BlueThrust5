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

include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/poll.php");

$consoleObj = new ConsoleOption($mysqli);
$pollObj = new Poll($mysqli);
$member = new Member($mysqli);

if(!$pollObj->select($_GET['pID'])) {
	echo "
		<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>
	";
	
	exit();
}

$viewPollResultsCID = $consoleObj->findConsoleIDByName("View Poll Results");
$consoleObj->select($viewPollResultsCID);

$pollInfo = $pollObj->get_info_filtered();

$member->select($_SESSION['btUsername']);
$blnMemberVoted = false;
if($member->authorizeLogin($_SESSION['btPassword']) && $pollObj->hasVoted($member->get_info("member_id"))) {
	$blnMemberVoted = true;	
}

if($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();	
}

$blnShowResults = false;
if($pollObj->totalVotes() > 0 && ($member->hasAccess($consoleObj) || $pollInfo['member_id'] == $memberInfo['member_id'] || $pollInfo['resultvisibility'] == "open" || ($pollInfo['resultvisibility'] == "votedonly" && $blnMemberVoted) || ($pollInfo['resultvisibility'] == "pollend" && $pollInfo['pollend'] < time()))) {
	$blnShowResults = true;
}



if($blnShowResults) {
	$arrResults = array("['Option', 'Votes']");
	$arrOptions = array();
	$x = 0;
	$countTotalVotes = 0;
	foreach($pollObj->getPollResults() as $pollOptionID => $votes) {
		$pollObj->objPollOption->select($pollOptionID);
		$pollOptionInfo = $pollObj->objPollOption->get_info_filtered();
		$arrResults[] = "['".$pollOptionInfo['optionvalue']."', ".$votes."]";
		$arrOptions[] = $x.": { color: '".$pollOptionInfo['color']."' }";
		$x++;
		$countTotalVotes += $votes;
	}
	
	$jsResults = implode(",", $arrResults);
	$jsSliceOptions = implode(",", $arrOptions);
	
	$googleChart = "
	 <script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>
	    <script type=\"text/javascript\">
	      google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = google.visualization.arrayToDataTable([
	          ".$jsResults."
	        ]);
	
	        var options = {
	          backgroundColor: { fill: 'none' },
	          is3D: true,
	          legend: 'none',
	          chartArea: { width: \"100%\", height: \"80%\", top: 0 },
	          slices: { ".$jsSliceOptions." }
	        };
	
	        var chart = new google.visualization.PieChart(document.getElementById('pollPieChart'));
	        chart.draw(data, options);
	      }
	    </script>
	";
	
	
	$EXTERNAL_JAVASCRIPT .= $googleChart;
}
// Start Page
$PAGE_NAME = "Poll - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");


$memberInfo = array();




$LOGGED_IN = false;
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;

}

$breadcrumbObj->setTitle("Poll Results");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Poll Results");
include($prevFolder."include/breadcrumb.php");

$member->select($pollInfo['member_id']);
$dispPollCreator = $member->getMemberLink();

$dispPollEnd = ($pollInfo['pollend'] == 0) ? "Never" : date("D M j, Y g:i a", $pollInfo['pollend']);

$dispPollAccess = "<span class='publicNewsColor'>Public</span>";
if($pollInfo['accesstype'] == "members") {
	$dispPollAccess = "<span class='pendingFont'>Members Only</span>";
}
elseif($pollInfo['accesstype'] == "memberslimited") {
	$dispPollAccess = "<span class='failedFont'>Limited</span>";
}

?>

<p class='largeFont' align='center'>
<?php echo $pollInfo['question']; ?>
</p>

<?php 
	if($blnShowResults) {
?>

<div class='pollContainer'>
	<div id='pollPieChart' class='pollChart'></div>
	<div class='pollLegend'>
		<b>Legend:</b><br><br>
		<?php 
		
			foreach($pollObj->getPollOptions() as $pollOptionID) {
				$pollObj->objPollOption->select($pollOptionID);
				$pollOptionInfo = $pollObj->objPollOption->get_info_filtered();
				echo "
					<div class='pollLegendSquare' style='background-color: ".$pollOptionInfo['color']."'></div><div class='pollLegendText'>".$pollOptionInfo['optionvalue']."</div><br>
				";
			}
			
		?>	
	</div>
	<div style='clear: both'></div>
</div>

<div class='pollInfoWrapper'>

	<b>Poll Information:</b>
	<div class='pollInfoDiv dashedBox'>
		<table class='formTable'>
			<tr>
				<td class='pollInfoLabel'>Created by: </td>
				<td class='main'><?php echo $dispPollCreator; ?></td>
			</tr>
			<tr>
				<td class='pollInfoLabel'>Date Created: </td>
				<td class='main'><?php echo getPreciseTime($pollInfo['dateposted']); ?></td>
			</tr>
			<tr>
				<td class='pollInfoLabel'>Poll Ends: </td>
				<td class='main'><?php echo $dispPollEnd; ?></td>
			</tr>
			<tr>
				<td class='pollInfoLabel'>Poll Access: </td>
				<td class='main'><?php echo $dispPollAccess; ?></td>
			</tr>
			<tr>
				<td class='pollInfoLabel'>Total Votes:</td>
				<td class='main'><?php echo $countTotalVotes; ?></td>
			</tr>
		<?php 
			if($pollInfo['lastedit_date'] != 0 && $member->select($pollInfo['lastedit_memberid'])) {
				echo "
					<tr>
						<td class='pollInfoLabel' valign='top'>Last edited by:</td>
						<td class='main'>".$member->getMemberLink()."<br>".getPreciseTime($pollInfo['lastedit_date'])."</td>
					</tr>
				";
			}
		?>
		</table>
	</div>
	<?php 
	
		
		if($pollInfo['displayvoters'] == 1 || ($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword']) && ($member->hasAccess($consoleObj) || $member->get_info("member_id") == $pollInfo['member_id']))) {
			echo "
				<br><br>
				<b>Voter Info:</b>
				<div class='pollInfoDiv dashedBox'>
					<table class='formTable'>
						";
			
			$memberVoters = 0;
			$counter = 0;
			foreach($pollObj->getVoterInfo() as $memberID => $voteInfo) {
				if($member->select($memberID)) {
					$memberVoters++;
					if($counter == 0) {
						$addCSS = "";
						$counter = 1;	
					}
					else {
						$addCSS = " alternateBGColor";
						$counter = 0;
					}
					
					$pollMemberInfo = $member->get_info_filtered();
					
					if($pollMemberInfo['profilepic'] == "") {
						$pollMemberInfo['profilepic'] = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
					}
					else {
						$pollMemberInfo['profilepic'] = $MAIN_ROOT.$pollMemberInfo['profilepic'];
					}
					
					$dispVoteDetails = "";
					$dispTimesVoted = 0;
					$dispLastVoted = 0;
					foreach($voteInfo as $pollOptionID => $info) {
						$pollObj->objPollOption->select($pollOptionID);
						
						$addS = ($info['votes'] > 1) ? "s" : "";
						
						$dispVoteDetails .= $pollObj->objPollOption->get_info_filtered("optionvalue").": ".$info['votes']." vote".$addS."<br>";
						
						$dispLastVoted = ($dispLastVoted < $info['lastvoted']) ? $info['lastvoted'] : $dispLastVoted;
						$dispTimesVoted += $info['votes'];
					}
					
					$addS = ($dispTimesVoted > 1) ? "s" : "";
										
					echo "
						<tr>
							<td class='pollProfilePic main".$addCSS."' valign='top'><img src='".$pollMemberInfo['profilepic']."' class='pollProfilePic'></td>
							<td class='main".$addCSS."' valign='top'>
								<span class='largeFont'>".$member->getMemberLink()."</span><br>
								<b>Voted:</b> ".$dispTimesVoted." time".$addS."<br>
								<b>Last Voted:</b> ".getPreciseTime($dispLastVoted)."<br>
								<b>Vote Details:</b><br>
								<p style='margin: 0px; padding-left: 10px'>
									".$dispVoteDetails."
								</p>
								
							</td>
						</tr>			
					";
				}
			}
			
			if($memberVoters == 0) {

				echo "
				
					<p align='center'>
						No members have voted.
					</p>
				
				";
				
			}
			
			
			echo "
					</table>
				</div>
			";
		}
		
	?>
</div>


<?php
	}
	else {

		if($pollObj->totalVotes() == 0) {
			$pollInfo['resultvisibility'] = "novotes";	
		}
		
		switch($pollInfo['resultvisibility']) {
			case "votedonly":
				$dispReason = ", you must vote first!";
				break;
			case "pollend":
				$dispReason = ", they will be displayed when the poll ends!";
				break;
			case "novotes":
				$dispReason = ", no one has voted yet!";
				break;
			default:
				$dispReason = ".";
		}
		
	
		
		
		echo "
		
			<div class='shadedBox' style='margin: 20px auto; width: 45%'>
				<p class='main' align='center'>
					<i>The results for this poll are unavailable".$dispReason."</i>
				</p>
			</div>
		
		";
		
	}
	
include($prevFolder."themes/".$THEME."/_footer.php");
?>
