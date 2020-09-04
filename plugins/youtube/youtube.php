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

	include_once($prevFolder."classes/basic.php");
	include_once($prevFolder."classes/btplugin.php");

	class Youtube extends Basic {
		
		// SET YOUR YOUTUBE CLIENT ID AND SECRET
		protected $clientID;
		protected $clientSecret;
		
		// DO NOT EDIT BELOW
		
		protected $oauthURL = "https://accounts.google.com/o/oauth2/auth";
		protected $tokenURL = "https://accounts.google.com/o/oauth2/token";
		public $tokenNonce;
		public $accessToken;
		public $refreshToken;
		public $objYTVideo;
		
		public function __construct($sqlConnection) {
			
			
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."youtube";
			$this->strTableKey = "youtube_id";			
			
			$this->objYTVideo = new Basic($sqlConnection, "youtube_videos", "youtubevideo_id");
			
			$this->objPlugin = new btPlugin($sqlConnection);
			$this->objPlugin->selectByName("Youtube Connect");
			
			$apiInfo = $this->objPlugin->getAPIKeys();
			
			$this->clientID = $apiInfo['clientID'];
			$this->clientSecret = $apiInfo['clientSecret'];
			
		}
		
		public function select($intIDNum, $numericIDOnly = true) {

			$returnVal = parent::select($intIDNum, $numericIDOnly);
			
			$this->accessToken = $this->arrObjInfo['access_token'];
			$this->refreshToken = $this->arrObjInfo['refresh_token'];
			
			return $returnVal;
		}
		
		public function hasYoutube($memberID) {
			
			$returnVal = false;
			if(is_numeric($memberID)) {
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE member_id = '".$memberID."'");
				if($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$this->select($row['youtube_id']);
					$returnVal = true;
				}
				
			}
			
			return $returnVal;
			
		}
		
		public function authorizeLogin($channelID) {

			$returnVal = false;
			$channelHash = md5($channelID);
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE loginhash = '".$channelHash."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				if($row['channel_id'] == $channelID) {
					$this->select($row['youtube_id']);
					$returnVal = true;
				}
			}
			
			return $returnVal;
			
		}
		
		public function getAccessToken($ytCode, $callbackURL) {
			
			$postData = "code=".$ytCode;
			$postData .= "&client_id=".$this->clientID;
			$postData .= "&client_secret=".$this->clientSecret;
			$postData .= "&redirect_uri=".urlencode($callbackURL);
			$postData .= "&grant_type=authorization_code";
			
			$response = $this->httpRequest($this->tokenURL, "POST", array("Content-Type: application/x-www-form-urlencoded"), $postData);

			$response = json_decode($response, true);
						
			if(isset($response['access_token'])) {
				$this->accessToken = $response['access_token'];	
			}
			
			return $response;
			
		}
		
		public function refreshAccessToken() {
			
			$postData .= "&client_id=".$this->clientID;
			$postData .= "&client_secret=".$this->clientSecret;
			$postData .= "&refresh_token=".$this->refreshToken;
			$postData .= "&grant_type=refresh_token";
			
			$response = $this->httpRequest($this->tokenURL, "POST", array("Content-Type: application/x-www-form-urlencoded"), $postData);
			$response = json_decode($response, true);
			
			if(isset($response['access_token'])) {
				$this->accessToken = $response['access_token'];
				
				if($this->intTableKeyValue != "") {
					$this->update(array("access_token"), array($response['access_token']));	
				}
				
			}
			
		}
		
		public function getChannelInfo($infoType="contentDetails", $countUsage=0) {
			
			$arrResponse = false;
			$arrTypes = array("contentDetails", "snippet", "statistics");
			if(isset($this->accessToken) && in_array($infoType, $arrTypes) && isset($this->refreshToken) && $countUsage < 2) {
				$response = file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=".$infoType."&mine=true&access_token=".$this->accessToken);

				$arrResponse = json_decode($response, true);
				
				if($arrResponse['error']['code'] == 401 || $response == "") {
					$this->refreshAccessToken();
					$newUsage = $countUsage+1;
					$arrResponse = $this->getChannelInfo($infoType, $newUsage);
				}
				
			}
			
			return $arrResponse;
		}
		
		
		public function getVideos($countUsage=0) {
			
			$arrResponse = false;
			if(isset($this->accessToken) && isset($this->refreshToken) && $countUsage < 2 && isset($this->arrObjInfo['uploads_id'])) {
				
				$response = file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".$this->arrObjInfo['uploads_id']."&access_token=".$this->accessToken);
				$arrResponse = json_decode($response, true);
				
				if($arrResponse['error']['code'] == 401 || $response == "") {
					$this->refreshAccessToken();
					$newUsage = $countUsage+1;
					$arrResponse = $this->getVideos($newUsage);
				}
				
			}
			
			return $arrResponse;
		}
		
		public function reloadCache() {
			
			if(isset($this->intTableKeyValue) && isset($this->accessToken) && isset($this->refreshToken)) {

				$channelInfo = $this->getChannelInfo();
				$channelSnippet = $this->getChannelInfo("snippet");
				$channelStats = $this->getChannelInfo("statistics");
				
				$arrColumns = array("channel_id", "uploads_id", "thumbnail", "subscribers", "title", "lastupdate", "videocount", "viewcount", "loginhash");
				$arrValues = array($channelInfo['items'][0]['id'], $channelInfo['items'][0]['contentDetails']['relatedPlaylists']['uploads'], $channelSnippet['items'][0]['snippet']['thumbnails']['medium']['url'], $channelStats['items'][0]['statistics']['subscriberCount'], $channelSnippet['items'][0]['snippet']['title'], time(), $channelStats['items'][0]['statistics']['videoCount'], $channelStats['items'][0]['statistics']['viewCount'], md5($channelInfo['items'][0]['id']));
				
				$this->update($arrColumns, $arrValues);
				
				$this->updateVideos();
				
			}
			
			
		}
		
		
		public function updateVideos() {
			
			if(isset($this->intTableKeyValue) && isset($this->accessToken) && isset($this->refreshToken)) {
				
				$arrVideoInfo = $this->getVideos();
				$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."youtube_videos WHERE youtube_id = '".$this->intTableKeyValue."'");
				$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."youtube_videos`");
				
				$videoCount = 0;
				$arrColumns = array("youtube_id", "member_id", "video_id", "thumbnail", "title", "dateuploaded");
				foreach($arrVideoInfo['items'] as $videoInfo) {
	
					$arrValues = array($this->intTableKeyValue, $this->arrObjInfo['member_id'], $videoInfo['snippet']['resourceId']['videoId'], $videoInfo['snippet']['thumbnails']['medium']['url'], $videoInfo['snippet']['title'], $videoInfo['snippet']['publishedAt']);
					
					$this->objYTVideo->addNew($arrColumns, $arrValues);					
				
					$videoCount++;
					if($videoCount > 4) {
						break;	
					}
				}
			
			}
			
		}
		
		public function getConnectLink($callbackURL) {
			
			$this->tokenNonce = md5(uniqid(rand().time()));
			
			$clientID = urlencode($this->clientID);
			
			$url = $this->oauthURL;
			$url .= "?client_id=".$clientID;
			$url .= "&redirect_uri=".urlencode($callbackURL);
			$url .= "&response_type=code";
			$url .= "&scope=".urlencode("https://www.googleapis.com/auth/youtube.readonly");
			$url .= "&access_type=offline";
			$url .= "&state=".$this->tokenNonce;
			
			return $url;
			
		}
		
		public function dispSubscribeButton() {
			

			if($this->arrObjInfo['subscribers'] >= 1000 && $this->arrObjInfo['subscribers'] < 1000000) {
				$dispSubscribers = floor($this->arrObjInfo['subscribers']/1000)."K";	
			}
			elseif($this->arrObjInfo['subscribers'] >= 1000000) {
				$dispSubscribers = floor($this->arrObjInfo['subscribers']/1000000)."M";
			}
			else {
				$dispSubscribers = number_format($this->arrObjInfo['subscribers'], 0);
			}
			
			$subHTML = "
				<div class='shadedBox' style='position: relative; width: 85%; margin-left: auto; margin-right: auto'>

					<div class='ytThumbnail'><img src='".$this->arrObjInfo['thumbnail']."'></div>
					<div class='ytInfoContainer'>
						<div class='ytChannelTitle'><span class='largeFont'><a href='http://www.youtube.com/channel/".$this->arrObjInfo['channel_id']."'><b>".$this->arrObjInfo['title']."</b></a></span></div>
						<div class='ytSubscribeButtonWrapper'>
							<a href='http://www.youtube.com/channel/".$this->arrObjInfo['channel_id']."?sub_confirmation=1'><div class='ytSubscribeButton'>Subscribe</div></a>
							<div class='ytBubble'>".$dispSubscribers."<div class='ytBubbleArrow'></div></div>
						</div>
					</div>
					
					<div class='ytVideoViews'>
						<div style='position: relative' class='largeFont'>
							<b>".number_format($this->arrObjInfo['subscribers'],0)."</b> Subscribers<br>
							<b>".number_format($this->arrObjInfo['videocount'],0)."</b> Videos<br>
							<b>".number_format($this->arrObjInfo['viewcount'],0)."</b> Views
						</div>
						
					</div>
					<div style='clear: both'></div>
				</div>
				
				
				
				<script type='text/javascript'>
					$(document).ready(function() {
						var bubbleRight = ($('.ytBubble').width()*-1)-20;
						$('.ytBubble').css('right', bubbleRight+'px');
					});
				</script>
			";
			
			return $subHTML;
			
		}
		
		public function httpRequest($url, $method, $headers=array(), $postfields=array()) {
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
			if($method == "POST") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			}
			elseif($method = "DELETE") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);				
			}
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		
			$result = curl_exec($ch);
			$info = curl_getinfo($ch);
		
			$this->httpCode = $info['http_code'];
		
			return $result;
		
		}

		public function delete() {
			
			$returnVal = false;
			if($this->intTableKeyValue != "" && $this->arrObjInfo['access_token'] != "") {
			
				$blnDelete = parent::delete();
				if($this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."youtube_videos WHERE youtube_id = '".$this->intTableKeyValue."'") && $blnDelete) {
					$returnVal = true;
					$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."youtube_videos`");				
					file_get_contents("https://accounts.google.com/o/oauth2/revoke?token=".$this->arrObjInfo['access_token']);
					
				}
				
			}
			
			return $returnVal;
			
		}
		
		public function getClientID() {
			return $this->clientID;	
		}
		
		public function getClientSecret() {
			return $this->clientSecret;	
		}
	}
		

?>