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


include_once("consoleoption.php");
include_once("profileoption.php");
include_once("rank.php");
include_once("medal.php");
include_once("forumboard.php");
include_once("social.php");
class Member extends Basic {

	protected $objProfileOption;
	public $objRank;
	public $objSocial;
	
	function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."members";
		$this->strTableKey = "member_id";
		
		$this->objProfileOption = new ProfileOption($sqlConnection);
		$this->objRank = new Rank($sqlConnection);
		$this->objSocial = new Social($sqlConnection);
	}
	
	
	
	function select($memberID) {
		$returnVal = false;
		if(is_numeric($memberID)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE member_id = '$memberID'");
			if($result->num_rows > 0) {
				
				$this->arrObjInfo = $result->fetch_assoc();
				$this->intTableKeyValue = $this->arrObjInfo['member_id'];
				$returnVal = true;
			}
			
		}
		else {
			$memberID = $this->MySQL->real_escape_string($memberID);
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE username = '$memberID'");
			
			if($result->num_rows > 0) {
				$this->arrObjInfo = $result->fetch_assoc();
				$this->intTableKeyValue = $this->arrObjInfo['member_id'];
				$returnVal = true;
				
				$this->objRank->select($this->arrObjInfo['rank_id']);
			}

		}

		$this->objSocial->memberID = $this->intTableKeyValue;
		
		return $returnVal;
		
	}
	
	function authorizeLogin($check_password, $encryptPW=0) {
		
		$checkRealPassword = $this->arrObjInfo['password'];
		$checkRealPassword2 = $this->arrObjInfo['password2'];
		
		if($encryptPW == 1) {
			
			$checkPass = crypt($check_password, $checkRealPassword2);
			
		}
		else {
			$checkPass = $check_password;
		}
		
		$returnVal = false;
		
		if($checkRealPassword == $checkPass && $this->arrObjInfo['disabled'] == 0) {
			$returnVal = true;
		}
		
		return $returnVal;
	
	}
	
	function set_password($new_password) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" ) {
			
			$passwordInfo = encryptPassword($new_password);
			
			if($this->update(array("password", "password2"), array($passwordInfo['password'], $passwordInfo['salt']))) {
				$returnVal = true;	
			}
			
		}
		
		return $returnVal;
		
	}
	
	
	/*
	 * 
	 * - playsGame Function -
	 * 
	 * Checks to see if the selected member plays a particular game identified by the gamesplayed_id
	 * 
	 * Returns true or false
	 * 
	 */
	function playsGame($gamesplayed_id) {
		
		$returnVal = false;
		if(is_numeric($gamesplayed_id)) {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."gamesplayed_members WHERE gamesplayed_id = '".$gamesplayed_id."' AND member_id = '".$this->intTableKeyValue."'");
		
			$num_rows = $result->num_rows;
			
			if($num_rows > 0) {
				$returnVal = true;
			}
		
		}
		
		return $returnVal;
	}
	
	/*
	 *  - gamesPlayed Function -
	 *  
	 *  Returns an array of games played ids
	 */
	
	function gamesPlayed() {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT gamesplayed_id FROM ".$this->MySQL->get_tablePrefix()."gamesplayed_members WHERE member_id = '".$this->intTableKeyValue."'");
		
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['gamesplayed_id'];
			}
		
		}
		
		return $returnArr;
		
	}
	
	
	/*
	 * 
	 * - getSquadList Function -
	 * 
	 * Returns an array of squad_id's for the selected member.  If the boolean value $founderOnly is set to true, then it only
	 * groups squads where the member is the founder.
	 * 
	 * 
	 */
	
	function getSquadList($founderOnly=false) {
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
			
			if($founderOnly) {
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads WHERE member_id = '".$this->intTableKeyValue."'";
			}
			else {
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE member_id = '".$this->intTableKeyValue."'";
			}
			
			$result = $this->MySQL->query($query);
			while($row = $result->fetch_array()) {
				$returnArr[] = $row['squad_id'];			
			}
		
		}
		
		return $returnArr;
		
	}
	
	
	
	/*
	 *
	* - getTournamentList Function -
	*
	* Returns an array of tournament_id's for the selected member.  If the boolean value $creatorOnly is set to true, then it only
	* groups tournaments where the member is the creator or manager.  If left as false will only return the tournaments that the member is playing in
	*
	*
	*/
	
	function getTournamentList($creatorOnly=false) {
		$returnArr = array();
	
		if($this->intTableKeyValue != "") {
			if($creatorOnly) {
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournaments WHERE member_id = '".$this->intTableKeyValue."'";
				$result = $this->MySQL->query($query);
				while($row = $result->fetch_array()) {
					$returnArr[] = $row['tournament_id'];
				}
			
				$query = "SELECT tournament_id FROM ".$this->MySQL->get_tablePrefix()."tournament_managers WHERE member_id = '".$this->intTableKeyValue."'";
				$result = $this->MySQL->query($query);
				while($row = $result->fetch_assoc()) {
					$returnArr[] = $row['tournament_id'];
				}
				
			}
			else {

				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE member_id = '".$this->intTableKeyValue."'";
				$result = $this->MySQL->query($query);
				while($row = $result->fetch_array()) {
					$teamArr[] = $row['team_id'];
				}


				$teamSQL = "('".implode("','", $teamArr)."')";
				
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentteams WHERE tournamentteam_id IN ".$teamSQL;
				$result = $this->MySQL->query($query);
				//echo $this->MySQL->error;
				while($row = $result->fetch_array()) {
					$returnArr[] = $row['tournament_id'];
					//echo $row['tournament_id']."<br>";
				}
				
				
				
			}
			
		//print_r($returnArr);
			
	
		}
	
		return $returnArr;
	
	}
	
	
	function hasAccess($consoleOption) {
		
		$returnVal = false;
		$consoleInfo = $consoleOption->get_info_filtered();

		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."console_members WHERE member_id = '".$this->intTableKeyValue."' AND console_id = '".$consoleInfo['console_id']."'");
			$num_rows = $result->num_rows;
			
			if($num_rows == 1) {
				$accessInfo = $result->fetch_assoc();
				

				if($accessInfo['allowdeny'] == 1) {
					$returnVal = true;	
				}

			}
			elseif($num_rows == 0 && $consoleOption->hasAccess($this->arrObjInfo['rank_id'])) {
				
				$returnVal = true;

			}
			

		}
		
		return $returnVal;
		
	}
	
	
	function getProfileValue($profileOptionID, $skipSelectOption=false) {
		
		$returnVal = "";
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."profileoptions_values WHERE member_id = '".$this->intTableKeyValue."' AND profileoption_id = '".$profileOptionID."'");

			if($result->num_rows == 1) {
				
				$row = $result->fetch_assoc();
				$returnVal = filterText($row['inputvalue']);
				
				$this->objProfileOption->select($profileOptionID);
				
				if($this->objProfileOption->isSelectOption() && !$skipSelectOption) {
					// returnVal is currently just a selectopt_id 
					// Look up what the value is for the selectopt_id
					
			
					$this->objProfileOption->objProfileOptionSelect->select($returnVal);
					
					$returnVal = $this->objProfileOption->objProfileOptionSelect->get_info_filtered("selectvalue");
		
				}
				
				
				
			}
			else {
				$returnVal = "Not Set";	
			}

		}
		
		return $returnVal;
		
	}
	
	function setProfileValue($profileOptionID, $profileOptionValue) {
		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue)) {
		
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."profileoptions_values WHERE member_id = '".$this->intTableKeyValue."' AND profileoption_id = '".$profileOptionID."'");
		
			if($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$this->objProfileOption->objProfileOptionValue->select($row['values_id']);
				$this->objProfileOption->objProfileOptionValue->delete();
			}
			
			
			if($this->objProfileOption->objProfileOptionValue->addNew(array("profileoption_id", "member_id", "inputvalue"), array($profileOptionID, $this->intTableKeyValue, $profileOptionValue))) {
				$returnVal = true;
			}

			
		}
		
		return $returnVal;

	}
	
	
	function getGameStatValue($gameStatID) {
	
		$returnVal = "";
		$gameStatObj = new Basic($this->MySQL, "gamestats", "gamestats_id");
		
		if($this->intTableKeyValue != "" && $gameStatObj->select($gameStatID)) {
			
			$gameStatInfo = $gameStatObj->get_info_filtered();
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."gamestats_members WHERE member_id = '".$this->intTableKeyValue."' AND gamestats_id = '".$gameStatID."'");
	
			if($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				
				if($gameStatInfo['textinput'] != 1) {
					$returnVal = round($row['statvalue'], $gameStatInfo['decimalspots']);
				}
				else {
					$returnVal = $row['stattext'];
				}
			
			}
	
	
		}
	
		return $returnVal;
	}
	
	
	function getMemberLink($args=array("color" => true)) {
		global $MAIN_ROOT;
		$returnVal = "";
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue)) {
			
			$memberRank = new Rank($this->MySQL);
			$memberRankCat = new Basic($this->MySQL, "rankcategory", "rankcategory_id");
			$memberInfo = $this->get_info_filtered();
			
			
			$memberRank->select($memberInfo['rank_id']);
			$rankInfo = $memberRank->get_info_filtered();
			
			$memberRankCat->select($rankInfo['rankcategory_id']);
			$memberColor = $memberRankCat->get_info_filtered("color");
			
			if($args['color']) {
				$returnVal = "<span style='color: ".$rankInfo['color']."'><a href='".$MAIN_ROOT."profile.php?mID=".$memberInfo['member_id']."' style='color: ".$memberColor."' title='".$memberInfo['username']."'>".$memberInfo['username']."</a></span>";
			}
			else {
				$returnVal = "<a href='".MAIN_ROOT."profile.php?mID=".$this->intTableKeyValue."'>".$memberInfo['username']."</a>";
			}
			
			
			if($args['wrapper'] === false) {
				$returnVal = MAIN_ROOT."profile.php?mID=".$this->intTableKeyValue;
			}
			
		}
		
		return $returnVal;
		
	}
	
	
	function postNotification($strMessage, $strIconType="general") {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			$objNotification = new Basic($this->MySQL, "notifications", "notification_id");
			$time = time();
			$arrColumns = array("member_id", "datesent", "message", "icontype");
			$arrValues = array($this->intTableKeyValue, $time, $strMessage, $strIconType);
		
			if($objNotification->addNew($arrColumns, $arrValues)) {
				$returnVal = true;	
			}
		
		}
		
		return $returnVal;
		
	}
	
	function sendPM($to, $subject, $message, $replypmID=0, $arrGroups=array()) {

		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			$pmObj = new Basic($this->MySQL, "privatemessages", "pm_id");
	
			if(is_array($to)) {
				
				$multiMemPMObj = new Basic($this->MySQL, "privatemessage_members", "pmmember_id");
				
				$arrColumns = array("sender_id", "datesent", "subject", "message", "originalpm_id");
				$arrValues = array($this->intTableKeyValue, time(), $subject, $message, $replypmID);
				
				if($pmObj->addNew($arrColumns, $arrValues)) {
					
					$pmInfo = $pmObj->get_info();
					
					$arrColumns = array("pm_id", "member_id", "grouptype", "group_id");
					foreach($to as $memberID) {
						
						$groupType = (is_array($arrGroups[$memberID])) ? $arrGroups[$memberID][0] : "";
						$groupID = (is_array($arrGroups[$memberID])) ? $arrGroups[$memberID][1] : "";
						
						$arrValues = array($pmInfo['pm_id'], $memberID, $groupType, $groupID);
						
						$multiMemPMObj->addNew($arrColumns, $arrValues);
						
					}
					$returnVal = true;
				}

			}
			else {
			
				$arrColumns = array("sender_id", "receiver_id", "datesent", "subject", "message", "originalpm_id");
				$arrValues = array($this->intTableKeyValue, $to, time(), $subject, $message, $replypmID);
				
				if($pmObj->addNew($arrColumns, $arrValues)) {
		
					$returnVal = true;
					
				}
			
			}
		
		}
		
		return $returnVal;
		
		
	}
	
	
	function countPMs($showOnlyNew=false) {
		
		$totalPMInbox = 0;
		if($this->intTableKeyValue != "") {
			if($showOnlyNew) {
				$result1 = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessages WHERE receiver_id = '".$this->intTableKeyValue."' AND status = '0' AND deletereceiver = '0' AND receiverfolder_id = '0'");
				$result2 = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessage_members WHERE member_id = '".$this->intTableKeyValue."' AND seenstatus = '0' AND deletestatus = '0' AND pmfolder_id = '0'");
			}
			else {
				$result1 = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessages WHERE receiver_id = '".$this->intTableKeyValue."' AND deletereceiver = '0' AND receiverfolder_id = '0'");
				$result2 = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessage_members WHERE member_id = '".$this->intTableKeyValue."' AND deletestatus = '0' AND pmfolder_id = '0'");
			}
			
			$totalSinglePM = $result1->num_rows;			
			$totalMultiPM = $result2->num_rows;
			
			
			$totalPMInbox = $totalSinglePM+$totalMultiPM;
		}
		
		return $totalPMInbox;
		
	}
	
	
	function addProfileView() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$profileViews = $this->arrObjInfo['profileviews'];
			
			$newProfileViews = $profileViews+1;
			
			$this->update(array("profileviews"), array($newProfileViews));
			
			$this->arrObjInfo['profileviews'] = $newProfileViews;
			
			$returnVal = true;
		}
		
		return $returnVal;
		
	}
	
	function countRecruits($returnList=false) {
		
		$returnVal = 0;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE recruiter = '".$this->intTableKeyValue."' AND disabled = '0'");
			
			if($returnList) {
				
				$returnArr = array();
				
				while($row = $result->fetch_assoc()) {
					
					$returnArr[] = $row['member_id'];	
					
				}
				
				$returnVal = $returnArr;
				
			}
			else {
				$returnVal = $result->num_rows;
			}
			
		}
		
		
		return $returnVal;
		
		
	}
	
	
	/*
	 * - getMedalList Function -
	 * 
	 * Returns an array of the selected member's medals.  
	 * If $blnIDKeys is set to true, the medalmember_id will be used for the keys in the returned array.
	 * 
	 */
	function getMedalList($blnIDKeys=false, $orderNumID=0) {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			switch($orderNumID) {
				case 1:
					$sqlDisplayOrder = "m.ordernum DESC";
					break;
				case 2:
					$sqlDisplayOrder = "m.name";
					break;
				default:
					$sqlDisplayOrder = "mm.dateawarded DESC";
					break;
			}
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."medals_members mm, ".$this->MySQL->get_tablePrefix()."medals m WHERE member_id = '".$this->intTableKeyValue."' AND m.medal_id = mm.medal_id ORDER BY ".$sqlDisplayOrder);
			while($row = $result->fetch_assoc()) {
				
				
				if($blnIDKeys) {
					$key = $row['medalmember_id'];	
					$returnArr[$key] = $row['medal_id'];
				}
				else {
					$returnArr[] = $row['medal_id'];	
				}
				
				
			}
			
			
		}
		
		return $returnArr;
	}
	
	/*
	 * - autoAwardMedals Method -
	 * 
	 * Awards the selected member a medal based on number of days in clan.
	 * 
	 */
	function autoAwardMedals() {
		
		if($this->intTableKeyValue != "") {
						
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."medals WHERE autodays != '0' OR autorecruits != '0' ORDER BY ordernum DESC");
			while($row = $result->fetch_assoc()) {
				$arrMedals[] = $row['medal_id'];				
			}
			
			$medalObj = new Medal($this->MySQL);
			$awardMedalObj = new Basic($this->MySQL, "medals_members", "medalmember_id");
			foreach($arrMedals as $medalID) {
				
				$medalObj->select($medalID);
				$arrMembers = $medalObj->getAssociateIDs();
				
				$arrFrozenMembers = $medalObj->getFrozenMembersList();
				$frozenDate = 0;
				if(in_array($this->intTableKeyValue, $arrFrozenMembers)) {
					$frozenMedalID = array_search($this->intTableKeyValue, $arrFrozenMembers);
					$medalObj->objFrozenMedal->select($frozenMedalID);
					
					$frozenDate = $medalObj->objFrozenMedal->get_info("freezetime");
					
				}
				
				$daysInClan = (time() - $this->arrObjInfo['datejoined'])/86400;
				
				if($medalObj->get_info("autodays") != 0 && ($daysInClan >= $medalObj->get_info("autodays") && !in_array($this->intTableKeyValue, $arrMembers)) && time() > $frozenDate) {
					$awardMedalObj->addNew(array("medal_id", "member_id", "dateawarded"), array($medalID, $this->intTableKeyValue, time()));					
					
					$this->postNotification("You have been awarded the ".$medalObj->get_info_filtered("name")." for being the clan for ".$medalObj->get_info("autodays")." days.");
					$this->logAction("Auto awarded medal for being in the clan for ".$medalObj->get_info("autodays")." days.");
				
				}
				
				if($medalObj->get_info("autorecruits") != 0 && ($this->countRecruits() >= $medalObj->get_info("autorecruits") && !in_array($this->intTableKeyValue, $arrMembers)) && time() > $frozenDate) {
					$awardMedalObj->addNew(array("medal_id", "member_id", "dateawarded"), array($medalID, $this->intTableKeyValue, time()));
				
					$this->postNotification("You have been awarded the ".$medalObj->get_info_filtered("name")." for recruiting ".$medalObj->get_info("autorecruits")." members.");
					$this->logAction("Auto awarded medal for recruiting ".$medalObj->get_info("autorecruits")." members.");
				
				}
				
				
				
				
			}
			
			
			
		}
	
	}
	
	
	/*
	 * - autoPromote Method -
	*
	* Awards the selected member a medal based on number of days in clan.
	*
	*/
	function autoPromote() {
	
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT rank_id FROM ".$this->MySQL->get_tablePrefix()."ranks WHERE autodays != '0' ORDER BY ordernum DESC");
			while($row = $result->fetch_assoc()) {
				$arrRanks[] = $row['rank_id'];
			}
	
			$rankObj = new Rank($this->MySQL);
			$rankObj->select($this->arrObjInfo['rank_id']);
			$memberRankInfo = $rankObj->get_info();
			$daysInClan = (time() - $this->arrObjInfo['datejoined'])/86400;
			foreach($arrRanks as $rankID) {

				$rankObj->select($rankID);

				if($rankObj->get_info("ordernum") > $memberRankInfo['ordernum'] && $memberRankInfo['rank_id'] != 1 && $daysInClan >= $rankObj->get_info("autodays") && time() > $this->arrObjInfo['freezerank']) {

					if($this->update(array("rank_id", "lastpromotion"), array($rankID, time()))) {
						$this->logAction("Auto promoted for being in the clan for ".$rankObj->get_info("autodays")." days.");
						$memberRankInfo['ordernum'] = $rankObj->get_info("ordernum");
					}
					
				}
	
			}
	
	
	
		}
	
	}
	
	
	public function awardMedal($medalID, $reason="") {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$medal = new Medal($this->MySQL);
			$medalList = $this->getMedalList();
			if($medal->select($medalID) && !in_array($medalID, $medalList)) {
				$medalMemberObj = new Basic($this->MySQL, "medals_members", "medalmember_id");
				$arrColumns = array("member_id", "medal_id", "dateawarded", "reason");
				$arrValues = array($this->intTableKeyValue, $medalID, time(), $reason);
				if($medalMemberObj->addNew($arrColumns, $arrValues)) {	
					
					$this->postNotification("You were awarded the medal: <b>".$medal->get_info_filtered("name")."</b>");
					
				}
			
			}
			
		}
		
		return $returnVal;
	}
	
	
	/*
	 * - Log Action Method -
	 * 
	 * Adds a new log entry into the logs involving this member's id
	 * 
	 */
	
	function logAction($message="") {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$logObj = new Basic($this->MySQL, "logs", "log_id");
			
			$arrColumns = array("member_id", "logdate", "message", "ipaddress");
			$arrValues = array($this->intTableKeyValue, time(), $message, $_SERVER['REMOTE_ADDR']);
			
			if($logObj->addNew($arrColumns, $arrValues)) {
				$returnVal = true;	
			}
		
		
		}
		
		return $returnVal;
		
	}
	
	
	/*
	 * - Select Admin Function -
	 * 
	 * Selects the admin account
	 * 
	 */
	
	function selectAdmin() {
		
		$returnVal= false;
		$result = $this->MySQL->query("SELECT member_id FROM ".$this->strTableName." WHERE rank_id = '1'");
		$row = $result->fetch_assoc();
		
		if($this->select($row['member_id'])) {
			$returnVal = true;
		}

		
		return $returnVal;
		
	}
	
	
	function get_privileges() {
		
		$returnArr= array();
		$filterArray = array();
		
		$rankObj = new Rank($this->MySQL);
		
		if($this->intTableKeyValue != "") {
			
			$rankObj->select($this->arrObjInfo['rank_id']);
			$arrPrivileges = $rankObj->get_privileges();
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."console_members WHERE member_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				if($row['allowdeny'] == 1 && !in_array($row['console_id'], $arrPrivileges)) {
					$arrPrivileges[] = $row['console_id'];
				}
				elseif($row['allowdeny'] == 0 && in_array($row['console_id'], $arrPrivileges)) {
					$key = array_search($row['console_id'], $arrPrivileges);
					$arrPrivileges[$key] = 0;
				}
			}
			
			
			
			
		}
		
		return $arrPrivileges;
		
	}
	
	/*
	 * - hasSeenTopic Function -
	 * 
	 * Returns true if the selected member has seen the forum topic.
	 */
	
	public function hasSeenTopic($topicID) {
	
		$returnVal = false;

		if($this->intTableKeyValue != "" && $topicID != "" && is_numeric($topicID)) {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."forum_topicseen WHERE forumtopic_id = '".$topicID."' AND member_id = '".$this->intTableKeyValue."'");
			
			if($result->num_rows > 0) {
				$returnVal = true;
			}

		}
		
		
		return $returnVal;
	
	}
	
	
	public function countForumPosts() {
		$returnVal = 0;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT member_id FROM ".$this->MySQL->get_tablePrefix()."forum_post WHERE member_id = '".$this->intTableKeyValue."'");
			$returnVal = $result->num_rows;
			
		}
		
		return $returnVal;
	}
	
	public function updateTableTime() {
		
		$arrCallingInfo = debug_backtrace();
		if($arrCallingInfo[1]['function'] == "addNew") {
			parent::updateTableTime();			
		}
	}
	
	
	public function requestedIA($returnID=false) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT iarequest_id FROM ".$this->MySQL->get_tablePrefix()."iarequest WHERE member_id = '".$this->intTableKeyValue."'");
			
			if(!$returnID) {
				$returnVal = ($result->num_rows > 0) ? true : false;
			}
			else {
				$row = $result->fetch_assoc();
				$returnVal = $row['iarequest_id'];	
			}
			
		}
		
		return $returnVal;
	}
	
	
	
	
	protected function getMemberPicture($setWidth="", $setHeight="", $db_name, $defaultpic, $cssClass=array()) {
		global $MAIN_ROOT, $THEME;
		
		$checkURL = parse_url($this->arrObjInfo[$db_name]);
		
		$avatarURL = $this->arrObjInfo[$db_name];
		if($this->arrObjInfo[$db_name] == "") {
			$avatarURL = $MAIN_ROOT."themes/".$THEME."/images/".$defaultpic;
		}
		elseif(!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
			$avatarURL = $MAIN_ROOT.$this->arrObjInfo[$db_name];
		}
		
		$arrStyle = array();
		if($setWidth != "") {
			$arrStyle['width'] = $setWidth;
		}
		
		if($setHeight != "") {
			$arrStyle['height'] = $setHeight;
		}
		
		$dispStyle = "";
		if(count($arrStyle) > 0) {
			$dispStyle = " style='";
			foreach($arrStyle as $attr => $value) {
				$dispStyle .= $attr.": ".$value.";";
			}
			$dispStyle .= "'";
		}
		
		$dispClass = "";
		if(count($cssClass) > 0) {
			$dispClass = " class='";
			foreach($cssClass as $class) {
				$dispClass .= $class." ";	
			}
			$dispClass .= "'";
		}
		
		return "<img src='".$avatarURL."'".$dispStyle.$dispClass.">";
		
	}
	
	public function getAvatar($setWidth="", $setHeight="") {
		return $this->getMemberPicture($setWidth, $setHeight, "avatar", "defaultavatar.png", array("avatarImg"));
	}
	
	
	public function getProfilePic($setWidth="", $setHeight="") {
		return $this->getMemberPicture($setWidth, $setHeight, "profilepic", "defaultprofile.png");
	}
	
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$info = $this->arrObjInfo;
			
			$returnVal = parent::delete();
			if($returnVal) {
				if($info['profilepic'] != "") {
					deleteFile(BASE_DIRECTORY.$info['profilepic']);	
				}
				
				if($info['avatar'] != "") {
					deleteFile(BASE_DIRECTORY.$info['avatar']);	
				}
			}
			
		}
		return $returnVal;
	}
	
}


?>