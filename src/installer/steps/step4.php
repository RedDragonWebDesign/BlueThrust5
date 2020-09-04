<?php
// Check if tables have already been installed.

$result = $mysqli->query("SHOW TABLES");
$arrTestTables = array();

while($row = $result->fetch_array()) {
	$arrTestTables[] = $row[0];
}

$countTableMatches = 0;
foreach($arrTableNames as $tableName) {
	$tempTableName = $_POST['tableprefix'].$tableName;

	if(in_array($tempTableName, $arrTestTables)) {
		$countTableMatches++;
	}

}


if($countTableMatches > 0) {

	echo "
	<div class='noteDiv'>
	<b>Note:</b> It appears you may have already installed Bluethrust Clan Scripts v4.  Running this script may overwrite information in your database.
	</div>
	";

}

?>