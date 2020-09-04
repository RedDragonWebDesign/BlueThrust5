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


// Get themes

$arrThemes = scandir("../themes");
$themeOptions = "";


foreach($arrThemes as $themeName) {
	
	$themeURL = "../themes/".$themeName;
	
	if(is_dir($themeURL) && $themeName != "." && $themeName != "..") {
		
		$dispThemeName = "";
		if(is_readable($themeURL."/THEMENAME.txt")) {
			$dispThemeName = file_get_contents($themeURL."/THEMENAME.txt");
		}
		
		$dispSelected = "";
		
		if($themeName == $websiteInfo['theme']) {
			$dispSelected = " selected";	
		}
		
		if($dispThemeName != "") {
			$themeOptions .= "<option value='".$themeName."'".$dispSelected.">".$dispThemeName."</option>";
		}
	}
}

if($themeOptions == "") {
	$themeOptions = "<option value=''>No Themes Installed!</option>";	
}


$arrMedalDisplayOrder = array(0 => "Date Awarded", 1 => "Display Order", 2 => "Alphabetical Order");
foreach($arrMedalDisplayOrder as $key => $value) {
	$dispSelected = "";
	if($websiteInfo['medalorder'] == $key) {
		$dispSelected = " selected";
	}
	$medaldisplayorder .= "<option value='".$key."'".$dispSelected.">".$value."</option>";	
}

$selectDebugOn = "";
if($websiteInfo['debugmode'] == 1) {
	$selectDebugOn = " selected";	
}

$selectHideInactive = "";
if($websiteInfo['hideinactive'] == 1) {
	$selectHideInactive = " selected";	
}

$selectShowNewsPosts = "";

if($websiteInfo['hpnews'] == 0) {
	$selectShowNewsPosts = " selected";	
}
else {
	$showCustomAmount = "";
	switch($websiteInfo['hpnews']) {

		case 5:
			$selectNumOfNewsPosts[5] = " selected";
			break;
		case 4:
			$selectNumOfNewsPosts[4] = " selected";
			break;
		case 3:
			$selectNumOfNewsPosts[3] = " selected";
			break;
		case 2:
			$selectNumOfNewsPosts[2] = " selected";
			break;
		case 1:
			$selectNumOfNewsPosts[1] = " selected";
			break;
		case -1:
			$selectNumOfNewsPosts[0] = " selected";
			break;
		default:
			$selectNumOfNewsPosts[6] = " selected";
			$showCustomAmount = $websiteInfo['hpnews'];
	}
	
}

$webInfoObj->select(1);
$websiteInfo = $webInfoObj->get_info_filtered();
$selectNewsPostsPerPage = array();
foreach(array(10,25,50,100) as $value) {
	if($value == $websiteInfo['news_postsperpage']) {
		$selectNewsPostsPerPage[$value] = " selected";
	}
}


?>

