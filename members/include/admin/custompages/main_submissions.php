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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];


if($cID == "") {
	$cID = $consoleObj->findConsoleIDByName("View Custom Form Submissions");
}


$intAddCustomPageID = $consoleObj->findConsoleIDByName("Add Custom Form Page");
$intManageCustomFormID = $consoleObj->findConsoleIDByName("Manage Custom Form Pages");

$counter = 0;
$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."customform ORDER BY name");
while($row = $result->fetch_assoc()) {

	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}


	$customFormPageObj->select($row['customform_id']);
	$totalUnseen = $customFormPageObj->countSubmissions(true);
	$totalSubmissions = $customFormPageObj->countSubmissions();
	
	
	$dispPages .= "
	<tr>
		<td class='dottedLine".$addCSS."' style='height: 24px' width=\"60%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&cfID=".$row['customform_id']."&action=edit'>".$row['name']."</a></b></td>
		<td align='center' style='height: 24px' class='dottedLine".$addCSS."' width=\"20%\">".$totalUnseen."</td>
		<td align='center' style='height: 24px' class='dottedLine".$addCSS."' width=\"20%\">".$totalSubmissions."</td>
			
	</tr>
	";
}

if($result->num_rows == 0) {

	$dispPages = "<tr><td colspan='3'><br><p align='center' class='main'><i>No custom form pages added yet!</i></p></td></tr>";
}


echo "
<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
	<tr>
		<td class='main' colspan='3' align='right'>
		&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCustomPageID."'>Add Custom Form Page</a> &laquo;&nbsp;&nbsp;&nbsp;&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intManageCustomFormID."'>Manage Custom Form Pages</a> &laquo;<br><br>
		</td>
	</tr>
	<tr>
		<td class='formTitle' width=\"60%\">Custom Page Name:</td>
		<td class='formTitle' width=\"20%\">Unseen Submissions:</td>
		<td class='formTitle' width=\"20%\">Total Submissions:</td>
	</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
	<tr><td colspan='3' class='dottedLine'></td></tr>
".$dispPages."
</table>

";



?>