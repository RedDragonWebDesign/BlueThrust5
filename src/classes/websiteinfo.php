<?php

include_once("basic.php");

class WebsiteInfo extends Basic {
	
	protected $arrKeys;
	protected $blnRefreshInfo;
	protected $strPagePath;
	public $objBTMail;
	
	public function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."websiteinfo";
		$this->strTableKey = "websiteinfo_id";
		
		$this->arrKeys = array();
		$this->blnRefreshInfo = true;
		$this->objBTMail = new btMail();
	}
	
	
	public function select($intIDNum, $numericIDOnly=true) {
		$temp = $this->arrObjInfo;
		$returnVal = parent::select($intIDNum, $numericIDOnly);
		
		
		if($this->blnRefreshInfo) {
			$this->arrObjInfo = array();
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName);
			while($row = $result->fetch_assoc()) {
				$this->arrObjInfo[$row['name']] = $row['value'];
				$this->arrKeys[$row['name']] = $row['websiteinfo_id'];		
			}
			$this->blnRefreshInfo = false;
		}
		else {
			$this->arrObjInfo = $temp;
		}
		
		return $returnVal;
		
	}
	
	
	public function multiUpdate($arrSettings, $arrValues) {
		
		$countErrors = 0;
		foreach($arrSettings as $key => $settingName) {
			if($this->select($this->arrKeys[$settingName])) {
			
				if(!$this->update(array("value"), array($arrValues[$key]))) {
					$countErrors++;	
				}
				
			}
			else {
				
				if(!$this->addNew(array("name", "value"), array($settingName, $arrValues[$key]))) {
					$countErrors++;	
				}
				
			}
		}
		
		return ($countErrors == 0);
		
	}
	
	public function update($arrColumns, $arrValues) {

		$this->blnRefreshInfo = true;
		return parent::update($arrColumns, $arrValues);
		
	}
	
	
	public function get_key($settingName) {
		return $this->arrKeys[$settingName];	
	}
	
	
	public function setPage($pagePath) {
		$this->strPagePath = $pagePath;
	}
	
	public function displayPage() {
		global $mysqli, $dbprefix, $hooksObj;
		include(BASE_DIRECTORY.$this->strPagePath);	
	}
	
	
}
