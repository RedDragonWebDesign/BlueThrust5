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
include_once($prevFolder."classes/rank.php");
include_once($prevFolder."classes/rankcategory.php");


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
$PAGE_NAME = "Ranks - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);
$rankObj = new Rank($mysqli);
$rankCatObj = new RankCategory($mysqli);

$breadcrumbObj->setTitle("Ranks");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Ranks");
include($prevFolder."include/breadcrumb.php");
?>


<table id='rankPageTable' class='formTable' style='width: 75%; margin-left: auto; margin-right: auto'>
<?php

$result = $mysqli->query("SELECT rankcategory_id FROM ".$dbprefix."rankcategory WHERE hidecat = '0' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$rankCatObj->select($row['rankcategory_id']);
	$rankCatInfo = $rankCatObj->get_info_filtered();
	if($rankCatInfo['useimage'] == 1 && $rankCatInfo['imageurl'] != "") {
		if($rankCatInfo['imagewidth'] == 0) {
			$imageURL = $rankCatObj->getLocalImageURL();
	
			$imageSize = getimagesize($imageURL);
			$rankCatInfo['imagewidth'] = $imageSize[0];
			
		}
		
		if($rankCatInfo['imageheight'] == 0) {
			$imageURL = $rankCatObj->getLocalImageURL();
			$imageSize = getimagesize($imageURL);
	
			$rankCatInfo['imageheight'] = $imageSize[1];
		}
		
		
		
		echo "
			<tr>
				<td class='main' align='center' colspan='2'><img src='".$rankCatInfo['imageurl']."' width='".$rankCatInfo['imagewidth']."' height='".$rankCatInfo['imageheight']."' onmouseover=\"showToolTip('<b>".$rankCatInfo['name']."</b><br>".$rankCatInfo['description']."')\" onmouseout='hideToolTip()'></td>
			</tr>
		";
	}
	else {
		
		$dispCatDesc = "";
		if($rankCatInfo['description'] != "") {
			$dispCatDesc = " style='cursor: pointer' onmouseover=\"showToolTip('<b>".$rankCatInfo['name']."</b><br>".$rankCatInfo['description']."')\" onmouseout='hideToolTip()'";	
		}
		
		echo "
			<tr>
				<td class='formTitle' align='center' colspan='2'><span".$dispCatDesc.">".$rankCatInfo['name']."</span></td>
			</tr>
		";
	}
	
	$arrRanks = $rankCatObj->getAssociateIDs("ORDER BY ordernum DESC");
	foreach($arrRanks as $rankID) {
		$rankObj->select($rankID);
		$rankInfo = $rankObj->get_info_filtered();
		
		if($rankInfo['hiderank'] == 0) {
			if($rankInfo['imagewidth'] == 0) {
				$imageURL = $rankObj->getLocalImageURL();
			
				$imageSize = getimagesize($imageURL);
				$rankInfo['imagewidth'] = $imageSize[0];
			
			}
			
			if($rankInfo['imageheight'] == 0) {
				$imageURL = $rankObj->getLocalImageURL();
				$imageSize = getimagesize($imageURL);
			
				$rankInfo['imageheight'] = $imageSize[1];
			}
			
			
			echo "
				<tr>
					<td align='center' valign='top' style='width: 50%'>
						<img src='".$rankInfo['imageurl']."' width='".$rankInfo['imagewidth']."' height='".$rankInfo['imageheight']."'>
					</td>
					<td valign='top' style='width: 50%' class='main'>
						<b>".$rankInfo['name']."</b><br>
						".nl2br($rankInfo['description'])."
					</td>
				</tr>
				<tr><td colspan='2'><br></td></tr>
			";
		}
		
	}
	echo "<tr><td colspan='2'><br></td></tr>";
	
}
echo "</table>";

include($prevFolder."themes/".$THEME."/_footer.php"); ?>