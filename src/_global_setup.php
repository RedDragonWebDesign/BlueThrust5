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
$SHOUTBOX_RELOAD_MS = 20000;
$COOKIE_EXP_TIME = time()+((60*60*24)*3); // Remember Me cookie. Expires in 3 days.

// Check PHP Version
if (version_compare(phpversion(), "7.0") < 0) {
	die("You must be using at least PHP version 7.0 in order to run BlueThrust Clan Scripts.  Your current PHP Version: ".phpversion().". You can usually change your PHP version in your website control panel. For example, cPanel.");
}

/** Useful debug function. Pretty prints the contents of a variable.
	Example usage: html_var_export($limit, '$limit'); */
function html_var_export($var, $var_name = null) {
	$output = '<span class="html-var-export">';

	if ( $var_name ) {
		$output .= $var_name . ' = ';
	}

	$output .= nl2br_and_nbsp(var_export($var, true)) . "</span><br /><br />";

	echo $output;
}

function nl2br_and_nbsp($string) {
	$string = nl2br($string);

	$string = nbsp($string);

	return $string;
}

function nbsp($string) {
	$string = preg_replace('/\t/', '&nbsp;&nbsp;&nbsp;&nbsp;', $string);

	// replace more than 1 space in a row with &nbsp;
	$string = preg_replace('/  /m', '&nbsp;&nbsp;', $string);
	$string = preg_replace('/ &nbsp;/m', '&nbsp;&nbsp;', $string);
	$string = preg_replace('/&nbsp; /m', '&nbsp;&nbsp;', $string);

	if ( $string == ' ' ) {
		$string = '&nbsp;';
	}

	// Convert 2 space tab to 4 space tab
	$string = preg_replace('/&nbsp;&nbsp;/m', '&nbsp;&nbsp;&nbsp;&nbsp;', $string);

	return $string;
}