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
include("facebook.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$fbObj = new Facebook($mysqli);

if($member->authorizeLogin($_SESSION['btPassword']) && $fbObj->hasFacebook($member->get_info("member_id"))) {
	
	$fbObj->accessToken = $fbObj->get_info_filtered("access_token");
	$fbObj->getFBInfo();
	
	$fbObj->delete();
	
	echo "
		
		<div class='shadedBox' style='width: 50%; margin-left: auto; margin-right: auto'>
		
			<p align='center' class='main' style='padding: 20px'>
				Successfully disconnected your Facebook account!<br><br>
				<a href='".$MAIN_ROOT."members'>Return to My Account</a>
			</p>
		
		</div>
	
	";
	
}


?>