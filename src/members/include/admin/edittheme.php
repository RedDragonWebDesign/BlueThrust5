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


$headerCode = file_get_contents("../themes/".$THEME."/_header.php");
$footerCode = file_get_contents("../themes/".$THEME."/_footer.php");
$themeCSSCode = htmlspecialchars(file_get_contents("../themes/".$THEME."/style.css"));
//$globalCSSCode = htmlspecialchars(file_get_contents("../themes/".$THEME."/btcs4.css"));


$headerCode = str_replace("&", "&#38;", $headerCode);
$headerCode = str_replace("<", "&lt;", $headerCode);
$headerCode = str_replace(">", "&gt;", $headerCode);

$footerCode = str_replace("&", "&#38;", $footerCode);
$footerCode = str_replace("<", "&lt;", $footerCode);
$footerCode = str_replace(">", "&gt;", $footerCode);



$websiteSettingsCID = $consoleObj->findConsoleIDByName("Website Settings");

?>

<div class='formDiv'>


	Use the form below to edit the selected theme's header, footer and CSS files.  To change which theme you are using, go to the <a href='<?php echo $MAIN_ROOT; ?>members/console.php?cID=<?php echo $websiteSettingsCID; ?>'>Website Settings</a> page.<br><br>
	
	<div class='errorDiv' id='errorDiv' style='display: none'>
		<strong>Unable to save theme information because the following errors occurred:</strong><br><br>
		<span id='errorMessage'></span>
	</div>
	
	<table class='formTable'>
		<tr>
			<td colspan='2' class='main'>
				<b>Theme Information</b>
				<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
			</td>
		</tr>
		<tr>
			<td class='formLabel' colspan='2'>
				Header:
			</td>
		</tr>
		<tr>
			<td colspan='2'>
			
				<div style='position: relative; background-color: white'>
					<div class='codeEditor' id='headerEditor'><?php echo $headerCode; ?></div>
				</div>
			
				<?php //<textarea cols='68' rows='12' id='headercode'><?php echo $headerCode; </textarea><br><br> ?>
			</td>
		</tr>
		<tr>
			<td class='formLabel' colspan='2'>
				Footer:
			</td>
		</tr>
		<tr>
			<td colspan='2'>
			
				<div style='position: relative; background-color: white'>
					<div class='codeEditor' id='footerEditor'><?php echo $footerCode; ?></div>
				</div>
				<?php //<textarea cols='68' rows='12' id='footercode'><?php echo $footerCode; </textarea><br><br> ?>
			</td>
		</tr>
		<tr>
			<td class='formLabel' colspan='2'>
				Theme CSS:
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div style='position: relative; background-color: white'>
					<div class='codeEditor' id='themeCSSEditor'><?php echo $themeCSSCode; ?></div>
				</div>
			
				<?php //<textarea cols='68' rows='12' id='themecsscode'><?php echo $themeCSSCode; </textarea><br><br> ?>
			</td>
		</tr>
		
		<?php 
		/*
		<tr>
			<td id='globalcss' class='formLabel' colspan='2'>
				Global CSS:
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div style='position: relative; background-color: white'>
					<div class='codeEditor' id='globalCSSEditor'><?php echo $globalCSSCode; ?></div>
				</div>
			
				<?php //<textarea cols='68' rows='12' id='globalcsscode'><?php echo $globalCSSCode; </textarea><br><br> ?>
			</td>
		</tr>
		*/
		?>
		<tr>
			<td colspan='2' class='main'><br>
				<b>Security</b>
				<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
				<div style='padding-left: 3px; padding-bottom: 15px'>
					For security purposes, please enter the admin key that is set in the config file.
				</div>
			</td>
		</tr>
		<tr>
			<td class='formLabel'>Admin Key:  <a href='javascript:void(0)' onmouseover="showToolTip('For extra security, please enter the admin key that is set in the config file.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='password' class='textBox' style='width: 100px' id='checkadmin'></td>
		</tr>
		<tr>
			<td colspan='2' align='center'>
				<br><br>
				<input type='button' onclick='editTheme()' id='submit' class='submitButton' value='Edit Theme'>
			</td>
		</tr>	
		<tr>
			<td colspan='2' align='center'>
				<p align='center' class='main'><span id='loadingspiral' style='display: none'><br><img src='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/images/loading-spiral2.gif' style='margin-bottom: 5px'><br><i>Saving...</i></span><span id='saveMessage'></span></p>
			</td>
		</tr>
	</table>


</div>

<div id='postResponse' style='display: none'></div>

<script type='text/javascript'>

	var headerEditor = ace.edit("headerEditor");
	var footerEditor = ace.edit("footerEditor");
	var themeCSSEditor = ace.edit("themeCSSEditor");
	//var globalCSSEditor = ace.edit("globalCSSEditor");

	
	headerEditor.getSession().setMode("ace/mode/php");
	footerEditor.getSession().setMode("ace/mode/php");
	themeCSSEditor.getSession().setMode("ace/mode/css");
	//globalCSSEditor.getSession().setMode("ace/mode/css");

	headerEditor.setTheme("ace/theme/eclipse");
	footerEditor.setTheme("ace/theme/eclipse");
	themeCSSEditor.setTheme("ace/theme/dreamweaver");
	//globalCSSEditor.setTheme("ace/theme/dreamweaver");
	
	headerEditor.setHighlightActiveLine(false);
	footerEditor.setHighlightActiveLine(false);
	themeCSSEditor.setHighlightActiveLine(false);
	//globalCSSEditor.setHighlightActiveLine(false);

	headerEditor.setShowPrintMargin(false);
	footerEditor.setShowPrintMargin(false);
	themeCSSEditor.setShowPrintMargin(false);
	//globalCSSEditor.setShowPrintMargin(false);

	
	function editTheme() {

		$(document).ready(function() {
			
			$('#loadingspiral').show();

			$.post("<?php echo $MAIN_ROOT; ?>members/include/admin/edittheme_submit.php", { checkadmin: $('#checkadmin').val(), headerCode: headerEditor.getValue(), footerCode: footerEditor.getValue(), themeCSSCode: themeCSSEditor.getValue() }, function(data) {
				
				$('#postResponse').html(data);
				$('#loadingspiral').hide();
				
			});

			
		});

	}
</script>