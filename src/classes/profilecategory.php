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

class ProfileCategory extends BasicOrder {
	
	
	function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."profilecategory";
		$this->strTableKey = "profilecategory_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."profileoptions";
		$this->strAssociateKeyName = "profileoption_id";
	
	}
	
	
	function delete() {
		
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