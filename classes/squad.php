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


include_once("basicsort.php");

class Squad extends Basic {
	
	
	public $objSquadMember;
	public $objSquadRank;
	public $arrSquadMemberInfo;
	public $arrSquadPrivileges;
	protected $blnManageAllSquads;
	
	
	
	function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."squads";
		$this->strTableKey = "squad_id";
		$this->objSquadMember = new Basic($sqlConnection, "squads_members", "squadmember_id");
		$this->objSquadRank = new BasicSort($sqlConnection, "squadranks", "squadrank_id", "squad_id");
		$this->arrSquadPrivileges = array("postnews", "managenews", "postshoutbox", "manageshoutbox", "addrank", "manageranks", "setrank", "editprofile", "sendinvites", "acceptapps", "removemember");
		
		$this->checkManageAllSquads();
		
	}
	
	
	public function checkManageAllSquads() {
		
		$this->blnManageAllSquads = false;
		if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {
			$member = new Member($this->MySQL);
			$consoleObj = new ConsoleOption($this->MySQL);
			
			$manageAllSquadsCID = $consoleObj->findConsoleIDByName("Manage All Squads");
			if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
				$consoleObj->select($manageAllSquadsCID);
				$this->blnManageAllSquads = $member->hasAccess($consoleObj);
			}
		}
		
		return $this->blnManageAllSquads;
	}
	
	public function countMembers() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squad_id = '".$this->intTableKeyValue."'");
			$returnVal = $result->num_rows;
			
		}
		
		
		return $returnVal;
		
	}
	
	
	public function getMemberList() {
	
		$returnVal = array();
	
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squad_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
	
				$returnVal[] = $row['member_id'];
	
			}
	
		}
	
		return $returnVal;
	
	}
	
	
	
	// Same as getMemberList but sorted in rank order.  Highest Rank to Lowest
	
	public function getMemberListSorted() {
		
		$returnVal = array();
		
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squad_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				
				$this->objSquadRank->select($row['squadrank_id']);
				
				$returnVal[$row['member_id']] = $this->objSquadRank->get_info("sortnum");
				
			}
			
			asort($returnVal);
			$returnArr = array_keys($returnVal);
		}
		
		return $returnArr;
		
	}
	

	
	public function getOutstandingInvites() {
		
		$returnVal = array();
		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadinvites WHERE squad_id = '".$this->intTableKeyValue."' AND status = '0'");
			while($row = $result->fetch_assoc()) {
		
				$returnVal[] = $row['receiver_id'];
		
			}
		
		}
		
		return $returnVal;
		
	}
	
	
	public function getOutstandingApplications() {
	
		$returnVal = array();
	
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadapps WHERE squad_id = '".$this->intTableKeyValue."' AND status = '0'");
			while($row = $result->fetch_assoc()) {
	
				$returnVal[] = $row['member_id'];
	
			}
	
		}
	
		return $returnVal;
	
	}
	
	/*
	 * - getRecruiterMembers Function -
	 * 
	 * Returns an array of squad member_id's (member table) who can accept/deny applications
	 */
	public function getRecruiterMembers() {
		
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadranks WHERE squad_id = '".$this->intTableKeyValue."' AND acceptapps = '1'");
			while($row = $result->fetch_assoc()) {
				$recruitRanks[] = $row['squadrank_id'];	
			}
			
			$sqlRecruitRanks = "('".implode("','", $recruitRanks)."')";
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squadrank_id IN ".$sqlRecruitRanks);
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['member_id'];			
			}
		}
		
		return $returnArr;
	}
	
	
	public function countRanks() {
	
		$returnVal = false;
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadranks WHERE squad_id = '".$this->intTableKeyValue."'");
			$returnVal = $result->num_rows;
	
		}
	
	
		return $returnVal;
	
	}
	
	
	
	public function getRankList() {
	
		$returnVal = array();
	
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadranks WHERE squad_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
	
				$returnVal[] = $row['squadrank_id'];
	
			}
	
		}
	
		return $returnVal;
	
	}
	
	public function getFounderRankID() {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			
			$founderID = $this->get_info("member_id");
			$founderSquadMemberID = $this->getSquadMemberID($founderID);
			
			$this->objSquadMember->select($founderSquadMemberID);
			$returnVal = $this->objSquadMember->get_info("squadrank_id");
			
		}
		
		
		return $returnVal;
		
	}

	
	/*
	 * 
	 * - getSquadMemberID Function -
	 * 
	 * Returns the squadmember_id value for the given $memberID for the selected squad.
	 * 
	 */
	
	public function getSquadMemberID($memberID) {
		
		$returnVal = false;
		if($this->intTableKeyValue && is_numeric($memberID)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squad_id = '".$this->intTableKeyValue."' AND member_id = '".$memberID."'");
			
			if($result->num_rows == 1) {
				$row = $result->fetch_array();
				$returnVal = $row['squadmember_id'];
			}

			
		}
		
		return $returnVal;
	}
	
	
	public function getNewsPostList($newsType) {
		
		// 1 - Public
		// 2 - Private
		// 3 - Shoutbox
		
		$returnArr = array();
		if($this->intTableKeyValue != "" && is_numeric($newsType) && ($newsType >= 1 && $newsType <= 3)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."squadnews WHERE squad_id = '".$this->intTableKeyValue."' AND newstype = '".$newsType."'");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['squadnews_id'];	
			}
			
		}
		
		return $returnArr;
		
	}
	
	
	public function memberHasAccess($memberID, $privName) {
		
		$returnVal = false;
		$intSquadMemberID = $this->getSquadMemberID($memberID);		
		
		if($this->blnManageAllSquads) {
			
			$returnVal = true;
			
		}
		elseif($intSquadMemberID !== false && in_array($privName, $this->arrSquadPrivileges)) {
			
			$this->objSquadMember->select($intSquadMemberID);
			$squadMemberRankID = $this->objSquadMember->get_info("squadrank_id");
			
			$this->objSquadRank->select($squadMemberRankID);
			$squadRankInfo = $this->objSquadRank->get_info();
			
			if($squadRankInfo[$privName] == 1) {
				
				$returnVal = true;
			}
			
		
		}
		
		return $returnVal;
		
	}
	
	
	public function delete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$info = $this->arrObjInfo;
			
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squads_members WHERE squad_id = '".$this->intTableKeyValue."'");
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squadnews WHERE squad_id = '".$this->intTableKeyValue."'");
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squadranks WHERE squad_id = '".$this->intTableKeyValue."'");
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squadinvites WHERE squad_id = '".$this->intTableKeyValue."'");
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squadapps WHERE squad_id = '".$this->intTableKeyValue."'");
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."squads WHERE squad_id = '".$this->intTableKeyValue."'");
			
			if(!$this->MySQL->error) {
				$returnVal = true;
				if($info['logourl'] != "" && file_exists(BASE_DIRECTORY.$info['logourl'])) {
					deleteFile(BASE_DIRECTORY.$info['logourl']);
				}
				
			}
			else {
				$this->MySQL->displayError("basic.php");
			}
		
		
		}
		
		return $returnVal;
	}
	
	public function getManageAllStatus() {
		return $this->blnManageAllSquads;
	}
	
}