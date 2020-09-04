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
include_once("basicorder.php");

include_once("forumpost.php");

class ForumBoard extends BasicSort {
	
	public $objPost;
	public $objTopic;
	public $objMemberAccess;
	public $objRankAccess;
	public $objMod;

	
	public function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."forum_board";
		$this->strTableKey = "forumboard_id";
		$this->strCategoryKey = "forumcategory_id";
		
		$this->objPost = new ForumPost($sqlConnection);

		$this->objTopic = $this->objPost->objTopic;
		
		$this->objMemberAccess = new Basic($sqlConnection, "forum_memberaccess", "forummemberaccess_id");
		$this->objRankAccess = new Basic($sqlConnection, "forum_rankaccess", "forumrankaccess_id");
		
		$this->objMod = new Basic($sqlConnection, "forum_moderator", "forummoderator_id");
		
	}
		
	
	public function delete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$arrSubForums = $this->getSubForums();
			
			$result[] = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_post WHERE forumboard_id = '".$this->intTableKeyValue."'");	
			$result[] = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_topic WHERE forumboard_id = '".$this->intTableKeyValue."'");
			$result[] = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_rankaccess WHERE board_id = '".$this->intTableKeyValue."'");
			$result[] = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_memberaccess WHERE board_id = '".$this->intTableKeyValue."'");
			$result[] = parent::delete();
			
			if(count($arrSubForums) > 0) {
				$subForumObj = new ForumBoard($this->MySQL);
				$arrColumns = array("sortnum", "subforum_id");
				foreach($arrSubForums as $subForumID) {
					$subForumObj->select($subForumID);
					$subForumInfo = $subForumObj->get_info();
					
					$newSortNum = $subForumInfo['sortnum']+($this->arrObjInfo['sortnum']-1);
					$arrValues = array($newSortNum, $this->arrObjInfo['subforum_id']);
					
					$subForumObj->update($arrColumns, $arrValues);
					
				}
				
				$subForumObj->resortOrder();
				
			}
			
			if(!in_array(false, $result)) {
				$returnVal = true;	
			}
			
		}
		
		return $returnVal;
		
	}
	
	/*
	 * - secureBoard Method -
	 * 
	 * Used when adding or editing a board to the forum.  Adds the allowed ranks and members to the database.
	 * 
	 * Returns true on success and false on failure
	 */
	
	public function secureBoard($arrRanks, $arrMembers) {
		
		$returnVal = false;
		$countErrors = 0;
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_rankaccess WHERE board_id = '".$this->intTableKeyValue."'");
			$arrColumns = array("rank_id", "board_id", "accesstype");
			foreach($arrRanks as $rankID => $accessValue) {
				$arrValues = array($rankID, $this->intTableKeyValue, $accessValue);
				if(!$this->objRankAccess->addNew($arrColumns, $arrValues)) {
					$countErrors++;
					break;
				}
			}
			
			if($countErrors == 0) {
				
				$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_memberaccess WHERE board_id = '".$this->intTableKeyValue."'");
				
				$arrColumns = array("member_id", "board_id", "accessrule");
				foreach($arrMembers as $memberID => $accessRule) {
					$arrValues = array($memberID, $this->intTableKeyValue, $accessRule);
					if(!$this->objMemberAccess->addNew($arrColumns, $arrValues)) {
						$countErrors++;
						break;
					}
				}				
			}
			
			
			$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."forum_rankaccess`");
			$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."forum_memberaccess`");
			
			
			if($countErrors == 0) {
				$returnVal = true;	
			}
			
		}
		
		return $returnVal;
	}
	
	public function getMemberAccessRules() {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."forum_memberaccess WHERE board_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				
				$returnArr[$row['member_id']] = $row['accessrule'];
				
			}

		}
		
		return $returnArr;
	}
	
	
	public function getRankAccessRules() {
	
		$returnArr = array();
		if($this->intTableKeyValue != "") {
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."forum_rankaccess WHERE board_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
	
				$returnArr[$row['rank_id']] = $row['accesstype'];
	
			}
			
			
		}
	
		return $returnArr;
	}
	
	public function memberHasAccess($memberInfo, $fullAccessOnly=false) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			if($this->arrObjInfo['accesstype'] == 1) {
				$checkCount = 0;
				
				
				$arrRankAccess = $this->getRankAccessRules();
				if($fullAccessOnly) {
					$checkAccess = $arrRankAccess[$memberInfo['rank_id']] == 0;
				}
				else {
					$checkAccess = ($arrRankAccess[$memberInfo['rank_id']] == 0 || $arrRankAccess[$memberInfo['rank_id']] == 1);	
				}
				
				
				if((isset($arrRankAccess[$memberInfo['rank_id']]) && $checkAccess) || $memberInfo['rank_id'] == 1) {
					$checkCount++;
				}
				
				$arrMembers = $this->getMemberAccessRules();
				$memberAccessIsSet = isset($arrMembers[$memberInfo['member_id']]);
				
				if($memberAccessIsSet && !$fullAccessOnly && $arrMembers[$memberInfo['member_id']] != 0) {
					$checkCount++;
				}
				elseif($memberAccessIsSet && $fullAccessOnly && $arrMembers[$memberInfo['member_id']] == 1) {
					$checkCount++;	
				}
				elseif($memberAccessIsSet && $arrMembers[$memberInfo['member_id']] == 0) {
					$checkCount = 0;	
				}
				
				
				
				if($checkCount > 0) {
					$returnVal = true;	
				}
				
			}
			else {
				$returnVal = true;	
			}
			
			
		}
		
		return $returnVal;
		
	}
	
	
	/*
	 * - memberIsMod Function -
	 * 
	 * Checks if a member is a mod of the selected board.
	 * Returns true if yes, false if no
	 */
	
	public function memberIsMod($memberID, $returnForumModeratorID=false) {

		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($memberID)) {
			
			$result = $this->MySQL->query("SELECT forummoderator_id FROM ".$this->MySQL->get_tablePrefix()."forum_moderator WHERE member_id = '".$memberID."' AND forumboard_id = '".$this->intTableKeyValue."'");
			if($result->num_rows > 0 && !$returnForumModeratorID) {
				$returnVal = true;	
			}
			elseif($result->num_rows > 0 && $returnForumModeratorID) {
				$row = $result->fetch_assoc();
				$returnVal = $row['forummoderator_id'];
			}
			
		}
		$this->memberID = $memberid;
		return $returnVal;
		
	}
	
	/*
	 * - getForumTopics Function -
	 * 
	 * Returns an array of forum topics sorted by the last post's date
	 */
	
	public function getForumTopics($sqlORDERBY = "", $sqlLIMIT = "") {
		
		$returnArr = array();
		
		if($sqlORDERBY == "") {
			$sqlORDERBY = " fp.dateposted DESC";	
		}

		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT ft.forumpost_id, ft.lastpost_id, fp.dateposted FROM ".$this->MySQL->get_tablePrefix()."forum_topic ft,  ".$this->MySQL->get_tablePrefix()."forum_post fp WHERE ft.forumboard_id = '".$this->intTableKeyValue."' AND fp.forumpost_id = ft.lastpost_id ORDER BY ".$sqlORDERBY.$sqlLIMIT);
			while($row = $result->fetch_assoc()) {
				
				//$this->objPost->select($row['lastpost_id']);
				//$datePosted = $this->objPost->get_info("dateposted");
				
				$returnArr[] = $row['forumpost_id'];
				
			}
			
			
			//arsort($returnArr);
			
		}
		
		return $returnArr;
	}

	
	public function hasNewTopics($memberID) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($memberID)) {
			
			$checkTime = time()-(60*60*24*7); // Checking topics with last posts dated within the last week
			$arrNewTopics = array();
			$result = $this->MySQL->query("SELECT ft.forumtopic_id FROM ".$this->MySQL->get_tablePrefix()."forum_topic ft, ".$this->MySQL->get_tablePrefix()."forum_post fp WHERE forumboard_id = '".$this->intTableKeyValue."' AND fp.forumpost_id = ft.lastpost_id AND fp.dateposted > '".$checkTime."'");
			while($row = $result->fetch_assoc()) {
				$arrNewTopics[] = $row['forumtopic_id'];	
			}
			
			if(count($arrNewTopics) > 0) {
				$sqlTopicIDs = "('".implode("','", $arrNewTopics)."')";
				$result = $this->MySQL->query("SELECT forumtopic_id FROM ".$this->MySQL->get_tablePrefix()."forum_topicseen WHERE member_id = '".$memberID."' AND forumtopic_id IN ".$sqlTopicIDs);
			
				if($result->num_rows != count($arrNewTopics)) {
					$returnVal = true;	
				}
			}
			
		}
		
		return $returnVal;
	}
	
	public function countTopics() {
		
		$returnVal = 0;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT forumtopic_id FROM ".$this->MySQL->get_tablePrefix()."forum_topic WHERE forumboard_id = '".$this->intTableKeyValue."'");
			
			$returnVal = $result->num_rows;
		}
		
		return $returnVal;
	}
	
	public function countPosts() {
		
		$returnVal = 0;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT fp.forumpost_id FROM ".$this->MySQL->get_tablePrefix()."forum_post fp, ".$this->MySQL->get_tablePrefix()."forum_topic ft WHERE forumboard_id = '".$this->intTableKeyValue."' AND fp.forumtopic_id = ft.forumtopic_id");
		
			$returnVal = $result->num_rows;
		}
		
		return $returnVal;

	}
	
	public function addMod($memberID) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($memberID) && !$this->memberIsMod($memberID)) {
			$returnVal = $this->objMod->addNew(array("member_id", "forumboard_id", "dateadded"), array($memberID, $this->intTableKeyValue, time()));
		}
		
		return $returnVal;
		
	}
	
	public function removeMod($memberID) {
		
		$returnVal = false;
		$checkMod = $this->memberIsMod($memberID, true);
		
		if($this->intTableKeyValue != "" && $checkMod !== false) {

			$this->objMod->select($checkMod);
			$returnVal = $this->objMod->delete();

		}
		
		return $returnVal;

	}
	
	public function getAllBoards() {
		
		$arrReturn = array();
		$temp = $this->intTableKeyValue;
		$dbprefix = $this->MySQL->get_tablePrefix();
		$result = $this->MySQL->query("SELECT ".$dbprefix."forum_board.forumboard_id FROM ".$dbprefix."forum_board, ".$dbprefix."forum_category WHERE ".$dbprefix."forum_board.forumcategory_id = ".$dbprefix."forum_category.forumcategory_id AND ".$dbprefix."forum_board.subforum_id = '0' ORDER BY ".$dbprefix."forum_category.ordernum DESC, ".$dbprefix."forum_board.sortnum");
		while($row = $result->fetch_assoc()) {
			$this->select($row['forumboard_id']);
			$arrReturn[] = $row['forumboard_id'];

			$arrReturn = array_unique(array_merge($arrReturn, $this->getAllSubForums()));
		}
		
		$this->select($intTableKeyValue);

		
		return $arrReturn;
	}
	
	
	public function getSubForums() {
		
		$arrReturn = array();
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT forumboard_id FROM ".$this->strTableName." WHERE subforum_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
				$arrReturn[] = $row['forumboard_id'];
			}
						
		}
		return $arrReturn;
	}
	
	
	function isSubforum() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && $this->arrObjInfo['subforum_id'] != 0) {
			$returnVal = true;
		}

		return $returnVal;
		
	}
	
	function calcBoardDepth($boardDepth=0) {
		
		
		if($this->isSubforum()) {
			$temp = $this->intTableKeyValue;
			$boardDepth++;
			
			$this->select($this->arrObjInfo['subforum_id']);
			$boardDepth = $this->calcBoardDepth($boardDepth);
			
			$this->select($temp);
		}

		return $boardDepth;
	}
	
	
	/*
	 * Returns all sub-forum IDs not just ones directly under the selected forum 
	 */
	public function getAllSubForums($arrIDs=array()) {
		
		$arrReturn = array();
		if($this->intTableKeyValue != "") {
			$temp = $this->intTableKeyValue;
			$subForums = $this->getSubForums();
			$arrReturn = array_merge($arrIDs, $subForums);
			foreach($subForums as $boardID) {
				$this->select($boardID);
				if(count($this->getSubForums()) > 0) {
					$arrReturn = $this->getAllSubForums($arrReturn);
				}
			}
			
			$arrReturn = array_unique($arrReturn);
			
			$this->select($temp);
		}
		
		return $arrReturn;
	}
	
	
	/*
	 * BasicSort Functions Re-written to filter by 2 categories
	 * 
	 */
	
	public function getHighestSortNum() {
		
		$returnVal = false;
		if($this->arrObjInfo[$this->strCategoryKey] != "") {
			$catKeyValue = $this->arrObjInfo[$this->strCategoryKey];

			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$catKeyValue."' AND subforum_id = '".$this->arrObjInfo['subforum_id']."'");
			$returnVal = $result->num_rows;
			
		}
		
		return $returnVal;
		
	}
	
	
	function makeRoom($strBeforeAfter) {
	
		$strBeforeAfter = strtolower($strBeforeAfter);
		$newSortNum = "false";
		if($this->intTableKeyValue != "" && ($strBeforeAfter == "before" OR $strBeforeAfter == "after")) {
			$consoleInfo = $this->arrObjInfo;
			$startSaving = false;
			$x = 1;
			$arrConsoleOptions = array();
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$consoleInfo[$this->strCategoryKey]."' AND subforum_id = '".$this->arrObjInfo['subforum_id']."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
	
				if($strBeforeAfter == "before" AND $row[$this->strTableKey] == $consoleInfo[$this->strTableKey]) {
					$newSortNum = $x;
					$x++;
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
				}
				elseif($strBeforeAfter == "after" AND $row[$this->strTableKey] == $consoleInfo[$this->strTableKey]) {
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
					$newSortNum = $x;
					$x++;
	
				}
				else {
	
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
	
				}
	
			}
	
			$updateArray = array();
	
			$updateRowName = array("sortnum");
			if(is_numeric($newSortNum)) {
				$intOriginalCID = $this->intTableKeyValue;
				foreach($arrConsoleOptions as $key => $value) {
	
					if($key != $value[1]) {
	
						$this->select($value[0]);
						$this->update(array("sortnum"), array($key));
	
					}
	
				}
	
				$this->select($intOriginalCID);
	
			}
	
		}
	
	
		return $newSortNum;
	}
	
	function resortOrder() {
		$counter = 1; // ordernum counter
		$consoleInfo = $this->arrObjInfo;
		$x = 0; // array counter
		$arrUpdateID = array();
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$consoleInfo[$this->strCategoryKey]."' AND subforum_id = '".$this->arrObjInfo['subforum_id']."' ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {
			$arrUpdateID[] = $row[$this->strTableKey];
			$x++;
		}
	
		$intOriginalConsole = $this->intTableKeyValue;
		foreach($arrUpdateID as $intUpdateID) {
			$arrUpdateCol[0] = "sortnum";
			$arrUpdateVal[0] = $counter;
			$this->select($intUpdateID);
			$this->update($arrUpdateCol, $arrUpdateVal);
			$counter++;
		}
	
		$this->select($intOriginalConsole);
	
	
		return true;
	}
	
	
	function selectByOrder($intOrderNum) {
	
		$returnVal = false;
		if(is_numeric($intOrderNum) && $this->arrObjInfo[$this->strCategoryKey] != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE sortnum = '".$intOrderNum."' AND ".$this->strCategoryKey." = '".$this->arrObjInfo[$this->strCategoryKey]."' AND subforum_id = '".$this->arrObjInfo['subforum_id']."'");
			if($result->num_rows > 0) {
				$this->arrObjInfo = $result->fetch_assoc();
				$returnVal = true;
				$this->intTableKeyValue = $this->arrObjInfo[$this->strTableKey];
				$returnVal = true;
			}
	
	
		}
	
		return $returnVal;
	
	}
	
	function validateOrder($intOrderNumID, $strBeforeAfter, $blnEdit = false, $intEditOrderNum = "") {
	
		$returnVal = false;
	
		$catKeyValue = $this->get_info($this->strCategoryKey);
		
		if($intOrderNumID == "first") {
			// "(no other categories)" selected, check to see if there are actually no other categories
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$catKeyValue."' AND subforum_id = '".$this->arrObjInfo['subforum_id']."'");
			$num_rows = $result->num_rows;
	
			if($num_rows == 0 || ($num_rows == 1 && $blnEdit)) {
				$returnVal = 1;
			}
	
		}
		elseif($this->select($intOrderNumID) && ($strBeforeAfter == "before" || $strBeforeAfter == "after")) {
	
	
			// Check first to see if we are editing or adding a new rank
	
			if($blnEdit) {
	
				// Editing...
				// Check to see if the rank's order is being changed or if its staying the same
	
	
				$addTo = -1; // Minus 1 if we chose "before"
				if($strBeforeAfter == "after") {
					$addTo = 1; // Add 1 if we chose "after"
				}
	
				// Get the ordernum of the rank that we are using to determine the order of the rank being edited (*** It was selected in the IF statement above ***)
				$thisCatOrderNum = $this->get_info("sortnum");
	
				$checkOrderNum = $intEditOrderNum+$addTo; // This is the new ordernum of the rank we are editing
	
				// If checkOrderNum is the same as intEditOrderNum then the order hasn't changed
				if($checkOrderNum != $intEditOrderNum) {
					$returnVal = $this->makeRoom($strBeforeAfter);
				}
				else {
					$returnVal= $intEditOrderNum;
				}
	
	
			}
			else {
	
				$returnVal = $this->makeRoom($strBeforeAfter);
	
			}
	
		}
	
		return $returnVal;
	}
	
	public function setSubForumID($subforumID) {

		if(is_numeric($subforumID)) {

			$this->arrObjInfo['subforum_id'] = $subforumID;
			
		}
		
	}
	
	
	public function showSearchForm() {
		global $MAIN_ROOT;
		$searchLabel = "Search";

		if($this->intTableKeyValue != "") {
			$filterBoard = $this->intTableKeyValue;
			$searchLabel = "Search Board";
		}
		
		if(is_numeric($this->objTopic->get_info("forumtopic_id"))) {
			$filterTopic = $this->objTopic->get_info("forumtopic_id");
			$searchLabel = "Search Topic";
		}
		
		define("SHOW_FORUMSEARCH", true);
		include("templates/searchform.php");
		
	}
	
}