<?php
	
	class Social extends BasicOrder {
		
		public $memberID;
		public $objSocialMember;
		
		public function __construct($sqlConnection) {
			
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."social";
			$this->strTableKey = "social_id";	
			
			$this->strAssociateKeyName = "socialmember_id";
			$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."social_members";
			$this->objSocialMember = new Basic($sqlConnection, "social_members", "socialmember_id");
			
		}
		
		
		public function getMemberSocialInfo($fullValue=false) {
			
			$arrReturn = array();
			if($this->memberID != "" && is_numeric($this->memberID)) {
				$result = $this->MySQL->query("SELECT ".$this->strAssociateTableName.".* FROM ".$this->strAssociateTableName.", ".$this->strTableName." WHERE ".$this->strTableName.".social_id = ".$this->strAssociateTableName.".social_id AND ".$this->strAssociateTableName.".member_id = '".$this->memberID."' AND ".$this->strAssociateTableName.".value != '' ORDER BY ".$this->strTableName.".ordernum DESC");
				while($row = $result->fetch_assoc()) {
					
					$this->select($row['social_id']);
					$arrReturn[$this->arrObjInfo['social_id']] = ($fullValue) ? $this->arrObjInfo['url'].$row['value'] : $row['value'];
					
				}
			}
			
			return $arrReturn;
			
		}
		
		public function getFullURL() {

			$returnVal = false;
			if($this->intTableKeyValue != "" && $this->memberID != "" && is_numeric($this->memberID)) {
				
				$this->objSocialMember->selectByMulti(array("social_id" => $this->intTableKeyValue, "member_id" => $this->memberID));
				
				$returnVal = $this->arrObjInfo['url'].$this->objSocialMember->get_info_filtered("value");
				
			}
			
			return $returnVal;
		}
		
		public function delete() {
			$returnVal = false;
			if($this->intTableKeyValue != "") {
				$info = $this->arrObjInfo;
				if(parent::delete()) {
					deleteFile(BASE_DIRECTORY.$info['icon']);
					$returnVal = true;
				}
				
			}
			
			return $returnVal;
		}
	}
	
?>