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

class ConsoleOption extends BasicSort {

	function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."console";
		$this->strTableKey = "console_id";
		$this->strCategoryKey = "consolecategory_id";
	}


	/*
	-Console Access Checker-
	
	intRankID: Database ID for the rank that you want to check
	
	This will check if a certain ranking has the privilege to use the selected console option.  
	You must first select a console option before using this method.
	
	
	*/
	
	function hasAccess($intRankID) {
		$returnVal = false;
		if(is_numeric($intRankID) && is_numeric($this->intTableKeyValue)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."rank_privileges WHERE rank_id = '$intRankID' AND console_id = '".$this->intTableKeyValue."'");
			$countRows = $result->num_rows;
			
			if($countRows > 0) {
				$returnVal = true;
			}
			elseif($intRankID == 1) {
				$returnVal = true;	
			}
			
		}
	
		return $returnVal;
	}
	
	function findConsoleIDByName($strConsolePageTitle) {
		
		$returnVal = false;
		$strConsoleName = $this->MySQL->real_escape_string($strConsolePageTitle);
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE pagetitle = '".$strConsoleName."'");
		if($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			if($this->select($row[$this->strTableKey])) {
				$returnVal = $row[$this->strTableKey];
			}
		}
		
		return $returnVal;
	}

	function getConsoleLinkByName($strConsolePageTitle, $htmlLink=true) {
		
		$temp = 0;
		if($this->intTableKeyValue != "") {
			$temp = $this->intTableKeyValue;	
		}
		
		$cID = $this->findConsoleIDByName($strConsolePageTitle);
		$returnVal = MAIN_ROOT."members/console.php?cID=".$cID;
		if($htmlLink) {
			
			$this->select($cID);	
			$pageTitle = $this->get_info_filtered("pagetitle");
			
			
			$returnVal = "<a href='".$returnVal."'>".$pageTitle."</a>";
			
			
			if($temp != 0) {
				$this->select($temp);	
			}
			
		}
		
		
		
		return $returnVal;
	}
	
	function getLink() {
		
		return MAIN_ROOT."members/console.php?cID=".$this->intTableKeyValue;	
		
	}
	
	/*
	 * - countMembers -
	 * 
	 * Counts the amount of members that have access to the selected console option.
	 * 
	 * returns a numeric value
	 * 
	 */
	function countMembers() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT rank_id FROM ".$this->MySQL->get_tablePrefix()."rank_privileges WHERE console_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				
				$arrRanks[] = $row['rank_id'];
				
			}
			
			$sqlRanks = "('".implode("','", $arrRanks)."')";
			$result = $this->MySQL->query("SELECT member_id FROM ".$this->MySQL->get_tablePrefix()."members WHER rank_id IN ".$sqlRanks);
			while($row = $result->fetch_assoc()) {
				$arrMembers[] = $row['member_id'];	
			}
			
			$sqlMembers = "('".implode("','", $arrMembers)."')";
			$countMembers = $result->num_rows;
			
			$addTo = 0;
			$result = $this->MySQL->query("SELECT allowdeny FROM ".$this->get_tablePrefix()."console_members WHERE console_id = '".$this->intTableKeyValue."' AND member_id IN ".$sqlMembers);
			while($row = $result->fetch_assoc()) {
				if($row['allowdeny'] == 0) {
					$addTo += -1;
				}
				else {
					$addTo += 1;
				}
			}
			
			$returnVal = $countMembers+$addTo;
			
		}
		
		return $returnVal;
		
		
	}
	
	
	/*
	
	-Delete Method-
	
	Will delete the selected row from the database.  You must first "select" a table row using the select method in order to delete.
	
	*/
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
	
			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."console_members WHERE console_id = '".$this->intTableKeyValue."'");
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."rank_privileges WHERE console_id = '".$this->intTableKeyValue."'");
			
			
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