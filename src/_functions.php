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

// This file should consist of functions. When possible, try to use pure functions... functions with no dependencies, just inputs and outputs. Pure functions are ideal for unit testing.

// General functions to filter out all <, >, ", and ' symbols
function filterArray($arrValues) {
	$newArray = array();
	foreach($arrValues as $key => $value) {
		$temp = str_replace("<", "&lt;", $value);
		$value = str_replace(">", "&gt;", $temp);
		$temp = str_replace("'", "&#39;", $value);
		$value = str_replace('"', '&quot;', $temp);
		$temp = str_replace("&middot;", "&#38;middot;", $value);
		$temp = str_replace("&raquo;", "&#38;raquo;", $temp);
		$temp = str_replace("&laquo;", "&#38;laquo;", $temp);
		
		$newArray[$key] = $temp;
	}
	return $newArray;
}

function filterText($strText) {
	$temp = str_replace("<", "&lt;", $strText);
	$value = str_replace(">", "&gt;", $temp);
	$temp = str_replace("'", "&#39;", $value);
	$value = str_replace('"', '&quot;', $temp);
	$temp = str_replace("&middot;", "&#38;middot;", $value);
	$temp = str_replace("&raquo;", "&#38;raquo;", $temp);
	$temp = str_replace("&laquo;", "&#38;laquo;", $temp);
	return $temp;
}

function getPreciseTime($intTime, $timeFormat="", $bypassTimeDiff=false) {
	$timeDiff = (!$bypassTimeDiff) ? time() - $intTime : 99999;

	if($timeDiff < 3) {
		$dispLastDate = "just now";
	}
	elseif($timeDiff < 60) {
		$dispLastDate = "$timeDiff seconds ago";
	}
	elseif($timeDiff < 3600) {
		$minDiff = round($timeDiff/60);
		$dispMinute = "minutes";
		if($minDiff == 1) {
			$dispMinute = "minute";
		}

		$dispLastDate = "$minDiff $dispMinute ago";
	}
	elseif($timeDiff < 86400) {
		$hourDiff = round($timeDiff/3600);
		$dispHour = "hours";
		if($hourDiff == 1) {
			$dispHour = "hour";
		}

		$dispLastDate = "$hourDiff $dispHour ago";
	}
	else {

		if($timeFormat == "") {
			$timeFormat = "D M j, Y g:i a";
		}


		$dispLastDate = date($timeFormat, $intTime);
		
	}

	return $dispLastDate;
}

function getDateUTC($time, $timeFormat = "D M j, Y g:i a") {
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("UTC"));
	$date->setTimestamp($time);
	
	return $date->format($timeFormat);
}


