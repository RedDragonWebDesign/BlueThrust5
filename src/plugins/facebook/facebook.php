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

	class Facebook extends Basic {

		
		protected $appID;
		protected $appSecret;
		
		
		public $facebookLoginURL = "https://www.facebook.com/dialog/oauth";
		public $facebookAccessTokenURL = "https://graph.facebook.com/oauth/access_token";
		public $facebookCheckTokenURL = "https://graph.facebook.com/debug_token";
		public $tokenNonce;
		public $accessToken;
		public $httpCode;
		public $arrFacebookInfo;
	
		public function __construct($sqlConnection) {
			
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."facebook";
			$this->strTableKey = "fbconnect_id";
			
			$this->objPlugin = new btPlugin($sqlConnection);
			$this->objPlugin->selectByName("Facebook Login");
			
			$apiInfo = $this->objPlugin->getAPIKeys();
			
			$this->appID = $apiInfo['appID'];
			$this->appSecret = $apiInfo['appSecret'];
			
		}
		
		
		public function hasFacebook($memberID) {
			
			$returnVal = false;
			if(is_numeric($memberID)) {
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE member_id = '".$memberID."'");
				if($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$this->select($row['fbconnect_id']);
					$returnVal = true;
				}
				
			}
			
			return $returnVal;
			
		}
		
		public function getProfilePic($picSize="") {
		
			$returnVal = false;
			if($this->intTableKeyValue != "") {
				
				$addToURL = "";
				$arrPicSizes = array("square", "small", "normal", "large");
				if($picSize != "" && in_array($picSize, $arrPicSizes)) {
					$addToURL = "?type=".$picSize;
				}
				
				$returnVal = "http://graph.facebook.com/".$this->arrObjInfo['facebook_id']."/picture".$addToURL;
				
			}
			
			return $returnVal;
		}
		
		
		public function authorizeLogin($facebookID) {
			
			$returnVal = false;
			
			if(is_numeric($facebookID)) {
				$query = "SELECT * FROM ".$this->strTableName." WHERE facebook_id = '".$facebookID."'";
				$result = $this->MySQL->query($query);
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
	
					$this->select($row['fbconnect_id']);
					$returnVal = true;
				}
			}
			
			return $returnVal;
		}
		
		// FACEBOOK API FUNCTIONS
		
		public function getFBConnectLink($callbackURL) {
			
			$this->tokenNonce = md5(uniqid(rand().time()));
			
			
			$fbConnectURL = $this->facebookLoginURL."?client_id=".$this->appID."&redirect_uri=".urlencode($callbackURL)."&scope=publish_actions&state=".$this->tokenNonce;
			
			return $fbConnectURL;
			
		}
		
		
		public function	getAccessToken($loginCode, $checkNonce, $callbackURL) {
			
			$accessTokenURL = $this->facebookAccessTokenURL."?client_id=".$this->appID."&redirect_uri=".urlencode($callbackURL)."&client_secret=".$this->appSecret."&code=".$loginCode;
			$params = array();
			
			if($this->tokenNonce == $checkNonce) {
				
				$response = file_get_contents($accessTokenURL);

				parse_str($response, $params);
				
				$this->accessToken = $params['access_token'];

			}
			
			
			return $params;
			
		}
		
		public function checkAccessToken() {
			
			$returnVal = false;
			if(isset($this->accessToken)) {

				$this->getFBInfo();
				
				$appToken = $this->generateAppToken();
				
				$checkTokenURL = $this->facebookCheckTokenURL."?input_token=".$this->accessToken."&access_token=".$appToken;

				$res = file_get_contents($checkTokenURL);
				$res = preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', $res);
				$response = json_decode($res, true);
		
				if($this->appID == $response['data']['app_id'] && $this->arrFacebookInfo['id'] == $response['data']['user_id']) {
					$returnVal = true;	
				}
				
			}
			
			return $returnVal;
			
		}
		
		public function generateAppToken() {
			
			$params = array();
			$accessTokenURL = $this->facebookAccessTokenURL."?client_id=".$this->appID."&client_secret=".$this->appSecret."&grant_type=client_credentials";
			
			$response = file_get_contents($accessTokenURL);
			
			parse_str($response, $params);
			
			return $params['access_token'];
			
		}
		
		
		public function getFBInfo() {
			
			$graph_url = "https://graph.facebook.com/me?access_token=".$this->accessToken;
			
			$fbResp = file_get_contents($graph_url);
			
			
			$jsonResponse = json_decode(file_get_contents($graph_url), true);
		
			$this->arrFacebookInfo = $jsonResponse;
			
			
			return $jsonResponse;
		}
		
		public function postStatus($message, $linkurl, $linkname) {
			
			$returnVal = "";
			if(isset($this->accessToken)) {
				
				$postURL = "https://graph.facebook.com/".$this->arrFacebookInfo['id']."/feed";
				
				$response = $this->httpRequest($postURL, "POST", array(), "access_token=".$this->accessToken."&caption=".urlencode($message)."&link=".urlencode($linkurl)."&name=".urlencode($linkname));
				
				$returnVal = $response;
			}
			
			return $returnVal;
			
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
			if($this->intTableKeyValue != "") {	
			
				$blnDelete = parent::delete();
				
				// Revoke access on Facebook
				
				$deleteURL = "https://graph.facebook.com/".$this->arrFacebookInfo['id']."/permissions?access_token=".$this->accessToken;

				$revokeAccess = $this->httpRequest($deleteURL, "DELETE", array(), "access_token=".$this->accessToken);
				
				if($blnDelete && $revokeAccess == "true") {

					$returnVal = true;
					
				}
				
			}
			
			return $returnVal;
			
		}
		
		
		public function getAppID() {
			return $this->appID;
		}
		
		public function getAppSecret() {
			return $this->appSecret;	
		}
		
	}


?>