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



class MenuCategory extends BasicSort {
	
	
	public function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."menu_category";
		$this->strTableKey = "menucategory_id";
		$this->strCategoryKey = "section";
		
	}
	
	
	public function getCategories($intSection, $intAccessType=1, $intHide=0) {
		$returnArr = array();
		
		$accessTypeSQL = " OR accesstype = '".$intAccessType."'";
		if($intAccessType == 3) {
			$accessTypeSQL = " OR accesstype = '1' OR accesstype = '2'";
		}

		if(is_numeric($intAccessType) && is_numeric($intHide) && is_numeric($intSection)) {
			$result = $this->MySQL->query("SELECT menucategory_id FROM ".$this->strTableName." WHERE (accesstype = '0'".$accessTypeSQL.") AND hide = '".$intHide."' AND section = '".$intSection."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['menucategory_id'];
			}
		}
		
		return $returnArr;
	}
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$info = $this->arrObjInfo;
			$returnVal = parent::delete();
			if($info['headertype'] == "image" && $info['headercode'] != "") {
				deleteFile(BASE_DIRECTORY.$info['headercode']);	
			}
		}

		return $returnVal;
	}
	
}