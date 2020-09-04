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


// Config File
$prevFolder = "";

include($prevFolder."_setup.php");
include($prevFolder."classes/member.php");
include_once($prevFolder."classes/medal.php");



// Classes needed for index.php


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


// Start Page
$PAGE_NAME = "Medals - ";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);
$medalObj = new Medal($mysqli);

$breadcrumbObj->setTitle("Medals");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Medals");
include($prevFolder."include/breadcrumb.php");
?>


<table class='formTable' style='width: 75%; margin-left: auto; margin-right: auto'>
	<tr>
		<td class='formTitle' style='width: 40%'>Medal:</td>
		<td class='formTitle' style='width: 60%'>Description:</td>
	</tr>
<?php

$result = $mysqli->query("SELECT medal_id FROM ".$dbprefix."medals ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	
	$medalObj->select($row['medal_id']);
	$medalObj->refreshImageSize();
	
	$medalInfo = $medalObj->get_info_filtered();
	
	echo "
		
		<tr>
			<td class='main' align='center' valign='top'>
				<img src='".$medalInfo['imageurl']."' width='".$medalInfo['imagewidth']."' height='".$medalInfo['imageheight']."'>
			</td>
			<td class='main' valign='top'>
				<b>".$medalInfo['name']."</b><br>
				".nl2br($medalInfo['description'])."
			</td>
		</tr>
		<tr><td colspan='2'><br></td></tr>
	";
	
}


echo "</table>";

include($prevFolder."themes/".$THEME."/_footer.php"); ?>