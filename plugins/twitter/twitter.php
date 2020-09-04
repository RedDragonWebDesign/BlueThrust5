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

	class Twitter extends Basic {
		


		protected $consumerKey;
		protected $consumerSecret;
		public $widgetID;
		
		
		public $requestTokenURL = "https://api.twitter.com/oauth/request_token";
		public $authorizeURL = "https://api.twitter.com/oauth/authorize";
		public $authenticateURL = "https://api.twitter.com/oauth/authenticate";
		public $accessTokenURL = "https://api.twitter.com/oauth/access_token";
		public $tweetURL = "https://api.twitter.com/1.1/statuses/update.json";
		public $twitterInfoURL = "https://api.twitter.com/1.1/account/verify_credentials.json";
		public $embedTweetURL = "https://api.twitter.com/1.1/statuses/oembed.json";
		public $arrParameters;
		public $oauthTokenSecret;
		public $oauthToken;
		
		protected $callbackURL;
		
		public $lastHTTPRequestInfo;
		public $lastResponse;
		
		protected $lastSig;
		protected $lastSignKey;
		public $lastAuthHeader;
		public $httpCode;
		
		public $objPlugin;
		
		public function __construct($sqlConnection) {
			
			
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."twitter";
			$this->strTableKey = "twitter_id";

			$this->objPlugin = new btPlugin($sqlConnection);
			$this->objPlugin->selectByName("Twitter Connect");
			
			$apiInfo = $this->objPlugin->getAPIKeys();
			
			$this->consumerKey = $apiInfo['consumerKey'];
			$this->consumerSecret = $apiInfo['consumerSecret'];
			$this->widgetID = $apiInfo['widgetID'];
			
			
			$this->arrParameters['oauth_consumer_key'] = $this->consumerKey;
			$this->arrParameters['oauth_signature_method'] = "HMAC-SHA1";
			$this->arrParameters['oauth_version'] = "1.0";
			
		}
		
		// Plugin Functions
		
		
		public function hasTwitter($memID) {


			$returnVal = false;
			if(is_numeric($memID)) {
				
				
				$query = "SELECT twitter_id FROM ".$this->MySQL->get_tablePrefix()."twitter WHERE member_id = '".$memID."'";
				$result = $this->MySQL->query($query);
				
				if($result->num_rows > 0) {
					
					$row = $result->fetch_assoc();
					$this->select($row['twitter_id']);
					
					$returnVal = true;	
					
				}
				
			
			}

			
			return $returnVal;
			
		}
		
		
		public function authorizeLogin($oauth_token, $oauth_token_secret) {
			
			$returnVal = false;
			
			if(isset($oauth_token) && isset($oauth_token_secret)) {
				
				$loginHash = md5($oauth_token);
												
				$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE loginhash = '".$loginHash."'");
				$row = $result->fetch_assoc();
				if($result->num_rows > 0 && $row['oauth_tokensecret'] == $oauth_token_secret) {
					
					$this->select($row['twitter_id']);

					
					$returnVal = true;
					
				}

				
			}
			
			
			return $returnVal;
		}
		
		
		public function reloadCacheInfo() {
			
			if($this->intTableKeyValue != "" && isset($this->oauthToken) && isset($this->oauthTokenSecret)) {
			
				if((time()-$this->arrObjInfo["lastupdate"]) > 1800) {
					$twitterInfo = $this->getTwitterInfo();
					
					if($twitterInfo !== false) {
						$embedTweet = $this->getEmbeddedTweet($twitterInfo['status']['id_str']);
						
						$arrColumns = array("lastupdate", "username", "name", "description", "followers", "following", "tweets", "profilepic", "lasttweet_id", "lasttweet_html");
						$arrValues = array(time(), $twitterInfo['screen_name'], $twitterInfo['name'], $twitterInfo['description'], $twitterInfo['followers_count'], $twitterInfo['friends_count'], $twitterInfo['statuses_count'], $twitterInfo['profile_image_url_https'], $twitterInfo['status']['id_str'], $embedTweet['html']);
						
						$this->update($arrColumns, $arrValues);
					}
					else {
						$this->delete();
						$this->arrObjInfo = array();
					}
					
				}
				
			}
			
		}
		
		public function dispCard() {
		
			$returnVal = "";
			if($this->intTableKeyValue != "") {
				$returnVal = "
					
									
					<div style='float: left; width: 25%; text-align: left'>
						<img src='".str_replace("_normal", "_bigger", $this->arrObjInfo['profilepic'])."' class='solidBox' style='padding: 0px'>
					</div>
					<div class='largeFont' style='width: 68%; float: left; margin-left: 10px; text-align: left'>
						<b><span class='breadCrumbTitle' style='padding: 0px'>".$this->arrObjInfo['name']."</span></b><br>
						<a href='http://twitter.com/".$this->arrObjInfo['username']."' target='_blank'>@".$this->arrObjInfo['username']."</a>
						<p class='main'>".$this->arrObjInfo['description']."</p>
						
						<div class='main' style='position: relative; overflow: auto; text-align: left'>
							<div style='float: left; margin-left: 10px'><a href='http://twitter.com/".$this->arrObjInfo['username']."' target='_blank'><b>".number_format($this->arrObjInfo['tweets'],0)."</b><br>TWEETS</a></div>
							<div style='float: left; margin-left: 10px'><a href='http://twitter.com/".$this->arrObjInfo['username']."/following' target='_blank'><b>".number_format($this->arrObjInfo['following'],0)."</b><br>FOLLOWING</a></div>
							<div style='float: left; margin-left: 10px'><a href='http://twitter.com/".$this->arrObjInfo['username']."/followers' target='_blank'><b>".number_format($this->arrObjInfo['followers'],0)."</b><br>FOLLOWERS</a></div>
						</div>
						
					</div>

				";
			}

			return $returnVal;
		}
		
		// Twitter connection functions below	
		
		public function generateNonce() {
			
			return md5(uniqid(rand().time(), true));
	
		}
		
		public function generateSignature($httpMethod, $reqURL) {
			
			ksort($this->arrParameters);
			$arrEncodedString = array();
			
			
			
			foreach($this->arrParameters as $key => $value) {
				
				$encodedString = "";
				
				$encodedKey = rawurlencode($key."=");
				$encodedValue = rawurlencode($value);
				
				$encodedString = $encodedKey.$encodedValue;
				$arrEncodedString[] = $encodedString;
			}
			
			$paramString = implode(rawurlencode("&"), $arrEncodedString);
			
			$sigString = strtoupper($httpMethod)."&".rawurlencode($reqURL)."&".$paramString;

			$this->lastSig = $sigString;
			
			$signingKey = rawurlencode($this->consumerSecret)."&".rawurlencode($this->oauthTokenSecret);
			
			$this->lastSignKey = $signingKey;
			
			$returnVal = base64_encode(hash_hmac("sha1", $sigString, $signingKey, true));
			
			return $returnVal;
			
		}
		
		public function prepareAuthHeader() {

			ksort($this->arrParameters);
			
			// Prepare Authorization Header
			
			foreach($this->arrParameters as $key => $value) {
			
				$arrHeaderParams[] = rawurlencode($key)."=\"".rawurlencode($value)."\"";
			
			}
			
			
			$arrHeader = array();
			$arrHeader[] = "Authorization: OAuth ".implode(", ", $arrHeaderParams);

			return $arrHeader;
			
		}
		
		
		public function getRequestToken($setCallBackURL = "") {
			
			if($setCallBackURL != "") {
				$this->callbackURL = $setCallBackURL;	
			}
			
			$this->arrParameters['oauth_callback'] = rawurlencode($this->callbackURL);
			$this->arrParameters['oauth_timestamp'] = time();
			$this->arrParameters['oauth_nonce'] = $this->generateNonce();
			$this->arrParameters['oauth_signature'] = $this->generateSignature("POST", $this->requestTokenURL);
			
			$this->arrParameters['oauth_callback'] = $this->callbackURL;
			
			$arrHeader = $this->prepareAuthHeader();
			
			
			$response = $this->httpRequest($this->requestTokenURL, "POST", $arrHeader);
			
			if($this->httpCode == 200) {
				
				$returnVal = $response;
				
				
			}
			else {
				
				$returnVal = false;
				
			}
			
			
			unset($this->arrParameters['oauth_callback']);
			
			return $returnVal;
			
		}
		
		public function getAccessToken($setOauthToken, $oauthVerifier) {
			
			if($setOauthToken != "") {
				$this->oauthToken = $setOauthToken;	
			}
			
			$this->arrParameters['oauth_token'] = $this->oauthToken;
			$this->arrParameters['oauth_timestamp'] = time();
			$this->arrParameters['oauth_nonce'] = $this->generateNonce();
			$this->arrParameters['oauth_verifier'] = $oauthVerifier;
			$this->arrParameters['oauth_signature'] = $this->generateSignature("POST", $this->accessTokenURL);
			
			unset($this->arrParameters['oauth_verifier']);
			
			$arrHeader = $this->prepareAuthHeader();
			
			
			$this->lastAuthHeader = $arrHeader;
			
			$arrPost = array();
			$arrPost['oauth_verifier'] = $oauthVerifier;
			$response = $this->httpRequest($this->accessTokenURL, "POST", $arrHeader, $arrPost);
			
			$this->lastResponse = $response;
			
			if($this->httpCode == 200) {
			
				$returnVal = $response;
			
			
			}
			else {
			
				$returnVal = false;
			
			}
		
			
			return $returnVal;
			
			
		}
		
		
		public function sendTweet($tweet) {
			
			$this->arrParameters['oauth_token'] = $this->oauthToken;
			$this->arrParameters['oauth_timestamp'] = time();
			$this->arrParameters['oauth_nonce'] = $this->generateNonce();
			$this->arrParameters['status'] = rawurlencode($tweet);
			$this->arrParameters['oauth_signature'] = $this->generateSignature("POST", $this->tweetURL);
			
			unset($this->arrParameters['status']);

			
			$arrHeader = $this->prepareAuthHeader();			
			
			$response = $this->httpRequest($this->tweetURL, "POST", $arrHeader, "status=".urlencode($tweet));
			
			if($this->httpCode == 200) {
				$returnVal = $response;
			}
			else {
				$returnVal = false;
			}
			
			return $returnVal;
			
		}
		
		public function getTwitterInfo() {
			

			$this->arrParameters['oauth_token'] = $this->oauthToken;
			$this->arrParameters['oauth_timestamp'] = time();
			$this->arrParameters['oauth_nonce'] = $this->generateNonce();
			$this->arrParameters['oauth_signature'] = $this->generateSignature("GET", $this->twitterInfoURL);
			
			$arrHeader = $this->prepareAuthHeader();
			
			
			$this->lastAuthHeader = $arrHeader;
			
			
			$response = $this->httpRequest($this->twitterInfoURL, "GET", $arrHeader);
			
			if($this->httpCode == 200) {
			
				$returnVal = json_decode($response, true);
			
			
			}
			else {
			
				$returnVal = false;
			
			}			
			
			return $returnVal;
			
		}
		
		
		public function getEmbeddedTweet($tweetID) {

			$returnArr = array();
			if(is_numeric($tweetID)) {
				
				$this->resetParamArray();

				$url = $this->embedTweetURL."?id=".$tweetID."&maxwidth=350&hide_media=1";
				
				$this->arrParameters['hide_media'] = 1;
				$this->arrParameters['id'] = $tweetID;
				$this->arrParameters['maxwidth'] = 350;
				$this->arrParameters['oauth_token'] = $this->oauthToken;
				$this->arrParameters['oauth_timestamp'] = time();
				$this->arrParameters['oauth_nonce'] = $this->generateNonce();
				$this->arrParameters['oauth_signature'] = $this->generateSignature("GET", $this->embedTweetURL);
				
			
				$arrHeader = $this->prepareAuthHeader();
						
				$this->lastAuthHeader = $arrHeader;

				$response = $this->httpRequest($url, "GET", $arrHeader);

				if($this->httpCode == 200) {
				
					$returnArr = json_decode($response, true);
				
				}
				
			}
			
			
			return $returnArr;
			
		}
		
		public function httpRequest($url, $method, $headers=array(), $postfields=array()) {
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
			if($method == "POST") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			}
			
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			
			
			$result = curl_exec($ch);
			$info = curl_getinfo($ch);
			
			
			$this->lastHTTPRequestInfo = $info;
			
			$this->httpCode = $info['http_code'];
			
			return $result;
			
		}
		
		public function resetParamArray() {
			
			$this->arrParameters = array();
			
			$this->arrParameters['oauth_consumer_key'] = $this->consumerKey;
			$this->arrParameters['oauth_signature_method'] = "HMAC-SHA1";
			$this->arrParameters['oauth_version'] = "1.0";
		
		}

		public function getConsumerKey() {
			return $this->consumerKey;	
		}
		
		public function getConsumerSecret() {
			return $this->consumerSecret;	
		}

	}

?>