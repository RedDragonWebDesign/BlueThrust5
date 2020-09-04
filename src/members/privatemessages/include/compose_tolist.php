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


include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/consoleoption.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info_filtered();
	$pmSessionID = $_POST['pmSessionID'];
	
	$arrSearch = array("member", "squad", "rank", "tournament", "rankcategory");
	
	foreach($arrSearch as $search) {
		
		$stripWord = $search."_";
		
		if(substr($_POST['composeID'], 0, strlen($stripWord)) == $stripWord) {
			$composeID = str_replace($stripWord, "", $_POST['composeID']);
			
			if(is_numeric($composeID)) {
				
				
				if(isset($_POST['remove'])) {
				
					$removeKey = array_search($composeID, $_SESSION['btComposeList'][$pmSessionID][$search]);
					if($removeKey !== false) {
						unset($_SESSION['btComposeList'][$pmSessionID][$search][$removeKey]);	
					}
					
					
				}
				else {
					$_SESSION['btComposeList'][$pmSessionID][$search][] = $composeID;
				}
				
				echo $composeID;
			}
			
		}
		
	}
	
	print_r($_SESSION['btComposeList'][$pmSessionID]);
	
	
	
}



?>