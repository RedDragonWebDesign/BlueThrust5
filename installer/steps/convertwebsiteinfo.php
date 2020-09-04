<?php

	if(!defined("CONVERT_WEBSITEINFO")) {
		exit();
	}
	
	$websiteInfoObj = new Basic($mysqli, "websiteinfo", "websiteinfo_id");
	$websiteInfoObj->select(1);
	$websiteInfo = $websiteInfoObj->get_info();
	$mysqli->query("DROP TABLE ".$_POST['tableprefix']."websiteinfo");
	
	$newWebsiteInfoSQL = "CREATE TABLE IF NOT EXISTS `".$_POST['tableprefix']."websiteinfo` (
  `websiteinfo_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`websiteinfo_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
	
	$mysqli->query($newWebsiteInfoSQL);
	$skipColumns = array("websiteinfo_id", "name", "value");
	
	foreach($websiteInfo as $key => $value) {
	
		if(!in_array($key, $skipColumns)) {
			$websiteInfoObj->addNew(array("name", "value"), array($key, $value));
		}
		
	}
	
	$websiteInfoObj->addNew(array("name", "value"), array("news_postsperpage", 10));
?>