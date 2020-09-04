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


class DownloadCategory extends Rank {
	
	
	function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."downloadcategory";
		$this->strTableKey = "downloadcategory_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."downloads";
		$this->strAssociateKeyName = "download_id";
		
	}
	

	public function selectBySpecialKey($specialKey) {
		
		$returnVal = false;

		$specialKey = $this->MySQL->real_escape_string($specialKey);

		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE specialkey = '".$specialKey."'");
		if($result->num_rows > 0) {
			$this->arrObjInfo = $result->fetch_assoc();
			$returnVal = true;
			$this->intTableKeyValue = $this->arrObjInfo[$this->strTableKey];
			
		}

		return $returnVal;
		
	}
	
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$countErrors = 0;
			$result = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			
			$result = $this->MySQL->query("DELETE FROM ".$this->get_tablePrefix()."download_extensions WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			if($countErrors == 0) {
				$returnVal = true;	
			}
			$this->resortOrder();
	
		}
	
		return $returnVal;
	
	}
	
	
	public function getExtensions() {
		
		$arrExtensions = array();
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."download_extensions WHERE downloadcategory_id = '".$this->intTableKeyValue."' ORDER BY extension_id");
			while($row = $result->fetch_assoc()) {
				
				$arrExtensions[] = $row['extension_id'];
				
			}
			
			
		}
		
		return $arrExtensions;
		
	}
	
	public function get_privileges() {
		return true;	
	}

	
	
}


?>