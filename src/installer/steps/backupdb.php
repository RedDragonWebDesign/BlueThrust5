<?php

echo "

Database tables found that <b>WILL</b> be overwritten...<br><br>
Attempting to save backup copy...<br><br>

";
$backupFileName = "dbbackup_".time().".txt";


if(file_put_contents($backupFileName, "") === false && !isset($_POST['checkBackup'])) {

	echo "

	<div id='showConfirm' style='display: none'>

	<p align='center'>
	<b>Note:</b> Unable to save a backup of previous database tables.<br><br>
	Are you sure you want to continue with the installation?
	</p>

	</div>

	<form action='index.php?step=3' method='post' id='confirmFormStep3'>
	<input type='hidden' name='dbuser' value='".$_POST['dbuser']."'>
	<input type='hidden' name='dbname' value='".$_POST['dbname']."'>
	<input type='hidden' name='dbpass' value='".$_POST['dbpass']."'>
	<input type='hidden' name='dbhost' value='".$_POST['dbhost']."'>
	<input type='hidden' name='tableprefix' value='".$_POST['tableprefix']."'>
	<input type='hidden' name='adminusername' value='".$_POST['adminusername']."'>
	<input type='hidden' name='adminpassword' value='".$_POST['adminpassword']."'>
	<input type='hidden' name='adminpassword_repeat' value='".$_POST['adminpassword_repeat']."'>
	<input type='hidden' name='adminkey' value='".$_POST['adminkey']."'>
	<input type='hidden' name='adminkey_repeat' value='".$_POST['adminkey_repeat']."'>
	<input type='hidden' name='step2submit' value='".$_POST['step2submit']."'>
	<input type='hidden' name='installType' value='".$_POST['installType']."'>
	<input type='hidden' name='checkBackup' value='1'>
	</form>

	<script type='text/javascript'>

	$('#showConfirm').dialog({

	title: 'Confirm',
	modal: true,
	width: 400,
	zIndex: 99999,
	resizable: false,
	show: 'scale',
	buttons: {
	'Yes': function() {
	$('#confirmFormStep3').submit();
},
'Cancel': function() {
window.location = 'index.php'
}
}

});

</script>

";


	exit();
}
else {
	$_POST['checkBackup'] = true;
}


if($_POST['checkBackup']) {
	
	foreach($arrTableMatches as $tableName) {

		// Get table structure
		$result = $mysqli->query("SHOW CREATE TABLE ".$tableName);
		$row = $result->fetch_array();

		$createTableSQL = $row[1];

		// Create Insert Statements

		$insertStmt = "INSERT INTO ".$tableName." (";

		$arrColumnNames = array();
		$result = $mysqli->query("DESCRIBE ".$tableName);
		while($row = $result->fetch_assoc()) {

			$arrColumnNames[] = $row['Field'];

			
		}


		$sqlInsertColumnNames = implode(", ", $arrColumnNames);

		$insertStmt .= $sqlInsertColumnNames.") VALUES ('";

		$arrInsertStmts = array();

		$result = $mysqli->query("SELECT * FROM ".$tableName);
		while($row = $result->fetch_assoc()) {
			$arrColumnValues = array();
			foreach($arrColumnNames as $columnName) {
				$arrColumnValues[] = $mysqli->real_escape_string($row[$columnName]);
			}

			$sqlInsertColumnValues = implode("','", $arrColumnValues);

			$arrInsertStmts[] = $insertStmt.$sqlInsertColumnValues."');";
			

		}

		$createTableSQL .= ";\n\n";
		file_put_contents($backupFileName, $createTableSQL, FILE_APPEND);

		foreach($arrInsertStmts as $insertStatement) {
			$insertStatement .= "\n";
			file_put_contents($backupFileName, $insertStatement, FILE_APPEND);
		}


		$mysqli->query("DROP TABLE ".$tableName);

	}
	
	//print_r($arrOldInsertStmts);

	echo "
	Backup created successfully.  <a href='".$backupFileName."' target='_blank'>Click Here</a> to view.<br><br>
	";

}

?>