<div class='formDiv'>


	Use the form below to modify your website's settings.<br><br>
	
	<div class='errorDiv' id='errorDiv' style='display: none'>
		<strong>Unable to save website settings because the following errors occurred:</strong><br><br>
		<span id='errorMessage'></span>
	</div>
	
	<table class='formTable'>
		<tr>
			<td colspan='2' class='main'>
				<b>Clan Information</b>
				<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
			</td>
		</tr>
		<tr>
			<td class='formLabel'>Clan Name:</td>
			<td class='main'><input type='text' id='clanname' value='<?php echo $websiteInfo['clanname']; ?>' class='textBox' style='width: 250px'></td>
		</tr>
		<tr>
			<td class='formLabel'>Clan Tag:</td>
			<td class='main'><input type='text' id='clantag' value='<?php echo $websiteInfo['clantag']; ?>' class='textBox' style='width: 50px'></td>
		</tr>
		<tr>
			<td colspan='2' class='main'><br>
				<b>Website Information</b>
				<div class='dottedLine' style='width: 90%; padding-top: 3px'></div>
			</td>
		</tr>
		<tr>
			<td class='formLabel'>Logo URL: <a href='javascript:void(0)' onmouseover="showToolTip('Depending on the theme you are using, you may have to edit the actual theme files to change the logo.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='logourl' value='<?php echo $websiteInfo['logourl']; ?>' class='textBox' style='width: 250px'></td>
		</tr>
		<!-- 
		<tr>
			<td class='formLabel'>Forum URL: <a href='javascript:void(0)' onmouseover="showToolTip('Depending on the theme you are using, you may have to edit the actual theme files to change the forum url.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='forumurl' value='<?php echo $websiteInfo['forumurl']; ?>' class='textBox' style='width: 250px'></td>
		</tr> -->
		<tr>
			<td class='formLabel'>Theme:</td>
			<td class='main'><select id='theme' class='textBox'><?php echo $themeOptions; ?></select></td>
		</tr>
		<tr>
			<td class='formLabel' style='width: 200px'>Max Diplomacy Requests: <a href='javascript:void(0)' onmouseover="showToolTip('Sets the number of times someone can send a diplomacy request.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='maxdiplomacy' value='<?php echo $websiteInfo['maxdiplomacy']; ?>' class='textBox' style='width: 30px'></td>
		</tr>
		<tr>
			<td class='formLabel'>Failed Login Attempts: <a href='javascript:void(0)' onmouseover="showToolTip('Sets the number of times someone can try to log in before they are banned.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='failedlogins' value='<?php echo $websiteInfo['failedlogins']; ?>' class='textBox' style='width: 30px'></td>
		</tr>
		<tr>
			<td class='formLabel'>Medal Display Order: <a href='javascript:void(0)' onmouseover="showToolTip('Set how you want awarded medals to be displayed in member\'s profiles.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><select id='medalorder' class='textBox'><?php echo $medaldisplayorder; ?></select></td>
		</tr>
		<tr>
			<td class='formLabel'>Debug Mode:</td>
			<td class='main'><select id='debugmode' class='textBox'><option value='0'>Off</option><option value='1'<?php echo $selectDebugOn; ?>>On</option></select></td>
		</tr>
		<tr>
			<td class='formLabel'>Hide Inactive Members: <a href='javascript:void(0)' onmouseover="showToolTip('If set to Yes, inactive members will be hidden on the Members page.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><select id='showinactive' class='textBox'><option value='0'>No</option><option value='1'<?php echo $selectHideInactive; ?>>Yes</option></select></td>
		</tr>
		<tr>
			<td class='formLabel'>News Posts Per Page:</td>
			<td class='main'><select id='newsPostsPerPage' class='textBox'><option value='10'<?php echo $selectNewsPostsPerPage[10]; ?>>10</option><option value='25'<?php echo $selectNewsPostsPerPage[25]; ?>>25</option><option value='50'<?php echo $selectNewsPostsPerPage[50]; ?>>50</option><option value='100'<?php echo $selectNewsPostsPerPage[100]; ?>>100</option></select></td>
		</tr>
		<tr>
			<td class='formLabel'>Show News on Homepage:</td>
			<td class='main'><select id='showNewsPosts' class='textBox'><option value='yes'>Yes</option><option value='no'<?php echo $selectShowNewsPosts; ?>>No</option></select></td>
		</tr>
		</table>
		<div id='hpNewsOptions' style='padding-left: 10px'>
			<table class='formTable' style='margin-top: 0px'>
				<tr>
					<td class='formLabel'>Number of Posts:</td>
					<td class='main'><select id='numOfNewsPosts' class='textBox'><option value='1'<?php echo $selectNumOfNewsPosts[1]; ?>>Latest Post</option><option value='2'<?php echo $selectNumOfNewsPosts[2]; ?>>Last 2 Posts</option><option value='3'<?php echo $selectNumOfNewsPosts[3]; ?>>Last 3 Posts</option><option value='4'<?php echo $selectNumOfNewsPosts[4]; ?>>Last 4 Posts</option><option value='5'<?php echo $selectNumOfNewsPosts[5]; ?>>Last 5 Posts</option><option value='all'<?php echo $selectNumOfNewsPosts[0]; ?>>Show All Posts</option><option value='custom'<?php echo $selectNumOfNewsPosts[6]; ?>>Custom Amount</option></select></td>
				</tr>
				<tr>
					<td class='formLabel'><span id='enterAmountTitle' style='display: none'>Enter Amount:</span></td>
					<td class='main'><span id='enterAmountTxt' style='display: none'><input type='text' id='customNewsAmount' class='textBox' style='width: 30px' value='<?php echo $showCustomAmount; ?>'></span></td>
				</tr>
			</table>
		</div>
		<table class='formTable' style='margin-top: 0px'>
		<tr>
			<td colspan='2' class='main'><br>
				<b>Latest Activity Information</b>
				<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
				<div style='padding-left: 3px; padding-bottom: 15px'>
					In this section you can control whether members will be disabled after a certain number of days of inactivity.
				</div>
			</td>
		</tr>
		<tr>
			<td class='formLabel'>Max Days: <a href='javascript:void(0)' onmouseover="showToolTip('Sets the number of days of inactivity before disabling a member.  Leave blank or 0 to not use this setting.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='maxdsl' value='<?php echo $websiteInfo['maxdsl']; ?>' class='textBox' style='width: 30px'></td>
		</tr>
		<tr>
			<td class='formLabel'>Low Disable Level: <a href='javascript:void(0)' onmouseover="showToolTip('Sets what color to display the latest activity date for a low chance of a member being disabled for inactivity.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='lowdsl' value='<?php echo $websiteInfo['lowdsl']; ?>' class='textBox' style='width: 100px'></td>
		</tr>
		<tr>
			<td class='formLabel'>Medium Disable Level: <a href='javascript:void(0)' onmouseover="showToolTip('Sets what color to display the latest activity date for a medium chance of a member being disabled for inactivity.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='meddsl' value='<?php echo $websiteInfo['meddsl']; ?>' class='textBox' style='width: 100px'></td>
		</tr>
		<tr>
			<td class='formLabel'>High Disable Level: <a href='javascript:void(0)' onmouseover="showToolTip('Sets what color to display the latest activity date for a high chance of a member being disabled for inactivity.')" onmouseout='hideToolTip()'>(?)</a></td>
			<td class='main'><input type='text' id='highdsl' value='<?php echo $websiteInfo['highdsl']; ?>' class='textBox' style='width: 100px'></td>
		</tr>
		<tr>
			<td class='main' align='center' colspan='2'>
				<br><br>
				<input type='button' class='submitButton' id='btnSaveSettings' value='Save'>
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

