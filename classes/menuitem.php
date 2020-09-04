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



class MenuItem extends BasicSort {
	
	public $objLink;
	public $objImage;
	public $objShoutbox;
	public $objCustomPage;
	public $objCustomBlock;
	
	public function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."menu_item";
		$this->strTableKey = "menuitem_id";
		$this->strCategoryKey = "menucategory_id";
		
		$this->objLink = new Basic($this->MySQL, "menuitem_link", "menulink_id");
		$this->objImage = new Basic($this->MySQL, "menuitem_image", "menuimage_id");
		$this->objShoutbox = new Basic($this->MySQL, "menuitem_shoutbox", "menushoutbox_id");
		$this->objCustomPage = new Basic($this->MySQL, "menuitem_custompage", "menucustompage_id");
		$this->objCustomBlock = new Basic($this->MySQL, "menuitem_customblock", "menucustomblock_id");
		
		
	}
	
	public function getItems($intCategory, $intAccessType=1, $intHide=0) {
		$returnArr = array();
		
		$accessTypeSQL = " OR accesstype = '".$intAccessType."'";
		if($intAccessType == 3) {
			$accessTypeSQL = " OR accesstype = '1' OR accesstype = '2'";
		}

		if(is_numeric($intAccessType) && is_numeric($intHide) && is_numeric($intCategory)) {
			$result = $this->MySQL->query("SELECT menuitem_id FROM ".$this->strTableName." WHERE (accesstype = '0'".$accessTypeSQL.") AND hide = '".$intHide."' AND menucategory_id = '".$intCategory."' ORDER BY sortnum");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['menuitem_id'];
			}
		}
		
		return $returnArr;
	}
	
	
	public function delete() {
		
		if($this->intTableKeyValue != "") {
			switch($this->arrObjInfo['itemtype']) {
				case "link":
					$this->objLink->select($this->arrObjInfo['itemtype_id']);
					$this->objLink->delete();
					break;
				case "image":
					$this->objImage->select($this->arrObjInfo['itemtype_id']);
					$info = $this->objImage->get_info();
					deleteFile(BASE_DIRECTORY.$info['imageurl']);
					$this->objImage->delete();
					break;
				case "shoutbox":
					$this->objShoutbox->select($this->arrObjInfo['itemtype_id']);
					$this->objShoutbox->delete();
					break;
				case "custompage":
					$this->objCustomPage->select($this->arrObjInfo['itemtype_id']);
					$this->objCustomPage->delete();
					break;
				case "customform":
					$this->objCustomPage->select($this->arrObjInfo['itemtype_id']);
					$this->objCustomPage->delete();
					break;
				case "customcode":
					$this->objCustomBlock->select($this->arrObjInfo['itemtype_id']);
					$this->objCustomBlock->delete();
					break;
				case "customformat":
					$this->objCustomBlock->select($this->arrObjInfo['itemtype_id']);
					$this->objCustomBlock->delete();
					break;
					
			}
			
			
			return parent::delete();
		}	
		
		
	}
	
}

?>