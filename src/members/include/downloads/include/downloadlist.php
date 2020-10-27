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


if(!isset($member)) {
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/download.php");
	include_once("../../../../classes/downloadcategory.php");
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("Manage Downloads");
	$consoleObj->select($cID);
	
	
	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		$memberInfo = $member->get_info();
	}
	else {
		exit();
	}
	
	$downloadObj = new Download($mysqli);
	$downloadCatObj = new DownloadCategory($mysqli);
	
}


echo "
	<table class='formTable' style='margin-top: 0px; border-spacing: 0px'>
		<tr><td class='dottedLine' colspan='4'></td></tr>
";

$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory WHERE specialkey = '' ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrDownloadCat[$row['downloadcategory_id']] = filterText($row['name']);
}


$dispOrderBY = (isset($_POST['orderby']) && $_POST['orderby'] == "name") ? "ORDER BY name" : "ORDER BY dateuploaded ";
$dispOrderBY .= (isset($_POST['dir']) && $_POST['dir'] == "asc") ? "ASC" : "DESC";


$editCatCID = $consoleObj->findConsoleIDByName("Manage Download Categories");
$addDLCID = $consoleObj->findConsoleIDByName("Add Download");

$totalDownloads = 0;
foreach($arrDownloadCat as $catID => $catName) {
	$downloadCatObj->select($catID);
	$arrDownloads = $downloadCatObj->getAssociateIDs($dispOrderBY);
	
	if(count($arrDownloads) > 0) {
		
		echo "
			<tr>
				<td class='main manageList dottedLine' colspan='2' style='width: 76%'><b><u>".$catName."</u></b></td>
				<td class='main manageList dottedLine' align='center' style='width: 12%'><a href='".$MAIN_ROOT."members/console.php?cID=".$addDLCID."&catID=".$catID."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/add.png' class='manageListActionButton' title='Add Download to ".$catName."'></a></td>
				<td class='main manageList dottedLine' align='center' style='width: 12%'><a href='".$MAIN_ROOT."members/console.php?cID=".$editCatCID."&action=edit&catID=".$catID."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit ".$catName." Category'></a></td>
			</tr>
		";
		
		$altBGCount = 0;
		foreach($arrDownloads as $dlID) {
			$downloadObj->select($dlID);
			$dlInfo = $downloadObj->get_info_filtered();
			
			if($altBGCount == 0) {
				$addCSS = "";
				$altBGCount = 1;
			}
			else {
				$addCSS = " alternateBGColor";
				$altBGCount = 0;
			}
			
			$dispTime = getPreciseTime($dlInfo['dateuploaded']);
			
			echo "
				<tr>
					<td class='main manageList dottedLine".$addCSS."' style='width: 46%; padding-left: 10px; font-weight: bold'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&action=edit&dlID=".$dlID."'>".$dlInfo['name']."</a></td>
					<td class='main manageList dottedLine".$addCSS."' style='width: 30%; padding-left: 5px'>".$dispTime."</td>
					<td class='main manageList dottedLine".$addCSS."' align='center' style='width: 12%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&action=edit&dlID=".$dlID."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit Download'></a></td>
					<td class='main manageList dottedLine".$addCSS."' align='center' style='width: 12%'><a href='javascript:void(0)' onclick=\"deleteDL('".$dlID."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton'></a></td>
				</tr>
			";
			
			$totalDownloads++;
		}
		
		
	}
	
}

echo "</table>";


if($totalDownloads == 0) {
	
	echo "
	
		<div class='shadedBox' style='margin: 20px auto; width: 40%'>
			<p class='main' align='center'>
				<i>No downloads added yet!</i>
			</p>
		</div>
	
	";
	
}


?>