<?php

// _global_setup.php is included into the main website, and the installer.
// _setup.php is only included into the main website.

// Error reporting default = off.
$debug = false;
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

function debug() {
	global $debug;
	$debug = true;
	mysqli_report(MYSQLI_REPORT_STRICT);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

// DECLARE GLOBAL VARIABLES
$SQL_PROFILER = [];
$SQL_CACHE_ENABLED = true;
$EXTERNAL_JAVASCRIPT = '';
$VERSION = 'v5';

// Check PHP Version

if(version_compare(phpversion(), "7.0") < 0) {
	die("You must be using at least PHP version 7.0 in order to run BlueThrust Clan Scripts.  Your current PHP Version: ".phpversion().". You can change your PHP version in your website control panel. For example, cPanel.");	
}