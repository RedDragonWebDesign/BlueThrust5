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


// This setup page should not be changed.  Edit _config.php to configure your website.

// There are two ways to turn on debug mode. You can turn it on here. Or you can turn it on in My Account -> Administrator Options -> Website Settings -> Debug Mode.
// Turning it on here gets the benefit of earlier PHP warnings. You get all of them, not just the ones that are thrown after the database is loaded.
// Debug Mode features: all PHP warnings, all SQL warnings, SQL profiler (query count, list of queries)
$debug = true;
define('SHOUTBOX_RELOAD_MS', 20000); // 20 seconds

// Error reporting default = off.
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

function debug() {
	mysqli_report(MYSQLI_REPORT_STRICT);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

if ( $debug ) {
	debug();
}

// DECLARE GLOBAL VARIABLES
$PAGE_NAME = "";
$EXTERNAL_JAVASCRIPT = '';
$SQL_PROFILER = [];

if (version_compare(PHP_VERSION, '7.0', '<')) {
	die("These scripts need PHP version 7.0 or later to run. Please change this setting in your web host control panel (for example, cPanel).");
}

ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 60*60*24*3);

if(isset($_COOKIE['btUsername']) && isset($_COOKIE['btPassword'])) {
	session_start();
	$_SESSION['btUsername'] = $_COOKIE['btUsername'];
	$_SESSION['btPassword'] = $_COOKIE['btPassword'];
}
else {
	session_start();
}

if(!isset($_SESSION['csrfKey'])) {
	$_SESSION['csrfKey'] = md5(uniqid());
}

// CONFIG.PHP INCLUDED HERE *************************
require_once($prevFolder."_config.php");
// **************************************************

define("BASE_DIRECTORY", $BASE_DIRECTORY);
//define("BASE_DIRECTORY", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$MAIN_ROOT));
define("MAIN_ROOT", $MAIN_ROOT);

// FUNCTIONS.PHP INCLUDED HERE **********************
require_once(BASE_DIRECTORY."_functions.php");
// **************************************************

define("FULL_SITE_URL", getHTTP().$_SERVER['SERVER_NAME'].MAIN_ROOT);

$mysqli = new btmysql($dbhost, $dbuser, $dbpass, $dbname);

$mysqli->set_tablePrefix($dbprefix);
$mysqli->set_testingMode(true);

$logObj = new Basic($mysqli, "logs", "log_id");

// Get Clan Info
$webInfoObj = new WebsiteInfo($mysqli);

$webInfoObj->select(1);

$websiteInfo = $webInfoObj->get_info_filtered();
$CLAN_NAME = $websiteInfo['clanname'];
$THEME = $websiteInfo['theme'];
define("THEME", $THEME);

$arrWebsiteLogoURL = parse_url($websiteInfo['logourl']);

if(!isset($arrWebsiteLogoURL['scheme']) || $arrWebsiteLogoURL['scheme'] == "") {
	$websiteInfo['logourl'] = $MAIN_ROOT."themes/".$THEME."/".$websiteInfo['logourl'];
}

$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

assert_options(ASSERT_BAIL);

// Check Debug Mode
if($websiteInfo['debugmode'] == 1) {
	debug();
}

// Check for Ban
$ipbanObj = new IPBan($mysqli);
if($ipbanObj->isBanned($IP_ADDRESS)) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
}

$websiteInfo['default_timezone'] = (!isset($websiteInfo['default_timezone']) || $websiteInfo['default_timezone'] == "") ? "UTC" : $websiteInfo['default_timezone'];
date_default_timezone_set($websiteInfo['default_timezone']);

$hooksObj = new btHooks();
$btThemeObj = new btTheme();
$clockObj = new Clock($mysqli);
$btThemeObj->setThemeDir($THEME);
$btThemeObj->setClanName($CLAN_NAME);
$btThemeObj->initHead();

require_once(BASE_DIRECTORY."plugins/mods.php");