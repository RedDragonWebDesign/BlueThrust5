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


class CustomForm extends Basic {
	
	
	public $objComponent;
	public $objSelectValue;
	public $objFormValue;
	public $objSubmission;
	
	
	function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableKey = "customform_id";
		$this->strTableName = $this->MySQL->get_tablePrefix()."customform";
		
		
		$this->objComponent = new BasicSort($sqlConnection, "customform_components", "component_id");
		$this->objSelectValue = new BasicSort($sqlConnection, "customform_selectvalues", "selectvalue_id");
		$this->objFormValue = new Basic($sqlConnection, "customform_values", "value_id");
		$this->objSubmission = new Basic($sqlConnection, "customform_submission", "submission_id");
		
	}
	
	
	function getComponents() {

		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT component_id FROM ".$this->MySQL->get_tablePrefix()."customform_components WHERE customform_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['component_id'];	
			}
			
		}
		
		return $returnArr;
		
	}
	
	
	
	function getSelectValues($intComponentID) {
	
		$returnArr = array();
		if($this->intTableKeyValue != "" && is_numeric($intComponentID)) {
	
			$result = $this->MySQL->query("SELECT selectvalue_id FROM ".$this->MySQL->get_tablePrefix()."customform_selectvalues WHERE component_id = '".$intComponentID."' ORDER BY componentvalue");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['selectvalue_id'];
			}
	
		}
	
		return $returnArr;
	
	}
	
	
	/*
	 * - addComponents Function -
	 * 
	 * A way to add/update multiple components at a time.  Insert the btFormComponent session array into $arrComponents.
	 * 
	 * 
	 * Returns true on success
	 * 
	 */
	
	function addComponents($arrComponents) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			$countErrors = 0;
			$intSortNum = 1;
			foreach($arrComponents as $value) {
			
				if(trim($value['name']) != "" && (isset($value['component_id']) && $this->objComponent->select($value['component_id']))) {
					$arrColumns = array("name", "componenttype", "required", "tooltip", "sortnum");
					$arrValues = array($value['name'], $value['type'], $value['required'], $value['tooltip'], $intSortNum);
					
					$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."customform_selectvalues WHERE component_id = '".$value['component_id']."'");
					
					if(!$this->objComponent->update($arrColumns, $arrValues)) {
						$countErrors++;
					}
					elseif($value['type'] == "select" || $value['type'] == "multiselect") {
					
						$newComponentID = $this->objComponent->get_info("component_id");
						foreach($value['cOptions'] as $selectValue) {
					
							if(trim($selectValue) != "" && !$this->objSelectValue->addNew(array("component_id", "componentvalue"), array($newComponentID, $selectValue))) {
								$countErrors++;
							}
					
						}
					
					}
					
					
					
					$intSortNum++;
				}
				elseif(trim($value['name']) != "") {
					
					$arrColumns = array("customform_id", "name", "componenttype", "required", "tooltip", "sortnum");
					$arrValues = array($this->intTableKeyValue, $value['name'], $value['type'], $value['required'], $value['tooltip'], $intSortNum);
					
					if(!$this->objComponent->addNew($arrColumns, $arrValues)) {
						$countErrors++;
					}
					elseif($value['type'] == "select" || $value['type'] == "multiselect") {
						
						$newComponentID = $this->objComponent->get_info("component_id");
						foreach($value['cOptions'] as $selectValue) {
							
							if(trim($selectValue) != "" && !$this->objSelectValue->addNew(array("component_id", "componentvalue"), array($newComponentID, $selectValue))) {
								$countErrors++;						
							}
							
						}
						
					}
					
					$intSortNum++;
				}
				
				
			}
			
			if($countErrors == 0) {
				$returnVal = true;	
			}
		}
		
		return $returnVal;
	}
	
	
	function countSubmissions($blnUnseenOnly=false) {
		
		$returnVal = false;

		if($this->intTableKeyValue != "") {

			if($blnUnseenOnly) {
				$result = $this->MySQL->query("SELECT submission_id FROM ".$this->MySQL->get_tablePrefix()."customform_submission WHERE seenstatus = '0' AND customform_id = '".$this->intTableKeyValue."'");
			}
			else {
				$result = $this->MySQL->query("SELECT submission_id FROM ".$this->MySQL->get_tablePrefix()."customform_submission WHERE customform_id = '".$this->intTableKeyValue."'");
			}
			
			
			$returnVal = $result->num_rows;
			
			
		}
		
		return $returnVal;
		
	}
	
	function getSubmissions() {
		
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT submission_id FROM ".$this->MySQL->get_tablePrefix()."customform_submission WHERE customform_id = '".$this->intTableKeyValue."' ORDER BY submitdate DESC");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['submission_id'];	
			}
		}
		
		return $returnArr;
	}
	
	
	function getSubmissionDetail($intSubmissionID) {
		
		$returnArr = array();
		$blnCheck1 = $this->objSubmission->select($intSubmissionID);
		$submissionInfo = $this->objSubmission->get_info();
		$blnCheck2 = $submissionInfo['customform_id'] == $this->intTableKeyValue;
		
		if($this->intTableKeyValue != "" && $blnCheck1 && $blnCheck2) {
			$returnArr = $submissionInfo;
			
			$arrComponents = $this->getComponents();
			
			foreach($arrComponents as $componentID) {
				
				$this->objComponent->select($componentID);
				$componentInfo = $this->objComponent->get_info_filtered();
				
				$result = $this->MySQL->query("SELECT formvalue FROM ".$this->MySQL->get_tablePrefix()."customform_values WHERE submission_id = '".$intSubmissionID."' AND component_id = '".$componentID."'");
				
				
				if($componentInfo['componenttype'] != "multiselect") {
					
					$row = $result->fetch_assoc();
					$returnArr['components'][$componentID] = $row['formvalue'];
					
					
				}
				else {
					
					while($row = $result->fetch_assoc()) {
						$returnArr['components'][$componentID][] = $row['formvalue'];						
					}
					
				}
				
				
			}
			
		}
		
		return $returnArr;
		
	}
	
	
	function delete() {

		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			$arrComponents = $this->getComponents();
			$sqlComponents = "('".implode("','", $arrComponents)."')";
			
			$blnDeleteSelectValues = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."customform_selectvalues WHERE component_id IN ".$sqlComponents);
			$blnDeleteFormValues = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."customform_values WHERE component_id IN ".$sqlComponents);
			$blnDeleteComponent = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."customform_components WHERE customform_id = '".$this->intTableKeyValue."'");
			$blnDeleteSubmission = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."customform_submission WHERE customform_id = '".$this->intTableKeyValue."'");
			$blnDeleteForm = $this->MySQL->query("DELETE FROM ".$this->get_tablePrefix()."customform WHERE customform_id = '".$this->intTableKeyValue."'");
			
			
			if($blnDeleteSelectValues && $blnDeleteFormValues && $blnDeleteComponent && $blnDeleteForm && $blnDeleteSubmission) {
				$returnVal = true;			
			}
		}
		
		return $returnVal;
		
	}
	
	
	
	
}



?>