<?php
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}		
// CUSTOM PROFILE OPTIONS

$profileCatObj = new ProfileCategory($mysqli);
$profileOptionObj = new ProfileOption($mysqli);

$member->select($memberInfo['member_id']);

$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {

	$profileCatObj->select($row['profilecategory_id']);
	
	$arrProfileOptions = $profileCatObj->getAssociateIDs("ORDER BY sortnum");
	
	
	echo "
		<div class='formTitle' style='text-align: center; margin-top: 20px'>".$profileCatObj->get_info_filtered("name")."</div>
		<table class='profileTable' style='border-top-width: 0px'>
	";
	
	foreach($arrProfileOptions as $profileOptionID) {
		
		$profileOptionObj->select($profileOptionID);
		
		
		echo "
		
		<tr>
			<td class='profileLabel alternateBGColor' valign='top'>".$profileOptionObj->get_info_filtered("name").":</td>
			<td class='main' style='padding-left: 10px' valign='top'>".$member->getProfileValue($profileOptionID)."</td>
		</tr>
		
		";
		
	}
	
	echo "</table>";
}

?>