<?php

include_once("basicorder.php");
include_once("member.php");
include_once("rankcategory.php");
include_once("squad.php");
include_once("tournament.php");

class PrivateMessage extends BasicOrder {
	
	public $multiMemPMObj;
	public $memberObj;
	public $rankCatObj;
	public $squadObj;
	public $tournamentObj;
	
	public function __construct($sqlConnection) {

		$this->MySQL = $sqlConnection;
		$this->strTableKey = "pm_id";
		$this->strTableName = $this->MySQL->get_tablePrefix()."privatemessages";
		
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."privatemessage_members";
		$this->strAssociateKeyName = "pmmember_id";
		
		
		$this->multiMemPMObj = new Basic($sqlConnection, "privatemessage_members", "pmmember_id");
		
		$this->memberObj = new Member($sqlConnection);
		$this->rankCatObj = new RankCategory($sqlConnection);
		$this->squadObj = new Squad($sqlConnection);
		$this->tournamentObj = new Tournament($sqlConnection);
	}
	
	
	public function getRecipients($blnNameOnly=false) {
		global $MAIN_ROOT;
		$arrGroups = array();
		
		if($this->intTableKeyValue != "" && $this->arrObjInfo['receiver_id'] == 0) {
			$arrGroups['list'] = array();
			$arrGroups['rank'] = array();
			$arrGroups['squad'] = array();
			$arrGroups['tournament'] = array();
			$arrGroups['rankcategory'] = array();
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."privatemessage_members WHERE pm_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				if($row['grouptype'] != "" && !in_array($row['group_id'], $arrGroups[$row['grouptype']])) {
					$arrGroups[$row['grouptype']][] = $row['group_id'];
					$dispName = "";
					
					switch($row['grouptype']) {
						case "rankcategory":
							$dispName = ($this->rankCatObj->select($row['group_id'])) ? $this->rankCatObj->get_info_filtered("name")." - Category" : "";
							break;
						case "rank":
							$dispName = ($this->memberObj->objRank->select($row['group_id'])) ? $this->memberObj->objRank->get_info_filtered("name")." - Rank" : "";
							break;
						case "squad":
							$dispName = ($this->squadObj->select($row['group_id'])) ? "<a href='".$MAIN_ROOT."squads/profile.php?sID=".$row['group_id']."'>".$this->squadObj->get_info_filtered("name")." Members</a>" : "";
							break;
						case "tournament":
							$dispName = ($this->tournamentObj->select($row['group_id'])) ? "<a href='".$MAIN_ROOT."tournaments/view.php?tID=".$row['group_id']."'>".$this->tournamentObj->get_info_filtered("name")." Players</a>" : "";
							break;
					}

					if($dispName != "" && !$blnNameOnly) {
						$arrGroups['list'][$row['pmmember_id']] = $row['member_id'];
					}
					elseif($dispName != "") {
						$arrGroups['list'][] = $dispName;
					}
					
				}
				elseif($row['grouptype'] == "") {
					$this->memberObj->select($row['member_id']);
					if($blnNameOnly) {
						$arrGroups['list'][] = $this->memberObj->getMemberLink();
					}
					else {
						$arrGroups['list'][] = $row['member_id'];
					}
				}
			}
			
			if($blnNameOnly) {
				$arrGroups['list'] = implode(", ", $arrGroups['list']);	
			}
		
		}
		
		return $arrGroups['list'];
		
	}
	
		// Gets folder based on Member ID
		function getFolder($memberID, $multiPM=false) {
			
			$returnVal = "";
			
			if($this->intTableKeyValue != "") {
				
				$arrRecipients = $this->getRecipients();
				
				if($this->arrObjInfo['sender_id'] == $memberID && !$multiPM) {
					$returnVal = $this->arrObjInfo['senderfolder_id'];
				}
				elseif($this->arrObjInfo['receiver_id'] == $memberID && !$multiPM) {
					$returnVal = $this->arrObjInfo['receiverfolder_id'];
				}
				elseif($this->arrObjInfo['receiver_id'] == 0 && in_array($memberID, $arrRecipients)) {
					$tempKey = array_search($memberID, $arrRecipients);
					$this->multiMemPMObj->select($tempKey);
					
					$returnVal = $this->multiMemPMObj->get_info("pmfolder_id");
				}
								
			}
			
			return $returnVal;
		}
	
}