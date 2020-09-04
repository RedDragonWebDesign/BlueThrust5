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


if(!$customPageObj->select($_GET['cpID'])) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}


$customPageInfo = $customPageObj->get_info_filtered();

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Manage Custom Pages</a> > ".$customPageInfo['pagename']."\");
});
</script>
";


$customPageHTML = addslashes($customPageObj->get_info("pageinfo"));
$addMenuItemCID = $consoleObj->findConsoleIDByName("Add Menu Item");

echo "
	<form action='console.php?cID=".$cID."' method='post'>
		<div class='formDiv'>
			<div class='errorDiv' id='errorDiv' style='display: none'>
				<strong>Unable to edit custom page because the following errors occurred:</strong><br><br>
				<span id='errorInfo'></span>
			</div>
			Use the form below to edit the selected custom page.  In order to display a custom page in the menu, go to the <a href='".$MAIN_ROOT."members/console.php?cID=".$addMenuItemCID."'>Add Menu Item</a> page.
			<br><br>
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Page Name:</td>
					<td class='main'><input type='text' id='pagename' class='textBox' value='".$customPageInfo['pagename']."' style='width: 250px'></td>
				</tr>
				<tr>
					<td class='formLabel'>Page URL: <a href='javascript:void(0)' onmouseover=\"showToolTip('This is the URL to use in your link to this custom page.  You cannot edit this field.')\" onmouseout='hideToolTip()'>(?)</a></td>
					<td class='main'><input type='text' class='textBox' onclick=\"$(this).select()\" value='".$MAIN_ROOT."custompage.php?pID=".$customPageInfo['custompage_id']."' style='width: 250px'></td>
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
						<textarea id='tinymceTextArea' name='wysiwygHTML' rows='15' style='width: 80%'>".$customPageInfo['pageinfo']."</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
					<br>
					<input type='button' class='submitButton' onclick='editCustomPage()' name='submit' value='Save' style='width: 125px'>
					<br>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<p align='center' class='main'><span id='loadingspiral' style='display: none'><br><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif' style='margin-bottom: 5px'><br><i>Saving...</i></span><span id='saveMessage'></span></p>
					</td>
				</tr>
			</table>
		</div>
	</form>
	
	
	<div id='postResponse' style='display: none'></div>
	
	<script type='text/javascript'>
		
		$('document').ready(function() {
			$('#tinymceTextArea').tinymce({
		
				script_url: '".$MAIN_ROOT."js/tiny_mce/tiny_mce.js',
				theme: 'advanced',
				theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,code,|,forecolorpicker,fontselect,fontsizeselect',
				theme_advanced_resizing: true
			
			});
		
		});

		function editCustomPage() {
			
			$('#htmlButton').click();
			$('#wysiwygHTML').val($('#wysiwygDiv').html());
			$('#loadingspiral').show();
			
			if($('#wysiwygHTML').is(\":visible\")) {
				$('#htmlButton').click();
				$('#htmlButton').mouseout();
			}
			
			$(document).ready(function() {
				$.post('".$MAIN_ROOT."members/include/admin/custompages/edit_submit.php', {
				wysiwygHTML: $('#tinymceTextArea').val(), pagename: $('#pagename').val(), cpID: '".$_GET['cpID']."'
				}, function(data) {
					$('#postResponse').html(data);
				});
			
			});
			
			
			
		}

	</script>
";
	

?>