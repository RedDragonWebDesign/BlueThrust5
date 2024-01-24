<?php

if (!isset($websiteInfo['forum_newindicator'])) {
	$websiteInfo['forum_newindicator'] = 7;
}

if (!isset($websiteInfo['emailqueue_delay']) || $websiteInfo['emailqueue_delay'] == "" || $websiteInfo['emailqueue_delay'] < 5) {
	$websiteInfo['emailqueue_delay'] = 30; // Default check every 30 min
}

if (!isset($websiteInfo['split_downloads'])) {
	$websiteInfo['split_downloads'] = false;
}

if (!isset($websiteInfo['default_timezone']) || $websiteInfo['default_timezone'] == "") {
	$websiteInfo['default_timezone'] = "UTC";
}

if (!isset($websiteInfo['allow_multiple_ips'])) {
	$websiteInfo['allow_multiple_ips'] = true;
}