function parseBBCode($strText) {
	global $MAIN_ROOT;

	// Basic Codes

	$arrBBCodes['Bold'] = array("bbOpenTag" => "[b]", "bbCloseTag" => "[/b]", "htmlOpenTag" => "<span style='font-weight: bold'>", "htmlCloseTag" => "</span>");
	$arrBBCodes['Italic'] = array("bbOpenTag" => "[i]", "bbCloseTag" => "[/i]", "htmlOpenTag" => "<span style='font-style: italic'>", "htmlCloseTag" => "</span>");
	$arrBBCodes['Underline'] = array("bbOpenTag" => "[u]", "bbCloseTag" => "[/u]", "htmlOpenTag" => "<span style='text-decoration: underline'>", "htmlCloseTag" => "</span>");
	$arrBBCodes['Image'] = array("bbOpenTag" => "[img]", "bbCloseTag" => "[/img]", "htmlOpenTag" => "<img src='", "htmlCloseTag" => "'>");
	$arrBBCodes['CenterAlign'] = array("bbOpenTag" => "[center]", "bbCloseTag" => "[/center]", "htmlOpenTag" => "<p align='center'>", "htmlCloseTag" => "</p>");
	$arrBBCodes['LeftAlign'] = array("bbOpenTag" => "[left]", "bbCloseTag" => "[/left]", "htmlOpenTag" => "<p align='left'>", "htmlCloseTag" => "</p>");
	$arrBBCodes['RightAlign'] = array("bbOpenTag" => "[right]", "bbCloseTag" => "[/right]", "htmlOpenTag" => "<p align='right'>", "htmlCloseTag" => "</p>");
	$arrBBCodes['Quote'] = array("bbOpenTag" => "[quote]", "bbCloseTag" => "[/quote]", "htmlOpenTag" => "<div class='forumQuote'>", "htmlCloseTag" => "</div>");
	$arrBBCodes['Code'] = array("bbOpenTag" => "[code]", "bbCloseTag" => "[/code]", "htmlOpenTag" => "<div class='forumCode'>", "htmlCloseTag" => "</div>");
	
	$randPollDiv = "poll_".md5(time().uniqid());
	
	$arrBBCodes['Poll'] = array("bbOpenTag" => "[poll]", "bbCloseTag" => "[/poll]", "htmlOpenTag" => "<div id='".$randPollDiv."'></div><script type='text/javascript'>embedPoll('".$MAIN_ROOT."', '".$randPollDiv."', '", "htmlCloseTag" => "');</script>");
	
	


	foreach($arrBBCodes as $bbCode) {

		$strText = str_ireplace($bbCode['bbOpenTag'],$bbCode['htmlOpenTag'],$strText);
		$strText = str_ireplace($bbCode['bbCloseTag'],$bbCode['htmlCloseTag'],$strText);

	}
	
	// Emoticons
	
	$arrEmoticonCodes = array(":)", ":(", ":D", ";)", ":p");
	$arrEmoticonImg = array("smile.png", "sad.png", "grin.png", "wink.png", "cheeky.png");
	
	foreach($arrEmoticonCodes as $key => $value) {
		
		$imgURL = "<img src='".$MAIN_ROOT."images/emoticons/".$arrEmoticonImg[$key]."' width='15' height='15'>";
		$strText = str_ireplace($value, $imgURL, $strText);
		
	}
	

	// Complex Codes, ex. Links, colors...

	$strText = preg_replace("/\[url](.*?)\[\/url]/i", "<a href='$1' target='_blank'>$1</a>", $strText); // Links no Titles
	$strText = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/i", "<a href='$1' target='_blank'>$2</a>", $strText); // Links with Titles

	
	
	$strText = preg_replace("/\[color=(.*)\](.*)\[\/color\]/i", "<span style='color: $1'>$2</span>", $strText); // Text Color

	$strText = str_replace("[/youtube]", "[/youtube]\n", $strText);
	$strText = preg_replace("/\[youtube\](http|https)(\:\/\/www\.youtube\.com\/watch\?v\=)(.*)\[\/youtube\]/i", "<iframe class='youtubeEmbed' src='http://www.youtube.com/embed/$3?wmode=opaque' frameborder='0' allowfullscreen></iframe>", $strText);
	$strText = preg_replace("/\[\youtube\](http|https)(\:\/\/youtu\.be\/)(.*)\[\/youtube\]/i", "<iframe class='youtubeEmbed' src='http://www.youtube.com/embed/$3?wmode=opaque' frameborder='0' allowfullscreen></iframe>", $strText);
	
	$strText = str_replace("[/twitch]", "[/twitch]\n", $strText);
	$strText = preg_replace("/\[twitch\](http|https)(\:\/\/www\.twitch\.tv\/)(.*)\[\/twitch\]/i", "<object class='youtubeEmbed' type='application/x-shockwave-flash' id='live_embed_player_flash' data='http://www.twitch.tv/widgets/live_embed_player.swf?channel=$3' bgcolor='#000000'><param name='allowFullScreen' value='true' /><param name='wmode' value='opaque' /><param name='allowScriptAccess' value='always' /><param name='allowNetworking' value='all' /><param name='movie' value='http://www.twitch.tv/widgets/live_embed_player.swf' /><param name='flashvars' value='hostname=www.twitch.tv&channel=$3&auto_play=false&start_volume=25' /></object>", $strText);
	
	$strText = autolink($strText);

	return $strText;
}

