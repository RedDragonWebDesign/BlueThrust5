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
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];


$categoryInfo = $categoryObj->get_info_filtered();

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members/index.php?select=".$consoleInfo['consolecategory_id']."'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > ".$categoryInfo['name']."\");
});
</script>
";


if($_POST['submit']) {
	
	// Check Name
	if(trim($_POST['catname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Category name may not be blank.<br>";
		$countErrors++;
	}
	
	
	// Check Order
	
	$intNewOrderSpot = $categoryObj->validateOrder($_POST['displayorder'], $_POST['beforeafter'], true, $categoryInfo['ordernum']);
	
	if($intNewOrderSpot === false) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		$countErrors++;
	}
	
	
	if($countErrors == 0) {
	
		$arrColumns = array("name", "ordernum");
		$arrValues = array($_POST['catname'], $intNewOrderSpot);
		$categoryObj->select($categoryInfo['forumcategory_id']);
		if($categoryObj->update($arrColumns, $arrValues)) {
	
			$forumCatInfo = $categoryObj->get_info_filtered();
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Edited Forum Category: <b>".$forumCatInfo['name']."</b>!
					</p>
				</div>
		
				<script type='text/javascript'>
					popupDialog('Edit Forum Category', '".$MAIN_ROOT."members', 'successBox');
				</script>
			";
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save category to the database.  Please contact the website administrator.<br>";
		}
	
		$categoryObj->resortOrder();
	}
	
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;
	}
	
	
}



if(!$_POST['submit']) {
	
	$arrDisplayOrder = $categoryObj->findBeforeAfter();
	$dispSelected = "";
	if($arrDisplayOrder[1] == "after") {
		$dispSelected = " selected";
	}
	
	$orderoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$selectCat = "";
		if($arrDisplayOrder[0] == $row['forumcategory_id']) {
			$selectCat = " selected";	
		}
		$orderoptions .= "<option value='".$row['forumcategory_id']."'".$selectCat.">".filterText($row['name'])."</option>";	
	}
	
	
	if($result->num_rows == 0) {
		$orderoptions = "<option value='first'>(first category)</option>";	
	}
	
	echo "
	
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."&catID=".$_GET['catID']."&action=edit' method='post'>
		<div class='formDiv'>
		";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to edit forum category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
			Use the form below to edit the selected forum category.<br>
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Category Name:</td>
					<td class='main'><input type='text' value='".$categoryInfo['name']."' name='catname' class='textBox' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Display Order:</td>
					<td class='main'>
						<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'".$dispSelected.">After</option></select><br>
						<select name='displayorder' style='margin-top: 3px' class='textBox'>".$orderoptions."</select>
					</td>
				</tr>
				<tr>
					<td class='main' colspan='2' align='center'><br>
						<input type='submit' name='submit' value='Edit Category' style='width: 125px' class='submitButton'>
					</td>
				</tr>
			</table>
		</div>
		</form>
	
	";
	
}




?>