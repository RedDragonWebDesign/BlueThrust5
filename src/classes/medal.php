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


include_once("rank.php");
class Medal extends Rank {
	
	
	public $objFrozenMedal;
    
    function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."medals";
		$this->strTableKey = "medal_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."medals_members";
		$this->strAssociateKeyName = "member_id";
		
		$this->objFrozenMedal = new Basic($sqlConnection, "freezemedals_members", "freezemedal_id");

	}

	
	
	/*
	 * Returns the number of members with the selected medal
	 */
	
	function countMembers() {
		
		$num_rows = 0;
		
		if(isset($this->intTableKeyValue)) {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."medals_members WHERE medal_id = '".$this->intTableKeyValue."'");
			$num_rows = $result->num_rows;
		}
		
		return $num_rows;

	}
	
	
	
	/*
	
	-Delete Method-
	
	Will delete the selected medal from the database.  You must first "select" a table row using the select method in order to delete.
	
	*/
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$info = $this->arrObjInfo;
			$countErrors = 0;
			$result = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
	
			if($this->MySQL->error) {
				$countErrors++;
			}

			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."medals_members WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			$this->resortOrder();
			
			if($countErrors == 0) {
				$returnVal = true;
				deleteFile(BASE_DIRECTORY.$info['imageurl']);
			}
			
	
		}
	
		return $returnVal;
	
	}
	
	public function getFrozenMembersList() {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."freezemedals_members WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
			
				$returnArr[$row['freezemedal_id']] = $row['member_id'];
				
			}
			
		}
		
		return $returnArr;
		
	}

}

?>
