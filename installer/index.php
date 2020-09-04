<?php

	if(isset($_COOKIE['btSessionID']) && $_COOKIE['btSessionID'] != "") {
		session_id($_COOKIE['btSessionID']);
		session_start();
		ini_set('session.use_only_cookies', 1);
	}
	else {
		session_start();
		ini_set('session.use_only_cookies', 1);
		if(isset($_SESSION['btRememberMe']) && $_SESSION['btRememberMe'] == 1 && (!isset($_COOKIE['btSessionID']) || $_COOKIE['btSessionID'] == "")) {
			$cookieExpTime = time()+((60*60*24)*3);
			setcookie("btSessionID", session_id(), $cookieExpTime);
		}
	}
	
	
	include_once("../classes/btmysql.php");
	include_once("../classes/member.php");
	include_once("../classes/consolecategory.php");
	include_once("../classes/consoleoption.php");
	include_once("../classes/websiteinfo.php");
	include("tablelist.php");
	include("tablecolumns.php");
	include("consoleinfo.php");
	$countErrors = 0;
	$dispError = "";
	
	ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set('display_errors', 0);
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Bluethrust Clan Website Manager - Clan Scripts v4 Installer</title>
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
		
		
		
		if(!file_exists("../_config.php")) {
			
			if(file_put_contents("../_config.php", "") === false) {

				echo "
					<div class='noteDiv'>
						<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 775.  Otherwise, you will need to manually create and fill out the _config.php file.
					</div>
				";

			}
			
		}
		elseif(file_exists("../_config.php") && !is_writable("../_config.php")) {
				
			echo "
				<div class='noteDiv'>
					<b>Note:</b> Unable to write to config file.  You can fix this by setting the file permissions on the _config.php file to 775.  Otherwise, you will need to manually create and fill out the _config.php file.
				</div>
			";
			
		}
		
		
		
		if($_GET['step'] == "" || $_GET['step'] == 1) {
			include("steps/step1.php");			
		}
		elseif($_GET['step'] == 2) {
			include("steps/step2.php");
		}
		elseif($_GET['step'] == 3) {
			include("steps/step3.php");	
		}
		?>
		
		
			</div>
			<div class='contentContainerBottom'></div>
			<div class='footerContainer'>
				Powered By: <a href='http://www.bluethrust.com' target='_blank'>Bluethrust Clan Scripts v4</a>
			</div>
		</div>
		
	</body>
</html>