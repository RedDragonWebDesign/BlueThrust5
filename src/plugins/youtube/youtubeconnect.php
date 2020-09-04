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
	
	$accessedByConsole = false;
	if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
		
		$prevFolder = "../../";
		include_once("../../_setup.php");
		include_once("../../classes/member.php");
		include_once("../../classes/rank.php");
		include_once("../../classes/consolecategory.php");
		
		// Plugin Info
		
		$PLUGIN_TABLE_NAME = $dbprefix."youtube";
		$PLUGIN_NAME = "Youtube Connect";
		
		
		// Start Page
		
		$consoleObj = new ConsoleOption($mysqli);
		
		$cID = $consoleObj->findConsoleIDByName("Youtube Connect");
		$consoleObj->select($cID);
		$consoleInfo = $consoleObj->get_info_filtered();
		
		
		$member = new Member($mysqli);
		$member->select($_SESSION['btUsername']);
		$memberInfo = $member->get_info_filtered();
		// Check Login
		$LOGIN_FAIL = true;
		if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
			$LOGIN_FAIL = false;			
		}
		else {
			die($MAIN_ROOT."members");	
		}
		
		include("youtube.php");
		
	}
	else {
		$memberInfo = $member->get_info_filtered();
		$consoleObj->select($_GET['cID']);
		
		include_once("../plugins/youtube/youtube.php");
		
		if(!$member->hasAccess($consoleObj)) {
			exit();
		}
		
		$accessedByConsole = true;
		include("../plugins/youtube/ytbuttoncss.php");
	}
	
	
	
	
	if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
		$dispHTTP = "http://";
	}
	else {
		$dispHTTP = "https://";
	}
	
	
	$ytObj = new Youtube($mysqli);
	
	if(!$ytObj->hasYoutube($memberInfo['member_id'])) {
		$countErrors = 0;
		$dispError = "";
		
		if($accessedByConsole && !isset($_GET['error'])) {
			
			echo "
				<script type='text/javascript'>
					window.location = '".$MAIN_ROOT."plugins/youtube/youtubeconnect.php';
				</script>
			";
			
			exit();	
		}
		
		if(isset($_GET['code']) && $_GET['state'] == $_SESSION['btYoutubeNonce'] && !isset($_GET['error'])) {
			$arrURLInfo = parse_url($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	
			$response = $ytObj->getAccessToken($_GET['code'], $arrURLInfo['scheme']."://".$arrURLInfo['host'].$arrURLInfo['path']);
			
			if(isset($response['access_token'])) {
				
				$ytObj->accessToken = $response['access_token'];
				$ytObj->refreshToken = $response['refresh_token'];
				$channelInfo = $ytObj->getChannelInfo();
				$channelSnippet = $ytObj->getChannelInfo("snippet");
				$channelStats = $ytObj->getChannelInfo("statistics");
				// Add User
				
				$arrColumns = array("member_id", "channel_id", "uploads_id", "thumbnail", "access_token", "refresh_token", "lastupdate", "subscribers", "title", "videocount", "viewcount", "loginhash");
				$arrValues = array($memberInfo['member_id'], $channelInfo['items'][0]['id'], $channelInfo['items'][0]['contentDetails']['relatedPlaylists']['uploads'], $channelSnippet['items'][0]['snippet']['thumbnails']['medium']['url'], $response['access_token'], $response['refresh_token'], time(), $channelStats['items'][0]['statistics']['subscriberCount'], $channelSnippet['items'][0]['snippet']['title'], $channelStats['items'][0]['statistics']['videoCount'], $channelStats['items'][0]['statistics']['viewCount'], md5($channelInfo['items'][0]['id']));
				
				$ytObj->addNew($arrColumns, $arrValues);
				$ytObj->updateVideos();
				
				echo "
					<script type='text/javascript'>
						window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."';
					</script>
				";
				exit();
				
			}
			else {
				
				echo "
				
					<script type='text/javascript'>
						window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."&error=1';
					</script>
					
				";
				exit();
			}
			
			
		}
		elseif(isset($_GET['error'])) {
			
			$countErrors++;
			$dispError = "Unable to connect to Youtube! Please try again.";
			
		}
		elseif(!isset($_GET['error']) && !isset($_GET['code'])) {
			
			$loginLink = $ytObj->getConnectLink($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			$_SESSION['btYoutubeNonce'] = $ytObj->tokenNonce;
			
			echo "
			
				<p class='main'>Redirecting to Youtube...</p>
				
				<script type='text/javascript'>
					window.location = '".$loginLink."';
				</script>
			
			";
			
			exit();
		}
		
		
		if($dispError != "") {
			
			echo "	
			
				<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%'>
				
					<p class='main' align='center'>
						".$dispError."<br><br>
						<a href='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."'>Retry</a>
					</p>
					
				</div>
			
			";
			
		}
		
	
	}
	else {
		// Has Connected Youtube Account
		
		$countErrors = 0;
		$dispError = "";
		$dispSuccess = false;
		
		if($_POST['submit']) {
			
			// Check Video Display
			$arrVideoDisplayCheck = array(0,1,2,3,4,5);
			if(!in_array($_POST['showvideos'], $arrVideoDisplayCheck) || !is_numeric($_POST['showvideos'])) {
				$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid video display amount.<br>";
				$countErrors++;
			}
			
			
			if($countErrors == 0) {
				$setShowInfoCard = ($_POST['showinfocard'] == 1) ? 1 : 0;
				$setAllowLogin = ($_POST['allowlogin'] == 1) ? 1 : 0;
			
				if($ytObj->update(array("allowlogin", "showsubscribe", "showvideos"), array($_POST['allowlogin'], $_POST['showinfocard'], $_POST['showvideos']))) {
					$dispSuccess = true;
				}
				else {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
				}
				
			}
			
			
		}
		
		$ytInfo = $ytObj->get_info_filtered();
		
		$checkVideos = array();
		if($ytInfo['showvideos'] == 0) {
			$checkVideos[0] = " selected";	
		}
		elseif($ytInfo['showvideos'] == 1) {
			$checkVideos[1] = " selected";	
		}
		
		
		$checkInfoCard = ($ytInfo['showsubscribe'] == 1) ? " checked" : "";
		$checkAllowLogin = ($ytInfo['allowlogin'] == 1) ? " checked" : "";
		
		echo "
			<div id='loadingSpiralDisconnect' class='loadingSpiral'>
				<p align='center'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
				</p>
			</div>
			<div id='connectedDiv'>
			<form action='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."' method='post'>
			<div class='formDiv'>
			";
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to save Youtube settings because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		
		echo "
			
				<table class='formTable'>
					<tr>
						<td colspan='2'>
							<div class='main dottedLine' style='margin-bottom: 20px; padding-bottom: 3px'>
								<b>Connected:</b>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<div id='loadingSpiral' class='loadingSpiral' style='padding-top: 0px'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Refreshing Data
								</p>
							</div>
							<div id='ytInfoCard'>".$ytObj->dispSubscribeButton()."</div>
							<div style='font-style: italic; text-align: center; margin-top: 3px; margin-left: auto; margin-right: auto; position: relative' class='main'>
								Last updated <span id='lastUpdateTime'>".getPreciseTime($ytInfo['lastupdate'])."</span>
								<p class='largeFont' style='font-style: normal; font-weight: bold' align='center'>
									<a style='cursor: pointer' id='btnDisconnect'>DISCONNECT ACCOUNT</a>
								</p>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<div class='main dottedLine' style='margin-bottom: 20px; padding-bottom: 3px'>
								<b>Profile Display Options:</b>
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Show Info Card: <a href='javascript:void(0)' onmouseover=\"showToolTip('An example of the Info Card is shown in the &quot;Connected&quot; section above.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td class='main'><input type='checkbox' name='showinfocard' value='1'".$checkInfoCard."></td>
					</tr>
					<tr>
						<td class='formLabel'>Video Display:</td>
						<td class='main'>
							<select name='showvideos' class='textBox'>
								<option value='0'".$checkVideos[0].">Don't Show Videos</option>
								<option value='1'".$checkVideos[1].">Most Recent Video</option>
								";
					for($i=2; $i<=5; $i++) {
						$dispChecked = "";
						if($ytInfo['showvideos'] == $i) {
							$dispChecked = " selected";	
						}
						echo "<option value='".$i."'".$dispChecked.">".$i." Most Recent Videos</option>";						
					}
		
		echo "
							</select>
						</td>
					</tr>
					<tr>
						<td colspan='2'><br>
							<div class='main dottedLine' style='margin-bottom: 2px; padding-bottom: 3px'>
								<b>Log In Options:</b>
							</div>
							<div style='padding-left: 3px; margin-bottom: 15px'>
								Check the box below to allow logging into this website through Youtube.
							</div>
						</td>
					</tr>
					<tr>
						<td class='formLabel'>Allow Log In:</td>
						<td class='main'><input type='checkbox' name='allowlogin' value='1'".$checkAllowLogin."></td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' value='Save' class='submitButton'>
						</td>
					</tr>
				</table>
			
			</div>
			</form>
			</div>
			
			<div id='disconnectDiv' style='display: none'>
			<p class='main' align='center'>
				Are you sure you want to disconnect your Youtube account?
			</p>
		</div>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#btnDisconnect').click(function() {
					
					$('#disconnectDiv').dialog({
						title: 'Disconnect Youtube',
						modal: true,
						zIndex: 99999,
						width: 400,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {
								$('#connectedDiv').fadeOut(250);
								$('#loadingSpiralDisconnect').show();
								$.post('".$MAIN_ROOT."plugins/youtube/disconnect.php', { }, function(data) {
								
									$('#connectedDiv').html(data);
									$('#loadingSpiralDisconnect').hide();
									$('#connectedDiv').fadeIn(250);
								
								});
								
								
								$(this).dialog('close');
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
						
					});
					$('.ui-dialog :button').blur();
				
				});
			
			});
		
		</script>
			
		";
		
		
		if((time()-$ytInfo['lastupdate']) > 1800) {
			
			echo "
				
				<script type='text/javascript'>
				
					$(document).ready(function() {
					
						$('#loadingSpiral').show();
						$('#ytInfoCard').fadeOut(250);
					
						$.post('".$MAIN_ROOT."plugins/youtube/reloadcache.php', { yID: '".$ytInfo['youtube_id']."' }, function(data) {
							
							postResult = JSON.parse(data);
							
							if(postResult['result'] == 'success') {
								$('#ytInfoCard').html(postResult['html']);
								$('#lastUpdateTime').html(postResult['time']);

							}							
							
							$('#ytInfoCard').fadeIn(250);
							$('#loadingSpiral').hide();
							
							var bubbleRight = ($('.ytBubble').width()*-1)-20;
							$('.ytBubble').css('right', bubbleRight+'px');
							
						});
					
					});
				
				</script>
			
			";
		}
		
		
		if($dispSuccess) {
			
			echo "
				<div id='successDiv' style='display: none'>
					<p align='center' class='main'>
						Youtube Connect Settings Saved!
					</p>
				</div>
				<script type='text/javascript'>
				
					$(document).ready(function() {
					
						$('#successDiv').dialog({
							title: 'Twitter Connect',
							modal: true,
							zIndex: 99999,
							width: 400,
							resizable: false,
							show: 'scale',
							buttons: {
								'Ok': function() {
									$(this).dialog('close');
								}
							}
							
						});
						$('.ui-dialog :button').blur();
					
					});
				
				</script>
			";
			
		}
		
	}

?>