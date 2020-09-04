CREATE TABLE IF NOT EXISTS `app_captcha` (
  `appcaptcha_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `captchatext` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appcaptcha_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `app_components` (
  `appcomponent_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `componenttype` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `required` int(11) NOT NULL,
  `tooltip` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`appcomponent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=227 ;

INSERT INTO `app_components` (`appcomponent_id`, `name`, `componenttype`, `required`, `tooltip`, `ordernum`) VALUES(225, 'Captcha', 'captcha', 0, '', 1);

CREATE TABLE IF NOT EXISTS `app_selectvalues` (
  `appselectvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `componentvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appselectvalue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

INSERT INTO `app_selectvalues` (`appselectvalue_id`, `appcomponent_id`, `componentvalue`) VALUES(1, 4, 'Haha');
INSERT INTO `app_selectvalues` (`appselectvalue_id`, `appcomponent_id`, `componentvalue`) VALUES(2, 4, 'No');
INSERT INTO `app_selectvalues` (`appselectvalue_id`, `appcomponent_id`, `componentvalue`) VALUES(3, 4, 'Yes');
INSERT INTO `app_selectvalues` (`appselectvalue_id`, `appcomponent_id`, `componentvalue`) VALUES(4, 0, 'test');

CREATE TABLE IF NOT EXISTS `app_values` (
  `appvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `appcomponent_id` int(11) NOT NULL,
  `memberapp_id` int(11) NOT NULL,
  `appvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`appvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `clocks` (
  `clock_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`clock_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

INSERT INTO `clocks` (`clock_id`, `name`, `color`, `timezone`, `ordernum`) VALUES(1, 'Eastern Time', '#1fdd00', 'America/New_York', 4);
INSERT INTO `clocks` (`clock_id`, `name`, `color`, `timezone`, `ordernum`) VALUES(2, 'Central Time', '#ff8400', 'America/Chicago', 3);
INSERT INTO `clocks` (`clock_id`, `name`, `color`, `timezone`, `ordernum`) VALUES(3, 'Mountain Time', '#5400ff', 'America/Denver', 2);
INSERT INTO `clocks` (`clock_id`, `name`, `color`, `timezone`, `ordernum`) VALUES(4, 'Pacific Time', '#0072ff', 'America/Los_Angeles', 1);

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=215 ;

INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(1, 1, 'Add New Rank', 'admin/addrank.php', 1, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(2, 1, 'Manage Ranks', 'admin/manageranks.php', 2, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(5, 2, 'Add Member', 'membermanagement/addmember.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(6, 2, 'Promote Member', 'membermanagement/promotemember.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(7, 2, 'Demote Member', 'membermanagement/demotemember.php', 8, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(8, 2, 'Set Member''s Rank', 'membermanagement/setrank.php', 9, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(9, 10, 'Add New Medal', 'admin/addmedal.php', 2, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(10, 10, 'Manage Medals', 'admin/managemedals.php', 3, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(11, 3, 'Edit Profile', 'editprofile.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(12, 1, '-separator-', '', 28, 1, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(20, 2, 'Disable a Member', 'membermanagement/disablemember.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(14, 1, 'Add Games Played', 'admin/addgamesplayed.php', 6, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(15, 1, 'Manage Games Played', 'admin/managegamesplayed.php', 7, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(19, 1, '-separator-', '', 5, 1, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(17, 1, 'Add Custom Page', 'admin/addcustompages.php', 9, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(18, 1, 'Manage Custom Pages', 'admin/managecustompages.php', 10, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(21, 2, 'Delete Member', 'membermanagement/deletemember.php', 5, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(22, 1, 'Add New Rank Category', 'admin/addrankcategory.php', 3, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(23, 1, 'Manage Rank Categories', 'admin/managerankcategories.php', 4, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(135, 10, '-separator-', '', 4, 0, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(25, 1, 'Add Console Option', 'admin/addconsoleoption.php', 15, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(33, 1, 'Manage Console Categories', 'admin/manageconsolecategories.php', 18, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(32, 1, 'Add New Console Category', 'admin/addconsolecategory.php', 17, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(31, 1, 'Manage Console Options', 'admin/manageconsole.php', 16, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(65, 1, 'Add Profile Option', 'admin/addprofileoption.php', 24, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(51, 1, '-separator-', '', 8, 1, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(52, 1, '-separator-', '', 14, 1, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(54, 14, '-separator-', '', 3, 1, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(55, 14, 'Add Download Category', 'admin/adddownloadcategory.php', 1, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(56, 14, 'Manage Download Categories', 'admin/managedownloadcategories.php', 2, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(62, 1, 'Website Settings', 'admin/sitesettings.php', 30, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(61, 1, 'Modify Current Theme', 'admin/edittheme.php', 29, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(60, 1, '-separator-', '', 23, 1, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(63, 1, 'Add Profile Category', 'admin/addprofilecategory.php', 26, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(64, 1, 'Manage Profile Categories', 'admin/manageprofilecategories.php', 27, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(66, 1, 'Manage Profile Options', 'admin/manageprofileoptions.php', 25, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(83, 9, 'Manage News', 'news/managenews.php', 5, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(82, 9, 'Post News', 'news/postnews.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(70, 2, '-separator-', '', 7, 0, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(71, 7, 'Create a Squad', 'squads/create.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(72, 7, 'View Your Squads', 'squads/index.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(73, 7, 'Apply to a Squad', 'squads/apply.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(74, 7, 'View Squad Invitations', 'squads/viewinvites.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(75, 8, 'Create a Tournament', 'tournaments/create.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(76, 8, 'Manage Tournaments', 'tournaments/manage.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(77, 8, 'Manage My Matches', 'tournaments/managematches.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(78, 17, 'Private Messages', 'privatemessages/index.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(84, 9, 'View Private News', 'news/privatenews.php', 6, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(80, 3, 'Edit My Game Stats', 'editmygamestats.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(85, 9, 'Post Comment', 'news/postcomment.php', 3, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(86, 2, 'Undisable Member', 'membermanagement/undisablemember.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(87, 10, 'Award Medal', 'medals/awardmedal.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(88, 10, 'Revoke Medal', 'medals/revokemedal.php', 5, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(89, 3, 'Change Password', 'changepassword.php', 6, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(90, 2, '-separator-', '', 11, 0, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(91, 2, 'Reset Member Password', 'membermanagement/resetpassword.php', 16, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(92, 3, 'View Logs', 'logs.php', 8, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(93, 9, 'Post in Shoutbox', 'news/postshoutbox.php', 4, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(96, 2, 'Registration Options', 'membermanagement/registrationoptions.php', 12, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(97, 2, 'Member Application', 'membermanagement/memberapplication.php', 13, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(98, 2, 'View Member Applications', 'membermanagement/viewapplications.php', 14, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(99, 11, 'Diplomacy: Add a Clan', 'diplomacy/addclan.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(100, 11, 'Diplomacy: Manage Clans', 'diplomacy/manageclans.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(101, 11, 'View Diplomacy Requests', 'diplomacy/viewrequests.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(102, 11, 'Manage Diplomacy Statuses', 'diplomacy/diplomacystatuses.php', 6, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(103, 11, '-seperator-', '', 4, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(104, 11, 'Add Diplomacy Status', 'diplomacy/adddiplomacystatus.php', 5, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(105, 12, 'Add Event', 'events/addevent.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(106, 12, 'Manage My Events', 'events/manage.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(107, 12, 'View Event Invitations', 'events/viewinvites.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(108, 1, 'Add Custom Form Page', 'admin/addcustomformpage.php', 11, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(109, 1, 'Manage Custom Form Pages', 'admin/managecustomforms.php', 12, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(110, 1, 'View Custom Form Submissions', 'admin/customformsubmissions.php', 13, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(111, 9, 'Modify News Ticker', 'news/newsticker.php', 8, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(113, 8, 'Join a Tournament', 'tournaments/join.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(114, 1, 'Member''s Only Pages', 'admin/membersonlypages.php', 31, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(118, 13, 'Add Forum Category', 'forum/addcategory.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(122, 13, 'Manage Boards', 'forum/manageboards.php', 8, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(119, 13, 'Manage Forum Categories', 'forum/managecategories.php', 5, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(120, 13, '-seperator-', '', 6, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(121, 13, 'Add Board', 'forum/addboard.php', 7, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(123, 13, 'Post Topic', 'forum/post.php', 2, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(124, 13, 'Manage Moderators', 'forum/managemoderators.php', 9, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(125, 13, 'Manage Forum Posts', 'forum/manageposts.php', 3, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(126, 3, 'Change Username', 'changeusername.php', 7, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(127, 2, 'Set Member''s Recruiter', 'membermanagement/setrecruiter.php', 17, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(128, 2, 'Set Member''s Recruit Date', 'membermanagement/setrecruitdate.php', 18, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(129, 2, '-seperator-', '', 15, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(134, 1, 'Clear Logs', 'admin/clearlogs.php', 32, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(136, 1, '-seperator-', '', 33, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(137, 1, 'Add Menu Category', 'admin/addmenucategory.php', 34, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(138, 1, 'Add Menu Item', 'admin/addmenuitem.php', 36, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(139, 1, 'Manage Menu Categories', 'admin/managemenucategory.php', 35, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(140, 1, 'Manage Menu Items', 'admin/managemenuitem.php', 37, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(141, 9, 'Manage Home Page Images', 'news/manageimages.php', 11, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(142, 9, 'Add Home Page Image', 'news/addimage.php', 10, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(143, 9, '-seperator-', '', 9, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(144, 13, 'Post Forum Attachments', 'forum/postattachments.php', 1, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(145, 14, 'Add Download', 'downloads/adddownload.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(146, 14, 'Manage Downloads', 'downloads/managedownloads.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(147, 13, '-seperator-', '', 10, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(148, 13, 'Forum Settings', 'forum/forumsettings.php', 11, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(188, 17, 'Add PM Folder', 'privatemessages/addfolder.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(150, 1, '-seperator-', '', 38, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(187, 17, '-separator-', '', 2, 0, 1, 0, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(165, 1, 'Plugin Manager', 'admin/pluginmanager.php', 39, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(171, 2, 'Set Promotion Power', 'membermanagement/setpromotionpower.php', 10, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(172, 2, '-seperator-', '', 19, 0, 1, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(173, 2, 'Set Member Inactive Status', 'membermanagement/iaoptions.php', 20, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(174, 2, 'View Inactive Requests', 'membermanagement/inactiverequests.php', 21, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(175, 3, 'Inactive Request', 'requestinactive.php', 9, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(176, 3, 'Cancel IA', 'cancelinactive.php', 1, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(189, 17, 'Manage PM Folders', 'privatemessages/managefolders.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(190, 9, 'HTML in News Posts', '', 1, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(191, 9, 'Manage Shoutbox Posts', 'news/manageshoutbox.php', 7, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(192, 2, 'Change Member Username', 'membermanagement/changememberusername.php', 6, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(193, 1, 'IP Banning', 'admin/ipbanning.php', 40, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(194, 18, 'Create a Poll', 'polls/createpoll.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(195, 18, 'Manage Polls', 'polls/managepolls.php', 4, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(196, 18, 'View Poll Results', '', 2, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(200, 13, 'Move Topic', 'forum/movetopic.php', 0, 0, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(201, 7, 'Manage All Squads', '', 0, 1, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(202, 12, 'Manage All Events', '', 0, 1, 0, 1, 1);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(203, 16, 'Add Social Media Icon', 'social/add.php', 1, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(204, 16, 'Manage Social Media Icons', 'social/manage.php', 2, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(211, 20, 'World Clock Settings', 'worldclocks/settings.php', 3, 0, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(209, 20, 'Add World Clock', 'worldclocks/addclock.php', 1, 1, 0, 1, 0);
INSERT INTO `console` (`console_id`, `consolecategory_id`, `pagetitle`, `filename`, `sortnum`, `adminoption`, `sep`, `defaultconsole`, `hide`) VALUES(210, 20, 'Manage World Clocks', 'worldclocks/manageclocks.php', 2, 0, 0, 1, 0);

CREATE TABLE IF NOT EXISTS `consolecategory` (
  `consolecategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `adminoption` int(1) NOT NULL,
  PRIMARY KEY (`consolecategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(1, 'Administrator Options', 1, 1);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(2, 'Member Management', 5, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(3, 'Account Options', 7, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(9, 'News', 6, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(7, 'Squads', 3, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(8, 'Tournaments', 2, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(10, 'Medals', 4, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(11, 'Diplomacy Options', 8, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(12, 'Events', 9, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(13, 'Forum Management', 10, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(14, 'Downloads', 11, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(16, 'Social Media Connect', 12, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(17, 'Private Messages', 13, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(18, 'Polls', 14, 0);
INSERT INTO `consolecategory` (`consolecategory_id`, `name`, `ordernum`, `adminoption`) VALUES(20, 'World Clocks', 16, 0);

CREATE TABLE IF NOT EXISTS `console_members` (
  `privilege_id` int(11) NOT NULL AUTO_INCREMENT,
  `console_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `allowdeny` int(1) NOT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `customform` (
  `customform_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageinfo` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `submitmessage` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `submitlink` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `specialform` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`customform_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `customform_components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `customform_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `componenttype` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `required` int(11) NOT NULL,
  `tooltip` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `customform_selectvalues` (
  `selectvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` int(11) NOT NULL,
  `componentvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`selectvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `customform_submission` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `customform_id` int(11) NOT NULL,
  `submitdate` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seenstatus` int(11) NOT NULL,
  PRIMARY KEY (`submission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `customform_values` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `formvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `custompages` (
  `custompage_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageinfo` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`custompage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

INSERT INTO `custompages` (`custompage_id`, `pagename`, `pageinfo`) VALUES(11, 'History', '<p style="text-align: center;">This is the clan history.</p>\n<p style="text-align: center;">&nbsp;</p>\n<p style="text-align: center;">This is actually just a custom page...</p>');
INSERT INTO `custompages` (`custompage_id`, `pagename`, `pageinfo`) VALUES(12, 'Rules', '<p style="text-align: center;">This is the clan rules page.</p>\n<p style="text-align: center;">&nbsp;</p>\n<p style="text-align: center;">This is actually<strong> just a</strong> custom page...</p>\n<p style="text-align: center;">&nbsp;</p>\n<p style="text-align: center;">&nbsp;</p>');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `diplomacy_status` (
  `diplomacystatus_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`diplomacystatus_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

INSERT INTO `diplomacy_status` (`diplomacystatus_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(1, 'Ally', 'images/diplomacy/status_50e3b3406ddf8.png', 0, 0, 3);
INSERT INTO `diplomacy_status` (`diplomacystatus_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(2, 'Enemy', 'images/diplomacy/status_50e3b36d60f5a.png', 20, 20, 1);
INSERT INTO `diplomacy_status` (`diplomacystatus_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(3, 'Neutral', 'images/diplomacy/status_50e3b37ebd1fc.png', 0, 0, 2);

CREATE TABLE IF NOT EXISTS `downloadcategory` (
  `downloadcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  `specialkey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`downloadcategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

INSERT INTO `downloadcategory` (`downloadcategory_id`, `name`, `ordernum`, `accesstype`, `specialkey`) VALUES(6, 'Replays', 2, 0, '');
INSERT INTO `downloadcategory` (`downloadcategory_id`, `name`, `ordernum`, `accesstype`, `specialkey`) VALUES(5, 'Forum Attachments', 1, 0, 'forumattachments');
INSERT INTO `downloadcategory` (`downloadcategory_id`, `name`, `ordernum`, `accesstype`, `specialkey`) VALUES(7, 'Videos', 3, 0, '');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `download_extensions` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `downloadcategory_id` int(11) NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`extension_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(10, 6, '.zip');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(9, 5, '');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(11, 6, '.rep');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(17, 7, '.avi');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(16, 7, '.wmv');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(15, 7, '.mov');
INSERT INTO `download_extensions` (`extension_id`, `downloadcategory_id`, `extension`) VALUES(18, 7, '.swf');

CREATE TABLE IF NOT EXISTS `eventchat` (
  `eventchat_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `datestarted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`eventchat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `eventchat_messages` (
  `eventchatmessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventchat_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eventchatmessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `eventchat_roomlist` (
  `eventchatlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventchat_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `inactive` int(11) NOT NULL,
  `lastseen` int(11) NOT NULL,
  PRIMARY KEY (`eventchatlist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `eventmessages` (
  `eventmessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eventmessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `eventmessage_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `eventmessage_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateposted` int(11) NOT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

CREATE TABLE IF NOT EXISTS `failban` (
  `failban_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`failban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forgotpass` (
  `rqid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `changekey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timeofrq` int(11) NOT NULL,
  PRIMARY KEY (`rqid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forum_attachments` (
  `forumattachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumpost_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  PRIMARY KEY (`forumattachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forum_category` (
  `forumcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`forumcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forum_memberaccess` (
  `forummemberaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `accessrule` int(11) NOT NULL,
  PRIMARY KEY (`forummemberaccess_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

CREATE TABLE IF NOT EXISTS `forum_moderator` (
  `forummoderator_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumboard_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`forummoderator_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forum_rankaccess` (
  `forumrankaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`forumrankaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `forum_topicseen` (
  `forumtopicseen_id` int(11) NOT NULL AUTO_INCREMENT,
  `forumtopic_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`forumtopicseen_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `freezemedals_members` (
  `freezemedal_id` int(11) NOT NULL AUTO_INCREMENT,
  `medal_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `freezetime` int(11) NOT NULL,
  PRIMARY KEY (`freezemedal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `gamesplayed` (
  `gamesplayed_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `imagewidth` int(11) NOT NULL,
  `imageheight` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`gamesplayed_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(7, 'Minecraft', 'images/gamesplayed/game_501f58d5683e4.png', 32, 32, 2);
INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(9, 'World of Warcraft', 'images/gamesplayed/game_508dc7963ba36.png', 0, 0, 5);
INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(12, 'Black Ops 2', 'images/gamesplayed/game_522d32661c6b3.png', 40, 40, 7);
INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(5, 'Starcraft', 'images/gamesplayed/game_4fc70ad0a7ab8.gif', 28, 14, 4);
INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(8, 'Call of Duty', 'images/gamesplayed/game_508dc503812e7.png', 60, 15, 3);
INSERT INTO `gamesplayed` (`gamesplayed_id`, `name`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`) VALUES(2, 'Starcraft 2', 'images/gamesplayed/game_4f9dc59c97b06.png', 48, 48, 6);

CREATE TABLE IF NOT EXISTS `gamesplayed_members` (
  `gamemember_id` int(11) NOT NULL AUTO_INCREMENT,
  `gamesplayed_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`gamemember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(12, 12, 'K/D Ratio', 'input', '', 0, 0, 2, 0, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(8, 9, 'Level', 'input', '', 0, 0, 0, 0, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(7, 2, 'Losses', 'input', '', 0, 0, 0, 1, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(13, 12, 'Kills', 'input', '', 0, 0, 0, 1, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(14, 12, 'Deaths', 'input', '', 0, 0, 0, 2, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(6, 2, 'Wins', 'input', '', 0, 0, 0, 0, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(5, 5, 'Losses', 'input', '', 0, 0, 0, 1, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(4, 5, 'Wins', 'input', '', 0, 0, 0, 0, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(1, 8, 'K/D Ratio', 'calculate', 'div', 2, 3, 2, 0, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(2, 8, 'Kills', 'input', '', 0, 0, 0, 1, 0, 0);
INSERT INTO `gamestats` (`gamestats_id`, `gamesplayed_id`, `name`, `stattype`, `calcop`, `firststat_id`, `secondstat_id`, `decimalspots`, `ordernum`, `hidestat`, `textinput`) VALUES(3, 8, 'Deaths', 'input', '', 0, 0, 0, 2, 0, 0);

CREATE TABLE IF NOT EXISTS `gamestats_members` (
  `gamestatmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `gamestats_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `statvalue` decimal(65,30) NOT NULL,
  `stattext` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateupdated` int(11) NOT NULL,
  PRIMARY KEY (`gamestatmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `hitcounter` (
  `hit_id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `dateposted` int(11) NOT NULL,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `totalhits` int(11) NOT NULL,
  PRIMARY KEY (`hit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `iarequest` (
  `iarequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `requestdate` int(11) NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `requeststatus` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewdate` int(11) NOT NULL,
  PRIMARY KEY (`iarequest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `iarequest_messages` (
  `iamessage_id` int(11) NOT NULL AUTO_INCREMENT,
  `iarequest_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `messagedate` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`iamessage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ipban` (
  `ipban_id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exptime` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`ipban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `logdate` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(2, 'Active Member Medal', 'Awarded for being an active clan member.', 'images/medals/medal_50d53660e7533.gif', 105, 30, 0, 0, 1);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(3, 'Forum Hero', 'Awarded for being active on the forums.', 'images/medals/medal_50d536249845b.gif', 105, 30, 0, 0, 3);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(4, 'Epic Medal', 'Awarded for being epic.', 'images/medals/medal_50d5361482940.gif', 105, 30, 0, 0, 4);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(5, 'test medal55', 'test', '', 75, 100, 0, 0, 9);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(6, 'Veteran Medal', 'Awarded after being in the clan for 90 days.', 'images/medals/medal_50d535a2dc0f8.gif', 105, 30, 90, 0, 7);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(7, 'Old Timer Medal', 'Awarded for being in the clan for 120 days.', 'images/medals/medal_50d535ef43360.gif', 105, 30, 0, 0, 6);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(8, 'Shooting Star Medal', 'Awarded for being in the clan 150 days.', 'images/medals/medal_50d536049e104.gif', 105, 30, 150, 0, 5);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(9, 'Silver Shield Medal', 'Awarded to members who help the clan with Web Design/Graphics, etc...', 'images/medals/medal_50d53640a63e9.gif', 105, 30, 0, 0, 2);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(10, 'Established Member Medal', 'Awarded after being in the clan for 30 days.', 'images/medals/medal_50d535cada75a.gif', 105, 30, 0, 0, 8);
INSERT INTO `medals` (`medal_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `autodays`, `autorecruits`, `ordernum`) VALUES(22, 'test medal', 'test', 'images/medals/medal_534d83276e1ff.gif', 75, 100, 0, 0, 10);

CREATE TABLE IF NOT EXISTS `medals_members` (
  `medalmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `medal_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `dateawarded` int(11) NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`medalmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `membersonlypage` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pageurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateadded` int(11) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

CREATE TABLE IF NOT EXISTS `menuitem_customblock` (
  `menucustomblock_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `blocktype` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `code` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menucustomblock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `menuitem_custompage` (
  `menucustompage_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `custompage_id` int(11) NOT NULL,
  `prefix` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `textalign` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menucustompage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

INSERT INTO `menuitem_custompage` (`menucustompage_id`, `menuitem_id`, `custompage_id`, `prefix`, `linktarget`, `textalign`) VALUES(11, 90, 12, '<b>&middot;</b> ', '', 'left');
INSERT INTO `menuitem_custompage` (`menucustompage_id`, `menuitem_id`, `custompage_id`, `prefix`, `linktarget`, `textalign`) VALUES(10, 89, 11, '<b>&middot;</b> ', '', 'left');


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `menuitem_link` (
  `menulink_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `linktarget` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `prefix` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `textalign` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menulink_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;

INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(30, 54, 'ranks.php', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(31, 55, 'medals.php', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(32, 56, 'diplomacy', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(33, 57, 'diplomacy/request.php', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(34, 67, 'index.php', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(36, 75, 'news', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(37, 76, 'members.php', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(38, 77, 'squads', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(39, 78, 'tournaments', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(40, 79, 'events', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(41, 80, 'forum', '', '<b>&middot;</b> ', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(42, 82, 'news', '', '', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(43, 83, 'members.php', '', '', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(44, 84, 'tournaments', '', '', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(45, 85, 'squads', '', '', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(46, 86, 'events', '', '', 'left');
INSERT INTO `menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES(47, 87, 'forum', '', '', 'left');


CREATE TABLE IF NOT EXISTS `menuitem_shoutbox` (
  `menushoutbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `menuitem_id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `percentwidth` int(1) NOT NULL,
  `percentheight` int(1) NOT NULL,
  `textboxwidth` int(11) NOT NULL,
  PRIMARY KEY (`menushoutbox_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `menuitem_shoutbox` (`menushoutbox_id`, `menuitem_id`, `width`, `height`, `percentwidth`, `percentheight`, `textboxwidth`) VALUES(1, 2, 0, 0, 0, 0, 0);


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(5, 0, 'Forum Activity', 4, 'customcode', 'FORUM ACTIVITY', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(1, 1, 'Shoutbox', 3, 'customcode', 'SHOUTBOX', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(4, 1, 'Newest Members', 4, 'customcode', 'NEWEST MEMBERS', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(24, 1, 'Log In', 1, 'customcode', 'LOG IN', 2, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(25, 1, 'Logged In', 2, 'customcode', 'LOGGED IN', 1, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(16, 0, 'Poll', 3, 'customcode', 'POLL', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(17, 0, 'Main Menu', 1, 'customcode', 'MAIN MENU', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(28, 2, 'Top Menu', 1, 'customcode', '', 0, 0);
INSERT INTO `menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES(29, 0, 'Top Players', 2, 'customcode', 'TOP PLAYERS', 0, 0);


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=91 ;

INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(2, 1, 'Shoutbox', 'shoutbox', 1, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(4, 4, 'Newest Members', 'newestmembers', 0, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(5, 5, 'Forum Activity', 'forumactivity', 0, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(54, 17, 'Ranks', 'link', 30, 0, 0, 4);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(55, 17, 'Medals', 'link', 31, 0, 0, 8);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(56, 17, 'Diplomacy', 'link', 32, 0, 0, 9);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(57, 17, 'Diplomacy Request', 'link', 33, 0, 0, 10);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(67, 17, 'Home', 'link', 34, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(68, 16, 'Poll', 'poll', 1, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(69, 24, 'Log In', 'login', 0, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(70, 25, 'Logged In', 'login', 0, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(75, 17, 'News', 'link', 36, 0, 0, 2);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(76, 17, 'Members', 'link', 37, 0, 0, 3);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(77, 17, 'Squads', 'link', 38, 0, 0, 5);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(78, 17, 'Tournaments', 'link', 39, 0, 0, 6);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(79, 17, 'Events', 'link', 40, 0, 0, 7);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(80, 17, 'Forum', 'link', 41, 0, 0, 13);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(81, 29, 'Top Player Links', 'top-players', 0, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(82, 28, 'News', 'link', 42, 0, 0, 1);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(83, 28, 'Members', 'link', 43, 0, 0, 2);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(84, 28, 'Tournaments', 'link', 44, 0, 0, 3);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(85, 28, 'Squads', 'link', 45, 0, 0, 4);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(86, 28, 'Events', 'link', 46, 0, 0, 5);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(87, 28, 'Forum', 'link', 47, 0, 0, 6);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(89, 17, 'History', 'custompage', 10, 0, 0, 11);
INSERT INTO `menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES(90, 17, 'Rules', 'custompage', 11, 0, 0, 12);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `datesent` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `icontype` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filepath` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `apikey` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateinstalled` int(11) NOT NULL,
  PRIMARY KEY (`plugin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `plugin_config` (
  `pluginconfig_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pluginconfig_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `plugin_pages` (
  `pluginpage_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) NOT NULL,
  `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pagepath` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`pluginpage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `polls` (`poll_id`, `member_id`, `question`, `accesstype`, `multivote`, `displayvoters`, `resultvisibility`, `maxvotes`, `pollend`, `dateposted`, `lastedit_date`, `lastedit_memberid`) VALUES(1, 13, 'Whats your favorite color?', 'public', 0, 0, 'open', 0, 0, 1395344025, 1395859418, 13);

CREATE TABLE IF NOT EXISTS `poll_memberaccess` (
  `pollmemberaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`pollmemberaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `poll_options` (
  `polloption_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `optionvalue` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`polloption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

INSERT INTO `poll_options` (`polloption_id`, `poll_id`, `optionvalue`, `color`, `sortnum`) VALUES(1, 1, 'Green', '#1AFF00', 0);
INSERT INTO `poll_options` (`polloption_id`, `poll_id`, `optionvalue`, `color`, `sortnum`) VALUES(2, 1, 'White', '#FFFFFF', 1);
INSERT INTO `poll_options` (`polloption_id`, `poll_id`, `optionvalue`, `color`, `sortnum`) VALUES(3, 1, 'Black', '#000000', 2);
INSERT INTO `poll_options` (`polloption_id`, `poll_id`, `optionvalue`, `color`, `sortnum`) VALUES(4, 1, 'Red', '#FF0000', 3);
INSERT INTO `poll_options` (`polloption_id`, `poll_id`, `optionvalue`, `color`, `sortnum`) VALUES(5, 1, 'Blue', '#0900FF', 4);

CREATE TABLE IF NOT EXISTS `poll_rankaccess` (
  `pollrankaccess_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `accesstype` int(11) NOT NULL,
  PRIMARY KEY (`pollrankaccess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `pollvote_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `polloption_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datevoted` int(11) NOT NULL,
  `votecount` int(11) NOT NULL,
  PRIMARY KEY (`pollvote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `privatemessage_folders` (
  `pmfolder_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`pmfolder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `profilecategory` (
  `profilecategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordernum` int(11) NOT NULL,
  PRIMARY KEY (`profilecategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

INSERT INTO `profilecategory` (`profilecategory_id`, `name`, `ordernum`) VALUES(1, 'Personal Information', 1);

CREATE TABLE IF NOT EXISTS `profileoptions` (
  `profileoption_id` int(11) NOT NULL AUTO_INCREMENT,
  `profilecategory_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `optiontype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`profileoption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

INSERT INTO `profileoptions` (`profileoption_id`, `profilecategory_id`, `name`, `optiontype`, `sortnum`) VALUES(2, 1, 'First Name', 'input', 2);
INSERT INTO `profileoptions` (`profileoption_id`, `profilecategory_id`, `name`, `optiontype`, `sortnum`) VALUES(3, 1, 'Gender', 'select', 1);
INSERT INTO `profileoptions` (`profileoption_id`, `profilecategory_id`, `name`, `optiontype`, `sortnum`) VALUES(6, 1, 'Last Name', 'input', 3);

CREATE TABLE IF NOT EXISTS `profileoptions_select` (
  `selectopt_id` int(11) NOT NULL AUTO_INCREMENT,
  `profileoption_id` int(11) NOT NULL,
  `selectvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortnum` int(11) NOT NULL,
  PRIMARY KEY (`selectopt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

INSERT INTO `profileoptions_select` (`selectopt_id`, `profileoption_id`, `selectvalue`, `sortnum`) VALUES(14, 3, 'Male', 2);
INSERT INTO `profileoptions_select` (`selectopt_id`, `profileoption_id`, `selectvalue`, `sortnum`) VALUES(13, 3, 'Alien', 1);
INSERT INTO `profileoptions_select` (`selectopt_id`, `profileoption_id`, `selectvalue`, `sortnum`) VALUES(15, 3, 'Female', 3);

CREATE TABLE IF NOT EXISTS `profileoptions_values` (
  `values_id` int(11) NOT NULL AUTO_INCREMENT,
  `profileoption_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `inputvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`values_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(1, 'Commanders', '', 6, 0, 0, 'The leaders of the clan', 0, 0, '#8A1212');
INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(2, 'Generals', '', 4, 0, 0, '', 0, 0, '#6C7273');
INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(7, 'Warrant Officers', '', 2, 0, 0, '', 0, 0, '#67A300');
INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(6, 'Officers', '', 3, 0, 0, '', 0, 0, '#048500');
INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(8, 'Enlisted', '', 1, 0, 0, '', 0, 0, '#4A2000');
INSERT INTO `rankcategory` (`rankcategory_id`, `name`, `imageurl`, `ordernum`, `hidecat`, `useimage`, `description`, `imagewidth`, `imageheight`, `color`) VALUES(16, 'Co-Commanders', '', 5, 0, 0, 'asdfsadfff', 0, 0, '#B35A1E');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=71 ;

INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(50, 8, 'Master Sergeant', '42 days in the clan.', 'images/ranks/rank_4fa5ecc2638ee.png', 50, 75, 9, 42, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(1, 3, 'Administrator', '', '', 0, 0, 1, 0, 1, 0, 0, '#7FFF00');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(49, 8, 'Gunnery Sergeant', '35 days in the clan.', 'images/ranks/rank_4fa5ec8766300.png', 50, 75, 8, 35, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(41, 1, 'Commander', 'This is a very important rank. They have to be very active and a good leader.', 'images/ranks/rank_517e082d87871.png', 50, 75, 28, 0, 0, 41, 0, '#15FF00');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(42, 16, 'Co-Commander', 'The Co-Commander helps the Commanders. Promoted by the Commander.', 'images/ranks/rank_517e0836044ec.png', 50, 75, 27, 0, 0, 31, 0, '#9C2525');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(43, 8, 'Recruit', 'Starting Rank', 'images/ranks/rank_4fa58088a6a9d.png', 50, 75, 2, 0, 0, 0, 0, '#00EAFF');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(44, 8, 'Private', '3 days in clan.', 'images/ranks/rank_4fa580f71decb.png', 50, 75, 3, 3, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(45, 8, 'Private First Class', '7 days in clan.', 'images/ranks/rank_4fa581276383b.png', 50, 75, 4, 7, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(46, 8, 'Corporal', '14 days in clan.', 'images/ranks/rank_4fa5eb8927175.png', 50, 75, 5, 14, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(47, 8, 'Sergeant', '21 days in the clan.', 'images/ranks/rank_4fa5ebcbef13c.png', 50, 75, 6, 21, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(48, 8, 'Staff Sergeant', '28 days in the clan.', 'images/ranks/rank_4fa5ec028e2a9.png', 50, 75, 7, 28, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(31, 2, 'General', 'Promoted by Co-Commanders and up.', 'images/ranks/rank_4fa57ff737f8f.png', 50, 75, 26, 0, 0, 66, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(51, 8, '1st Sergeant', '49 days in the clan.', 'images/ranks/rank_4fa5ed08200bc.png', 50, 75, 10, 49, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(52, 8, 'Sergeant Major', '56 days in the clan.', 'images/ranks/rank_4fa5ed82cb573.png', 50, 75, 11, 56, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(53, 7, 'Warrant Officer W1', 'Promoted by 2nd Lieutanent or Higher.', 'images/ranks/rank_4fa5f1e607f8a.png', 50, 75, 12, 0, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(54, 7, 'Warrant Officer W2', 'Promoted by 2nd Lieutanent or Higher.', 'images/ranks/rank_4fa5f35316e43.png', 50, 75, 13, 0, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(55, 7, 'Warrant Officer W3', 'Promoted by 2nd Lieutanent or Higher.', 'images/ranks/rank_4fa5f37660647.png', 50, 75, 14, 0, 0, 0, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(56, 7, 'Chief Warrant Officer W4', 'Promoted by 1st Lieutanent or Higher.', 'images/ranks/rank_4fa5f3d842b99.png', 50, 75, 15, 0, 0, 52, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(57, 7, 'Chief Warrant Officer W5', 'Promoted by 1st Lieutanent or Higher.', 'images/ranks/rank_4fa5f407bba12.png', 50, 75, 16, 0, 0, 52, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(58, 6, '2nd Lieutenant', 'Promoted by Captain or Higher.', 'images/ranks/rank_4fa5f51b9c48c.png', 50, 75, 17, 0, 0, 55, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(59, 6, '1st Lieutenant', 'Promoted by Captain or Higher.', 'images/ranks/rank_4fa5f54c265ce.png', 50, 75, 18, 0, 0, 57, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(60, 6, 'Captain', 'Promoted by Colonel or Higher.', 'images/ranks/rank_4fa5f5ddbca9a.png', 50, 75, 19, 0, 0, 59, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(61, 6, 'Major', 'Promoted by Colonel or Higher.', 'images/ranks/rank_4fa5f614eabf2.png', 50, 75, 20, 0, 0, 59, 0, '#FF6F00');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(62, 6, 'Lieutenant Colonel', 'Promoted by Colonel or Higher.', 'images/ranks/rank_517e084db9628.png', 50, 75, 21, 0, 0, 59, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(63, 6, 'Colonel', 'Promoted by Brigadier General or Higher.', 'images/ranks/rank_517e0842d66f0.png', 50, 75, 22, 0, 0, 62, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(64, 2, 'Brigadier General', 'Promoted by Lieutenant General or Higher.', 'images/ranks/rank_4fa5f9c2eb082.png', 50, 75, 23, 0, 0, 63, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(65, 2, 'Major General', 'Promoted by General or Higher.', 'images/ranks/rank_4fa5f9f050643.png', 50, 75, 24, 0, 0, 63, 0, '#ffffff');
INSERT INTO `ranks` (`rank_id`, `rankcategory_id`, `name`, `description`, `imageurl`, `imagewidth`, `imageheight`, `ordernum`, `autodays`, `hiderank`, `promotepower`, `autodisable`, `color`) VALUES(66, 2, 'Lieutenant General', 'Promoted by General or Higher.', 'images/ranks/rank_4fa5fa1568f34.png', 50, 75, 25, 0, 0, 64, 0, '#ffffff');

CREATE TABLE IF NOT EXISTS `rank_privileges` (
  `privilege_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `console_id` int(11) NOT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2683 ;

INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2, 1, 2);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(3, 1, 3);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(6, 1, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(10, 1, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(11, 1, 12);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(12, 1, 14);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(13, 1, 15);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(14, 1, 17);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(15, 1, 18);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(16, 1, 19);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2180, 31, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2179, 31, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(836, 58, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(856, 57, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(855, 57, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(872, 56, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(871, 56, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(920, 55, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(901, 54, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(900, 54, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(927, 53, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(926, 53, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(938, 52, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(948, 51, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(958, 50, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(968, 49, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(978, 48, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(988, 47, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(998, 46, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(835, 58, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(165, 1, 25);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1527, 43, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2682, 41, 140);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(61, 1, 22);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(62, 1, 23);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2681, 41, 138);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2680, 41, 139);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2679, 41, 137);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2110, 42, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2109, 42, 18);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2108, 42, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(816, 59, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(815, 59, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2240, 60, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2239, 60, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(782, 61, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(781, 61, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(780, 61, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1514, 62, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1512, 62, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1491, 63, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1489, 63, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(707, 64, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(706, 64, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(678, 65, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(677, 65, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(649, 66, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(648, 66, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(340, 1, 34);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(339, 1, 33);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(338, 1, 32);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(337, 1, 31);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2107, 42, 17);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(779, 61, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1511, 62, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1488, 63, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(367, 1, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2678, 41, 136);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2178, 31, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(852, 57, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(832, 58, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(812, 59, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(778, 61, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1510, 62, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1487, 63, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(703, 64, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(674, 65, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(645, 66, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(451, 1, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2677, 41, 134);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2106, 42, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2177, 31, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(851, 57, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(831, 58, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(811, 59, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2237, 60, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1509, 62, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1486, 63, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(702, 64, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(673, 65, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(644, 66, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(482, 1, 51);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(483, 1, 52);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(494, 1, 63);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(493, 1, 62);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(492, 1, 61);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(491, 1, 60);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(495, 1, 64);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(496, 1, 65);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(497, 1, 66);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(575, 1, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(574, 1, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(509, 1, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(517, 1, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(518, 1, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(519, 1, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(520, 1, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(573, 1, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1526, 43, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(776, 61, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(775, 61, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(564, 1, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(565, 1, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(772, 61, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(771, 61, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(578, 1, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(580, 1, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(581, 1, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(582, 1, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(583, 1, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(584, 1, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(585, 1, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2105, 42, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2104, 42, 102);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2103, 42, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2101, 42, 104);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2100, 42, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2099, 42, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2098, 42, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2097, 42, 103);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2096, 42, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2094, 42, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2093, 42, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2092, 42, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2091, 42, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2090, 42, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2089, 42, 101);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2176, 31, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2174, 31, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2173, 31, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2172, 31, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2171, 31, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2169, 31, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2168, 31, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2167, 31, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2166, 31, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2165, 31, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2164, 31, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2163, 31, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2161, 31, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2160, 31, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2159, 31, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2158, 31, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2157, 31, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2156, 31, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(652, 66, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(654, 66, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(655, 66, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(656, 66, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(657, 66, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(658, 66, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(659, 66, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(661, 66, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(662, 66, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(663, 66, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(664, 66, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(665, 66, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(666, 66, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(667, 66, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(669, 66, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(670, 66, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(671, 66, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(672, 66, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(681, 65, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(683, 65, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(684, 65, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(685, 65, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(686, 65, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(687, 65, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(688, 65, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(690, 65, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(691, 65, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(692, 65, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(693, 65, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(694, 65, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(695, 65, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(696, 65, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(698, 65, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(699, 65, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(700, 65, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(701, 65, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(710, 64, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(712, 64, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(713, 64, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(714, 64, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(715, 64, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(716, 64, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(717, 64, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(719, 64, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(720, 64, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(721, 64, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(722, 64, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(723, 64, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(724, 64, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(725, 64, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(727, 64, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(728, 64, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(729, 64, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(730, 64, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1485, 63, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1484, 63, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1482, 63, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1481, 63, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1479, 63, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1478, 63, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1477, 63, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1476, 63, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1475, 63, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1474, 63, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1473, 63, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1508, 62, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1507, 62, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1505, 62, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1504, 62, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1502, 62, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1501, 62, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1500, 62, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1499, 62, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1498, 62, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1497, 62, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1496, 62, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(784, 61, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(785, 61, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(786, 61, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(787, 61, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(788, 61, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(790, 61, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2236, 60, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2235, 60, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2234, 60, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2233, 60, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2232, 60, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2230, 60, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2229, 60, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2228, 60, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2227, 60, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2226, 60, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2225, 60, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(818, 59, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(819, 59, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(820, 59, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(821, 59, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(822, 59, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(824, 59, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(825, 59, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(826, 59, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(827, 59, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(828, 59, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(830, 59, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(838, 58, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(839, 58, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(840, 58, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(841, 58, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(842, 58, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(844, 58, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(845, 58, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(846, 58, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(847, 58, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(848, 58, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(850, 58, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(858, 57, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(859, 57, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(860, 57, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(861, 57, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(862, 57, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(864, 57, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(865, 57, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(866, 57, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(867, 57, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(868, 57, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(870, 57, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(875, 56, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(876, 56, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(878, 56, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(879, 56, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(880, 56, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(881, 56, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(882, 56, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(884, 56, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(885, 56, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(886, 56, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(887, 56, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(888, 56, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(890, 56, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(919, 55, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(918, 55, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(914, 55, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(913, 55, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(905, 54, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(906, 54, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(907, 54, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(909, 54, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(910, 54, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(911, 54, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(912, 54, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(922, 55, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(923, 55, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(924, 55, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(925, 55, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(930, 53, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(931, 53, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(932, 53, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(934, 53, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(935, 53, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(936, 53, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(937, 53, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(939, 52, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(941, 52, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(942, 52, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(943, 52, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(945, 52, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(946, 52, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(947, 52, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(949, 51, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(951, 51, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(952, 51, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(953, 51, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(955, 51, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(956, 51, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(957, 51, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(959, 50, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(961, 50, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(962, 50, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(963, 50, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(965, 50, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(966, 50, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(967, 50, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(969, 49, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(971, 49, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(972, 49, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(973, 49, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(975, 49, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(976, 49, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(977, 49, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(979, 48, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(981, 48, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(982, 48, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(983, 48, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(985, 48, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(986, 48, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(987, 48, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(989, 47, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(991, 47, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(992, 47, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(993, 47, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(995, 47, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(996, 47, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(997, 47, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(999, 46, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1001, 46, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1002, 46, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1003, 46, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1005, 46, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1006, 46, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1007, 46, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1284, 45, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1282, 45, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1281, 45, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1279, 45, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1278, 45, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1271, 44, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1269, 44, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1268, 44, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1266, 44, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1265, 44, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1525, 43, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1524, 43, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1522, 43, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1048, 50, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1049, 49, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1839, 1, 135);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2088, 42, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1520, 43, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1264, 44, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1277, 45, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1055, 46, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1056, 47, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1057, 48, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2155, 31, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1059, 51, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1060, 52, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1061, 53, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1062, 54, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1063, 55, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1064, 56, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1065, 57, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1066, 58, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1067, 59, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2224, 60, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1069, 61, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1072, 64, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1073, 65, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1074, 66, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1076, 1, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2676, 41, 114);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2675, 41, 62);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2674, 41, 61);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2673, 41, 12);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2672, 41, 64);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2671, 41, 63);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2670, 41, 66);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2669, 41, 65);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2668, 41, 60);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2667, 41, 33);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2666, 41, 32);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2665, 41, 91);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2664, 41, 31);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2663, 41, 25);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1106, 1, 96);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1107, 1, 97);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1138, 1, 99);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1139, 1, 100);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1140, 1, 101);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1141, 1, 102);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1143, 1, 104);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1519, 43, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1518, 43, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2662, 41, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2661, 41, 52);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2660, 41, 110);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2659, 41, 109);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2658, 41, 141);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2657, 41, 108);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2656, 41, 90);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2655, 41, 171);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2087, 42, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2085, 42, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2084, 42, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2083, 42, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2082, 42, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2081, 42, 100);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2080, 42, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1274, 44, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1275, 44, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1276, 44, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1287, 45, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1288, 45, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1289, 45, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1290, 50, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1291, 49, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2654, 41, 142);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2079, 42, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1517, 43, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1295, 44, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1296, 45, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1297, 46, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1298, 47, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1299, 48, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1301, 51, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1302, 52, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1303, 53, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1304, 54, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1305, 55, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1306, 56, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1307, 57, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1308, 58, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1309, 59, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2223, 60, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1311, 61, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1314, 64, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1315, 65, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1316, 66, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1319, 1, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1320, 50, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1321, 49, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2078, 42, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1516, 43, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1325, 44, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1326, 45, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1327, 46, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1328, 47, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1329, 48, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2153, 31, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1331, 51, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1332, 52, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1333, 53, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1334, 54, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1335, 55, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1336, 56, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1337, 57, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1338, 58, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1339, 59, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2222, 60, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1341, 61, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1493, 62, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1470, 63, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1344, 64, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1345, 65, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1346, 66, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1349, 1, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2653, 41, 18);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1515, 43, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1492, 62, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1469, 63, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2652, 41, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2076, 42, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1603, 1, 103);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1622, 50, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1623, 49, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2651, 41, 143);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2075, 42, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1626, 43, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1627, 44, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1628, 45, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1629, 46, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1630, 47, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1631, 48, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2152, 31, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1633, 51, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1634, 52, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1635, 53, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1636, 54, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1637, 55, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1638, 56, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1639, 57, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1640, 58, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1641, 59, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2221, 60, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1643, 61, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1644, 62, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1645, 63, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1646, 64, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1647, 65, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1648, 66, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1649, 1, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1651, 50, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1652, 49, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2650, 41, 17);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2074, 42, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1655, 43, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1656, 44, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1657, 45, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1658, 46, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1659, 47, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1660, 48, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2151, 31, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1662, 51, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1663, 52, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1664, 53, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1665, 54, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1666, 55, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1667, 56, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1668, 57, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1669, 58, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1670, 59, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2220, 60, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1672, 61, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1673, 62, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1674, 63, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1675, 64, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1676, 65, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1677, 66, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1679, 1, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1682, 50, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1683, 49, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2073, 42, 99);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1686, 43, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1687, 44, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1688, 45, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1689, 46, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1690, 47, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1691, 48, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2150, 31, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1693, 51, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1694, 52, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1695, 53, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1696, 54, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1697, 55, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1698, 56, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1699, 57, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1700, 58, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1701, 59, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2219, 60, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1703, 61, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1704, 62, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1705, 63, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1706, 64, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1707, 65, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1708, 66, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1711, 1, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2649, 41, 8);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1715, 1, 1);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2648, 41, 111);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2647, 41, 92);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2646, 41, 51);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2645, 41, 7);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2644, 41, 70);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2643, 41, 15);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2642, 41, 192);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2641, 41, 102);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2640, 41, 89);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2639, 41, 84);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2638, 41, 14);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2637, 41, 104);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2636, 41, 88);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2635, 41, 83);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2634, 41, 21);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2633, 41, 19);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2632, 41, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2631, 41, 103);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2630, 41, 93);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2629, 41, 86);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2628, 41, 77);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2627, 41, 72);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2626, 41, 23);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2625, 41, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2624, 41, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2623, 41, 107);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2622, 41, 101);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2621, 41, 85);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2620, 41, 76);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2072, 42, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2149, 31, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1821, 64, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1822, 65, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1823, 66, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1824, 1, 125);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1829, 1, 121);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1831, 1, 9);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2619, 41, 74);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2148, 31, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1835, 64, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1836, 65, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1837, 66, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1838, 1, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2618, 41, 54);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2617, 41, 22);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2616, 41, 11);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2615, 41, 10);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2614, 41, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2071, 42, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2070, 42, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2613, 41, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2069, 42, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2147, 31, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1964, 64, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1965, 65, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1966, 66, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(1967, 1, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2612, 41, 123);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2068, 42, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2146, 31, 5);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2218, 60, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2611, 41, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2610, 41, 106);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2063, 1, 55);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2609, 41, 100);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2065, 1, 56);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2608, 41, 80);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2067, 1, 54);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2244, 50, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2245, 49, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2607, 41, 73);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2247, 42, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2248, 43, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2249, 44, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2250, 45, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2251, 46, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2252, 47, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2253, 48, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2254, 31, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2255, 51, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2256, 52, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2257, 53, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2258, 54, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2259, 55, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2260, 56, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2261, 57, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2262, 58, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2263, 59, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2264, 60, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2265, 61, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2266, 62, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2267, 63, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2268, 64, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2269, 65, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2270, 66, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2272, 1, 175);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2273, 50, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2274, 49, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2606, 41, 82);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2276, 42, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2277, 43, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2278, 44, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2279, 45, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2280, 46, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2281, 47, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2282, 48, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2283, 31, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2284, 51, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2285, 52, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2286, 53, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2287, 54, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2288, 55, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2289, 56, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2290, 57, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2291, 58, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2292, 59, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2293, 60, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2294, 61, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2295, 62, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2296, 63, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2297, 64, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2298, 65, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2299, 66, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2301, 1, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2302, 50, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2303, 49, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2605, 41, 56);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2305, 42, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2306, 43, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2307, 44, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2308, 45, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2309, 46, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2310, 47, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2311, 48, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2312, 31, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2313, 51, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2314, 52, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2315, 53, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2316, 54, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2317, 55, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2318, 56, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2319, 57, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2320, 58, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2321, 59, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2322, 60, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2323, 61, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2324, 62, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2325, 63, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2326, 64, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2327, 65, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2328, 66, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2329, 1, 113);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2604, 41, 9);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2346, 42, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2347, 31, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2348, 64, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2349, 65, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2350, 66, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2351, 1, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2603, 41, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2353, 42, 171);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2354, 1, 171);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2602, 41, 2);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2356, 42, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2357, 31, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2358, 56, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2359, 57, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2360, 58, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2361, 59, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2362, 60, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2363, 61, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2364, 62, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2365, 63, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2366, 64, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2367, 65, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2368, 66, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2369, 1, 6);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2601, 41, 190);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2371, 42, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2372, 31, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2373, 64, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2374, 65, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2375, 66, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2376, 1, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2377, 50, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2378, 49, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2600, 41, 176);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2380, 42, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2381, 43, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2382, 44, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2383, 45, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2384, 46, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2385, 47, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2386, 48, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2387, 31, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2388, 51, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2389, 52, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2390, 53, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2391, 54, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2392, 55, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2393, 56, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2394, 57, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2395, 58, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2396, 59, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2397, 60, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2398, 61, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2399, 62, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2400, 63, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2401, 64, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2402, 65, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2403, 66, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2404, 1, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2406, 50, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2407, 49, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2409, 42, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2410, 43, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2411, 44, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2412, 45, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2413, 46, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2414, 47, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2415, 48, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2416, 31, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2417, 51, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2418, 52, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2419, 53, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2420, 54, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2421, 55, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2422, 56, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2423, 57, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2424, 58, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2425, 59, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2426, 60, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2427, 61, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2428, 62, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2429, 63, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2430, 64, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2431, 65, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2432, 66, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2433, 1, 187);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2599, 41, 144);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2598, 41, 105);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2597, 41, 99);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2596, 41, 87);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2595, 41, 78);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2594, 41, 75);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2524, 1, 190);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2593, 41, 71);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2526, 1, 192);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2527, 50, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2528, 49, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2592, 41, 55);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2530, 42, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2531, 43, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2532, 44, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2533, 45, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2534, 46, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2535, 47, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2536, 48, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2537, 31, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2538, 51, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2539, 52, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2540, 53, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2541, 54, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2542, 55, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2543, 56, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2544, 57, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2545, 58, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2546, 59, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2547, 60, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2548, 61, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2549, 62, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2550, 63, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2551, 64, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2552, 65, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2553, 66, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2554, 1, 188);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2555, 50, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2556, 49, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2591, 41, 20);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2558, 42, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2559, 43, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2560, 44, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2561, 45, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2562, 46, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2563, 47, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2564, 48, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2565, 31, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2566, 51, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2567, 52, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2568, 53, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2569, 54, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2570, 55, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2571, 56, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2572, 57, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2573, 58, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2574, 59, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2575, 60, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2576, 61, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2577, 62, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2578, 63, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2579, 64, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2580, 65, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2581, 66, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2582, 1, 189);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2590, 41, 1);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2584, 42, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2585, 31, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2586, 64, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2587, 65, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2588, 66, 98);
INSERT INTO `rank_privileges` (`privilege_id`, `rank_id`, `console_id`) VALUES(2589, 1, 98);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

INSERT INTO `social` (`social_id`, `name`, `icon`, `iconwidth`, `iconheight`, `url`, `tooltip`, `ordernum`) VALUES(1, 'Facebook', 'images/socialmedia/facebook.png', 24, 24, '', 'Entire entire Facebook URL.', 5);
INSERT INTO `social` (`social_id`, `name`, `icon`, `iconwidth`, `iconheight`, `url`, `tooltip`, `ordernum`) VALUES(2, 'Twitter', 'images/socialmedia/twitter.png', 24, 24, 'http://www.twitter.com/', 'Enter your Twitter username.', 4);
INSERT INTO `social` (`social_id`, `name`, `icon`, `iconwidth`, `iconheight`, `url`, `tooltip`, `ordernum`) VALUES(3, 'Youtube', 'images/socialmedia/youtube.png', 24, 24, 'http://youtube.com/', 'Enter your Youtube username.', 3);
INSERT INTO `social` (`social_id`, `name`, `icon`, `iconwidth`, `iconheight`, `url`, `tooltip`, `ordernum`) VALUES(4, 'Google Plus', 'images/socialmedia/googleplus.png', 24, 24, '', 'Enter entire Google Plus URL.', 2);
INSERT INTO `social` (`social_id`, `name`, `icon`, `iconwidth`, `iconheight`, `url`, `tooltip`, `ordernum`) VALUES(5, 'Twitch', 'images/socialmedia/twitch.png', 24, 24, 'http://twitch.tv/', 'Enter your Twitch username.', 1);

CREATE TABLE IF NOT EXISTS `social_members` (
  `socialmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `social_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`socialmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `squads_members` (
  `squadmember_id` int(11) NOT NULL AUTO_INCREMENT,
  `squad_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `squadrank_id` int(11) NOT NULL,
  `datejoined` int(11) NOT NULL,
  `lastpromotion` int(11) NOT NULL,
  `lastdemotion` int(11) NOT NULL,
  PRIMARY KEY (`squadmember_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tableupdates` (
  `tableupdate_id` int(11) NOT NULL AUTO_INCREMENT,
  `tablename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updatetime` int(11) NOT NULL,
  PRIMARY KEY (`tableupdate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tournamentplayers` (
  `tournamentplayer_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `displayname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tournamentplayer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tournamentpools` (
  `tournamentpool_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `finished` int(11) NOT NULL,
  PRIMARY KEY (`tournamentpool_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tournamentteams` (
  `tournamentteam_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seed` int(11) NOT NULL,
  PRIMARY KEY (`tournamentteam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tournament_connect` (
  `tournamentconnect_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `clanname` varchar(255) NOT NULL,
  `clanurl` text NOT NULL,
  `connected` int(11) NOT NULL,
  PRIMARY KEY (`tournamentconnect_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tournament_managers` (
  `tournamentmanager_id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`tournamentmanager_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `websiteinfo` (
  `websiteinfo_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`websiteinfo_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=66 ;

INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(1, 'clanname', 'Bluethrust Clan Website Manager: Clan Scripts v4');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(2, 'clantag', '[bT]');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(3, 'preventhack', '5555');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(4, 'maxdsl', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(5, 'theme', 'ribbonwow');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(6, 'lowdsl', '#00FF00');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(7, 'meddsl', '#FFFF52');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(8, 'highdsl', '#F75B5B');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(9, 'logourl', 'images/logo.png');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(10, 'forumurl', 'http://localhost/cs4git/forum');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(11, 'failedlogins', '8');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(12, 'maxdiplomacy', '10');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(13, 'mostonline', '2');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(14, 'mostonlinedate', '1362280462');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(15, 'memberregistration', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(16, 'memberapproval', '1');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(17, 'medalorder', '1');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(18, 'newsticker', 'Welcome to the Site!');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(19, 'newstickercolor', '#FFFFFF');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(20, 'newstickersize', '14');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(21, 'newstickerbold', '');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(22, 'newstickeritalic', '');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(23, 'debugmode', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(24, 'privateforum', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(25, 'privateprofile', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(26, 'updatemenu', '1399187245');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(27, 'hpimagetype', 'slider');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(28, 'hpimagewidth', '600');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(29, 'hpimageheight', '400');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(30, 'hpimagewidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(31, 'hpimageheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(32, 'forum_showmedal', '1');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(33, 'forum_medalcount', '5');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(34, 'forum_medalwidth', '50');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(35, 'forum_medalheight', '13');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(36, 'forum_medalwidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(37, 'forum_medalheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(38, 'forum_showrank', '1');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(39, 'forum_rankwidth', '50');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(40, 'forum_rankheight', '75');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(41, 'forum_rankwidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(42, 'forum_rankheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(43, 'forum_postsperpage', '10');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(44, 'forum_topicsperpage', '');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(45, 'forum_imagewidth', '500');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(46, 'forum_imageheight', '500');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(47, 'forum_sigwidth', '500');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(48, 'forum_sigheight', '150');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(49, 'forum_imagewidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(50, 'forum_imageheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(51, 'forum_sigwidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(52, 'forum_sigheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(53, 'forum_linkimages', '1');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(54, 'forum_hidesignatures', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(55, 'forum_avatarwidth', '50');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(56, 'forum_avatarheight', '50');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(57, 'forum_avatarwidthunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(58, 'forum_avatarheightunit', 'px');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(59, 'hideinactive', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(60, 'hpnews', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(61, 'sortnum', '0');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(62, 'news_postsperpage', '10');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(63, 'default_timezone', 'America/New_York');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(64, 'date_format', 'l, F j, Y');
INSERT INTO `websiteinfo` (`websiteinfo_id`, `name`, `value`) VALUES(65, 'display_date', '1');
