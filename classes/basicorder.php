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

class BasicOrder extends Basic {

	protected $strAssociateTableName; // See the getAssociateIDs function for an explaination of "Associates"
	protected $strAssociateKeyName;

	
	public function __construct($sqlConnection, $tableName, $tableKey) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix().$tableName;
		$this->strTableKey = $tableKey;
	}
	
	
	/*
	 * - selectByOrder Method -
	 *
	 *   Way to select a rank by ordernum.  Essentially the same as the normal select method from basic except using the ordernum.
	 *
	 *   intOrderNum: The number order for the needed rank.
	 *
	 *   Returns true when the table row is found.
	 *   Returns false when the table row is not found.
	 *
	 */
	
	function selectByOrder($intOrderNum) {
	
		$returnVal = false;
		if(is_numeric($intOrderNum)) {
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ordernum = '".$intOrderNum."'");
			if($result->num_rows > 0) {
				$this->arrObjInfo = $result->fetch_assoc();
				$returnVal = true;
				$this->intTableKeyValue = $this->arrObjInfo[$this->strTableKey];
				$returnVal = true;
			}
	
	
		}
	
		return $returnVal;
	
	}
	
	
	/*
	 * - getHighestOrder Function -
	 *
	 *  Returns the highest ordernum in the rank table
	 *
	 */
	
	function getHighestOrderNum() {
		$result = $this->MySQL->query("SELECT MAX(ordernum) FROM ".$this->strTableName);
		$row = $result->fetch_assoc();
	
		return $row['MAX(ordernum)'];
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
	 * - validateOrder Method -
	 *
	 *  Helper method to check if a selected order is valid and "makesRoom" for new order.
	 *  Used on pages that need to check if an ordernum that is being selected is valid. ex. Add New xxxxx Category
	 *
	 *  ** Be Careful when using this Method --> It selects the rank with ID intOrderNumID
	 *  ** Make sure to re-select the rank you have selected originally after using.
	 *
	 *  Returns a number to be used for ordernum
	 *
	*/
	
	function validateOrder($intOrderNumID, $strBeforeAfter, $blnEdit = false, $intEditOrderNum = "") {
	
		$returnVal = false;
	
		if($intOrderNumID == "first") {
			// "(no other categories)" selected, check to see if there are actually no other categories
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName);
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
	
	
				$addTo = 1; // Add 1 if we chose "before"
				if($strBeforeAfter == "after") {
					$addTo = -1; // Minus 1 if we chose "after"
				}
	
				// Get the ordernum of the rank that we are using to determine the order of the rank being edited (*** It was selected in the IF statement above ***)
				$thisCatOrderNum = $this->get_info("ordernum");
	
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
	
	/*
	 * - resortOrder Method -
	 *
	 * Mainly used on the edit rank page and after makeRoom is used.
	 * This method re-sorts the rank table so that there are no spaces in between the ordernums.
	 *
	 * Ex. After makeRoom is called, the ordernums might be 1,2,4,5,6.
	 * 	   This will re-sort the rank table so the ordernums go 1,2,3,4,5
	 *
	 *
	 */
	
	function resortOrder() {
		
		$counter = 1; // ordernum counter
		$x = 0; // array counter
		$arrUpdateID = array();
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." ORDER BY ordernum");
		if($result) {
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
		}
		
		return true;
	}
	
	
	/*
	 * - Move Method -
	 *
	 * Easy way to move a rank either up or down 1 spot.  Used mainly on the manage page.
	 *
	 *
	 * Returns true on successful move
	 * Returns false when no move is made
	 *
	 */
	
	
	function move($strDir) {
	
	
		$returnVal = false;
	
		if($this->intTableKeyValue != "" AND ($strDir == "up" OR $strDir == "down")) {
			$intOriginalRank = $this->intTableKeyValue;
			$intOrderNum = $this->arrObjInfo['ordernum'];
	
			$moveUp = $intOrderNum+1;
			$moveDown = $intOrderNum-1;
	
			$makeMove = "";
	
			if($strDir == "up" AND $this->selectByOrder($moveUp)) {
				$makeMove = "before";
			}
			elseif($strDir == "down" AND $this->selectByOrder($moveDown)) {
				$makeMove = "after";
			}
	
	
			if($makeMove != "") {
				$newSpot = $this->makeRoom($makeMove);
	
				if(is_numeric($newSpot)) {
					$this->select($intOriginalRank);
					$this->update(array("ordernum"), array($newSpot));
					$returnVal = true;
				}
	
				$this->resortOrder();
			}
		}
	
	
		return $returnVal;
	
	}
	
	
	
	/*
	 * - findBeforeAfter Function -
	 *
	 *  Easy way to find the ordernum of the rank either before or after.  Finds the id of the rank before the selected rank,
	 *  unless its the last rank, which it will return the rank which it is after.
	 *
	 *  Returns an array with 2 items, [0] equals the rank id, [1] equals before, after or first (if no other ranks)
	 *
	 */
	
	
	function findBeforeAfter() {
		$returnArr = "";
		if($this->intTableKeyValue != "") {
			$intHighestOrderNum = $this->getHighestOrderNum();
			$intOriginalRank = $this->intTableKeyValue;
	
			$strBeforeAfter = "before";
			$intNextOrderID = 0;
			$addTo = -1;
	
	
			if($this->arrObjInfo['ordernum'] == 1 && $intHighestOrderNum != 1) {
				$strBeforeAfter = "after";
				$addTo = 1;
			}
			elseif($intHighestOrderNum == 1) {
				$strBeforeAfter = "first";
			}
	
			$checkNextOrder = $this->arrObjInfo['ordernum']+$addTo;
	
			if($this->selectByOrder($checkNextOrder)) {
				$intNextOrderID = $this->arrObjInfo[$this->strTableKey];
			}
	
			$returnArr = array($intNextOrderID, $strBeforeAfter);
			
	
		}
		return $returnArr;
	}

	
	
	/*
	 * - getAssociateIDs Function -
	 *
	 *  I was unsure of the best way to name what I am calling "Associates" to the rank class and the ones that extend it.  The only way I can
	 *  think of explaining it is to give examples.
	 *
	 *  EX1. Members is the associate to Ranks because 1 member can only have 1 rank, but many members can have the same 1 rank.
	 *  EX2. Ranks is the associate to Rank Category because any one particular Rank can only have 1 Rank Cateogry, where as a Rank Category can contain many Ranks.
	 *
	 *  If you can understand what my thought process is from the above examples, then hopefully you can figure out good ways to use this function
	 *  if you want to modify the scripts.
	 *
	 *
 	 *  Returns an array of IDs for the associated table
	 *
	 */
	
	function getAssociateIDs($sqlOrderBY = "", $bypassFilter=false) {
	
		$arrReturn = array();
		if(!$bypassFilter) {
			$sqlOrderBY = $this->MySQL->real_escape_string($sqlOrderBY);
		}
		
		if($this->intTableKeyValue != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->strAssociateTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."' ".$sqlOrderBY);
			while($row = $result->fetch_assoc()) {
				$arrReturn[] = $row[$this->strAssociateKeyName];
			}
		}
	
		return $arrReturn;
	
	}

	
	function set_assocTableName($tableName) {
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix().$tableName;
	}
	
	function set_assocTableKey($tableKey) {
		$this->strAssociateKeyName = $tableKey;
	}
	
	
	
	function delete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$blnDelete1 = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			if($this->strAssociateTableName != "") {
				$blnDelete2 = $this->MySQL->query("DELETE FROM ".$this->strAssociateTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
				$this->MySQL->query("OPTIMIZE TABLE `".$this->strAssociateTableName."`");
			}
			else {
				$blnDelete2 = true;	
			}
			
			if($blnDelete1 && $blnDelete2) {
				$returnVal = true;	
			}
			
			$this->resortOrder();
			
			$this->MySQL->query("OPTIMIZE TABLE `".$this->strTableName."`");
			
		}
		
		return $returnVal;
	}	
	

}

?>