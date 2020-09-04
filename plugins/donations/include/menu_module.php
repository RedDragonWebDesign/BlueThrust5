<?php

	function donationManageMenuItem() {
		global $mysqli, $formObj;
		$menuItemObj = $formObj->objSave;
		
		
		if(isset($_GET['action']) && $_GET['action'] == "edit" && $menuItemObj->get_info("itemtype") == "donation") {
		
			$_POST['itemtype'] = "donation";
			$arrComponents = $formObj->components;
			
			$campaignID = $menuItemObj->get_info("itemtype_id");

			// Get Sort Order
			$sortOrder = $arrComponents['fakeSubmit']['sortorder'];
			
			
			$arrComponents['itemtype']['html'] = "<div class='formInput'><b>Donation Campaign</b></div>";
			
			// Donation Section Options
			$donationOptions = array();
			$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."donations_campaign WHERE dateend > '".time()."' OR dateend = '0' ORDER BY title");
			while($row = $result->fetch_assoc()) {
				$donationOptions[$row['donationcampaign_id']] = filterText($row['title']);			
			}
			
			if(count($donationOptions) == 0) {
				$donationOptions['none'] = "No Campaigns Running";	
			}
	
			$donationSectionOptions = array(
				"donation_campaign" => array(
					"type" => "select",
					"display_name" => "Select Campaign",
					"attributes" => array("class" => "formInput textBox"),
					"options" => $donationOptions,
					"value" => $campaignID	
				)		
			);
			
			
			// Add new section for donations
			$arrComponents['donationoptions'] = array(
				"type" => "section",
				"options" => array("section_title" => "Donation Campaign Options:"),
				"sortorder" => $sortOrder,
				"attributes" => array("id" => "donationCampaign"),
				"components" => $donationSectionOptions
			);
			
			
			
			$formObj->components = $arrComponents;
			$formObj->afterSave[] = "saveDonationMenuItem";
						
		}
		
	}
	
	function donationAddMenuItem() {
		global $mysqli, $formObj, $arrAfterJS, $arrItemTypeChangesJS;

		$arrComponents = $formObj->components;
		$menuItemObj = $formObj->objSave;
		
		// Get Sort Order
		$sortOrder = $arrComponents['fakeSubmit']['sortorder'];
		
		$arrComponents['fakeSubmit']['sortorder'] = $sortOrder+1;
		
		
		// Add donation campaign to list of item types
		
		$arrComponents['itemtype']['options']['donation'] = "Donation Campaign";
		
		
		// Donation Section Options
		$donationOptions = array();
		$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."donations_campaign WHERE dateend > '".time()."' OR dateend = '0' ORDER BY title");
		while($row = $result->fetch_assoc()) {
			$donationOptions[$row['donationcampaign_id']] = filterText($row['title']);			
		}
		
		if(count($donationOptions) == 0) {
			$donationOptions['none'] = "No Campaigns Running";	
		}

		$donationSectionOptions = array(
			"donation_campaign" => array(
				"type" => "select",
				"display_name" => "Select Campaign",
				"attributes" => array("class" => "formInput textBox"),
				"options" => $donationOptions		
			)		
		);
		
		
		// Add new section for donations
		$arrComponents['donationoptions'] = array(
			"type" => "section",
			"options" => array("section_title" => "Donation Campaign Options:"),
			"sortorder" => $sortOrder,
			"attributes" => array("id" => "donationCampaign", "style" => "display: none"),
			"components" => $donationSectionOptions
		);
		
		
		// Modify JS for new donation section
		
		$arrItemTypeChangesJS['donationCampaign'] = "donation";
		$arrAfterJS['itemType'] = prepareItemTypeChangeJS($arrItemTypeChangesJS);
		
		$afterJS = "

			$(document).ready(function() {
			";	
				
			foreach($arrAfterJS as $value) {
				$afterJS .= $value."\n";	
			}
				
		$afterJS .= "		
			});
			
		";
		
		
		// Apply new components to form
		$formObj->components = $arrComponents;
		$formObj->embedJS = $afterJS;
		$formObj->afterSave[] = "saveDonationMenuItem";
	
	}
	
	
	function saveDonationMenuItem() {
		
		if($_POST['itemtype'] != "donation") { return false; }
	
		global $menuItemObj;
	
		$menuItemObj->update(array("itemtype_id"), array($_POST['donation_campaign']));
		
	}
	
	function displayDonationMenuModule() {
		$menuItemInfo = $GLOBALS['menu_item_info'];
		if($menuItemInfo['itemtype'] != "donation") { return false; }
		
		global $mysqli;
		if(!class_exists("DonationCampaign")) {
			include(BASE_DIRECTORY."plugins/donations/classes/campaign.php");
		}
		
		$campaignObj = new DonationCampaign($mysqli);
		$donationObj = new btPlugin($mysqli);
		
		if($campaignObj->select($menuItemInfo['itemtype_id']) && $donationObj->selectByName("Donations")) {
			$progressBarColor = $donationObj->getConfigInfo("goalprogresscolor");
			$progressBarBackColor = $donationObj->getConfigInfo("goalprogressbackcolor");
			$campaignInfo = $campaignObj->get_info_filtered();
			$campaignDesc = $campaignObj->get_info("description");
			$dispCampaignDesc = substr($campaignDesc, 0, 100);
			$dispCampaignDesc = ($campaignDesc != $dispCampaignDesc) ? $dispCampaignDesc."..." : $dispCampaignDesc;
			
			$dispCampaignDesc = nl2br(parseBBCode(filterText($dispCampaignDesc)));
			
			$daysLeft = "";
			if(($campaignInfo['dateend'] != 0) || ($campaignInfo['dateend'] == 0 && $campaignInfo['currentperiod'] != 0)) {
				$daysLeft = $campaignObj->getDaysLeft();			
			}
			
			$dispGoal = "";
			if($campaignInfo['goalamount'] > 0) {

				// Graph
				$goalCompletePercent = round(($campaignObj->getTotalDonationAmount()/$campaignInfo['goalamount'])*100);
				$goalCompletePercent = ($goalCompletePercent > 100) ? "100%" : $goalCompletePercent."%";
				$dispGoal = " of ".$campaignObj->formatAmount($campaignInfo['goalamount'], 2)." goal";
				
				$dispProgressBar = "
					<div class='donationProgressContainer' style='background-color: ".$progressBarBackColor."'>
						<div style='width: ".$goalCompletePercent."; background-color: ".$progressBarColor."'></div>
					</div>
				";
				
			}
			
			$donationsInfo = $campaignObj->getDonationInfo();
			$totalDonations = count($donationsInfo);

			$donationsFormatted = $campaignObj->formatAmount($campaignObj->getTotalDonationAmount());
			
			$currentEndDate = $campaignObj->getCurrentEndDate();
			$dispEndingDate = "";
			if($currentEndDate != 0) {
				$dispExclaimation = ($daysLeft < 3) ? "!" : "";
				$dispEndingDate = "<div class='donateMenuItemStat'><b>".$campaignObj->getFormattedEndDate()." left".$dispExclaimation."</b></div>";
			}
			
			echo "
			
				<div class='donateMenuItemContainer'>
					<div class='donateMenuItemTitle'><a href='".$campaignObj->getLink()."'>".$campaignInfo['title']."</a></div>
					<div class='donateMenuItemDesc main'>
						".$dispCampaignDesc."					
					</div>
					<p align='center'>
						<a href='".$campaignObj->getLink()."'><input type='button' value='Donate!' class='submitButton'></a>
					</p>
					<div class='donateMenuItemStatsDiv main'>".$dispProgressBar."
						<div class='donateMenuItemStat'><b>".$donationsFormatted."</b><br>raised".$dispGoal."</div>
						".$dispEndingDate."
						<div style='clear: both'></div>
					</div>
			";

				if(count($donationsInfo) > 0) {
					
					echo "<p class='donateMenuItemTitle'><b>Latest Donators:</b></p>";	
					$campaignObj->showDonatorList(false, 2);					
					
				}
			
			echo "
				</div>
			
			";
			
		}
		
	}
	
	
	function initDonationMenuMod() {
		global $hooksObj, $mysqli, $btThemeObj;
		
		$modsConsoleObj = new ConsoleOption($mysqli);
		$modsManageMenusCID = $modsConsoleObj->findConsoleIDByName("Manage Menu Items");
		
		$modsAddMenusCID = $modsConsoleObj->findConsoleIDByName("Add Menu Item");
		
		$hooksObj->addHook("menu_item", "displayDonationMenuModule");
		$hooksObj->addHook("console-".$modsAddMenusCID, "donationAddMenuItem");
		$hooksObj->addHook("console-".$modsManageMenusCID, "donationManageMenuItem");
		
		$btThemeObj->addHeadItem("donation-css", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."plugins/donations/donations.css'>");
	}

	
	
	initDonationMenuMod();
?>