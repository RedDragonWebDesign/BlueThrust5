<?php
	require_once("classes/btmysql.php");
	require_once("tablelist.php");
	$countErrors = 0;
	$dispError = "";

?>

<!DOCTYPE html>
<html lang="en-us">
	<head>
		<title>Bluethrust Clan Website Manager - Clan Scripts Installer</title>
		<link rel='stylesheet' type='text/css' href='installer/style.css'>
		<script type='text/javascript' src='js/jquery-1.6.4.min.js'></script>
		<link rel='stylesheet' type='text/css' href='js/css/jquery-ui-1.8.17.custom.css'>
		<script type='text/javascript' src='js/jquery-ui-1.8.17.custom.min.js'></script>
		<script type='text/javascript' src='js/main.js'></script>
	</head>
	<body>
	
		<div id='toolTip'></div>
		<div id='toolTipWidth'></div>
		
		<?php



		if (!file_exists("_config.php")) {
			if (file_put_contents("_config.php", "") === false) {
				echo "
					<div class='noteDiv'>
						<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 755.  Otherwise, you will need to manually create and fill out the _config.php file.  Go <a href='configinstructions.php'>HERE</a> to view instructions on how to fill out the config file.
					</div>
				";
			}
		} elseif (file_exists("_config.php") && !is_writable("_config.php")) {
			echo "
				<div class='noteDiv'>
					<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 755.  Otherwise, you will need to manually create and fill out the _config.php file.  Go <a href='configinstructions.php'>HERE</a> to view instructions on how to fill out the config file.
				</div>
			";
		}



		if ($_GET['step'] == "" || $_GET['step'] == 1) {
			require_once("steps/step1.php");
		} elseif ($_GET['step'] == 2) {
			require_once("steps/step2.php");
		} elseif ($_GET['step'] == 3) {
			require_once("steps/step3.php");
		}
		?>
		
		
	</body>
</html>