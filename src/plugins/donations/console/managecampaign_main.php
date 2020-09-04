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

	if(!defined("LOGGED_IN") || !LOGGED_IN) { die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."'</script>"); }
	
	$objManageList->intAddCID = $consoleObj->findConsoleIDByName("Create a Donation Campaign");
	$objManageList->strEditItemLink = MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&campaignID=";
	$objManageList->strDeleteLink = MAIN_ROOT."members/console.managelist.delete.php?cID=".$_GET['cID'];
	$objManageList->arrActionList = array("edit", "delete", "dispDonationLogIcon");
	$objManageList->strItemTitle = "Campaign:";
	$objManageList->orderBy = "title";
	$objManageList->strNameTableColumn = "title";
	
	$setupManageListArgs = $objManageList->getListArray();
	
	
	function dispDonationLogIcon($campaignID) {
		
		return "<a href='".MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&campaignID=".$campaignID."&p=log' title='View Donation Log'><img src='".MAIN_ROOT."plugins/donations/money.png' class='manageListActionButton'></a>";
		
	}
	
?>