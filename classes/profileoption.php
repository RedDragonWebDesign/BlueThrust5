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

class ProfileOption extends BasicSort {
	
	
	public $objProfileOptionSelect;
	public $objProfileOptionValue;

	function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."profileoptions";
		$this->strTableKey = "profileoption_id";
		$this->strCategoryKey = "profilecategory_id";
		$this->objProfileOptionSelect = new Basic($sqlConnection, "profileoptions_select", "selectopt_id");
		$this->objProfileOptionValue = new Basic($sqlConnection, "profileoptions_values", "values_id");

		
	}
	
	
	function getSelectValues() {
		$returnVal = false;
		
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue) && $this->arrObjInfo['optiontype'] == "select") {
			
			$returnArr = array();
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."profileoptions_select WHERE profileoption_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
				
				$returnArr[$row['selectopt_id']] = $row['selectvalue'];
				
			}
			
			$returnVal = $returnArr;
		}
		
		return $returnVal;
	}
	
	
	function addNewSelectValue($strValue, $intSortNum) {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "" && trim($strValue) != "") {
			$arrColumns = array("profileoption_id", "selectvalue", "sortnum");
			$arrValues = array($this->intTableKeyValue, $strValue, $intSortNum);
		
			if($this->objProfileOptionSelect->addNew($arrColumns, $arrValues)) {
				$returnVal = true;
			}
		
		}
		
		return $returnVal;
		
	}
	
	
	function isSelectOption() {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue)) {
			if($this->arrObjInfo['optiontype'] == "select") {
				$returnVal = true;
			}			
		}
		
		return $returnVal;
		
	}
	
	
	function delete() {
		
		$returnVal = false;
		
		
			if($this->intTableKeyValue != "") {
			$countErrors = 0;
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."profileoptions_select WHERE profileoption_id = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."profileoptions_values WHERE profileoption_id = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			$this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE profileoption_id = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			if($countErrors == 0) {
				$returnVal = true;	
			}
		
		}
		
		return $returnVal;
		
		
	}
	


}

?>