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

require_once('_global_setup.php');

// This setup page should not be changed.  Edit _config.php to configure your website.

ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 60*60*24*3);

if(!isset($prevFolder)) {
	$prevFolder = "";	
}


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

require_once($prevFolder."_config.php");
define("BASE_DIRECTORY", $BASE_DIRECTORY);
//define("BASE_DIRECTORY", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$MAIN_ROOT));
define("MAIN_ROOT", $MAIN_ROOT);


$PAGE_NAME = "";
require_once($prevFolder."include/lib_autolink/lib_autolink.php");

require_once(BASE_DIRECTORY."_functions.php");

// Class Loaders
function BTCS4Loader($class_name) {
	if(file_exists(BASE_DIRECTORY."classes/".strtolower($class_name).".php")) {
		require_once(BASE_DIRECTORY."classes/".strtolower($class_name).".php");
	}
	elseif(file_exists(require_once(BASE_DIRECTORY."classes/formcomponents/".strtolower($class_name).".php"))) {
		require_once(BASE_DIRECTORY."classes/formcomponents/".strtolower($class_name).".php");
	}
}
spl_autoload_register("BTCS4Loader", true, true);
require_once(BASE_DIRECTORY."include/phpmailer/PHPMailerAutoload.php");

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

// Default websiteinfo values
require_once(BASE_DIRECTORY."include/websiteinfo_defaults.php");


if(!isset($_SESSION['appendIP'])) {
	$_SESSION['appendIP'] = substr(md5(uniqid().time()),0,10);
}

$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

// Check Debug Mode
if($websiteInfo['debugmode'] == 1) {
	debug();
}

// Check for Ban
$ipbanObj = new IPBan($mysqli);
if($ipbanObj->isBanned($IP_ADDRESS)) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
}

date_default_timezone_set($websiteInfo['default_timezone']);


$hooksObj = new btHooks();
$btThemeObj = new btTheme();
$clockObj = new Clock($mysqli);
$btThemeObj->setThemeDir($THEME);
$btThemeObj->setClanName($CLAN_NAME);
$btThemeObj->initHead();
$breadcrumbObj = new BreadCrumb();

require_once(BASE_DIRECTORY."plugins/mods.php");

// Caches for commonly queried SQL tables. Reduces the # of SQL queries.
// Example:
	// members/index.php goes from 1653 queries to 566 queries.
	// index.php goes from 129 queries to 67 queries.
// Only cache tables where you are not going to have to read the new data on the same page. Else you may introduce hard to diagnose bugs.
// Make sure your table has a primary_key, and that the SELECT query is picking by the primary_key.
$sqlCache = [];
if ( $SQL_CACHE_ENABLED ) {
	$tablesToCache = [
		'clocks' => 'clock_id',
		'console' => 'console_id',
		// 'console_members' => 'privilege_id',
		'consolecategory' => 'consolecategory_id',
		'gamesplayed' => 'gamesplayed_id',
		'menu_category' => 'menucategory_id',
		'menu_item' => 'menuitem_id',
		'menuitem_link' => 'menulink_id',
		// 'rank_privileges' => 'privilege_id',
		'rankcategory' => 'rankcategory_id',
		'ranks' => 'rank_id',
	];
	
	foreach ( $tablesToCache as $table => $primaryKey ) {
		$sqlCache[$table] = [];
		$result = $mysqli->query("SELECT * FROM ".$dbprefix.$table);
		if ( $result ) {
			while ( $row = $result->fetch_assoc() ) {
				$sqlCache[$table][$row[ $primaryKey ]] = $row;
			}
		}
	}

	// classes/member.php::hasAccess()
	$sqlCache['console_members'] = [];
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."console_members");
	if ( $result ) {
		while ( $row = $result->fetch_assoc() ) {
			$sqlCache['console_members'][] = $row;
		}
	}

	// classes/consoleoptions.php::hasAccess()
	$sqlCache['rank_privileges'] = [];
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."rank_privileges");
	if ( $result ) {
		while ( $row = $result->fetch_assoc() ) {
			$sqlCache['rank_privileges'][] = $row;
		}
	}
	
	// classes/consoleoptions.php::findConsoleIDByName()
	$sqlCache['console-pagetitle'] = [];
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."console");
	if ( $result ) {
		while ( $row = $result->fetch_assoc() ) {
			$sqlCache['console-pagetitle'][ $row['pagetitle'] ] = $row;
		}
	}
}