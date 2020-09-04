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
$PAGE_NAME = "Squads - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle("Squads");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Squads");
include($prevFolder."include/breadcrumb.php");

?>

		
<div style='margin: 0px auto; '>
<table class='formTable' style='margin-left: auto; margin-right: auto'>

	<tr>
		<td class='formTitle' width="30%">Squad Name:</td>
		<td class='formTitle' width="25%">Founder:</td>
		<td class='formTitle' width="25%">Date Created:</td>
		<td class='formTitle' width="20%">Status:</td>
	</tr>
	<?php
		$memberObj = new Member($mysqli);
		$counter = 0;
		$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."squads ORDER BY name");
		while($row = $result->fetch_assoc()) {
			
			
			if($row['recruitingstatus'] == 1) {
				$dispRecruiting = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/bluedot.png' title='Recruiting Open'>";	
			}
			else {
				$dispRecruiting = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/graydot.png' title='Recruiting Closed'>";
			}
			
			$memberObj->select($row['member_id']);
			$dispMemberLink = $memberObj->getMemberLink();
			
			if($counter == 1) {
				$addCSS = " alternateBGColor";
				$counter = 0;
			}
			else {
				$addCSS = "";
				$counter = 1;
			}
			
			echo "
				<tr>
					<td class='main ".$addCSS."'><a href='".$MAIN_ROOT."squads/profile.php?sID=".$row['squad_id']."'>".filterText($row['name'])."</a></td>
					<td class='main ".$addCSS."'>".$dispMemberLink."</td>
					<td class='main ".$addCSS."' align='center'>".getPreciseTime($row['datecreated'], "M j, Y g:i a")."</td>
					<td class='main ".$addCSS."' align='center'>".$dispRecruiting."</td>
				</tr>
			";
		}
		
		if($result->num_rows == 0) {
			
			echo "
				<tr>
					<td class='main' colspan='4'>
						<p align='center'>
							There are currently no squads created for this clan!
						</p>
					</td>
				</tr>
			
			";
			
		}
		
		
	?>
	<tr>
		<td class='main' colspan='4' valign='top'>
			<p align='center' style='margin-top: 50px'>
				<b>Total Squads:</b> <?php echo $result->num_rows; ?><br><br>
				<b>Key:</b> Recruiting Open - <img src='<?php echo $MAIN_ROOT."themes/".$THEME; ?>/images/bluedot.png' style='vertical-align: middle'> | Recruiting Closed - <img src='<?php echo $MAIN_ROOT."themes/".$THEME; ?>/images/graydot.png' style='vertical-align: middle'>
			</p>
		</td>
	</tr>
</table>
</div>

<?php

include($prevFolder."themes/".$THEME."/_footer.php");


?>