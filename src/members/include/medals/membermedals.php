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
include_once("../../../classes/medal.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$memberObj = new Member($mysqli);

$medalObj = new Medal($mysqli);

$medalOptions = "<option value=''>Select</option>";

if($member->authorizeLogin($_SESSION['btPassword']) && $memberObj->select($_POST['mID'])) {
	
	
	$arrMedals = $memberObj->getMedalList();
	
	foreach($arrMedals as $medalID) {
		
		$medalObj->select($medalID);
		$medalInfo = $medalObj->get_info_filtered();
		
		$medalOptions .= "<option value='".$medalInfo['medal_id']."'>".$medalInfo['name']."</option>";
		
	}
	
	
	
}

echo $medalOptions;


?>