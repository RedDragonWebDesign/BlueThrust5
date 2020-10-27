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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$addAutoDisableMessage = "";
if($websiteInfo['maxdsl'] != 0) {
	$addAutoDisableMessage = " and will not be auto-disabled for not logging in after ".$websiteInfo['maxdsl']." days";
}

echo "
	<div class='formDiv'>
		Below is a listing of all member's who have requested to be inactive.<br><br>
		When a member is inactive they can still log in, but will not have access to any of their console options".$addAutoDisableMessage.".
		
		<div id='loadingSpiral' style='display: none'>
			<p align='center' class='main'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
		
		<div id='iaRequestList'>
		";		

include("include/inactiverequestlist.php");

echo "
		</div>
	</div>

";
	


?>