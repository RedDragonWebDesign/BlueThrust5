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
$prevFolder = "../";

include($prevFolder."_setup.php");
include($prevFolder."classes/member.php");
include_once($prevFolder."classes/rank.php");



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
$PAGE_NAME = "Recruiters - Top Players - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);

$breadcrumbObj->setTitle("Top Players: Recruiters");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Top Players: Recruiters");

include($prevFolder."include/breadcrumb.php");


	$result = $mysqli->query("SELECT * FROM ".$dbprefix."members WHERE disabled = '0' AND rank_id != '1'");
	while($row = $result->fetch_assoc()) {
		$member->select($row['member_id']);
		
		$arrMembers[$row['member_id']] = $member->countRecruits();
		
	}
	
	
	if($_GET['sort'] != "up") {
		$dispSort = "<a href='".$MAIN_ROOT."top-players/recruiters.php?sort=up'><img src='".$MAIN_ROOT."themes/".$THEME."/images/downarrow.png'></a>";
		$_GET['sort'] = "down";
		arsort($arrMembers);
	}
	else {
		$dispSort = "<a href='".$MAIN_ROOT."top-players/recruiters.php?sort=down'><img src='".$MAIN_ROOT."themes/".$THEME."/images/uparrow.png'></a>";
		$_GET['sort'] = "up";
		asort($arrMembers);
	}
	
	
	echo "
		<table class='formTable' style='margin-top: 50px'>
			<tr>
				<td class='formTitle' align='center' style='width: 5%; height: 14px'>#</td>
				<td class='formTitle' style='width: 60%'>Member</td>
				<td class='formTitle' align='center' style='width: 35%'>Recruits - ".$dispSort."</td>
			</tr>
	";
	
	
	$counter = 0;
	foreach($arrMembers as $memberID => $statValue) {
		$counter++;
	
		$addCSS = "";
		if($counter%2 == 0) {
			$addCSS = " alternateBGColor";
		}
	
		$member->select($memberID);
		echo "
		<tr>
			<td class='main".$addCSS."' style='height: 30px'>".$counter.".</td>
			<td class='main".$addCSS."' style='height: 30px; padding-left: 20px'>".$member->getMemberLink()."</td>
			<td class='main".$addCSS."' align='center' style='height: 30px'>".$statValue."</td>
		</tr>
	
		";
	
	
		if($counter >= 10) {
			break;
		}
	}
	
	if($counter < 10) {
		for($i=($counter+1); $i<=10; $i++) {
			$addCSS = "";
			if($i%2 == 0) {
				$addCSS = " alternateBGColor";
			}
	
	
			echo "
			<tr>
				<td class='main".$addCSS."' style='height: 30px'>".$i.".</td>
				<td class='main".$addCSS."' style='height: 30px; padding-left: 20px'><i>Empty</i></td>
				<td class='main".$addCSS."' align='center' style='height: 30px'>-</td>
			</tr>
			";
		}
	}
	
	echo "</table>";
	include($prevFolder."themes/".$THEME."/_footer.php");
	
	?>