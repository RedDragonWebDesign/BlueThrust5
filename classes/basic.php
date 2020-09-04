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


class Basic {


	protected $MySQL;
	protected $strTableName;
	protected $strTableKey;
	protected $intTableKeyValue;
	protected $strTablePrefix;
	protected $arrObjInfo;
	
	public function __construct($sqlConnection, $tableName, $tableKey) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix().$tableName;
		$this->strTableKey = $tableKey;
	}
	

	/*
	
	-Selector Method-
	
	intIDNum: The primary key used to identify the needed table row.  Must be numeric unless $numericIDOnly is set to false.
	
	This will set $arrObjInfo to the table row array returned by the select statement, with the column titles set as the array keys.
	Use the get_info method to get the values for the table row with the selected id number.
	
	Returns true when the table row is found
	Returns false when no table row is found
	
	*/
	public function select($intIDNum, $numericIDOnly = true) {
	
		$returnVal = false;
		if(!$numericIDOnly) {
			$intIDNum = $this->MySQL->real_escape_string($intIDNum);
			$checkID = true;
		}
		else {
			$checkID = is_numeric($intIDNum);
		}
		

		if($checkID) {
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '$intIDNum'");
			if($result->num_rows > 0) {
				$this->arrObjInfo = $result->fetch_assoc();
				$returnVal = true;
				$this->intTableKeyValue = $intIDNum;
			}
		}

		
		return $returnVal;
	}
	
	
