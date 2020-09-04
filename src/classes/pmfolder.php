<?php

	include_once("basicsort.php");
	
	
	class PMFolder extends BasicSort {
	
		const INBOX_ID = 0;
		const SENTBOX_ID = -1;
		const TRASH_ID = -2;
		
		public $intMemberID;
				
		public function __construct($sqlConnection) {
	
			$this->MySQL = $sqlConnection;
			$this->strTableKey = "pmfolder_id";
			$this->strTableName = $this->MySQL->get_tablePrefix()."privatemessage_folders";
			$this->intMemberID = 0;
			
			//$this->strAssociateKeyName = "pm_id";
			//$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."privatemessages";
			
			$this->strCategoryKey = "member_id";
			
		}
		
		public function select($intIDNum, $numericIDOnly = true) {
			$arrSpecialFolders = array("Inbox" => self::INBOX_ID, "Sent Messages" => self::SENTBOX_ID, "Trash" => self::TRASH_ID);
			if(in_array($intIDNum, $arrSpecialFolders)) {
				$this->arrObjInfo['name'] = array_search($intIDNum, $arrSpecialFolders);
				$this->intTableKeyValue = $intIDNum;

			}
			else {
				$returnVal = parent::select($intIDNum, numericIDOnly);	
			}
			
			return $returnVal;
		}
	
		function isMemberFolder() {

			$returnVal = false;
			if(($this->intTableKeyValue != "" && $this->arrObjInfo['member_id'] == $this->intMemberID) || ($this->intTableKeyValue == 0 || $this->intTableKeyValue == -1 || $this->intTableKeyValue == -2)) {
				$returnVal = true;	
			}
			
			return $returnVal;
		}
				
		
		function listFolders($memberID=0) {

			if($memberID != 0) {
				$this->intMemberID = $memberID;
			}
			
			$returnArr = array();
			if(isset($this->intMemberID) && is_numeric($this->intMemberID)) {

				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessage_folders WHERE member_id = '".$this->intMemberID."' ORDER BY sortnum");
				while($row = $result->fetch_assoc()) {
					$returnArr[$row['pmfolder_id']] = $row['name'];					
				}
				
			}
						
			return $returnArr;
			
		}
		
		function getFolderContents() {
			
			$arrPM = array();
			$arrMultiPM = array();			
			
			if($this->intTableKeyValue !== "" && $this->intMemberID != 0) {
				$pmTable = $this->MySQL->get_tablePrefix()."privatemessages";
				$pmMultiTable = $this->MySQL->get_tablePrefix()."privatemessage_members";
				
				if($this->intTableKeyValue == -1) {
					$filterSQL = "senderfolder_id = '".$this->intTableKeyValue."' AND sender_id = '".$this->intMemberID."' AND deletesender = '0'";	
				}
				else {
					$filterSQL = "receiver_id = '".$this->intMemberID."' AND receiverfolder_id = '".$this->intTableKeyValue."' AND deletereceiver = '0'";
				}
				
				
				//echo "SELECT pm_id, datesent FROM ".$pmTable." WHERE (senderfolder_id = '".$this->intTableKeyValue."' AND sender_id = '".$this->intMemberID."' AND deletesender = '0') OR (receiver_id = '".$this->intMemberID."' AND receiverfolder_id = '".$this->intTableKeyValue."' AND deletereceiver = '0')";
				$result = $this->MySQL->query("SELECT pm_id, datesent FROM ".$pmTable." WHERE ".$filterSQL);
				while($row = $result->fetch_assoc()) {
					$arrPM[$row['pm_id']] = $row['datesent'];
				}
				
				$result = $this->MySQL->query("SELECT ".$pmMultiTable.".pmmember_id, ".$pmMultiTable.".pm_id, ".$pmTable.".datesent FROM ".$pmTable.", ".$pmMultiTable." WHERE ".$pmMultiTable.".pm_id = ".$pmTable.".pm_id AND ".$pmMultiTable.".pmfolder_id = '".$this->intTableKeyValue."' AND ".$pmMultiTable.".deletestatus = '0' AND ".$pmMultiTable.".member_id = '".$this->intMemberID."'");
				while($row = $result->fetch_assoc()) {

					$arrPM[$row['pm_id']] = $row['datesent'];
					$arrMultiPM[$row['pm_id']] = $row['pmmember_id'];
					
				}
				
				arsort($arrPM);
				
			}
			
			$returnArr = array($arrPM, $arrMultiPM);
			
			return $returnArr;
		}
		
		
		// Used to select special folders (Inbox, Sent, Trash)
		function setFolder($folderID) {
			if(is_numeric($folderID)) {
				$this->intTableKeyValue = $folderID;
			}
		}
		
	
	}