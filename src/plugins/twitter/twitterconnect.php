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

if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}


include_once("../plugins/twitter/twitter.php");


$twitterObj = new Twitter($mysqli);

if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && $_GET['oauth_token'] == $_SESSION['btOauth_Token'] && !$twitterObj->hasTwitter($memberInfo['member_id'])) {
	// CALLBACK
	$twitterObj->oauthTokenSecret = $_SESSION['btOauth_Token_Secret'];
	$response = $twitterObj->getAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
	
	if($twitterObj->httpCode == 200) {
		parse_str($response, $oauthArray);
		$arrColumns = array("member_id", "oauth_token", "oauth_tokensecret", "loginhash");
		$arrValues = array($memberInfo['member_id'], $oauthArray['oauth_token'], $oauthArray['oauth_token_secret'], md5($oauthArray['oauth_token']));
		
		
		if(!$twitterObj->authorizeLogin($oauthArray['oauth_token'], $oauthArray['oauth_token_secret'])) {
		
			$twitterObj->addNew($arrColumns, $arrValues);
			
			echo "
				<script type='text/javascript'>
					window.location = '".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."';
				</script>
			";
			
		}
		else {

			echo "
			
				<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%'>
					<p class='main' align='center'>
						The chosen twitter account is already associated with a member on this site!<br><br>
						<a href='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."'>Retry</a>
					</p>
				</div>
			
			";
			
		}
		
	}
	else {

		echo "
		
			<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%'>
				<p class='main' align='center'>
					Unable to connect account!  Please Try Again.<br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."'>Retry</a>
				</p>
			</div>
		
		";
	}
	
	
}
elseif(isset($_GET['denied'])) {	
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."members';
		</script>
	";
	exit();
}
elseif(!$twitterObj->hasTwitter($memberInfo['member_id'])) {
	// CONNECT
			
	$response = $twitterObj->getRequestToken($dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$twitterObj->hasTwitter($memberInfo['member_id']);
	if($response !== false) {
		parse_str($response, $arrOutput);	
		
		$_SESSION['btOauth_Token'] = $arrOutput['oauth_token'];
		$_SESSION['btOauth_Token_Secret'] = $arrOutput['oauth_token_secret'];
		
		echo "
					
			<p>Redirecting to Twitter...</p>
			<script type='text/javascript'>
			
				window.location = '".$twitterObj->authorizeURL."?oauth_token=".$arrOutput['oauth_token']."';
			
			</script>

		";
		
	}
	else {
		
		echo "
			
			<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%'>
				<p class='main' align='center'>
					Unable to connect account!  Please Try Again.<br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."'>Retry</a>
				</p>
			</div>
		
		";
		
	}
	
}
elseif($twitterObj->hasTwitter($memberInfo['member_id'])) {
	
	
	$dispSuccess = false;
	if($_POST['submit']) {

		$setShowFeed = ($_POST['showfeed'] == 1) ? 1 : 0;
		$setEmbedTweet = ($_POST['embedlasttweet'] == 1) ? 1 : 0;
		$setInfoCard = ($_POST['showinfo'] == 1) ? 1 : 0;
		$setAllowLogin = ($_POST['allowlogin'] == 1) ? 1 : 0;
		
		$arrColumns = array("showfeed", "embedtweet", "infocard", "allowlogin");
		$arrValues = array($setShowFeed, $setEmbedTweet, $setInfoCard, $setAllowLogin);
		
		$twitterObj->update($arrColumns, $arrValues);
		
		$dispSuccess = true;
		
	}
	
	
	
	// MEMBER ALREADY HAS TWITTER CONNECTED
	
	$twitterObj->oauthToken = $twitterObj->get_info("oauth_token");
	$twitterObj->oauthTokenSecret = $twitterObj->get_info("oauth_tokensecret");
	
	$twitterObj->reloadCacheInfo();	
	
	$twitterInfo = $twitterObj->get_info_filtered();

	$checkShowFeed = ($twitterInfo['showfeed'] == 1) ? " checked" : "";
	$checkEmbedTweet = ($twitterInfo['embedtweet'] == 1) ? " checked" : "";
	$checkInfoCard = ($twitterInfo['infocard'] == 1) ? " checked" : "";
	$checkAllowLogin = ($twitterInfo['allowlogin'] == 1) ? " checked" : "";
	
	echo "
	
		<div id='connectedDiv'>
			<form action='".$MAIN_ROOT."members/console.php?cID=".$_GET['cID']."' method='post'>
				<div class='formDiv'>
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
				
								<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 50%; overflow: auto'>
									
									".$twitterObj->dispCard()."
						
								</div>
								<div style='font-style: italic; text-align: center; margin-top: 3px; margin-left: auto; margin-right: auto; position: relative' class='main'>
									Last updated ".getPreciseTime($twitterInfo['lastupdate'])."
									<p class='largeFont' style='font-style: normal; font-weight: bold' align='center'>
										<a style='cursor: pointer' id='btnDisconnect'>DISCONNECT ACCOUNT</a>
									</p>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan='2'><br><br>
								<div class='main dottedLine' style='margin-bottom: 2px; padding-bottom: 3px'>
									<b>Profile Display Options:</b>
								</div>
								<div style='padding-left: 3px; margin-bottom: 15px'>
									Use the form below to set which items from Twitter will show in your profile.
								</div>
							</td>
						</tr>
						<tr>
							<td class='formLabel'>Show Feed:</td>
							<td class='main'><input type='checkbox' name='showfeed' value='1'".$checkShowFeed."></td>
						</tr>
						<tr>
							<td class='formLabel'>Embed Last Tweet:</td>
							<td class='main'><input type='checkbox' name='embedlasttweet' value='1'".$checkEmbedTweet."></td>
						</tr>
						<tr>
							<td class='formLabel'>Show Info Card: <a href='javascript:void(0)' onmouseover=\"showToolTip('An example of the Info Card is shown in the &quot;Connected&quot; section above.')\" onmouseout='hideToolTip()'>(?)</a></td>
							<td class='main'><input type='checkbox' name='showinfo' value='1'".$checkInfoCard."></td>
						</tr>
						<tr>
							<td colspan='2'><br>
								<div class='main dottedLine' style='margin-bottom: 2px; padding-bottom: 3px'>
									<b>Log In Options:</b>
								</div>
								<div style='padding-left: 3px; margin-bottom: 15px'>
									Check the box below to allow logging into this website through Twitter.
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
					<br>
				</div>
			</form>
		</div>
		
		<div id='disconnectDiv' style='display: none'>
			<p class='main' align='center'>
				Are you sure you want to disconnect your Twitter account?
			</p>
		</div>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				$('#btnDisconnect').click(function() {
					
					$('#disconnectDiv').dialog({
						title: 'Disconnect Twitter',
						modal: true,
						zIndex: 99999,
						width: 400,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {
								$('#connectedDiv').fadeOut(250);
								$.post('".$MAIN_ROOT."plugins/twitter/disconnect.php', { }, function(data) {
								
									$('#connectedDiv').html(data);
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
	
	
	if($dispSuccess) {

		echo "
			<div id='successDiv' style='display: none'>
				<p align='center' class='main'>
					Twitter Connect Settings Saved!
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
else {
	echo "
	
		<script type='text/javascript'>
			
			window.location = '".$MAIN_ROOT."members';
		
		</script>
	
	";
}



?>