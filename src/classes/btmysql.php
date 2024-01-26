<?php

/*
 * BlueThrust Clan Scripts
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

	/** In debug mode, this query() override method will enable SQL query profiling. That is, it will keep track of every query made, and it will be printed at the bottom of the page. */
	function query($query, $resultmode = MYSQLI_STORE_RESULT): mysqli_result|bool {
		global $SQL_PROFILER, $debug;
		if ( $debug ) {
			$start = microtime(true);
		}
		$result = parent::query($query, $resultmode);
		if ( $debug) {
			$end = microtime(true);
			$diff = round($end - $start, 3);
			$SQL_PROFILER[] = [
				'query' => $query,
				'stack_trace' => debug_string_backtrace(),
				'duration' => $diff,
			];
		}
		return $result;
	}

	public function __construct($host, $username, $passwd, $dbname = "", $port = null, $socket = null) {

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

	public function displayError($pageName = "") {
		if ($this->bt_TestingMode) {
			die($pageName." - ".$this->error);
		}
	}

	public function getParamTypes($arrValues) {
		$strParamTypes = "";
		if (is_array($arrValues)) {
			foreach ($arrValues as $value) {
				$valuetype = gettype($value);
				switch ($valuetype) {
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

	/**
	 * Binds variables to an SQL prepared statement as parameters. Similar to what PDO does with its 'WHERE field1 = ? AND field2 = ?' syntax. The params are inserted where the question marks are.
	 * @param object $objMySQLiStmt
	 * @param array $arrValues The values to bind to the statement, in order. For example, ['paramValue1', 'paramValue2']
	 * @return object $objMySQLiStmt
	 */
	public function bindParams($objMySQLiStmt, $arrValues) {
		$returnVal = false;

		// Get a string of letter codes corresponding to the types of each parameter. For example, if you have 3 parameters and they are all strings, the code is "sss". If you have 2 parameters and one is a double and one is an int, the code is "di".
		$strParamTypes = $this->getParamTypes($arrValues);

		// Create an array whose first value (spot 0) is the $strParamTypes, and all additional values are the $arrValues. For example, ['ss', 'paramValue1', 'paramValue2']
		$tmpParams = array_merge([$strParamTypes], array_values($arrValues));
		// TODO: can we delete these 6 lines below? $tmpParams above might provide the format we need without the foreach loop. maybe unit test before deleting.
		$arrParams = [];
		foreach ($tmpParams as $key => $value) {
			$arrParams[$key] = &$tmpParams[$key];
		}

		if (!call_user_func_array([$objMySQLiStmt, "bind_param"], $arrParams)) {
			// TODO: can probably get rid of $returnVal and just return the appropriate values
			$returnVal = false;
			echo $objMySQLiStmt->error;
			echo "<br><br>";
			$this->displayError("btmysql.php - bindParams");
		} else {
			// TODO: guard clause instead of else
			$returnVal = $objMySQLiStmt;
		}

		return $returnVal;
	}

	public function optimizeTables() {
		$tables = [];
		$result = $this->query("SHOW TABLE STATUS WHERE Data_free > 0");
		while ($row = $result->fetch_assoc()) {
			$tables[] = "`".$row['Name']."`";
		}

		$optimizeTables = implode(", ", $tables);

		if (count($tables) > 0) {
			$this->query("OPTIMIZE TABLE ".$optimizeTables);
		}
	}

}
