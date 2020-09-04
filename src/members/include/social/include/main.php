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
	
	$objManageList->intAddCID = $consoleObj->findConsoleIDByName("Add Social Media Icon");
	$objManageList->strEditItemLink = MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&sID=";
	$objManageList->strDeleteLink = MAIN_ROOT."members/console.managelist.delete.php?cID=".$_GET['cID'];
	$objManageList->arrActionList = array("moveup", "movedown", "edit", "delete");
	$objManageList->strItemTitle = "Name:";
	
	$setupManageListArgs = $objManageList->getListArray();
	
?>