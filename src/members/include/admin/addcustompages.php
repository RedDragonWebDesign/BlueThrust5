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

$customPageObj = new Basic($mysqli, "custompages", "custompage_id");
$countErrors = 0;
$dispError = "";
if($_POST['submit']) {
	
	
	if(trim($_POST['pagename']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a page name for your custom page.<br>";
	}
	
	
	if($countErrors == 0) {
		
		$_POST['wysiwygHTML'] = str_replace("<?", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("?>", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("&lt;?", "", $_POST['wysiwygHTML']);
		$_POST['wysiwygHTML'] = str_replace("?&gt;", "", $_POST['wysiwygHTML']);
		
		if($customPageObj->addNew(array("pagename", "pageinfo"), array($_POST['pagename'], $_POST['wysiwygHTML']))) {
			$intManageCustomPagesID = $consoleObj->findConsoleIDByName("Manage Custom Pages");
			$customPageInfo = $customPageObj->get_info();
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Added Custom Page: <b>".$customPageInfo['pagename']."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Add Custom Pages', '".$MAIN_ROOT."members/console.php?cID=".$intManageCustomPagesID."&cpID=".$customPageInfo['custompage_id']."&action=edit', 'successBox');
				</script>
			";
			
			
			
		}
		else {
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to add custom page.  Please try again!<br>";
			$_POST['submit'] = false;
			$_POST['wysiwygHTML'] = addslashes($_POST['wysiwygHTML']);
		}
		
	}
	else {
		$_POST['submit'] = false;
		$_POST['wysiwygHTML'] = addslashes($_POST['wysiwygHTML']);
	}
	
	
}


if(!$_POST['submit']) {
	
	$addMenuItemCID = $consoleObj->findConsoleIDByName("Add Menu Item");
	
	echo "
	<form action='console.php?cID=".$cID."' method='post'>
	<div class='formDiv'>
	
	";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to add custom page because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
		Fill out the form below to add a custom page.  In order to display a custom page in the menu, go to the <a href='".$MAIN_ROOT."members/console.php?cID=".$addMenuItemCID."'>Add Menu Item</a> page.
		<br><br>
		<table class='formTable'>
			<tr>
				<td class='formLabel'>Page Name:</td>
				<td class='main'><input type='text' name='pagename' class='textBox' value='".$_POST['pagename']."' style='width: 250px'></td>
			</tr>
			<tr>
				<td colspan='2' class='main'><br>
					<b>Page Information</b>
					<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					<div style='padding-left: 3px; padding-bottom: 15px'>
						Use the text editor below to format your custom page.  You may also use HTML by clicking the HTML button.  All formatting buttons are disabled when editing the HTML.
					</div>
				</td>
			</tr>
			<tr>
				<td colspan='2' style='padding-left: 10px' align='center'>
					<textarea id='tinymceTextArea' name='wysiwygHTML' style='width: 80%' rows='15'>".$_POST['wysiwygHTML']."</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
				<br>
				<input type='submit' class='submitButton' name='submit' value='Add Custom Page'>
				<br>
				</td>
			</tr>
		</table>
		</div>
		</form>
		
		

		<script type='text/javascript'>

			$('document').ready(function() {
				$('#tinymceTextArea').tinymce({
			
					script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
					theme: 'advanced',
					theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
					theme_advanced_resizing: true
				
				});
			
			});

	";
	
	if($dispError != "") {
		echo "
			$('#wysiwygDiv').html('".$_POST['wysiwygHTML']."');
			
		";
	}
	
	echo "

		</script>
	";
	
	
	
	
}


?>