<div id='themeChanged' style='display: none'>
	<p class='main' align='center'>
		You are about to change your theme to <span id='alertThemeName'></span>.  Your current menu's may not be suited for this theme.  Please choose an option below to continue.<br><br>The menu's for this theme will be saved whichever option you choose.
	</p>
	<p class='main' align='center'><br>
		<input type='button' class='submitButton' id='defaultMenus' value='Use Default Theme Menus'><br><br>
		<input type='button' class='submitButton' id='lastSavedMenus' value='Use Last Saved Theme Menus'><br><br>
		<input type='button' class='submitButton' id='noChangeMenus' value='Do Not Change Menus'><br><br>
	</p>
</div>

<div id='themeChangeError' style='display: none'>

	<p class='main' align='center'>
	
		Unable to save theme menus!  You will need to manually export the Menu SQL to save for future use.
	
	</p>

</div>


<div id='themeChangeNoPrevious' style='display: none'>

	<p class='main' align='center'>
	
		There were no previous menus saved!  Would you like to use the default menus for this theme?
	
	</p>

</div>

<script type='text/javascript'>

	var blnSkip = false;
	
	$(document).ready(function() {


		$('#showNewsPosts').change(function() {
			if($(this).val() == "no") {
				$('#hpNewsOptions').hide();
			}
			else {
				$('#hpNewsOptions').show();
			}

		});

		$('#numOfNewsPosts').change(function() {

			if($(this).val() == "custom") {
				$('#enterAmountTitle').show();
				$('#enterAmountTxt').show();
			}
			else {
				$('#enterAmountTitle').hide();
				$('#enterAmountTxt').hide();
			}	

		});

		$('#numOfPosts').change();

		$('#showNewsPosts').change();
		
		$('#lowdsl').miniColors({
			change: function(hex, rgb) { }
		});

		$('#meddsl').miniColors({
			change: function(hex, rgb) { }
		});

		$('#highdsl').miniColors({
			change: function(hex, rgb) { }
		});

		$('#defaultMenus').click(function() {
			$.post('<?php echo $MAIN_ROOT; ?>themes/menuexport.php', { }, function(data) {
				if($.trim(data) != "1" && !blnSkip) {
					$('#themeChangeError').dialog({
						title: 'Theme Change - Error',
						modal: true,
						width: 400,
						resizable: false,
						show: 'scale',
						zIndex: 999999,
						buttons: {
							'Continue without saving': function() {
								
								blnSkip = true;
								$('#defaultMenus').click();
								$(this).dialog('close');
								
							},
							'Cancel': function() {
								$('#themeChanged').dialog('close');
								$(this).dialog('close');
							}
						}
						

					});
				}
				else {
					$.post("<?php echo $MAIN_ROOT; ?>themes/"+$('#theme').val()+"/menuimport_default.php", { }, function() {

					});
					blnSkip = false;
					saveSettings();
					$('#themeChanged').dialog('close');
				}
			});
			
		});

		$('#lastSavedMenus').click(function() {
			
			$.post('<?php echo $MAIN_ROOT; ?>themes/menuexport.php', { }, function(data) {

				if($.trim(data) != "1" && !blnSkip) {
					$('#themeChangeError').dialog({
						title: 'Theme Change - Error',
						modal: true,
						width: 400,
						resizable: false,
						show: 'scale',
						zIndex: 999999,
						buttons: {
							'Continue without saving': function() {
								
								blnSkip = true;
								$('#lastSavedMenus').click();
								$(this).dialog('close');
								
							},
							'Cancel': function() {
								$('#themeChanged').dialog('close');
								$(this).dialog('close');
							}
						}
						

					});
				}
				else {
					$.post("<?php echo $MAIN_ROOT; ?>themes/"+$('#theme').val()+"/menuimport_saved.php", { }, function(data2) {
						if($.trim(data2) != "1") {
							$('#themeChangeNoPrevious').dialog({
								title: 'Theme Changed - Error',
								modal: true,
								width: 400,
								resizable: false,
								zIndex: 999999,
								show: 'scale',
								buttons: {
									'Yes': function() {
										
										$('#defaultMenus').click();
										$(this).dialog('close');

									},
									'Cancel': function() {
										$('#themeChanged').dialog('close');
										$(this).dialog('close');
									}
								}
							});
						}
						else {

							blnSkip = false;
							saveSettings();
							$('#themeChanged').dialog('close');

						}
					});
				}


			});


		});


		$('#noChangeMenus').click(function() {

			saveSettings();
			$('#themeChanged').dialog('close');
			
		});

		
		$('#btnSaveSettings').click(function() {

			var currentTheme = '<?php echo $websiteInfo['theme']; ?>';

			if(currentTheme != $('#theme').val()) {
				$('#alertThemeName').html($('#theme option:selected').text());
				$('#themeChanged').dialog({
					title: 'Website Settings - Theme Change',
					modal: true,
					zIndex: 9999,
					width: 450,
					show: 'scale',
					resizable: false,
					buttons: {
						'Cancel': function() {
							$(this).dialog('close');
						}
					}

				});

			}
			else {

				saveSettings();

			}

			

		});

		

	});


	function saveSettings() {

		var currentTheme = '<?php echo $websiteInfo['theme']; ?>';
		
		$(document).ready(function() {

			$('#loadingspiral').show();

			$.post("<?php echo $MAIN_ROOT; ?>members/include/admin/sitesettings_submit.php", { clanName: $('#clanname').val(), clanTag: $('#clantag').val(), logoURL: $('#logourl').val(), forumURL: $('#forumurl').val(), themeName: $('#theme').val(), maxDiplomacy: $('#maxdiplomacy').val(), failedLogins: $('#failedlogins').val(), maxDSL: $('#maxdsl').val(), lowDSL: $('#lowdsl').val(), medDSL: $('#meddsl').val(), highDSL: $('#highdsl').val(), medalOrder: $('#medalorder').val(), debugMode: $('#debugmode').val(), hideInactive: $('#showinactive').val(), showHPNews: $('#showNewsPosts').val(), numOfNewsPosts: $('#numOfNewsPosts').val(), customNewsAmount: $('#customNewsAmount').val(), newsPostsPerPage: $('#newsPostsPerPage').val() }, function(data) {
				$('#postResponse').html(data);
				$('#loadingspiral').hide();
			});

		});
		
	}
	
</script>
