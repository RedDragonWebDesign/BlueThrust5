<?php 
	
	if(!defined("MAIN_ROOT")) { exit(); } 
	global $donationPlugin, $campaignInfo, $campaignObj;
	
	
	$member = new Member($mysqli);
	$donationForm = new Form();

	include("include/donate_form.php");

	if($donationPlugin->getConfigInfo("mode") != "live") {

		echo "
			<div class='errorDiv'><p><strong>NOTE:</strong> This plugin is currently set to sandbox mode!  In order to properly receive donations it must be set to Live mode.</p></div>
		";
		
	}
	
	
	$donationsInfo = $campaignObj->getDonationInfo();
	$totalDonations = count($donationsInfo);

	$donationsFormatted = $campaignObj->formatAmount($campaignObj->getTotalDonationAmount());
	
	$dispGoal = "";
	if($campaignInfo['goalamount'] > 0) {
		$dispGoal = " of ".$campaignObj->formatAmount($campaignInfo['goalamount'], 2)." goal";
	
		// Graph
		$goalCompletePercent = round(($campaignObj->getTotalDonationAmount()/$campaignInfo['goalamount'])*100);
		$goalCompletePercent = ($goalCompletePercent > 100) ? "100%" : $goalCompletePercent."%";
		
	}
	
	$daysLeft = "";
	$dispEndDate = "";
	if(($campaignInfo['dateend'] != 0) || ($campaignInfo['dateend'] == 0 && $campaignInfo['currentperiod'] != 0)) {

		$currentEndDate = $campaignObj->getCurrentEndDate();
		$daysLeft = $campaignObj->getDaysLeft();
		$dispEndDate = "<div class='main' style='text-align: center'><br>This campaign will end on ".date("F j, Y", $currentEndDate)."</div>";
		
	}
	
	
?>

<div class='donationsLeft'>

	<?php $donationForm->show(); ?>
	
	<br>
	
	<div class='dottedLine donationMessagesSectionTitle'>Donation Messages:</div>
	<div class='donationMessagesDiv'>
	
		<?php $campaignObj->showMessagesList(); ?>
	
	</div>
	

</div>

<div class='donationsRight'>

	<div class='dottedLine largeFont' style='margin-top: 15px'><b>Donation Statistics:</b></div>
	<p class='numberCounts'><?php echo $totalDonations; ?></p>
	<p class='main'>donations</p>
	<br>
	<p class='numberCounts'><?php echo $donationsFormatted ?></p>
	<p class='main'>raised<?php echo $dispGoal; ?></p>
	<?php 
		if($dispGoal != "") {
			
			$dispDaysLeft = ($daysLeft != "") ? "<div class='donationsDaysLeft'>".$campaignObj->getFormattedEndDate()." left</div>" : "";
			
			$progressBarColor = $donationPlugin->getConfigInfo("goalprogresscolor") == "" ? "black" : $donationPlugin->getConfigInfo("goalprogresscolor");
			$progressBarBackColor = $donationPlugin->getConfigInfo("goalprogressbackcolor") == "" ? "gray" : $donationPlugin->getConfigInfo("goalprogressbackcolor");
			
			
			echo "
				<br>
				<div class='donationProgressContainer' style='background-color: ".$progressBarBackColor."'><div style='background-color: ".$progressBarColor."; width: ".$goalCompletePercent."'></div></div>
				<div class='main donationGoalText'>".$goalCompletePercent.$dispDaysLeft."</div>
				".$dispEndDate."
			";
			
		}
		elseif($daysLeft != "") {
			echo "
				<br>		
				<p class='numberCounts'>".$daysLeft."</p>		
				<p class='main'>".pluralize("day", $daysLeft)." left</p>		
			";
		}

		
		if($campaignInfo['description'] != "") {
			echo "
				<br>
				<div class='dottedLine largeFont' style='margin-top: 15px'><b>Campaign Description:</b></div>
				<div class='main' style='padding-top: 3px'>".$campaignInfo['description']."</div>
			";
		}
	
		$medalObj = new Medal($mysqli);
		if($campaignInfo['awardmedal'] != 0 && $medalObj->select($campaignInfo['awardmedal'])) {
			$medalInfo = $medalObj->get_info_filtered();			
			
			$dispStyle = $medalInfo['imagewidth'] != 0 ? "width: ".$medalInfo['imagewidth']."px;" : "";
			$dispStyle .= $medalInfo['imageheight'] != 0 ? "height: ".$medalInfo['imageheight']."px;" : "";
			
			$dispStyle = ($dispStyle != "") ? " style='".$dispStyle."'" : "";
			
			
			echo "
				<br>
				<div class='dottedLine largeFont' style='margin-top: 15px'><b>Member Reward:</b></div>
				<div class='main' style='padding-top: 3px'>Members who donate to this campaign will receive:</div>
				<br>
				<p class='main' align='center'><img src='".$medalInfo['imageurl']."'".$dispStyle."><br>".$medalInfo['name']."</p>
			";
		}
		
	?>
	<br>
	<div class='dottedLine largeFont' style='margin-top: 15px'><b>Donators:</b></div>
	<?php 
	
		$campaignObj->showDonatorList();
		
	?>
</div>

<div style='clear: both'></div>