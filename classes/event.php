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


include_once("basic.php");
include_once("basicorder.php");
include_once("news.php");
include_once("member.php");

class Event extends Basic {
	
	public $objEventMember;
	public $objEventPosition;
	public $objEventMessage;
	public $objEventMessageComment;
	public $arrPositionOptions = array("modchat", "invitemembers", "manageinvites", "postmessages", "managemessages", "attendenceconfirm", "editinfo", "eventpositions");
	public $arrInviteStatus = array(0 => "Invited", 1 => "Attending", 2 => "Maybe", 3 => "Not Attending");
	protected $blnManageAllEvents;
	
	public function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableKey = "event_id";
		$this->strTableName = $this->MySQL->get_tablePrefix()."events";
		
		$this->objEventMember = new Basic($sqlConnection, "events_members", "eventmember_id");
		$this->objEventPosition = new BasicSort($sqlConnection, "eventpositions", "position_id", "event_id");
		$this->objEventMessage = new News($sqlConnection, "eventmessages", "eventmessage_id", "eventmessage_comment", "comment_id");
		$this->objEventMessageComment = $this->objEventMessage->objComment;
		
		$this->blnManageAllEvents = false;
		$this->checkManageAllEvents();
		
	}
	
	
	public function checkManageAllEvents() {
		
		$this->blnManageAllEvents = false;
		if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {
			$member = new Member($this->MySQL);
			$consoleObj = new ConsoleOption($this->MySQL);
			
			$manageAllEventsCID = $consoleObj->findConsoleIDByName("Manage All Events");
			if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
				$consoleObj->select($manageAllEventsCID);
				$this->blnManageAllEvents = $member->hasAccess($consoleObj);
			}
		}
		
		return $this->blnManageAllEvents;
		
	}
	
	public function getManageAllStatus() {
		return $this->blnManageAllEvents;	
	}
	
	
	public function inviteMember($memberID, $invitedByMID=0) {

		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			if(!in_array($memberID, $this->getInvitedMembers(true)) && $memberID != $this->arrObjInfo['member_id']) {
				$arrColumns = array("event_id", "member_id", "invitedbymember_id");
				$arrValues = array($this->intTableKeyValue, $memberID, $invitedByMID);
				
				if($this->objEventMember->addNew($arrColumns, $arrValues)) {
					$returnVal = true;
				}	
			}
			else {
				$returnVal = "dup";	
			}
			
		}
		
		return $returnVal;
		
	}
	
	
	
	public function getInvitedMembers($returnMemberIDs=false) {
		
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."events_members WHERE event_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
	
				if($returnMemberIDs) {
					$returnArr[] = $row['member_id'];
				}
				else {
					$returnArr[] = $row['eventmember_id'];				
				}
				
			}
			
		}
		
		return $returnArr;
				
	}
	
	/*
	 * - getEventMemberID -
	 * 
	 * Returns the eventmember_id based off of the member_id (intMemberID).  Will select this member if blnSelectMember is set to true.
	 */
	public function getEventMemberID($intMemberID, $blnSelectMember=false) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($intMemberID)) {
		
			$result = $this->MySQL->query("SELECT eventmember_id FROM ".$this->MySQL->get_tablePrefix()."events_members WHERE event_id = '".$this->intTableKeyValue."' AND member_id = '".$intMemberID."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$returnVal = $row['eventmember_id'];
				
				if($blnSelectMember) {
					$this->objEventMember->select($returnVal);	
				}
				
			}
		}
		
		return $returnVal;
		
	}
	
	public function getPositions($sqlOrderBy = "") {
		$returnArr = array();

		if($this->intTableKeyValue != "") {
			
			if($sqlOrderBy == "") {
				$sqlOrderBy = " ORDER BY sortnum";
			}
			else {
				$sqlOrderBy = $this->MySQL->real_escape_string($sqlOrderBy);	
			}
			
			$result = $this->MySQL->query("SELECT position_id FROM ".$this->MySQL->get_tablePrefix()."eventpositions WHERE event_id = '".$this->intTableKeyValue."'".$sqlOrderBy);
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['position_id'];
			}
			
		}
		
		return $returnArr;
		
	}
	
	
	public function memberHasAccess($memberID, $privilegeName) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
		
			// Check if member is the creator, if so he has access.
			if($memberID == $this->arrObjInfo['member_id'] || $this->blnManageAllEvents) {
				$returnVal = true;				
			}
			else {
				// Otherwise check if their position has access
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."events_members WHERE member_id = '".$memberID."' AND event_id = '".$this->intTableKeyValue."'");
				if($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					

					if($this->objEventPosition->select($row['position_id'])) {
						if($this->objEventPosition->get_info($privilegeName) == 1) {
							$returnVal = true;							
						}
					}
					elseif($privilegeName == "invitemembers" && $this->arrObjInfo['invitepermission'] == 1) {
						$returnVal = true;						
					}
					elseif($privilegeName == "postmessages" && $this->arrObjInfo['messages'] == 1) {
						$returnVal;						
					}
					
				}
			}
		}
		
		
		return $returnVal;
		
	}
	
	
	public function notifyEventInvites($strMessage) {
		
		if($this->intTableKeyValue != "") {
			$objMember = new Member($this->MySQL);
			
			$arrInvitedMembers = $this->getInvitedMembers(true);
			foreach($arrInvitedMembers as $value) {

				if($objMember->select($value)) {
					$objMember->postNotification($strMessage);	
				}
				
			}
		
		}
		
		return true;
	}
	
	public function chatRoomStarted() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT eventchat_id FROM ".$this->MySQL->get_tablePrefix()."eventchat WHERE event_id = '".$this->intTableKeyValue."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$returnVal = $row['eventchat_id'];
			}
			
			
		}
		
		return $returnVal;
		
	}
	
	
	public function delete() {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT eventmessage_id FROM ".$this->MySQL->get_tablePrefix()."eventmessages WHERE event_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
			
				if($this->objEventMessage->select($row['eventmessage_id'])) {
					$this->objEventMessage->delete();
				}
	
			}
			
			$countErrors = 0;
			
			if($this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."eventpositions WHERE event_id = '".$this->intTableKeyValue."'")) {
				$countErrors++;
			}
			
			if($this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."events_members WHERE event_id = '".$this->intTableKeyValue."'")) {
				$countErrors++;	
			}
			
			
			if($this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."events WHERE event_id = '".$this->intTableKeyValue."'")) {
				$countErrors++;	
			}
		
			if($countErrors == 0) {
				$returnVal = true;	
			}
		
		}
		
		return $returnVal;
		
	}

	
	
}



?>