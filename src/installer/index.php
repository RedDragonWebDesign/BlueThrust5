<?php

require_once("../_global_setup.php");

if (isset($_COOKIE['btSessionID']) && $_COOKIE['btSessionID'] != "") {
	session_id($_COOKIE['btSessionID']);
	session_start();
	ini_set('session.use_only_cookies', 1);
} else {
	session_start();
	ini_set('session.use_only_cookies', 1);
	if (
		isset($_SESSION['btRememberMe']) &&
		$_SESSION['btRememberMe'] == 1 &&
		(
			! isset($_COOKIE['btSessionID']) ||
			$_COOKIE['btSessionID'] == ""
		)
	) {
		setcookie("btSessionID", session_id(), $COOKIE_EXP_TIME);
	}
}

require_once("../classes/btmysql.php");
require_once("../classes/btmail.php");
require_once("../classes/member.php");
require_once("../classes/consolecategory.php");
require_once("../classes/consoleoption.php");
require_once("../classes/websiteinfo.php");
require_once("tablelist.php");
require_once("tablecolumns.php");
require_once("consoleinfo.php");
$countErrors = 0;
$dispError = "";

?>

<!DOCTYPE html>
<html lang="en-us">
	<head>
		<title>Bluethrust Clan Website Manager - Clan Scripts Installer</title>
		<link rel='stylesheet' type='text/css' href='style.css'>
		<script type='text/javascript' src='../js/jquery-1.6.4.min.js'></script>
		<link rel='stylesheet' type='text/css' href='../js/css/jquery-ui-1.8.17.custom.css'>
		<script type='text/javascript' src='../js/jquery-ui-1.8.17.custom.min.js'></script>
		<script type='text/javascript' src='../js/main.js'></script>
	</head>
	<body>
	
		<div id='toolTip'></div>
		<div id='toolTipWidth'></div>
		
		<div class='topBarDiv'>
			<div class='logoDiv'></div>
			<div class='installerLogoDiv'></div>
		</div>
		
		<div class='contentContainer'>
			<div class='contentContainerTop'></div>
			<div class='contentContainerCenter'>
		<?php



		if (!file_exists("../_config.php")) {
			if (file_put_contents("../_config.php", "") === false) {
				echo "
					<div class='noteDiv'>
						<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 775.  Otherwise, you will need to manually create and fill out the _config.php file.
					</div>
				";
			}
		} elseif (file_exists("../_config.php") && !is_writable("../_config.php")) {
			echo "
				<div class='noteDiv'>
					<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 775.  Otherwise, you will need to manually create and fill out the _config.php file.
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
		
		
			</div>
			<div class='contentContainerBottom'></div>
			<div class='footerContainer'>
				Powered By:
					<a href='https://github.com/RedDragonWebDesign/BlueThrust5' target='_blank'>BlueThrust Clan Scripts <?php echo $VERSION; ?></a><br>
				
				Based On:
					<a href='http://bluethrust.com' target='_blank'>Bluethrust Clan Scripts v4</a><br>
			</div>
		</div>
		
	</body>
</html>
