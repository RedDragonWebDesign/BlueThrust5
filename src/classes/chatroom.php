<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("basic.php");
require_once("event.php");
class ChatRoom extends Basic {

	protected $objChatMessage;
	protected $objEvent;
	protected $objChatRoomList;

	public function __construct($sqlConnection) {

		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."eventchat";
		$this->strTableKey = "eventchat_id";
		$this->objChatMessage = new Basic($sqlConnection, "eventchat_messages", "eventchatmessage_id");
		$this->objChatRoomList = new Basic($sqlConnection, "eventchat_roomlist", "eventchatlist_id");

		$this->objEvent = new Event($sqlConnection);
	}


	public function select($intIDNum) {

		$returnVal = false;
		if (is_numeric($intIDNum) && parent::select($intIDNum)) {
			if ($this->objEvent->select($this->arrObjInfo['event_id'])) {
				$returnVal = true;
			}
		}

		return $returnVal;
	}

	public function postMessage($strMessage, $intMemberID) {

		$returnVal = false;
		if ($this->intTableKeyValue != "") {
			$returnVal = $this->objChatMessage->addNew([$this->strTableKey, "member_id", "message", "dateposted"], [$this->intTableKeyValue, $intMemberID, $strMessage, time()]);
		}

		return $returnVal;
	}

	public function getRoomList($blnActiveOnly = false) {

		$addSQL = "";
		if ($blnActiveOnly) {
			$addSQL = " AND inactive = '0'";
		}

		$returnArr = [];
		if ($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."eventchat_roomlist WHERE eventchat_id = '".$this->intTableKeyValue."'".$addSQL);
			while ($row = $result->fetch_assoc()) {
				$tempVar = $row['eventchatlist_id'];
				$returnArr[$tempVar] = $row['member_id'];
			}
		}

		return $returnArr;
	}

	public function enterRoom($intMemberID) {

		$returnVal = false;
		if ($this->intTableKeyValue != "") {
			$arrRoomList = $this->getRoomList();

			if (!in_array($intMemberID, $arrRoomList)) {
				$returnVal = $this->objChatRoomList->addNew(["member_id", "eventchat_id"], [$intMemberID, $this->intTableKeyValue]);
			} elseif (in_array($intMemberID, $arrRoomList)) {
				$intChatListID = array_search($intMemberID, $arrRoomList);
				$this->objChatRoomList->select($intChatListID);
				if ($this->objChatRoomList->get_info("inactive") == 1) {
					$returnVal = $this->objChatRoomList->update(["inactive"], [0]);
				}
			}
		}

		return $returnVal;
	}


	public function leaveRoom($intMemberID) {

		$returnVal = false;
		if ($this->intTableKeyValue != "") {
			$arrRoomList = $this->getRoomList();
			if (in_array($intMemberID, $arrRoomList)) {
				$intChatListID = array_search($intMemberID, $arrRoomList);
				$this->objChatRoomList->select($intChatListID);
				$returnVal = $this->objChatRoomList->update(["inactive"], [1]);
			} else {
				$returnVal = true;
			}
		}

		return $returnVal;
	}



}
