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
class RankCategory extends Rank {
	
	
	function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."rankcategory";
		$this->strTableKey = "rankcategory_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."ranks";
		$this->strAssociateKeyName = "rank_id";
	}
	
	
	function get_privileges() {
		
		
		return false;
	}
	
	
	/*
	 * - getRanks Function -
	 * 
	 * Returns an array of rank ids for all ranks with the selected Rank Category
	 * 
	 */
	
	function getRanks() {
		
		$arrRanks = array();
		
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."ranks WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."' ORDER BY ordernum DESC");
			while($row = $result->fetch_assoc()) {
				
				$arrRanks[] = $row['rank_id'];
				
			}
			
		}
		
		return $arrRanks;
		
	}
	
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			$this->resortOrder();
			if(!$this->MySQL->error) {
				$returnVal = true;
			}
			else {
				$this->MySQL->displayError("basic.php");
			}
	
		}
	
		return $returnVal;
	
	}
	
	
}

?>