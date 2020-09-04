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
$PAGE_NAME = "Diplomacy - ";
include($prevFolder."themes/".$THEME."/_header.php");

$diplomacyStatusObj = new BasicOrder($mysqli, "diplomacy_status", "diplomacystatus_id");

$breadcrumbObj->setTitle("Diplomacy");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Diplomacy");
?>

<div class='breadCrumbTitle'>Diplomacy</div>
<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
	<a href='<?php echo $MAIN_ROOT; ?>'>Home</a> > Diplomacy
</div>
		
<div style='margin: 0px auto; '>
<table class='formTable' style='margin-left: auto; margin-right: auto'>

	<tr>
		<td class='formTitle' width="45%">Clan Name:</td>
		<td class='formTitle' width="30%">Leader(s):</td>
		<td class='formTitle' width="25%">Status:</td>
	</tr>
	
	
	<?php
		$counter = 0;
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy ORDER BY clanname");
		while($row = $result->fetch_assoc()) {
			$diplomacyStatusObj->select($row['diplomacystatus_id']);
			
			$statusInfo = $diplomacyStatusObj->get_info_filtered();

			
			if($statusInfo['imageurl'] == "") {
				$dispStatus = $statusInfo['name'];	
			}
			else {
				
				if(strpos($statusInfo['imageurl'], "http://") === false) {
					$statusInfo['imageurl'] = "../".$statusInfo['imageurl'];	
				}
				
				
				$dispImgWidth = "";
				$dispImgHeight = "";
				if($statusInfo['imagewidth'] != 0) {
					$dispImgWidth = " width = '".$statusInfo['imagewidth']."' ";	
				}
				
				if($statusInfo['imageheight'] != 0) {
					$dispImgWidth = " height = '".$statusInfo['imageheight']."' ";
				}
				
				$dispStatus = "<img src='".$statusInfo['imageurl']."'".$dispImgWidth.$dispImgHeight." title='".$statusInfo['name']."'>";
				
			}
			
			$addCSS = "";
			if($counter%2 == 0) {
				$addCSS = " alternateBGColor";
			}
			$counter++;
			
			echo "
				<tr>
					<td class='main".$addCSS."' style='padding: 3px'><a href='info.php?dID=".$row['diplomacy_id']."'>".filterText($row['clanname'])."</a></td>
					<td class='main".$addCSS."' style='padding: 3px' align='center'>".filterText($row['leaders'])."</td>
					<td class='main".$addCSS."' style='padding: 3px' align='center'>".$dispStatus."</td>
				</tr>
			
			";
			
		}
	
	?>
	
</table>

<?php
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."diplomacy_status WHERE imageurl != '' ORDER BY ordernum DESC");
	$counter = 0;
	if($result->num_rows > 0) {

		echo "
		
			<div style='margin-top: 50px; margin-left: auto; margin-right: auto'>

				<table class='formTable' align='center' style='width: 300px; margin-left: auto; margin-right: auto'>
					<tr>
						<td colspan='2' class='formTitle' align='center'>Status Key</td>
					</tr>
			";
		
		while($row = $result->fetch_assoc()) {
			
			
			if(strpos($row['imageurl'], "http://") === false) {
				$row['imageurl'] = "../".$row['imageurl'];
			}
			
			$dispImgWidth = "";
			$dispImgHeight = "";
			if($statusInfo['imagewidth'] != 0) {
				$dispImgWidth = " width = '".$statusInfo['imagewidth']."' ";
			}
			
			if($statusInfo['imageheight'] != 0) {
				$dispImgWidth = " height = '".$statusInfo['imageheight']."' ";
			}
			
			
			$addCSS = "";
			if($counter%2 == 0) {
				$addCSS = " alternateBGColor";
			}
			$counter++;
			
			echo "
				<tr>
					<td class='main".$addCSS."' style='width: 200px' align='center'>".filterText($row['name'])."</td>
					<td class='main".$addCSS."' style='width: 100px' align='center'><img src='".filterText($row['imageurl'])."'".$dispImgWidth.$dispImgHeight."></td>
				</tr>
			";
		}
		
		echo "
			</table>
			</div>
		
		";
	}
		
?>
</div>


<?php

include($prevFolder."themes/".$THEME."/_footer.php");


?>