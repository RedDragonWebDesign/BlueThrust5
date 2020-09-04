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

	if(defined("SHOW_ACCESSCACHE")) {
		
		
		$accessObj->arrAccessTables = json_decode($_SESSION['btAccessCacheTables'][$_POST['cacheID']], true);
		$accessObj->arrAccessTypes = json_decode($_SESSION['btAccessCacheTypes'][$_POST['cacheID']], true);
		
		if($_POST['accessType'] == "rank") {
			$accessObj->dispSetRankAccess(false);			
		}
		else {
			$accessObj->dispSetMemberAccess(false);
		}
		
		
	}


?>