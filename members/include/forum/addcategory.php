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


include_once("../classes/basicorder.php");

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



$dispError = "";
$countErrors = 0;

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


if($_POST['submit']) {
	
	// Check Name
	if(trim($_POST['catname']) == "") {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Category name may not be blank.<br>";
		$countErrors++;	
	}
	
	
	// Check Order
	
	$intNewOrderSpot = $categoryObj->validateOrder($_POST['displayorder'], $_POST['beforeafter']);
	
	if($intNewOrderSpot === false) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid display order.<br>";
		$countErrors++;
	}
	
	
	if($countErrors == 0) {
		
		$arrColumns = array("name", "ordernum");
		$arrValues = array($_POST['catname'], $intNewOrderSpot);
		if($categoryObj->addNew($arrColumns, $arrValues)) {

			$forumCatInfo = $categoryObj->get_info_filtered();
			echo "
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully Added New Forum Category: <b>".$forumCatInfo['name']."</b>!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Add Forum Category', '".$MAIN_ROOT."members', 'successBox');
			</script>
			";
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save category to the database.  Please contact the website administrator.<br>";
		}
		
	}
	
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}
}


if(!$_POST['submit']) {
	
	$orderoptions = "";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."forum_category ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$orderoptions .= "<option value='".$row['forumcategory_id']."'>".filterText($row['name'])."</option>";
		
	}
	
	if($result->num_rows == 0) {
		$orderoptions = "<option value='first'>(first category)</option>";		
	}
	
	
	echo "
	
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add new forum category because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	
	echo "
				Use the form below to add a new forum category.<br>
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Category Name:</td>
						<td class='main'><input type='text' value='".$_POST['catname']."' name='catname' class='textBox' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Display Order:</td>
						<td class='main'>
							<select name='beforeafter' class='textBox'><option value='before'>Before</option><option value='after'>After</option></select><br>
							<select name='displayorder' style='margin-top: 3px' class='textBox'>".$orderoptions."</select>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' value='Add Category' style='width: 125px' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		
	";
	
}