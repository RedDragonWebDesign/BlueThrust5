<?php 

if(!defined("MAIN_ROOT")) { exit(); }

$sql = "

CREATE TABLE IF NOT EXISTS `".$dbprefix."donations` (
  `donation_id` int(11) NOT NULL AUTO_INCREMENT,
  `donationcampaign_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `datesent` int(11) NOT NULL,
  `amount` decimal(62,2) NOT NULL,
  `hideamount` int(11) NOT NULL,
  `paypalemail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `response` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`donation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".$dbprefix."donations_campaign` (
  `donationcampaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `datestarted` int(11) NOT NULL,
  `dateend` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `recurringunit` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `recurringamount` int(11) NOT NULL,
  `currentperiod` int(11) NOT NULL,
  `goalamount` decimal(62,2) NOT NULL,
  `allowname` int(11) NOT NULL DEFAULT '1',
  `allowmessage` int(11) NOT NULL DEFAULT '1',
  `allowhiddenamount` int(11) NOT NULL,
  `minimumamount` decimal(65,2) NOT NULL,
  `awardmedal` int(11) NOT NULL,
  `currency` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`donationcampaign_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".$dbprefix."donations_errorlog` (
  `donationerror_id` int(11) NOT NULL AUTO_INCREMENT,
  `datesent` int(11) NOT NULL,
  `response` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`donationerror_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

";

?>