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

class btMySQL extends MySQLi {

	protected $bt_TablePrefix;
	protected $bt_TestingMode;

	
	public function __construct($host, $username, $passwd, $dbname = "", $port=null, $socket=null) {

		$host = !isset($host) ? ini_get("mysqli.default_host") : $host;
		$username = !isset($username) ? ini_get("mysqli.default_user") : $username;
		$passwd = !isset($passwd) ? ini_get("mysqli.default_pw") : $passwd;
		$port = !isset($port) ? ini_get("mysqli.default_port") : $port;
		$socket = !isset($socket) ? ini_get("mysqli.default_socket") : $socket;
		
		parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
		
		$this->query("SET SESSION sql_mode = ''");
		
		
	}
	
	
	public function set_tablePrefix($tablePrefix) {
		$this->bt_TablePrefix = $tablePrefix;
	}
	
	public function get_tablePrefix() {
		return $this->bt_TablePrefix;
	}
	
	public function set_testingMode($testModeValue) {
		$this->bt_TestingMode = $testModeValue;
	}
	
	public function displayError($pageName="") {
		if($this->bt_TestingMode) {
			die($pageName." - ".$this->error);
		}
	}
	
	public function getParamTypes($arrValues) {
		$strParamTypes = "";
		if(is_array($arrValues)) {
			foreach($arrValues as $value) {
				$valuetype = gettype($value);
				switch($valuetype) {
					case "integer":
						$strParamTypes .= "i";
						break;
					case "double":
						$strParamTypes .= "d";
						break;
					default:
						$strParamTypes .= "s";
				}
				
			}
		}
		return $strParamTypes;
	}
	
	public function bindParams($objMySQLiStmt, $arrValues) {
		$returnVal = false;
		$strParamTypes = $this->getParamTypes($arrValues);
		
		$tmpParams = array_merge(array($strParamTypes), $arrValues);
		$arrParams = array();
		foreach($tmpParams as $key=>$value) {
			$arrParams[$key] = &$tmpParams[$key];
		}
		
		
		if(!call_user_func_array(array($objMySQLiStmt, "bind_param"), $arrParams)) {
			$returnVal = false;
			echo $objMySQLiStmt->error;
			echo "<br><br>";
			$this->displayError("btmysql.php - bindParams");
		}
		else {
			$returnVal = $objMySQLiStmt;
		}
	
		
		return $returnVal;
		
	}
	
	public function optimizeTables() {
		$tables = array();
		$result = $this->query("SHOW TABLE STATUS WHERE Data_free > 0");
		while($row = $result->fetch_assoc()) {
			$tables[] = "`".$row['Name']."`";
		}
		
		$optimizeTables = implode(", ", $tables);
		
		if(count($tables) > 0) {
			$this->query("OPTIMIZE TABLE ".$optimizeTables);
		}
		
	}

}


?>