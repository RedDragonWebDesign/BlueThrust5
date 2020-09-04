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

if(!defined("SHOW_FOLDERLIST")) {
	exit();
}


echo "
	<table class='formTable'  style='margin-top: 0px; border-spacing: 0px'>
		<tr><td class='dottedLine' colspan='5'></td></tr>
	";

$pmFolderObj->setCategoryKeyValue($memberInfo['member_id']);
$intHighestOrder = $pmFolderObj->getHighestSortNum();
$arrFolderList = $pmFolderObj->listFolders($memberInfo['member_id']);
$x = 0;
$counter = 0;
foreach($arrFolderList as $folderID => $folderName) {
	$pmFolderObj->select($folderID);
	$pmFolderInfo = $pmFolderObj->get_info();
	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}
	
	$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveFolder('up', '".$folderID."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton' title='Move Up'></a>";
	$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveFolder('down', '".$folderID."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton' title='Move Down'></a>";
	
	if($x == 0) {
		$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";	
	}
	

	if($intHighestOrder == $pmFolderInfo['sortnum']) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height='24'>";
	}	
	
	
	echo "
		<tr>
			<td class='main manageList dottedLine".$addCSS."' style='width: 76%; padding-left: 10px'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&fID=".$folderID."&action=edit'>".filterText($folderName)."</a></b></td>
			<td class='main manageList dottedLine".$addCSS."' style='width: 6%'>".$dispUpArrow."</td>
			<td class='main manageList dottedLine".$addCSS."' style='width: 6%'>".$dispDownArrow."</td>
			<td class='main manageList dottedLine".$addCSS."' style='width: 6%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&fID=".$folderID."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit Folder'></td>
			<td class='main manageList dottedLine".$addCSS."' style='width: 6%'><a href='javascript:void(0)' onclick=\"deleteFolder('".$folderID."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' title='Delete Folder'></a></td>
		</tr>	
	";
	
	$x++;
}

echo "
	</table>
";


if($x == 0) {
	
	echo "
		<div class='shadedBox' style='margin-top: 20px; width: 45%; margin-left: auto; margin-right: auto'>
			<p class='main' align='center'>
				<i>No PM Folders added yet!</i>
			</p>
		</div>
	
	";
	
}

?>