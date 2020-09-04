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

/*
 * An extension of the Basic Class. It includes methods which allow you to easily add 
 * things to the database in a set order within different categories.  
 * 
 */

class BasicSort extends Basic {
	
	public $strCategoryKey;
	
	public function __construct($sqlConnection, $tableName, $tableKey, $categoryKey) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix().$tableName;
		$this->strTableKey = $tableKey;
		$this->strCategoryKey = $categoryKey;
	}
	
	
	
	/*
	 * - makeRoom Method -
	*
	* A simple way to make room for a new console option.  It adjusts the sortnum's of all the console options within a specific
	* console category.
	*
	* strBeforeAfter: String of either "before" or "after"
	*
	* Returns the sortnum of the new console option on success and "false" on failure.
	*
	*/
	
	function makeRoom($strBeforeAfter) {
	
		$strBeforeAfter = strtolower($strBeforeAfter);
		$newSortNum = "false";
		if($this->intTableKeyValue != "" AND ($strBeforeAfter == "before" OR $strBeforeAfter == "after")) {
			$consoleInfo = $this->arrObjInfo;
			$startSaving = false;
			$x = 1;
			$arrConsoleOptions = array();
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$consoleInfo[$this->strCategoryKey]."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
	
				if($strBeforeAfter == "before" AND $row[$this->strTableKey] == $consoleInfo[$this->strTableKey]) {
					$newSortNum = $x;
					$x++;
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
				}
				elseif($strBeforeAfter == "after" AND $row[$this->strTableKey] == $consoleInfo[$this->strTableKey]) {
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
					$newSortNum = $x;
					$x++;
	
				}
				else {
	
					$arrConsoleOptions[$x][0] = $row[$this->strTableKey];
					$arrConsoleOptions[$x][1] = $row['sortnum'];
					$x++;
	
				}
	
			}
	
			$updateArray = array();
	
			$updateRowName = array("sortnum");
			if(is_numeric($newSortNum)) {
				$intOriginalCID = $this->intTableKeyValue;
				foreach($arrConsoleOptions as $key => $value) {
	
					if($key != $value[1]) {
	
						$this->select($value[0]);
						$this->update(array("sortnum"), array($key));
	
					}
	
				}
	
				$this->select($intOriginalCID);
	
			}
	
		}
	
	
		return $newSortNum;
	}
	
	
	/*
	 * - resortOrder Method -
	*
	* Mainly used on the edit console page and after makeRoom is used.
	* This method re-sorts the console table so that there are no spaces in between the sortnums.
	*
	* Ex. After makeRoom is called, the sortnums might be 1,2,4,5,6.
	* 	  This will re-sort the console table so the sortnums go 1,2,3,4,5
	*
	*
	*/
	
	function resortOrder() {
		$counter = 1; // ordernum counter
		$consoleInfo = $this->arrObjInfo;
		$x = 0; // array counter
		$arrUpdateID = array();
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$consoleInfo[$this->strCategoryKey]."' ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {
			$arrUpdateID[] = $row[$this->strTableKey];
			$x++;
		}
	
		$intOriginalConsole = $this->intTableKeyValue;
		foreach($arrUpdateID as $intUpdateID) {
			$arrUpdateCol[0] = "sortnum";
			$arrUpdateVal[0] = $counter;
			$this->select($intUpdateID);
			$this->update($arrUpdateCol, $arrUpdateVal);
			$counter++;
		}
	
		$this->select($intOriginalConsole);
	
	
		return true;
	}
	
	
	public function getHighestSortNum() {
		
		$returnVal = false;
		if($this->arrObjInfo[$this->strCategoryKey] != "") {
			$catKeyValue = $this->arrObjInfo[$this->strCategoryKey];
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$catKeyValue."'");
			$returnVal = $result->num_rows;
			
		}
		
		return $returnVal;
		
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
	
		$catKeyValue = $this->get_info($this->strCategoryKey);
		
		if($intOrderNumID == "first") {
			// "(no other categories)" selected, check to see if there are actually no other categories
	
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strCategoryKey." = '".$catKeyValue."'");
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
	
	
				$addTo = -1; // Minus 1 if we chose "before"
				if($strBeforeAfter == "after") {
					$addTo = 1; // Add 1 if we chose "after"
				}
	
				// Get the ordernum of the rank that we are using to determine the order of the rank being edited (*** It was selected in the IF statement above ***)
				$thisCatOrderNum = $this->get_info("sortnum");
	
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
			$intOrderNum = $this->arrObjInfo['sortnum'];
	
			$moveUp = $intOrderNum-1;
			$moveDown = $intOrderNum+1;
	
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
					$this->update(array("sortnum"), array($newSpot));
					$returnVal = true;
				}
	
				$this->resortOrder();
			}
		}
	
	
		return $returnVal;
	
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
		if(is_numeric($intOrderNum) && $this->arrObjInfo[$this->strCategoryKey] != "") {
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE sortnum = '".$intOrderNum."' AND ".$this->strCategoryKey." = '".$this->arrObjInfo[$this->strCategoryKey]."'");
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
			$intHighestOrderNum = $this->getHighestSortNum();
			$intOriginalRank = $this->intTableKeyValue;
	
			$strBeforeAfter = "before";
			$intNextOrderID = 0;
			$addTo = 1;
	
	
			if($this->arrObjInfo['sortnum'] == $intHighestOrderNum && $intHighestOrderNum != 1) {
				$strBeforeAfter = "after";
				$addTo = -1;
			}
			elseif($intHighestOrderNum == 1) {
				$strBeforeAfter = "first";
			}
	
			$checkNextOrder = $this->arrObjInfo['sortnum']+$addTo;
	
			if($this->selectByOrder($checkNextOrder)) {
				$intNextOrderID = $this->arrObjInfo[$this->strTableKey];
			}
	
			$returnArr = array($intNextOrderID, $strBeforeAfter);
			
			$this->select($intOriginalRank);
	
		}
		return $returnArr;
	}
	
	
	/*
	 * A way to set the value of the category key so an object doesn't have to be selected before the
	 * certain methods/functions can work.
	 */
	
	function setCategoryKeyValue($intCatKeyValue) {
		if(is_numeric($intCatKeyValue)) {
			$this->arrObjInfo[$this->strCategoryKey] = $intCatKeyValue;
		}

		return $this->arrObjInfo[$this->strCategoryKey];
	}
	
	function delete() {
		
		if($this->intTableKeyValue != "") {
			$blnDelete = parent::delete();
			$this->resortOrder();
		}
		
	}
	
}

?>