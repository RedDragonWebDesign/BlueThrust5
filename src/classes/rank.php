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

class Rank extends BasicOrder {
	
	
	
	function __construct($sqlConnection) {
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."ranks";
		$this->strTableKey = "rank_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."members";
		$this->strAssociateKeyName = "member_id";
	}

	
	function get_privileges() {
	
		$arrPrivileges = array();
		
		if($this->intTableKeyValue == 1) {
			$result = $this->MySQL->query("SELECT console_id FROM ".$this->MySQL->get_tablePrefix()."console ORDER BY sortnum");
		}
		else {
			$result = $this->MySQL->query("SELECT rp.console_id FROM ".$this->MySQL->get_tablePrefix()."rank_privileges rp, ".$this->MySQL->get_tablePrefix()."console c WHERE rank_id = '".$this->intTableKeyValue."' AND c.console_id = rp.console_id ORDER BY c.sortnum");
		}

		
		
		while($row = $result->fetch_assoc()) {
			$arrPrivileges[] = $row['console_id'];
		}
	
	
		return $arrPrivileges;
	}
	
	
	/*
	 * - countMembers Function -
	 * 
	 * 
	 * Returns the number of members with the current selected rank
	 */
	
	function countMembers() {
		$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."members WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
		$num_rows = $result->num_rows;
		
		
		return $num_rows;
	}
	
	/*
	 * - getLocalImageURL Function -
	 * 
	 * Used to determine if the image attached to the selected rank is a local image or external image.
	 * 
	 * Returns FALSE when the image is an external url.
	 * Returns the local image address when the image is on the server.
	 * 
	 */
	
	function getLocalImageURL() {
		global $MAIN_ROOT;
		$returnVal = false;
		if($this->intTableKeyValue != "") {
					
			if(strpos($this->arrObjInfo['imageurl'], "http://") === false) {
				
				$returnVal = $this->arrObjInfo['imageurl'];
				
			}

		}
		
		return $returnVal;
	}
	
	/*
	 * - Refresh Image Size Method -
	 * 
	 * If imageheight and imagewidth are not set, this will find the actual size of the image and 
	 * set it to the arrObjInfo['imagewidth'] and arrObjInfo['imageheight']
	 * 
	 */
	function refreshImageSize() {
		
		if($this->intTableKeyValue != "") {
			
			
			if($this->arrObjInfo['imagewidth'] == 0) {
				$imageURL = $this->getLocalImageURL();
			
				$imageSize = getimagesize($imageURL);
				$this->arrObjInfo['imagewidth'] = $imageSize[0];
			
			}
			
			if($this->arrObjInfo['imageheight'] == 0) {
				$imageURL = $this->getLocalImageURL();
			
				$imageSize = getimagesize($imageURL);
				$this->arrObjInfo['imageheight'] = $imageSize[1];
			
			}
		
		
		}
		
		
	}

	
	
	/*
	 * - delete Method -
	 * 
	 * Special delete method for rank to also delete privilege permissions associated with this rank from the rank_privileges table.
	 */
	
	public function delete() {

		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			
			$imageURL = $this->getLocalImageURL();
			if($imageURL !== false) {
				
				deleteFile(BASE_DIRECTORY.$imageURL);
				
			}
			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."rank_privileges WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
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
	
	
	public function get_info($returnSingleValue = "") {
		global $MAIN_ROOT;
		$result = parent::get_info($returnSingleValue);
		
		if(substr($result['imageurl'],0,4) != "http") {
			if($returnSingleValue == "") {
			
				$fullImageURL = $MAIN_ROOT.$result['imageurl'];
				$result['imageurl'] = $fullImageURL;
			}
			elseif($returnSingleValue == "imageurl") {
				$fullImageURL = $MAIN_ROOT.$result;
				$result = $fullImageURL;
			}
		}
		return $result;
		
	}
	
	
	public function get_info_filtered($returnSingleValue = "") {
		global $MAIN_ROOT;
		$result = parent::get_info_filtered($returnSingleValue);
		
		if(substr($result['imageurl'],0,4) != "http") {
			if($returnSingleValue == "") {
			
				$fullImageURL = $MAIN_ROOT.$result['imageurl'];
				$result['imageurl'] = $fullImageURL;
			}
			elseif($returnSingleValue == "imageurl") {
				$fullImageURL = $MAIN_ROOT.$result;
				$result = $fullImageURL;
			}
		}
		
		return $result;
		
	}
	
	
}


?>