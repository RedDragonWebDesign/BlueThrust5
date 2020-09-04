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
	
	$objManageList->intAddCID = $consoleObj->findConsoleIDByName("Add New Medal");
	$objManageList->strEditItemLink = MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&mID=";
	$objManageList->strDeleteLink = MAIN_ROOT."members/include/admin/medals/delete.php";
	$objManageList->arrActionList = array("moveup", "movedown", "edit", "delete");
	$objManageList->strItemTitle = "Medal Name:";

	$setupManageListArgs = $objManageList->getListArray();
	
?>