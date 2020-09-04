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
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}



echo "
<script type='text/javascript'>

$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Download Categories\");
});

</script>
";

include_once($prevFolder."classes/downloadcategory.php");

$cID = $_GET['cID'];


if($cID == "") {
	$cID = $consoleObj->findConsoleIDByName("Manage Download Categories");	
}


$intAddNewDownloadCatID = $consoleObj->findConsoleIDByName("Add Download Category");


$intHighestOrder = $downloadCatObj->getHighestOrderNum();
$counter = 0;
$x = 1;
$result = $mysqli->query("SELECT * FROM ".$dbprefix."downloadcategory ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}

	if($x == 1) {
		$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveDownloadCat('up', '".$row['downloadcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton' title='Move Up'></a>";
	}

	if($x == $intHighestOrder) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveDownloadCat('down', '".$row['downloadcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton' title='Move Down'></a>";
	}

	if($row['specialkey'] != "") {
		$dispDeleteButton = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	}
	else {
		$dispDeleteButton = "<a href='javascript:void(0)' onclick=\"deleteDownloadCat('".$row['downloadcategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' title='Delete Category'></a>";
	}
	
	
	$dispCats .= "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&catID=".$row['downloadcategory_id']."&action=edit'>".$row['name']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='console.php?cID=".$cID."&catID=".$row['downloadcategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit Category'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDeleteButton."</td>
	</tr>
	";

	$x++;
}


if($x == 1) {
	$dispCats = "<tr><td colspan='5'><br><p align='center' class='main'><i>No categories added yet!</i></p></td></tr>";
}

echo "


<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
	<tr>
		<td class='main' colspan='2' align='right'>
			&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewDownloadCatID."'>Add New Download Category</a> &laquo;<br><br>
		</td>
	</tr>
	<tr>
		<td class='formTitle' width=\"76%\">Category Name:</td>
		<td class='formTitle' width=\"24%\">Actions:</td>
	</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
	<tr>
		<td colspan='5' class='dottedLine'></td>
	</tr>
	".$dispCats."
</table>


";

?>