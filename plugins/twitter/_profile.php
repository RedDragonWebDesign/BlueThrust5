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

	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	include_once($prevFolder."plugins/twitter/twitter.php");
	
	$twitterObj = new Twitter($mysqli);

	
	if($twitterObj->hasTwitter($memberInfo['member_id'])) {
		
		$twitterObj->oauthToken = $twitterObj->get_info("oauth_token");
		$twitterObj->oauthTokenSecret = $twitterObj->get_info("oauth_tokensecret");
		
		$twitterObj->reloadCacheInfo();
		$twitterInfo = $twitterObj->get_info();
		
		
		if(($twitterInfo['infocard']+$twitterInfo['embedtweet']+$twitterInfo['showfeed']) > 0) {
		
			echo "
				<div class='formTitle' style='position: relative; text-align: center; margin-top: 20px'>Twitter</div>
				
				<table class='profileTable' style='border-top-width: 0px'>
					<tr>
						<td class='main' align='center'>
							";
			
			if($twitterInfo['infocard'] == 1) {
	
				
				echo "<div class='shadedBox' style='margin: 20px auto; width: 70%; overflow: auto'>".$twitterObj->dispCard()."</div>";
				
			}
			
			if($twitterInfo['embedtweet']) {
				
				
				echo "<div style='position: relative; margin-left: auto; margin-right: auto; margin-top: 20px'>";
				echo $twitterInfo['lasttweet_html'];
				echo "</div>";	
				
			}
			
			if($twitterInfo['showfeed']) {
	
				echo "
					<div style='position: relative; margin: 20px auto; width: 70%'>
						<a class=\"twitter-timeline\"  href=\"https://twitter.com/".$twitterInfo['username']."\"  data-widget-id=\"".$twitterObj->widgetID."\">Tweets by @".$twitterInfo['username']."</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
					</div>
				";
				
			}
			
			
			
			echo "
						</td>
					</tr>
				</table>		
			";
		
		}
		
	}
	
	
?>


