<?php

	class Twitch {
		
		private $MySQL;
		private $socialObj;
		private $pluginObj;
		private $memberObj;
		private $twitchSocialID;
		private $arrGameImageSizes;
		private $arrPreviewImageSizes;
		public $data;

		
		public function __construct($sqlConnection) {

			$this->MySQL = $sqlConnection;
			$this->socialObj = new Social($sqlConnection);
			$this->pluginObj = new btPlugin($sqlConnection);
			$this->memberObj = new Member($sqlConnection);
			
			$this->pluginObj->selectByName("Twitch");
			$this->twitchSocialID = $this->pluginObj->getConfigInfo("twitchsocial_id");
			
			$this->socialObj->select($this->twitchSocialID);
			
			$this->arrGameImageSizes = array(
				"small" => array("width" => "52", "height" => "72"),
				"medium" => array("width" => "136", "height" => "190"),
				"large" => array("width" => "272", "height" => "380")
			);
			
			$this->arrPreviewImageSizes = array(
				"small" => array("width" => "80", "height" => "50"),
				"medium" => array("width" => "320", "height" => "200"),
				"large" => array("width" => "640", "height" => "400")
			);
			
		}
		
		public function getGameImageURL($game, $size="small") {
			$arrSizes = array_keys($this->arrGameImageSizes);
			if(!in_array($size, $arrSizes)) {
				$size = "small";	
			}
			
			
			return "http://static-cdn.jtvnw.net/ttv-boxart/".urlencode($game)."-".$this->arrGameImageSizes[$size]['width']."x".$this->arrGameImageSizes[$size]['height'].".jpg";
			
		}
		
		public function getStreamInfo($memberID) {

			$returnVal = array();
			$arrMembers = $this->getMembers();
			if(in_array($memberID, $arrMembers)) {
			
				$twitchName = $this->getTwitchName($memberID);
				
				if(substr($twitchName,0,strlen("http://twitch.tv/")) == "http://twitch.tv/") {
					$twitchName = substr($twitchName, strlen("http://twitch.tv/"));
				}
				elseif(substr($twitchName,0,strlen("http://www.twitch.tv/")) == "http://www.twitch.tv/") {
					$twitchName = substr($twitchName, strlen("http://www.twitch.tv/"));
				}
				
				$returnVal = $this->httpRequest("https://api.twitch.tv/kraken/streams/".$twitchName);
				
				$this->data['streamInfo'] = json_decode($returnVal, true);
			}
			
			return $returnVal;
		}
		
		public function getMembers() {
		
			$arrSocialMembers = $this->socialObj->getAssociateIDs("ORDER BY value");
			$arrMembers = array();

			foreach($arrSocialMembers as $socialMemberID) {
				$this->socialObj->objSocialMember->select($socialMemberID);
				$selectMember = $this->memberObj->select($this->socialObj->objSocialMember->get_info("member_id"));
				
				if($selectMember && $this->socialObj->objSocialMember->get_info("value") != "" && $this->memberObj->get_info("disabled") == 0) {
					$memberID = $this->socialObj->objSocialMember->get_info("member_id");
					$arrMembers[$socialMemberID] = $memberID;
				}
			}
			return $arrMembers;
		}
		
		
		public function isOnline($memberID, $resetStreamInfo=true) {

			if($resetStreamInfo) { $this->getStreamInfo($memberID); }
			
			$returnVal = false;
			if($this->data['streamInfo']['stream'] != null) {
				$returnVal = true;
			}
			
			return $returnVal;
		}
		
		public function displayMemberCard($memberID) {
			global $hooksObj, $webInfoObj;
			
			$this->memberObj->select($memberID);
			
			// Gather data for card
			$arrCardData['memberID'] = $memberID;
			$arrCardData['memberInfo'] = $this->memberObj->get_info_filtered();
			$arrCardData['memberLink'] = $this->memberObj->getMemberLink();
			$arrCardData['online'] = $this->isOnline($memberID);
			$arrCardData['twitchName'] = $this->getTwitchName($memberID);
			$arrCardData['game'] = $this->data['streamInfo']['stream']['game'];
			$arrCardData['viewers'] = $this->data['streamInfo']['stream']['viewers'];
			$arrCardData['streamTitle'] = $this->data['streamInfo']['stream']['channel']['status'];
			
			$arrCardData['rawData'] = $this->data['streamInfo'];				
			
			
			
			$this->data['memberCard'] = $arrCardData;
		
			$webInfoObj->twitchObj = $this;
			$webInfoObj->setPage("plugins/twitch/include/membercard.php");
			$hooksObj->run("twitch-plugin-display-card");
			
			$webInfoObj->displayPage();
			
		}
		
		public function displayAllMemberCards() {

			$counter = 0;
			foreach($this->getMembers() as $memberID) {
				
				$this->displayMemberCard($memberID);
				$counter++;
			}

			return $counter;
		}
		
		public function getTwitchName($memberID) {

			$returnVal = "None";
			$socialMemberID = array_search($memberID, $this->getMembers());
			
			if($this->socialObj->objSocialMember->select($socialMemberID)) {
				
				$returnVal = $this->socialObj->objSocialMember->get_info_filtered("value");				
				
			}

			return $returnVal;
		}
		
		public function hasTwitch($memberID) {

			$returnVal = false;
			if($this->memberObj->select($memberID)) {
				$memberInfo = $this->memberObj->get_info_filtered();
				$returnVal = in_array($this->memberObj->get_info("member_id"), $this->getMembers());
				$this->data['memberID'] = $memberInfo['member_id'];
				
			}
			
			return $returnVal;
		}
		
		
		public function httpRequest($url, $method="GET", $headers=array(), $postfields=array()) {
		
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
			if($method == "POST") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			}
			elseif($method == "DELETE") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);				
			}
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
			$result = curl_exec($ch);			
		
			return $result;
			
		}
		
		
	}

?>