/*
	 * Select by multiple arguments.
	 * 
	 * Format argument array as array[columnName] = value
	 * 
	 */
	
	public function selectByMulti($arrWhats) {
		
		$returnVal = false;
		if(is_array($arrWhats)) {
			
			$arrSQL = array();
			foreach($arrWhats as $columnName => $value) {
				$arrSQL[] = $columnName." = ?";
			}
			
			$setSQL = implode(" AND ", $arrSQL);

			$query = "SELECT ".$this->strTableKey." FROM ".$this->strTableName." WHERE ".$setSQL;
			$stmt = $this->MySQL->prepare($query);
			$returnID = "";

			if($stmt) {
	
				$this->MySQL->bindParams($stmt, $arrWhats);
				$stmt->execute();
				$stmt->bind_result($result);
				$stmt->fetch();	
				$returnID = $result;			
				$stmt->close();

			}
		
			

			$returnVal = $this->select($returnID);	
			
			
		}
		
		return $returnVal;
	}
	
	
	/*
	 * Get multi rows, returns an array of get_info_filtered, 
	 * 
	 * Format filterArgs array as array[columnName] = value
	 * 
	 */
	
	public function get_entries($filterArgs=array(), $orderBy="", $blnNotFiltered=true, $filterComparators=array()) {
		
		$returnVal = false;
		$returnArr = array();
		$arrSelect = array();
		$selectBackID = "";
		
		if($this->intTableKeyValue != "") {
			$selectBackID = $this->intTableKeyValue;
		}
		
		$setSQL = "";
		if(count($filterArgs) > 0) {
			
			$arrSQL = array();
			foreach($filterArgs as $columnName => $value) {
				
			$setComparator = isset($filterComparators[$columnName]) ? $filterComparators[$columnName] : "=";
				
				$arrSQL[] = $columnName." ".$setComparator." ?";
				
			}
			
			$setSQL = implode(" AND ", $arrSQL);
			
			if($setSQL != "") {
				$setSQL = " WHERE ".$setSQL;	
			}
		}
		
		if($orderBy != "") {
			$orderBy = "ORDER BY ".$orderBy;
		}
		
		$query = "SELECT ".$this->strTableKey." FROM ".$this->strTableName.$setSQL." ".$orderBy;
		$stmt = $this->MySQL->prepare($query);
		$returnID = "";

		if($stmt) {

			if(count($filterArgs) > 0) {
				$this->MySQL->bindParams($stmt, $filterArgs);
			}
			
			$stmt->execute();
			$stmt->bind_result($result);
			
			while($stmt->fetch()) {
				
				$arrSelect[] = $result;	
				
			}
			
			$stmt->close();

		}

		foreach($arrSelect as $selectKey) {
			$this->select($selectKey);
			$returnArr[] = $blnNotFiltered ? $this->get_info_filtered() : $this->get_info();
		}
		
		
		if($selectBackID != "") {
			$this->select($selectBackID);
		}
		
		return $returnArr;
		
	}
	
	
	/*
	
	-Easy way to send an INSERT statement-
	
	arrColumns: Array of the column names that will be inserted into
	arrValues: Array of values that will be inserted into the column names in arrColumns
	
	Both arrays must contain the same amount of values and must line up with each other in order insert the desired values.
	
	After the query is sent, it will assign the strTableKeyValue with the last insert id and will "select" it, using the select method.
	
	Returns true if INSERT query is successful
	Returns false if there is an error
	
	*/
	public function addNew($arrColumns, $arrValues) {
		$returnVal = false;
		
		
		
		if(is_array($arrColumns)) {
			$sqlColumns = implode(",", $arrColumns);
			$sqlValues = rtrim(str_repeat("?, ", count($arrColumns)),", ");
			
		}


		$stmt = $this->MySQL->prepare("INSERT INTO ".$this->strTableName." (".$sqlColumns.") VALUES (".$sqlValues.")");
		
		if(is_array($arrValues)) {
			foreach($arrValues as $key=>$value) {
				$temp = str_replace("&gt;", ">", $value);
				$value = str_replace("&lt;", "<", $temp);
				$temp = str_replace('&quot;', '"', $value);
				$value = str_replace("&#39;", "'", $temp);
				$temp = str_replace("&#38;middot;", "&middot;", $value);
				$temp = str_replace("&#38;raquo;", "&raquo;", $temp);
				$temp = str_replace("&#38;laquo;", "&laquo;", $temp);
		
				$arrValues[$key] = $temp;
			}
		
			
			$stmt = $this->MySQL->bindParams($stmt, $arrValues);
			
		}
		
			
		if($stmt->execute()) {
			$this->select($stmt->insert_id);
			$returnVal = true;
			$this->updateTableTime();
		}
		else {
			echo $this->MySQL->displayError("basic.php - addNew");
		}

		return $returnVal;
	
	}
	
	
	/*
	
	-Easy way to send an UPDATE query-
	
	arrTableColumns: Array of the table column names that will be updated
	arrColumnValues: Array of the values to update each given table column
	
	Both arrays need to contain the same amount of values and must line up with each other in order to update the correct column
	
	Must first use the select method before updating
	
	Returns true if successfully updates
	
	*/
	public function update($arrTableColumns, $arrColumnValues) {
		
		$returnVal = false;
		if(is_array($arrTableColumns) AND is_array($arrColumnValues) AND $this->intTableKeyValue != null) {
		
			if(count($arrTableColumns) == count($arrColumnValues)) {
			
				$combinedArray = array_combine($arrTableColumns, $arrColumnValues);
				
				foreach($combinedArray as $key=>$value) {
					$temp = str_replace("&gt;", ">", $value);
					$value = str_replace("&lt;", "<", $temp);
					$temp = str_replace('&quot;', '"', $value);
					$value = str_replace("&#39;", "'", $temp);
					$temp = str_replace("&#38;middot;", "&middot;", $value);
					$value = str_replace("&#38;raquo;", "&raquo;", $temp);
					$temp = str_replace("&#38;laquo;", "&laquo;", $value);
					
					$arrQuery[] = $key." = ?";
					$arrValues[] = $temp;
				}
				
				
				
				$updateQuery = implode(", ", $arrQuery);
		
				$stmt = $this->MySQL->prepare("UPDATE ".$this->strTableName." SET ".$updateQuery." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
				
				$stmt = $this->MySQL->bindParams($stmt, $arrValues);
				
				
				if($stmt->execute()) {
					$this->select($this->intTableKeyValue);
					$returnVal = true;
					
					$this->updateTableTime();
					
					
					
				}
				else {
					$this->MySQL->displayError("basic.php - update");
				}
			
			}
		
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
			
			if(!$this->MySQL->error) {
				$returnVal = true;
			}
			else {
				$this->MySQL->displayError("basic.php");
			}
			
			$this->MySQL->query("OPTIMIZE TABLE `".$this->strTableName."`");
			$this->updateTableTime();
		}
		
		return $returnVal;
	
	}
	
	
	// Getter and Setter Methods
	
	public function get_info($returnSingleValue = "") {
		$returnVal = "";
		if($returnSingleValue == "") {
			$returnVal = $this->arrObjInfo;
		}
		else {
			
			$returnVal = $this->arrObjInfo[$returnSingleValue];
		}
		
		return $returnVal;
	}
	
	public function get_info_filtered($returnSingleValue = "") {
		
		$arrFilteredInfo = array();
		foreach($this->arrObjInfo as $key => $value) {
			$temp = str_replace("<", "&lt;", $value);
			$value = str_replace(">", "&gt;", $temp);
			$temp = str_replace("'", "&#39;", $value);
			$value = str_replace('"', '&quot;', $temp);
			$temp = str_replace("&middot;", "&#38;middot;", $value);
			$temp = str_replace("&raquo;", "&#38;raquo;", $temp);
			$temp = str_replace("&laquo;", "&#38;laquo;", $temp);
			
			$arrFilteredInfo[$key] = $temp;
		}
		
		$returnVal = "";
		if($returnSingleValue == "") {
			$returnVal = $arrFilteredInfo;
		}
		else {
			$returnVal = $arrFilteredInfo[$returnSingleValue];	
		}
		
		return $returnVal;
	}
	
	
	public function set_tableName($tableName) {
		$this->strTableName = $tableName;
	}
	
	public function set_tableKey($tableKey) {
		$this->strTableKey = $tableKey;
	}
	
	public function get_tableKey() {
		return $this->strTableKey;	
	}
	
	public function set_tablePrefix($tablePrefix) {
		$this->strTablePrefix = $tablePrefix;
	}
	
	public function get_tablePrefix() {
		return $this->strTablePrefix;
	}
	
	public function get_keyvalue() {
		return $this->intTableKeyValue;
	}
	
	public function get_MySQL() {
		return $this->MySQL;	
	}
	
	public function updateTableTime() {
		
		$result = $this->MySQL->query("SELECT tablename FROM ".$this->MySQL->get_tablePrefix()."tableupdates WHERE tablename = '".$this->strTableName."'");
		if($result->num_rows > 0) {
			$this->MySQL->query("UPDATE ".$this->MySQL->get_tablePrefix()."tableupdates SET updatetime = '".time()."' WHERE tablename = '".$this->strTableName."'");
		}
		else {
			$this->MySQL->query("INSERT INTO ".$this->MySQL->get_tablePrefix()."tableupdates (tablename, updatetime) VALUES ('".$this->strTableName."', '".time()."')");
		}
		
	}

}

?>