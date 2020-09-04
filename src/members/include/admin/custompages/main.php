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


echo "
<script type='text/javascript'>

$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Custom Pages\");
});

</script>
";


$cID = $_GET['cID'];


if($cID == "") {
	$cID = $consoleObj->findConsoleIDByName("Manage Custom Pages");
}


$intAddCustomPageID = $consoleObj->findConsoleIDByName("Add Custom Page");


$counter = 0;
$result = $mysqli->query("SELECT * FROM ".$mysqli->get_tablePrefix()."custompages ORDER BY pagename");
while($row = $result->fetch_assoc()) {
	
	if($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	else {
		$addCSS = "";
		$counter = 1;
	}
	
	
	$dispPages .= "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"80%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&cpID=".$row['custompage_id']."&action=edit'>".$row['pagename']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='console.php?cID=".$cID."&cpID=".$row['custompage_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Custom Page'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"10%\"><a href='javascript:void(0)' onclick=\"deleteCustomPage('".$row['custompage_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Custom Page'></a></td>
	</tr>
	";
}

if($result->num_rows == 0) {
	
	$dispPages = "<tr><td colspan='3'><br><p align='center' class='main'><i>No custom pages added yet!</i></p></td></tr>";
}


echo "
<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
	<tr>
		<td class='main' colspan='2' align='right'>
		&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCustomPageID."'>Add Custom Page</a> &laquo;<br><br>
		</td>
	</tr>
	<tr>
		<td class='formTitle' width=\"80%\">Custom Page Name:</td>
		<td class='formTitle' width=\"20%\">Actions:</td>
	</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
	<tr><td colspan='3' class='dottedLine'></td></tr>
".$dispPages."
</table>


";

?>