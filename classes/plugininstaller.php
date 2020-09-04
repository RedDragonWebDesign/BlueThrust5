<?php


	class PluginInstaller {
		
		protected $MySQL;
		protected $objPlugin;
		protected $sql = "";
		protected $pluginID;
		protected $errors = array();
		public $arrPluginTables = array();
		public $pluginDir = "";
		public $pluginName;
		public $pluginPages = array();
		public $pluginConsoleOptions = array();
		public $pluginConsoleCategory = "";
		
		public function __construct($sqlConnection) {
			
			$this->MySQL = $sqlConnection;
			$this->objPlugin = new btPlugin($sqlConnection);
			
		}
		
		
		public function setPluginTables($arrTables) {
			
			$this->arrPluginTables = $arrTables;
			
		}
		
		public function setPluginDirectory($pluginDir) {
			$this->pluginDir = $pluginDir;	
		}
		
		public function setSQL($strSQL) {
			
			if(is_file($strSQL)) {
				include($strSQL);
			}
			else {
				$this->sql = $strSQL;	
			}
			
		}
		
		public function isInstalled() {
			$returnVal = false;
			
			if($this->pluginDir != "") {
				$returnVal = in_array($this->pluginDir, $this->objPlugin->getPlugins("filepath"));
			}
			
			return $returnVal;
		}
		
		
		public function checkTableConflicts() {
			
			$returnVal = false;
			
			$result = $this->MySQL->query("SHOW TABLES");
	
			while($row = $result->fetch_array()) {
				if(in_array($row[0], $this->arrPluginTables)) {
					$returnVal = true;
					$this->error[] = "The table, <b>".$row[0]."</b> is already used in your database.";
				}
			}
			
			
			return $returnVal;
		}
		
		
		public function addNewConsoleItems() {
			
			$consoleCatID = $this->addConsoleCategory();
			
			$this->addConsoleOptions($consoleCatID);
			
		}
		
		
		public function addConsoleCategory() {

			$consoleCatID = "";
			if($this->pluginConsoleCategory != "") {
				$result = $this->MySQL->query("SELECT consolecategory_id FROM ".$this->MySQL->get_tablePrefix()."consolecategory WHERE name = '".$this->pluginConsoleCategory."'");
				if($result->num_rows == 0) {
					$consoleCatObj = new ConsoleCategory($this->MySQL);
					$newOrderNum = $consoleCatObj->getHighestOrderNum()+1;
					$consoleCatObj->addNew(array("name", "ordernum"), array($this->pluginConsoleCategory, $newOrderNum));
					$consoleCatID = $consoleCatObj->get_info("consolecategory_id");
				}
				else {
					$row = $result->fetch_assoc();
					$consoleCatID = $row['consolecategory_id'];	
				}
			}
			
			
			return $consoleCatID;
		}
		
		public function addConsoleOptions($consoleCatID) {

			$consoleObj = new ConsoleOption($this->MySQL);
			$consoleObj->setCategoryKeyValue($consoleCatID);
			$newSortNum = $consoleObj->getHighestSortNum()+1;
			
			foreach($this->pluginConsoleOptions as $consoleOptionInfo) {
				$consoleObj->addNew(array("consolecategory_id", "pagetitle", "filename", "sortnum"), array($consoleCatID, $consoleOptionInfo['pagetitle'], $consoleOptionInfo['filename'], $newSortNum++));
			}
			
		}
		
		public function addPluginPages() {
			
			foreach($this->pluginPages as $pluginPageInfo) {
				
				$this->objPlugin->pluginPage->addNew(array("plugin_id", "page", "pagepath"), array($this->pluginID, $pluginPageInfo['page'], $pluginPageInfo['pagepath']));
			
			}	
			
		}
		
		public function importSQL() {

			$returnVal = true;
			if($this->sql != "") {
				if($this->MySQL->multi_query($this->sql)) {
					
					do {
						if($result = $this->MySQL->store_result()) {
							$result->free();
						}
					}
					while($this->MySQL->next_result());

				}
				else {
					$returnVal = false;
				}		
				
			}
			
			return $returnVal;
		}
		
		
		public function addPlugin() {

			$this->objPlugin->addNew(array("name", "filepath", "dateinstalled"), array($this->pluginName, $this->pluginDir, time()));
					
			$this->pluginID = $this->objPlugin->get_info("plugin_id");
			$this->objPlugin->pluginPage->setCategoryKeyValue($this->pluginID);
			
		}
		
		
		public function install() {

			$returnVal['result'] = "fail";
			if($this->pluginName != "" && $this->pluginDir != "" && !$this->checkTableConflicts() && !$this->isInstalled() && $this->importSQL()) {
				
				$this->addPlugin();
	
				$this->addPluginPages();
				
				$this->addNewConsoleItems();
				
				$returnVal['result'] = "success";
				
			}
			

			
			
			if(count($this->errors) > 0) {
				
				$returnVal['result'] = "fail";
				$returnVal['errors'] = $this->errors;
				
			}
			
			
			echo json_encode($returnVal);
		}
		
		
		public function dropPluginPageTables() {
			
			foreach($this->arrPluginTables as $tableName) {
		
				$dropSQL = "DROP TABLE `".$tableName."`";
				if($this->MySQL->query($dropSQL)) {
					$countDrops++;	
				}
				
			}
			
		}
		
		public function removeConsoleOptions() {
			$consoleObj = new ConsoleOption($this->MySQL);
			foreach($this->pluginConsoleOptions as $consoleOptionInfo) {
				$consoleOptionID = $consoleObj->findConsoleIDByName($consoleOptionInfo['pagetitle']);
				if($consoleObj->select($consoleOptionID)) {

					$consoleObj->delete();
					
				}
			}
			
		}
		
		public function uninstall() {
		
			$this->pluginID = array_search($this->pluginDir, $this->objPlugin->getPlugins("filepath"));
			$this->objPlugin->select($this->pluginID);
			
			if($this->objPlugin->delete()) {
				
				$this->dropPluginPageTables();	
				$this->removeConsoleOptions();
				
				$returnVal['result'] = "success";
			}
			else {
				
				$returnVal['result'] = "fail";
			}
			
			echo json_encode($returnVal);
			
		}
	}


?>