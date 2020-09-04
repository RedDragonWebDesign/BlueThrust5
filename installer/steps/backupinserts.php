<?php

$backupFileName = "dbbackupinserts_".time().".txt";

$oldInsertSQL = "";
$alterSQL = "";

	foreach($arrTableMatches as $tableName) {

		//if($tableName != $_POST['tableprefix']."console" && $tableName != $_POST['tableprefix']."consolecategory") {

			// Create Insert Statements
	
			$insertStmt = "INSERT INTO ".$tableName." (";
	
			$tableNameNoPrefix = ($_POST['tableprefix'] != "") ? substr($tableName, strlen($_POST['tableprefix'])) : $tableName;
			
			$arrColumnNames = array();
			$result = $mysqli->query("DESCRIBE ".$tableName);
			while($row = $result->fetch_assoc()) {
	
				if($row['Field'] != "privilege_id") {
					$arrColumnNames[] = $row['Field'];
					
					// Check for custom table rows
					
					if(!in_array($row['Field'], $arrTableColumns[$tableNameNoPrefix])) {
						$dispNotNull = (strtoupper($row['Null']) == "NO") ? " NOT NULL" : "";
						$dispAutoIncrement = (strtoupper($row['Extra']) == "AUTO_INCREMENT") ? " AUTO_INCREMENT " : "";
						$dispPrimaryKey = (strtoupper($row['Key']) == "PRI") ? " PRIMARY KEY" : "";
						$dispDefault = (trim($row['Default']) != "") ? " DEFAULT '".$row['default']."' " : "";
						$alterSQL .= "ALTER TABLE `".$tableName."` ADD `".$row['Field']."` ".$row['Type'].$dispNotNull.$dispAutoIncrement.$dispDefault.$dispPrimaryKey.";";
					}
					
				}

			}
	
	
			$sqlInsertColumnNames = implode(", ", $arrColumnNames);
	
			$insertStmt .= $sqlInsertColumnNames.") VALUES ('";
	
			$arrInsertStmts = array();
	
			$result = $mysqli->query("SELECT * FROM ".$tableName);
			while($row = $result->fetch_assoc()) {
				$arrColumnValues = array();
				$blnDoNotAdd = false;
				foreach($arrColumnNames as $columnName) {
					
					$arrColumnValues[] = $mysqli->real_escape_string($row[$columnName]);
					
					if($tableName == $_POST['tableprefix']."rank_privileges" && $columnName == "rank_id" && $row[$columnName] == 1) {
						$blnDoNotAdd = true;
					}
					
				}
	
				$sqlInsertColumnValues = implode("','", $arrColumnValues);
				
				if(!$blnDoNotAdd) {
					$arrInsertStmts[] = $insertStmt.$sqlInsertColumnValues."');";
				}
	
			}
	
	
			
			foreach($arrInsertStmts as $insertStatement) {
				$oldInsertSQL .= $insertStatement;
				file_put_contents($backupFileName, $insertStatement, FILE_APPEND);
			}

		
		//}
	}

?>