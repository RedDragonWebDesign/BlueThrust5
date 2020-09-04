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

include_once("../../_setup.php");
include_once("../../classes/member.php");
$prevFolder = "../../";
include("twitter.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$twitterObj = new Twitter($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $twitterObj->hasTwitter($member->get_info("member_id"))) {
	
	$twitterObj->delete();
	
	echo "
		
		<div class='shadedBox' style='width: 75%; margin-left: auto; margin-right: auto'>
		
			<p class='main' style='padding: 20px'>
				Successfully disconnected your Twitter account!<br><br>
				To complete the process you must go to the <a href='https://twitter.com/settings/applications' target='_blank'>Application Settings</a> page in your Twitter account and click the revoke access button.
			</p>
		
		</div>
	
	";
	
}


?>