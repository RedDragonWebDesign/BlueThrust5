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
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Console Options\");
});

</script>
";


$cOptObj = new ConsoleOption($mysqli);
$intAddConsoleOptionsCID = $cOptObj->findConsoleIDByName("Add Console Option");

$intManageConsoleCatCID = $cOptObj->findConsoleIDByName("Manage Console Categories");


if($cID == "") {
	$cID = $cOptObj->findConsoleIDByName("Manage Console Options");
}

$arrConsoleCatIDs = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."consolecategory ORDER BY ordernum DESC");
while($row = $result->fetch_assoc()) {
	$arrConsoleCatIDs[] = $row['consolecategory_id'];
}

foreach($arrConsoleCatIDs as $consoleCatID) {
	$consoleCatObj->select($consoleCatID);
	$consoleCatInfo = $consoleCatObj->get_info_filtered();
	$catAssoc = $consoleCatObj->getAssociateIDs("ORDER BY sortnum");
	
	$dispConsoles .= "<tr><td class='dottedLine main' style='text-decoration: underline; padding-top: 5px; padding-bottom: 5px'><b>".$consoleCatInfo['name']."</b></td><td colspan='2' class='dottedLine' align='center'><a href='javascript:void(0)' onclick=\"addSeparator('".$consoleCatInfo['consolecategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/insertseparator1.png' title='Insert Separator in ".$consoleCatInfo['name']."'></td><td colspan='2' class='dottedLine' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intManageConsoleCatCID."&catID=".$consoleCatInfo['consolecategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Console Category'></a></tr>";
	$intHighestOrder = count($catAssoc);
	$counter = 0;
	$x = 1;
	foreach($catAssoc as $consoleID) {
		$consoleObj->select($consoleID);
		$consoleInfo = $consoleObj->get_info_filtered();
		
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
			$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveConsole('up', '".$consoleInfo['console_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
		}
		
		if($x == $intHighestOrder) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
		}
		else {
			$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveConsole('down', '".$consoleInfo['console_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
		}
		

		$dispConsoles .= "
		<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&cnID=".$consoleInfo['console_id']."&action=edit'>".$consoleInfo['pagetitle']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='console.php?cID=".$cID."&cnID=".$consoleInfo['console_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Console Option'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='javascript:void(0)' onclick=\"deleteConsole('".$consoleInfo['console_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Console Option'></a></td>
		</tr>
		";
		$x++;
	}
	
	$dispConsoles .= "<tr><td colspan='5' style='padding-top: 3px'></td></tr>";
}

echo "


<table class='formTable' style='border-spacing: 1px'>
	<tr>
		<td class='main' colspan='2' align='right'>
			&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddConsoleOptionsCID."'>Add New Console Option</a> &laquo;<br><br>
		</td>
	</tr>
	<tr>
		<td class='formTitle' width=\"76%\">Console Option Name:</td>
		<td class='formTitle' width=\"24%\">Actions:</td>
	</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
<tr><td colspan='5' class='dottedLine'></td></tr>
".$dispConsoles."
</table>


";

?>