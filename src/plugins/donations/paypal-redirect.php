<?php

	include("../../_setup.php");
	include("classes/donate-paypalclass.php");
	include_once("classes/campaign.php");
	
	$donationPlugin = new btPlugin($mysqli);
	$campaignObj = new DonationCampaign($mysqli);
	$customVars = array();
	
	if($donationPlugin->selectByName("Donations") && $donationPlugin->getConfigInfo("email") != "" && $campaignObj->select($_GET['campaign_id'])) {
		
		
		$notifyURL = FULL_SITE_URL."plugins/donations/paypal-ipn.php";
		
		$p = new paypal_class();
		$member = new Member($mysqli);
		$campaignInfo = $campaignObj->get_info_filtered();
		
		$p->setMode($donationPlugin->getConfigInfo("mode"));
		
		$link = $p->paypal_url."?cmd=_donations";
		$_POST['business'] = $donationPlugin->getConfigInfo("email");
		$_POST['item_name'] = "Donation for ".$campaignInfo['title'];
		$_POST['notify_url'] = $notifyURL;
		$_POST['rm'] = 1;
		$_POST['return'] = FULL_SITE_URL."plugins/donations/?campaign_id=".$_GET['campaign_id']."&p=thankyou";
		if($campaignInfo['currency'] != "") {
			$_POST['currency_code'] = $campaignInfo['currency'];	
		}
		
		// Check For Custom Variables
		if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword']) && $member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
			
			$customVars['member_id'] = $member->get_info("member_id");
			
		}
		
		$customVars['campaign_id'] = $_GET['campaign_id'];
		$addToLink = "";
		$customVals = array("name", "message", "hideamount");
		$filterFormInputs = array("submit", "checkCSRF");
		if(($campaignInfo['minimumamount'] > 0 && $_POST['amount'] >= $campaignInfo['minimumamount']) || $campaignInfo['minimumamount'] <= 0) {
			foreach($_POST as $key => $value) {
	
				
				if(in_array($key, $customVals)) {
					$customVars[$key] = $value;
				}
				elseif(!in_array($key, $filterFormInputs)) {
					$addToLink .= "&".$key."=".$value;
				}
			}
			
			$jsonCustomVars = json_encode($customVars);
			$link .= "&custom=".$jsonCustomVars.$addToLink;
			
			//echo $link;
			header("Location: ".$link);
		}
		else {
			header("Location: ".$MAIN_ROOT."plugins/donations/?campaign_id=".$campaignInfo['donationcampaign_id']."&fail=amount");	
		}
		
	}
	else {

		echo "
		
			<script type='text/javascript'>
				window.location = '".MAIN_ROOT."';
			</script>
		
		";
		
	}

?>