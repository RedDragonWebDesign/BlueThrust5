CREATE TABLE IF NOT EXISTS `app_captcha` (
  `appcaptcha_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `captchatext` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appcaptcha_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `app_components` (
  `appcomponent_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `componenttype` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `required` int(11) NOT NULL,
  `tooltip` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`appcomponent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `app_selectvalues` (
  `appselectvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `componentvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appselectvalue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `app_values` (
  `appvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `memberapp_id` int(11) NOT NULL,
  `appvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `clocks` (
  `clock_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`clock_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `console` (
  `console_id` int(11) NOT NULL AUTO_INCREMENT,
  `consolecategory_id` int(11) NOT NULL,
  `pagetitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  `adminoption` int(11) NOT NULL,
  `sep` int(11) NOT NULL,
  `defaultconsole` int(11) NOT NULL,
  `hide` int(1) NOT NULL,
  PRIMARY KEY (`console_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `consolecategory` (
  `consolecategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `adminoption` int(1) NOT NULL,
  PRIMARY KEY (`consolecategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `console_members` (
  `privilege_id` int(11) NOT NULL AUTO_INCREMENT,
  `console_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `allowdeny` int(1) NOT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `customform` (
  `customform_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageinfo` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `submitmessage` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `submitlink` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `specialform` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`customform_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `customform_components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `customform_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `componenttype` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `required` int(11) NOT NULL,
  `tooltip` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `customform_selectvalues` (
  `selectvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` int(11) NOT NULL,
  `componentvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`selectvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `customform_submission` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `customform_id` int(11) NOT NULL,
  `submitdate` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seenstatus` int(11) NOT NULL,
  PRIMARY KEY (`submission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `customform_values` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `formvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `custompages` (
  `custompage_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageinfo` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`custompage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `diplomacy` (
  `diplomacy_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `diplomacystatus_id` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  `clanname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `leaders` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clansize` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `clantag` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `skill` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `gamesplayed` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extrainfo` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`diplomacy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `diplomacy_request` (
  `diplomacyrequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateadded` int(11) NOT NULL,
  `diplomacystatus_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clanname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clantag` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `clansize` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `gamesplayed` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `leaders` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `confirmemail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`diplomacyrequest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `diplomacy_status` (
  `diplomacystatus_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`diplomacystatus_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `downloadcategory` (
  `downloadcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  `specialkey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`downloadcategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `downloads` (
  `download_id` int(11) NOT NULL AUTO_INCREMENT,
  `downloadcategory_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateuploaded` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mimetype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filesize` int(11) NOT NULL,
  `splitfile1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `splitfile2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `downloadcount` int(11) NOT NULL,
  PRIMARY KEY (`download_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `download_extensions` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `downloadcategory_id` int(11) NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`extension_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventchat` (
  `eventchat_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `datestarted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`eventchat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventchat_messages` (
  `eventchatmessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventchat_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eventchatmessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventchat_roomlist` (
  `eventchatlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventchat_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `inactive` int(11) NOT NULL,
  `lastseen` int(11) NOT NULL,
  PRIMARY KEY (`eventchatlist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventmessages` (
  `eventmessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eventmessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventmessage_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventmessage_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventpositions` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  `modchat` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invitemembers` int(11) NOT NULL,
  `manageinvites` int(11) NOT NULL,
  `postmessages` int(11) NOT NULL,
  `managemessages` int(11) NOT NULL,
  `attendenceconfirm` int(11) NOT NULL,
  `editinfo` int(11) NOT NULL,
  `eventpositions` int(11) NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `startdate` int(11) NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enddate` int(11) NOT NULL,
  `publicprivate` int(11) NOT NULL,
  `visibility` int(11) NOT NULL,
  `messages` int(11) NOT NULL,
  `invitepermission` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `events_members` (
  `eventmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `invitedbymember_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `attendconfirm_admin` int(11) NOT NULL,
  `attendconfirm_member` int(11) NOT NULL,
  `hide` int(11) NOT NULL,
  PRIMARY KEY (`eventmember_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `failban` (
  `failban_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`failban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forgotpass` (
  `rqid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `changekey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timeofrq` int(11) NOT NULL,
  PRIMARY KEY (`rqid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_attachments` (
  `forumattachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumpost_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  PRIMARY KEY (`forumattachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_board` (
  `forumboard_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumcategory_id` int(11) NOT NULL,
  `subforum_id` int(11) NOT NULL,
  `lastpost_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `accesstype` int(11) NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`forumboard_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_category` (
  `forumcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`forumcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_memberaccess` (
  `forummemberaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `accessrule` int(11) NOT NULL,
  PRIMARY KEY (`forummemberaccess_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_moderator` (
  `forummoderator_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumboard_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`forummoderator_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_post` (
  `forumpost_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumtopic_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `lastedit_date` int(11) NOT NULL,
  `lastedit_member_id` int(11) NOT NULL,
  PRIMARY KEY (`forumpost_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_rankaccess` (
  `forumrankaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`forumrankaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_topic` (
  `forumtopic_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumboard_id` int(11) NOT NULL,
  `forumpost_id` int(11) NOT NULL,
  `lastpost_id` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `replies` int(11) NOT NULL,
  `lockstatus` int(11) NOT NULL,
  `stickystatus` int(11) NOT NULL,
  PRIMARY KEY (`forumtopic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_topicseen` (
  `forumtopicseen_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumtopic_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`forumtopicseen_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `freezemedals_members` (
  `freezemedal_id` int(11) NOT NULL AUTO_INCREMENT,
  `medal_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `freezetime` int(11) NOT NULL,
  PRIMARY KEY (`freezemedal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `gamesplayed` (
  `gamesplayed_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`gamesplayed_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `gamesplayed_members` (
  `gamemember_id` int(11) NOT NULL AUTO_INCREMENT,
  `gamesplayed_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`gamemember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `gamestats` (
  `gamestats_id` int(11) NOT NULL AUTO_INCREMENT,
  `gamesplayed_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stattype` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `calcop` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `firststat_id` int(11) NOT NULL,
  `secondstat_id` int(11) NOT NULL,
  `decimalspots` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  `hidestat` int(11) NOT NULL,
  `textinput` int(11) NOT NULL,
  PRIMARY KEY (`gamestats_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `gamestats_members` (
  `gamestatmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `gamestats_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `statvalue` decimal(65,30) NOT NULL,
  `stattext` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateupdated` int(11) NOT NULL,
  PRIMARY KEY (`gamestatmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `hitcounter` (
  `hit_id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `dateposted` int(11) NOT NULL,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `totalhits` int(11) NOT NULL,
  PRIMARY KEY (`hit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `iarequest` (
  `iarequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `requestdate` int(11) NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `requeststatus` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewdate` int(11) NOT NULL,
  PRIMARY KEY (`iarequest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `iarequest_messages` (
  `iamessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `iarequest_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `messagedate` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`iamessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `imageslider` (
  `imageslider_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `messagetitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `fillstretch` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `membersonly` int(11) NOT NULL,
  PRIMARY KEY (`imageslider_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ipban` (
  `ipban_id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exptime` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`ipban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `logdate` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `medals` (
  `medal_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `autodays` int(11) NOT NULL,
  `autorecruits` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`medal_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `medals_members` (
  `medalmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `medal_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateawarded` int(11) NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`medalmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `memberapps` (
  `memberapp_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `applydate` int(11) NOT NULL,
  `ipaddress` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `memberadded` int(11) NOT NULL,
  `seenstatus` int(11) NOT NULL,
  PRIMARY KEY (`memberapp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rank_id` int(11) NOT NULL,
  `profilepic` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `avatar` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maingame_id` int(11) NOT NULL,
  `birthday` int(11) NOT NULL,
  `datejoined` int(11) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `lastseen` int(11) NOT NULL,
  `lastseenlink` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `loggedin` int(11) NOT NULL,
  `lastpromotion` int(11) NOT NULL,
  `lastdemotion` int(11) NOT NULL,
  `timesloggedin` int(11) NOT NULL,
  `recruiter` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profileviews` int(11) NOT NULL,
  `defaultconsole` int(11) NOT NULL,
  `disabled` int(11) NOT NULL,
  `disableddate` int(11) NOT NULL,
  `notifications` int(11) NOT NULL,
  `topicsperpage` int(11) NOT NULL,
  `postsperpage` int(11) NOT NULL,
  `freezerank` int(11) NOT NULL,
  `forumsignature` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `promotepower` int(11) NOT NULL,
  `onia` int(11) NOT NULL,
  `inactivedate` int(11) NOT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `membersonlypage` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menuitem_customblock` (
  `menucustomblock_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `blocktype` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `code` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menucustomblock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menuitem_custompage` (
  `menucustompage_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `custompage_id` int(11) NOT NULL,
  `prefix` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `textalign` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menucustompage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menuitem_image` (
  `menuimage_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `imagealign` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menuimage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menuitem_link` (
  `menulink_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `prefix` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `textalign` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menulink_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menuitem_shoutbox` (
  `menushoutbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `percentwidth` int(1) NOT NULL,
  `percentheight` int(1) NOT NULL,
  `textboxwidth` int(11) NOT NULL,
  PRIMARY KEY (`menushoutbox_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_category` (
  `menucategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `section` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  `headertype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `headercode` longtext COLLATE utf8_unicode_ci NOT NULL,
  `accesstype` int(11) NOT NULL,
  `hide` int(11) NOT NULL,
  PRIMARY KEY (`menucategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_item` (
  `menuitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `menucategory_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `itemtype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `itemtype_id` int(11) NOT NULL,
  `accesstype` int(1) NOT NULL,
  `hide` int(1) NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`menuitem_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `newstype` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `postsubject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `newspost` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `lasteditmember_id` int(11) NOT NULL,
  `lasteditdate` int(11) NOT NULL,
  `hpsticky` int(11) NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `datesent` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `icontype` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filepath` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `apikey` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateinstalled` int(11) NOT NULL,
  PRIMARY KEY (`plugin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `plugin_config` (
  `pluginconfig_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pluginconfig_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `plugin_pages` (
  `pluginpage_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pagepath` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`pluginpage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `question` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `accesstype` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `multivote` int(11) NOT NULL,
  `displayvoters` int(11) NOT NULL,
  `resultvisibility` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maxvotes` int(11) NOT NULL,
  `pollend` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `lastedit_date` int(11) NOT NULL,
  `lastedit_memberid` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_memberaccess` (
  `pollmemberaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`pollmemberaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_options` (
  `polloption_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `optionvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`polloption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_rankaccess` (
  `pollrankaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`pollrankaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `pollvote_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `polloption_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datevoted` int(11) NOT NULL,
  `votecount` int(11) NOT NULL,
  PRIMARY KEY (`pollvote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `privatemessages` (
  `pm_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `datesent` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `originalpm_id` int(11) NOT NULL,
  `deletesender` int(11) NOT NULL,
  `deletereceiver` int(11) NOT NULL,
  `senderfolder_id` int(11) NOT NULL DEFAULT '-1',
  `receiverfolder_id` int(11) NOT NULL,
  PRIMARY KEY (`pm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `privatemessage_folders` (
  `pmfolder_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`pmfolder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `privatemessage_members` (
  `pmmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `grouptype` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `seenstatus` int(11) NOT NULL,
  `deletestatus` int(11) NOT NULL,
  `pmfolder_id` int(11) NOT NULL,
  PRIMARY KEY (`pmmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `profilecategory` (
  `profilecategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`profilecategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `profileoptions` (
  `profileoption_id` int(11) NOT NULL AUTO_INCREMENT,
  `profilecategory_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `optiontype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`profileoption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `profileoptions_select` (
  `selectopt_id` int(11) NOT NULL AUTO_INCREMENT,
  `profileoption_id` int(11) NOT NULL,
  `selectvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`selectopt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `profileoptions_values` (
  `values_id` int(11) NOT NULL AUTO_INCREMENT,
  `profileoption_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `inputvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`values_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `rankcategory` (
  `rankcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `hidecat` int(11) NOT NULL,
  `useimage` int(1) NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `color` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rankcategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ranks` (
  `rank_id` int(11) NOT NULL AUTO_INCREMENT,
  `rankcategory_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  `autodays` int(11) NOT NULL,
  `hiderank` int(11) NOT NULL,
  `promotepower` int(11) NOT NULL,
  `autodisable` int(11) NOT NULL,
  `color` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `rank_privileges` (
  `privilege_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `console_id` int(11) NOT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `social` (
  `social_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` text COLLATE utf8_unicode_ci NOT NULL,
  `iconwidth` int(11) NOT NULL,
  `iconheight` int(11) NOT NULL,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `tooltip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`social_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `social_members` (
  `socialmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `social_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`socialmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squadapps` (
  `squadapp_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `squad_id` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `applydate` int(11) NOT NULL,
  `dateaction` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `squadmember_id` int(11) NOT NULL,
  PRIMARY KEY (`squadapp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squadinvites` (
  `squadinvite_id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `datesent` int(11) NOT NULL,
  `dateaction` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `startingrank_id` int(11) NOT NULL,
  PRIMARY KEY (`squadinvite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squadnews` (
  `squadnews_id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `newstype` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `postsubject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `newspost` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `lasteditmember_id` int(11) NOT NULL,
  `lasteditdate` int(11) NOT NULL,
  PRIMARY KEY (`squadnews_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squadranks` (
  `squadrank_id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  `postnews` int(11) NOT NULL,
  `managenews` int(11) NOT NULL,
  `postshoutbox` int(11) NOT NULL,
  `manageshoutbox` int(11) NOT NULL,
  `addrank` int(11) NOT NULL,
  `manageranks` int(11) NOT NULL,
  `editprofile` int(11) NOT NULL,
  `sendinvites` int(11) NOT NULL,
  `acceptapps` int(11) NOT NULL,
  `setrank` int(11) NOT NULL,
  `removemember` int(11) NOT NULL,
  PRIMARY KEY (`squadrank_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squads` (
  `squad_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `logourl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `recruitingstatus` int(11) NOT NULL,
  `datecreated` int(11) NOT NULL,
  `privateshoutbox` int(11) NOT NULL,
  `website` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`squad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `squads_members` (
  `squadmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `squadrank_id` int(11) NOT NULL,
  `datejoined` int(11) NOT NULL,
  `lastpromotion` int(11) NOT NULL,
  `lastdemotion` int(11) NOT NULL,
  PRIMARY KEY (`squadmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tableupdates` (
  `tableupdate_id` int(11) NOT NULL AUTO_INCREMENT,
  `tablename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updatetime` int(11) NOT NULL,
  PRIMARY KEY (`tableupdate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournamentmatch` (
  `tournamentmatch_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `team1_id` int(11) NOT NULL,
  `team2_id` int(11) NOT NULL,
  `team1score` int(11) NOT NULL,
  `team2score` int(11) NOT NULL,
  `outcome` int(11) NOT NULL,
  `replayteam1url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `replayteam2url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `adminreplayurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `team1approve` int(11) NOT NULL,
  `team2approve` int(11) NOT NULL,
  `nextmatch_id` int(11) NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`tournamentmatch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournamentplayers` (
  `tournamentplayer_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `displayname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tournamentplayer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournamentpools` (
  `tournamentpool_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `finished` int(11) NOT NULL,
  PRIMARY KEY (`tournamentpool_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournamentpools_teams` (
  `poolteam_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `pool_id` int(11) NOT NULL,
  `team1_id` int(11) NOT NULL,
  `team2_id` int(11) NOT NULL,
  `team1score` int(11) NOT NULL,
  `team2score` int(11) NOT NULL,
  `team1approve` int(1) NOT NULL,
  `team2approve` int(1) NOT NULL,
  `replayteam1url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `replayteam2url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `winner` int(11) NOT NULL,
  PRIMARY KEY (`poolteam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournaments` (
  `tournament_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `gamesplayed_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seedtype` int(11) NOT NULL,
  `startdate` int(11) NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `eliminations` int(11) NOT NULL,
  `playersperteam` int(11) NOT NULL,
  `maxteams` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `requirereplay` int(10) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`tournament_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournamentteams` (
  `tournamentteam_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seed` int(11) NOT NULL,
  PRIMARY KEY (`tournamentteam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tournament_connect` (
  `tournamentconnect_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `clanname` varchar(255) NOT NULL,
  `clanurl` text NOT NULL,
  `connected` int(11) NOT NULL,
  PRIMARY KEY (`tournamentconnect_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tournament_managers` (
  `tournamentmanager_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`tournamentmanager_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `websiteinfo` (
  `websiteinfo_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`websiteinfo_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
