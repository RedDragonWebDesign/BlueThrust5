<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("../../_setup.php");
require_once("../../classes/member.php");
$prevFolder = "../../";
require_once("youtube.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$ytObj = new Youtube($mysqli);

if ($member->authorizeLogin($_SESSION['btPassword']) && $ytObj->hasYoutube($member->get_info("member_id"))) {
	$ytObj->delete();

	echo "
		
		<div class='shadedBox' style='width: 50%; margin-left: auto; margin-right: auto'>
		
			<p align='center' class='main' style='padding: 20px'>
				Successfully disconnected your Youtube account!<br><br>
				<a href='".$MAIN_ROOT."members'>Return to My Account</a>
			</p>
		
		</div>
	
	";
}
