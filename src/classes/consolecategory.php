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

include_once("basicorder.php");


class ConsoleCategory extends BasicOrder {
	
	
	function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."consolecategory";
		$this->strTableKey = "consolecategory_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."console";
		$this->strAssociateKeyName = "console_id";
		
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
	
	
	/*
	 * - makeRoom Method -
	*
	* Method to re-sort rank order numbers just before adding a new rank.  You must first select a rank before using
	* this method.
	*
	* Takes in either before and after and will set a spot for the new rank to be added before or after the rank that
	* is currently selected.  It will then return the new order number for the new rank.
	*
	* strBeforeAfter: String of either "before" or "after"
	*
	* Returns the ordernum for the new rank on success or "false" on error
	* 
	* Reason for Override: preventing the editing of admin categories was causing problems
	*
	*/
	
	function makeRoom($strBeforeAfter) {
	
		$intRankID = $this->intTableKeyValue;
		if($intRankID != null) {
	
			$intNewRankOrderNum = 0;
			$arrRanks = array();
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." ORDER BY ordernum");
			$x = 1;
			while($row = $result->fetch_assoc()) {
	
				if($row[$this->strTableKey] == $intRankID) {
	
					if($strBeforeAfter == "after") {
						$intNewRankOrderNum = $x;
						$x++;
						$arrRanks[$x] = $row[$this->strTableKey];
						$x++;
					}
					elseif($strBeforeAfter == "before") {
						$arrRanks[$x] = $row[$this->strTableKey];
						$x++;
						$intNewRankOrderNum = $x;
						$x++;
					}
	
				}
				else {
					$arrRanks[$x] = $row[$this->strTableKey];
					$x++;
				}
			}
	
	
			if($intNewRankOrderNum == 0) {
				// intNewRank should not equal 0 after the above loop.
				// The test will be if a numeric value is returned, so if it returns this string, something went wrong.
				$intNewRankOrderNum = "false";
			}
	
	
			if(is_numeric($intNewRankOrderNum)) {
	
				$intOriginalRank = $this->intTableKeyValue;
	
				foreach($arrRanks as $key => $value) {
	
					$arrColumns[0] = "ordernum";
					$arrValues[0] = $key;
	
					$this->select($value);
					$this->update($arrColumns, $arrValues);
	
				}
	
				$this->select($intOriginalRank);
	
			}
	
			return $intNewRankOrderNum;
		}
	
	
	}
	
	
	/*
	 * - resortOrder Method -
	*
	* Mainly used on the edit console category page and after makeRoom is used.
	* This method re-sorts the rank table so that there are no spaces in between the ordernums.
	*
	* Ex. After makeRoom is called, the ordernums might be 1,2,4,5,6.
	* 	   This will re-sort the rank table so the ordernums go 1,2,3,4,5
	*
	*
	* Reason for Override: preventing the editing of admin categories was causing problems
	*
	*/
	
	function resortOrder() {
		$counter = 1; // ordernum counter
		$x = 0; // array counter
		$arrUpdateID = array();
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." ORDER BY ordernum");
		while($row = $result->fetch_assoc()) {
			$arrUpdateID[] = $row[$this->strTableKey];
			$x++;
		}
	
		$intOriginalRank = $this->intTableKeyValue;
		foreach($arrUpdateID as $intUpdateID) {
			$arrUpdateCol[0] = "ordernum";
			$arrUpdateVal[0] = $counter;
			$this->select($intUpdateID);
			$this->update($arrUpdateCol, $arrUpdateVal);
			$counter++;
		}
	
		$this->select($intOriginalRank);
	
		return true;
	}
	
	
}


?>