function autoLinkImage($strText) {
	$strText = preg_replace("/<img src=(\"|\')(.*)(\"|\')>/", "<a href='$2' target='_blank'><img src='$2'></a>", $strText);
	$strText = preg_replace("/<img src=(\"|\')(.*)(\"|\') alt=(\"|\')(.*)(\"|\') width=(\"|\')(.*)(\"|\') height=(\"|\')(.*)(\"|\') \/>/", "<a href='$2' target='_blank'><img src='$2' width='$8' height='$11'></a>", $strText);
	$strText = preg_replace("/<img src=(\"|\')(.*)(\"|\') alt=(\"|\')(.*)(\"|\') \/>/", "<a href='$2' target='_blank'><img src='$2'></a>", $strText);
	
	return $strText;
}

function deleteFile($filename) {
	$returnVal = false;
	if(file_exists($filename)) {
		$returnVal = unlink($filename);	
	}
	
	return $returnVal;
}

function getHTTP() {
	if(!isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && (trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off"))) {
		$dispHTTP = "http://";
	}
	else {
		$dispHTTP = "https://";
	}
	
	return $dispHTTP;
}

function addArraySpace($arr, $space, $atSpot) {
	$newArr = array();
	$i=0;
	foreach($arr as $key => $value) {
		
		if($atSpot == $key) {

			for($x=0; $x<$space; $x++) {
				$newArr[$i] = "";
				$i++;
			}
			
			$newArr[$i] = $value;
		}
		else {
			$newArr[$i] = $value;	
		}
	
		$i++;	
	}
	
	return $newArr;
}


function pluralize($word, $num) {
	if($num == 1) {
		$returnVal = $word;
	}
	else {
		$returnVal = $word."s";
	}
	
	return $returnVal;
}

function encryptPassword($password) {
	$randomString = substr(md5(uniqid("", true)),0,22);
	$randomNum = rand(4,10);
	if($randomNum < 10) {
		$randomNum = "0".$randomNum;
	}
	
	$strSalt = "$2a$".$randomNum."$".$randomString;
	$encryptPassword = crypt($password, $strSalt);
	
	$returnArr = array("password" => $encryptPassword, "salt" => $strSalt);
	
	return $returnArr;
}

function getSelected($arrValues, $selectedValue) {
	$returnArr = array();
	foreach($arrValues as $value) {
		$returnArr[$value] = ($value == $selectedValue) ? " selected" : "";
	}
	return $returnArr;
}

function debug_string_backtrace() {
	ob_start();
	debug_print_backtrace();
	$trace = ob_get_contents();
	ob_end_clean();

	// Remove first item from backtrace as it's this function which
	// is redundant.
	$trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);
	
	// sanitize HTML
	$trace = htmlspecialchars($trace);
	
	// Put each stack trace on its own line
	$trace = preg_replace('/\n/', '<br />', $trace);
	
	// Delete all but 1st stack trace
	// $trace = preg_replace('/\n.*/', '', $trace);
	
	return $trace;
}

/** Dump your entire SQL table into an array. You can use this function to do a `WHERE $condition1Field = $condition1Value AND $condition2Field = $condition2Value` type query. */
function sql_array_select_where($sqlTableAsArray, $condition1Field, $condition1Value, $condition2Field = false, $condition2Value = false) {
	$result = [];
	foreach ( $sqlTableAsArray as $row ) {
		$condition2 = true;
		if ( $condition2Field ) {
			$condition2 = isset($row[$condition2Field]) && $row[$condition2Field] == $condition2Value;
		}
		
		if (
			isset($row[$condition1Field]) &&
			$row[$condition1Field] == $condition1Value &&
			$condition2
		) {
			$result[] = $row;
		}
	}
	return $result;
}