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
include_once($prevFolder."classes/game.php");

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
$PAGE_NAME = "Inactive Members - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$member = new Member($mysqli);
$rankObj = new Rank($mysqli);
$gameObj = new Game($mysqli);

?>
<div class='breadCrumbTitle'>Inactive Members</div>
<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
	<a href='<?php echo $MAIN_ROOT; ?>'>Home</a> > Inactive Members
</div>

<table class='formTable'>
	<tr>
		<td class='formTitle'>Rank:</td>
		<td class='formTitle'>Username:</td>
		<td class='formTitle'>Main Game:</td>
		<td class='formTitle'>Inactive Since:</td>
	</tr>
<?php 

	$result = $mysqli->query("SELECT ".$dbprefix."members.member_id, ".$dbprefix."ranks.ordernum FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."members.onia = '1' AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.rank_id != '1' ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$member->select($row['member_id']);
		$rankObj->select($member->get_info("rank_id"));
		
		$memberListInfo = $member->get_info_filtered();
		$rankListInfo = $rankObj->get_info_filtered();
		
		$dispMainGame = "Not Set";
		if($gameObj->select($memberListInfo['maingame_id'])) {
			$gameObj->refreshImageSize();
			$gameInfo = $gameObj->get_info_filtered();
			$dispMainGame = "<img src='".$gameInfo['imageurl']."' width='".$gameInfo['imagewidth']."' height='".$gameInfo['imageheight']."' onmouseover=\"showToolTip('".$gameInfo['name']."')\" onmouseout='hideToolTip()'>";
		}

		
		echo "
			<tr>
				<td class='main' align='center'>
					<img src='".$rankListInfo['imageurl']."' width='".$rankListInfo['imagewidth']."' height='".$rankListInfo['imageheight']."' onmouseover=\"showToolTip('".$rankListInfo['name']."')\" onmouseout='hideToolTip()'>
				</td>
				<td class='main'>".$member->getMemberLink()."</td>
				<td class='main' align='center'>".$dispMainGame."</td>
				<td class='main' align='center'>".getPreciseTime($memberListInfo['inactivedate'])."</td>
			</tr>
		
		";
		
	}

?>
</table>

<?php 

if($result->num_rows > 0) {
	echo "
		<p align='center'>
			<b>Total Inactive Members:</b>	<?php echo $result->num_rows; ?>
		</p>
	";
}
else {
	
	echo "
	
		<div class='shadedBox' style='width: 40%; margin: 20px auto'>
			<p class='main' align='center'>
				There are currently no inactive members.
			</p>
		</div>
		
	";
	
}

include($prevFolder."themes/".$THEME."/_footer.php"); ?>