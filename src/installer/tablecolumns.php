<?php

$arrTableColumns['app_captcha'][0] = "appcaptcha_id";
$arrTableColumns['app_captcha'][1] = "appcomponent_id";
$arrTableColumns['app_captcha'][2] = "ipaddress";
$arrTableColumns['app_captcha'][3] = "captchatext";

$arrTableColumns['app_components'][0] = "appcomponent_id";
$arrTableColumns['app_components'][1] = "name";
$arrTableColumns['app_components'][2] = "componenttype";
$arrTableColumns['app_components'][3] = "required";
$arrTableColumns['app_components'][4] = "tooltip";
$arrTableColumns['app_components'][5] = "ordernum";

$arrTableColumns['app_selectvalues'][0] = "appselectvalue_id";
$arrTableColumns['app_selectvalues'][1] = "appcomponent_id";
$arrTableColumns['app_selectvalues'][2] = "componentvalue";

$arrTableColumns['app_values'][0] = "appvalue_id";
$arrTableColumns['app_values'][1] = "appcomponent_id";
$arrTableColumns['app_values'][2] = "memberapp_id";
$arrTableColumns['app_values'][3] = "appvalue";

$arrTableColumns['clocks'][0] = "clock_id";
$arrTableColumns['clocks'][1] = "name";
$arrTableColumns['clocks'][2] = "color";
$arrTableColumns['clocks'][3] = "timezone";
$arrTableColumns['clocks'][4] = "ordernum";

$arrTableColumns['comments'][0] = "comment_id";
$arrTableColumns['comments'][1] = "news_id";
$arrTableColumns['comments'][2] = "member_id";
$arrTableColumns['comments'][3] = "dateposted";
$arrTableColumns['comments'][4] = "message";

$arrTableColumns['console'][0] = "console_id";
$arrTableColumns['console'][1] = "consolecategory_id";
$arrTableColumns['console'][2] = "pagetitle";
$arrTableColumns['console'][3] = "filename";
$arrTableColumns['console'][4] = "sortnum";
$arrTableColumns['console'][5] = "adminoption";
$arrTableColumns['console'][6] = "sep";
$arrTableColumns['console'][7] = "defaultconsole";
$arrTableColumns['console'][8] = "hide";

$arrTableColumns['console_members'][0] = "privilege_id";
$arrTableColumns['console_members'][1] = "console_id";
$arrTableColumns['console_members'][2] = "member_id";
$arrTableColumns['console_members'][3] = "allowdeny";

$arrTableColumns['consolecategory'][0] = "consolecategory_id";
$arrTableColumns['consolecategory'][1] = "name";
$arrTableColumns['consolecategory'][2] = "ordernum";
$arrTableColumns['consolecategory'][3] = "adminoption";

$arrTableColumns['customform'][0] = "customform_id";
$arrTableColumns['customform'][1] = "name";
$arrTableColumns['customform'][2] = "pageinfo";
$arrTableColumns['customform'][3] = "submitmessage";
$arrTableColumns['customform'][4] = "submitlink";
$arrTableColumns['customform'][5] = "specialform";

$arrTableColumns['customform_components'][0] = "component_id";
$arrTableColumns['customform_components'][1] = "customform_id";
$arrTableColumns['customform_components'][2] = "name";
$arrTableColumns['customform_components'][3] = "componenttype";
$arrTableColumns['customform_components'][4] = "required";
$arrTableColumns['customform_components'][5] = "tooltip";
$arrTableColumns['customform_components'][6] = "sortnum";

$arrTableColumns['customform_selectvalues'][0] = "selectvalue_id";
$arrTableColumns['customform_selectvalues'][1] = "component_id";
$arrTableColumns['customform_selectvalues'][2] = "componentvalue";
$arrTableColumns['customform_selectvalues'][3] = "sortnum";

$arrTableColumns['customform_submission'][0] = "submission_id";
$arrTableColumns['customform_submission'][1] = "customform_id";
$arrTableColumns['customform_submission'][2] = "submitdate";
$arrTableColumns['customform_submission'][3] = "ipaddress";
$arrTableColumns['customform_submission'][4] = "seenstatus";

$arrTableColumns['customform_values'][0] = "value_id";
$arrTableColumns['customform_values'][1] = "submission_id";
$arrTableColumns['customform_values'][2] = "component_id";
$arrTableColumns['customform_values'][3] = "formvalue";

$arrTableColumns['custompages'][0] = "custompage_id";
$arrTableColumns['custompages'][1] = "pagename";
$arrTableColumns['custompages'][2] = "pageinfo";

$arrTableColumns['diplomacy'][0] = "diplomacy_id";
$arrTableColumns['diplomacy'][1] = "member_id";
$arrTableColumns['diplomacy'][2] = "diplomacystatus_id";
$arrTableColumns['diplomacy'][3] = "dateadded";
$arrTableColumns['diplomacy'][4] = "clanname";
$arrTableColumns['diplomacy'][5] = "leaders";
$arrTableColumns['diplomacy'][6] = "website";
$arrTableColumns['diplomacy'][7] = "clansize";
$arrTableColumns['diplomacy'][8] = "clantag";
$arrTableColumns['diplomacy'][9] = "skill";
$arrTableColumns['diplomacy'][10] = "gamesplayed";
$arrTableColumns['diplomacy'][11] = "extrainfo";

$arrTableColumns['diplomacy_request'][0] = "diplomacyrequest_id";
$arrTableColumns['diplomacy_request'][1] = "ipaddress";
$arrTableColumns['diplomacy_request'][2] = "dateadded";
$arrTableColumns['diplomacy_request'][3] = "diplomacystatus_id";
$arrTableColumns['diplomacy_request'][4] = "email";
$arrTableColumns['diplomacy_request'][5] = "name";
$arrTableColumns['diplomacy_request'][6] = "clanname";
$arrTableColumns['diplomacy_request'][7] = "clantag";
$arrTableColumns['diplomacy_request'][8] = "clansize";
$arrTableColumns['diplomacy_request'][9] = "gamesplayed";
$arrTableColumns['diplomacy_request'][10] = "website";
$arrTableColumns['diplomacy_request'][11] = "leaders";
$arrTableColumns['diplomacy_request'][12] = "message";
$arrTableColumns['diplomacy_request'][13] = "confirmemail";

$arrTableColumns['diplomacy_status'][0] = "diplomacystatus_id";
$arrTableColumns['diplomacy_status'][1] = "name";
$arrTableColumns['diplomacy_status'][2] = "imageurl";
$arrTableColumns['diplomacy_status'][3] = "imagewidth";
$arrTableColumns['diplomacy_status'][4] = "imageheight";
$arrTableColumns['diplomacy_status'][5] = "ordernum";

$arrTableColumns['download_extensions'][0] = "extension_id";
$arrTableColumns['download_extensions'][1] = "downloadcategory_id";
$arrTableColumns['download_extensions'][2] = "extension";

$arrTableColumns['downloadcategory'][0] = "downloadcategory_id";
$arrTableColumns['downloadcategory'][1] = "name";
$arrTableColumns['downloadcategory'][2] = "ordernum";
$arrTableColumns['downloadcategory'][3] = "accesstype";
$arrTableColumns['downloadcategory'][4] = "specialkey";

$arrTableColumns['downloads'][0] = "download_id";
$arrTableColumns['downloads'][1] = "downloadcategory_id";
$arrTableColumns['downloads'][2] = "member_id";
$arrTableColumns['downloads'][3] = "dateuploaded";
$arrTableColumns['downloads'][4] = "name";
$arrTableColumns['downloads'][5] = "filename";
$arrTableColumns['downloads'][6] = "mimetype";
$arrTableColumns['downloads'][7] = "filesize";
$arrTableColumns['downloads'][8] = "splitfile1";
$arrTableColumns['downloads'][9] = "splitfile2";
$arrTableColumns['downloads'][10] = "description";
$arrTableColumns['downloads'][11] = "downloadcount";

$arrTableColumns['eventchat'][0] = "eventchat_id";
$arrTableColumns['eventchat'][1] = "event_id";
$arrTableColumns['eventchat'][2] = "datestarted";
$arrTableColumns['eventchat'][3] = "status";

$arrTableColumns['eventchat_messages'][0] = "eventchatmessage_id";
$arrTableColumns['eventchat_messages'][1] = "eventchat_id";
$arrTableColumns['eventchat_messages'][2] = "member_id";
$arrTableColumns['eventchat_messages'][3] = "dateposted";
$arrTableColumns['eventchat_messages'][4] = "message";

$arrTableColumns['eventchat_roomlist'][0] = "eventchatlist_id";
$arrTableColumns['eventchat_roomlist'][1] = "eventchat_id";
$arrTableColumns['eventchat_roomlist'][2] = "member_id";
$arrTableColumns['eventchat_roomlist'][3] = "inactive";
$arrTableColumns['eventchat_roomlist'][4] = "lastseen";

$arrTableColumns['eventmessage_comment'][0] = "comment_id";
$arrTableColumns['eventmessage_comment'][1] = "eventmessage_id";
$arrTableColumns['eventmessage_comment'][2] = "member_id";
$arrTableColumns['eventmessage_comment'][3] = "dateposted";
$arrTableColumns['eventmessage_comment'][4] = "comment";

$arrTableColumns['eventmessages'][0] = "eventmessage_id";
$arrTableColumns['eventmessages'][1] = "event_id";
$arrTableColumns['eventmessages'][2] = "member_id";
$arrTableColumns['eventmessages'][3] = "dateposted";
$arrTableColumns['eventmessages'][4] = "message";

$arrTableColumns['eventpositions'][0] = "position_id";
$arrTableColumns['eventpositions'][1] = "event_id";
$arrTableColumns['eventpositions'][2] = "name";
$arrTableColumns['eventpositions'][3] = "sortnum";
$arrTableColumns['eventpositions'][4] = "modchat";
$arrTableColumns['eventpositions'][5] = "description";
$arrTableColumns['eventpositions'][6] = "invitemembers";
$arrTableColumns['eventpositions'][7] = "manageinvites";
$arrTableColumns['eventpositions'][8] = "postmessages";
$arrTableColumns['eventpositions'][9] = "managemessages";
$arrTableColumns['eventpositions'][10] = "attendenceconfirm";
$arrTableColumns['eventpositions'][11] = "editinfo";
$arrTableColumns['eventpositions'][12] = "eventpositions";

$arrTableColumns['events'][0] = "event_id";
$arrTableColumns['events'][1] = "member_id";
$arrTableColumns['events'][2] = "title";
$arrTableColumns['events'][3] = "description";
$arrTableColumns['events'][4] = "location";
$arrTableColumns['events'][5] = "startdate";
$arrTableColumns['events'][6] = "timezone";
$arrTableColumns['events'][7] = "enddate";
$arrTableColumns['events'][8] = "publicprivate";
$arrTableColumns['events'][9] = "visibility";
$arrTableColumns['events'][10] = "messages";
$arrTableColumns['events'][11] = "invitepermission";

$arrTableColumns['events_members'][0] = "eventmember_id";
$arrTableColumns['events_members'][1] = "event_id";
$arrTableColumns['events_members'][2] = "member_id";
$arrTableColumns['events_members'][3] = "invitedbymember_id";
$arrTableColumns['events_members'][4] = "position_id";
$arrTableColumns['events_members'][5] = "status";
$arrTableColumns['events_members'][6] = "attendconfirm_admin";
$arrTableColumns['events_members'][7] = "attendconfirm_member";
$arrTableColumns['events_members'][8] = "hide";

$arrTableColumns['failban'][0] = "failban_id";
$arrTableColumns['failban'][1] = "pagename";
$arrTableColumns['failban'][2] = "ipaddress";

$arrTableColumns['forgotpass'][0] = "rqid";
$arrTableColumns['forgotpass'][1] = "username";
$arrTableColumns['forgotpass'][2] = "email";
$arrTableColumns['forgotpass'][3] = "changekey";
$arrTableColumns['forgotpass'][4] = "timeofrq";

$arrTableColumns['forum_attachments'][0] = "forumattachment_id";
$arrTableColumns['forum_attachments'][1] = "forumpost_id";
$arrTableColumns['forum_attachments'][2] = "download_id";

$arrTableColumns['forum_board'][0] = "forumboard_id";
$arrTableColumns['forum_board'][1] = "forumcategory_id";
$arrTableColumns['forum_board'][2] = "subforum_id";
$arrTableColumns['forum_board'][3] = "lastpost_id";
$arrTableColumns['forum_board'][4] = "name";
$arrTableColumns['forum_board'][5] = "description";
$arrTableColumns['forum_board'][6] = "accesstype";
$arrTableColumns['forum_board'][7] = "sortnum";

$arrTableColumns['forum_category'][0] = "forumcategory_id";
$arrTableColumns['forum_category'][1] = "name";
$arrTableColumns['forum_category'][2] = "ordernum";

$arrTableColumns['forum_memberaccess'][0] = "forummemberaccess_id";
$arrTableColumns['forum_memberaccess'][1] = "board_id";
$arrTableColumns['forum_memberaccess'][2] = "member_id";
$arrTableColumns['forum_memberaccess'][3] = "accessrule";

$arrTableColumns['forum_moderator'][0] = "forummoderator_id";
$arrTableColumns['forum_moderator'][1] = "forumboard_id";
$arrTableColumns['forum_moderator'][2] = "member_id";
$arrTableColumns['forum_moderator'][3] = "dateadded";

$arrTableColumns['forum_post'][0] = "forumpost_id";
$arrTableColumns['forum_post'][1] = "forumtopic_id";
$arrTableColumns['forum_post'][2] = "member_id";
$arrTableColumns['forum_post'][3] = "dateposted";
$arrTableColumns['forum_post'][4] = "title";
$arrTableColumns['forum_post'][5] = "message";
$arrTableColumns['forum_post'][6] = "lastedit_date";
$arrTableColumns['forum_post'][7] = "lastedit_member_id";

$arrTableColumns['forum_rankaccess'][0] = "forumrankaccess_id";
$arrTableColumns['forum_rankaccess'][1] = "board_id";
$arrTableColumns['forum_rankaccess'][2] = "rank_id";
$arrTableColumns['forum_rankaccess'][3] = "accesstype";

$arrTableColumns['forum_topic'][0] = "forumtopic_id";
$arrTableColumns['forum_topic'][1] = "forumboard_id";
$arrTableColumns['forum_topic'][2] = "forumpost_id";
$arrTableColumns['forum_topic'][3] = "lastpost_id";
$arrTableColumns['forum_topic'][4] = "views";
$arrTableColumns['forum_topic'][5] = "replies";
$arrTableColumns['forum_topic'][6] = "lockstatus";
$arrTableColumns['forum_topic'][7] = "stickystatus";

$arrTableColumns['forum_topicseen'][0] = "forumtopicseen_id";
$arrTableColumns['forum_topicseen'][1] = "forumtopic_id";
$arrTableColumns['forum_topicseen'][2] = "member_id";

$arrTableColumns['freezemedals_members'][0] = "freezemedal_id";
$arrTableColumns['freezemedals_members'][1] = "medal_id";
$arrTableColumns['freezemedals_members'][2] = "member_id";
$arrTableColumns['freezemedals_members'][3] = "freezetime";

$arrTableColumns['gamesplayed'][0] = "gamesplayed_id";
$arrTableColumns['gamesplayed'][1] = "name";
$arrTableColumns['gamesplayed'][2] = "imageurl";
$arrTableColumns['gamesplayed'][3] = "imagewidth";
$arrTableColumns['gamesplayed'][4] = "imageheight";
$arrTableColumns['gamesplayed'][5] = "ordernum";

$arrTableColumns['gamesplayed_members'][0] = "gamemember_id";
$arrTableColumns['gamesplayed_members'][1] = "gamesplayed_id";
$arrTableColumns['gamesplayed_members'][2] = "member_id";

$arrTableColumns['gamestats'][0] = "gamestats_id";
$arrTableColumns['gamestats'][1] = "gamesplayed_id";
$arrTableColumns['gamestats'][2] = "name";
$arrTableColumns['gamestats'][3] = "stattype";
$arrTableColumns['gamestats'][4] = "calcop";
$arrTableColumns['gamestats'][5] = "firststat_id";
$arrTableColumns['gamestats'][6] = "secondstat_id";
$arrTableColumns['gamestats'][7] = "decimalspots";
$arrTableColumns['gamestats'][8] = "ordernum";
$arrTableColumns['gamestats'][9] = "hidestat";
$arrTableColumns['gamestats'][10] = "textinput";

$arrTableColumns['gamestats_members'][0] = "gamestatmember_id";
$arrTableColumns['gamestats_members'][1] = "gamestats_id";
$arrTableColumns['gamestats_members'][2] = "member_id";
$arrTableColumns['gamestats_members'][3] = "statvalue";
$arrTableColumns['gamestats_members'][4] = "stattext";
$arrTableColumns['gamestats_members'][5] = "dateupdated";

$arrTableColumns['hitcounter'][0] = "hit_id";
$arrTableColumns['hitcounter'][1] = "ipaddress";
$arrTableColumns['hitcounter'][2] = "dateposted";
$arrTableColumns['hitcounter'][3] = "pagename";
$arrTableColumns['hitcounter'][4] = "totalhits";

$arrTableColumns['iarequest'][0] = "iarequest_id";
$arrTableColumns['iarequest'][1] = "member_id";
$arrTableColumns['iarequest'][2] = "requestdate";
$arrTableColumns['iarequest'][3] = "reason";
$arrTableColumns['iarequest'][4] = "requeststatus";
$arrTableColumns['iarequest'][5] = "reviewer_id";
$arrTableColumns['iarequest'][6] = "reviewdate";

$arrTableColumns['iarequest_messages'][0] = "iamessage_id";
$arrTableColumns['iarequest_messages'][1] = "iarequest_id";
$arrTableColumns['iarequest_messages'][2] = "member_id";
$arrTableColumns['iarequest_messages'][3] = "messagedate";
$arrTableColumns['iarequest_messages'][4] = "message";

$arrTableColumns['imageslider'][0] = "imageslider_id";
$arrTableColumns['imageslider'][1] = "name";
$arrTableColumns['imageslider'][2] = "messagetitle";
$arrTableColumns['imageslider'][3] = "message";
$arrTableColumns['imageslider'][4] = "imageurl";
$arrTableColumns['imageslider'][5] = "fillstretch";
$arrTableColumns['imageslider'][6] = "ordernum";
$arrTableColumns['imageslider'][7] = "link";
$arrTableColumns['imageslider'][8] = "linktarget";
$arrTableColumns['imageslider'][9] = "membersonly";

$arrTableColumns['ipban'][0] = "ipban_id";
$arrTableColumns['ipban'][1] = "ipaddress";
$arrTableColumns['ipban'][2] = "exptime";
$arrTableColumns['ipban'][3] = "dateadded";

$arrTableColumns['logs'][0] = "log_id";
$arrTableColumns['logs'][1] = "member_id";
$arrTableColumns['logs'][2] = "logdate";
$arrTableColumns['logs'][3] = "ipaddress";
$arrTableColumns['logs'][4] = "message";

$arrTableColumns['medals'][0] = "medal_id";
$arrTableColumns['medals'][1] = "name";
$arrTableColumns['medals'][2] = "description";
$arrTableColumns['medals'][3] = "imageurl";
$arrTableColumns['medals'][4] = "imagewidth";
$arrTableColumns['medals'][5] = "imageheight";
$arrTableColumns['medals'][6] = "autodays";
$arrTableColumns['medals'][7] = "autorecruits";
$arrTableColumns['medals'][8] = "ordernum";

$arrTableColumns['medals_members'][0] = "medalmember_id";
$arrTableColumns['medals_members'][1] = "medal_id";
$arrTableColumns['medals_members'][2] = "member_id";
$arrTableColumns['medals_members'][3] = "dateawarded";
$arrTableColumns['medals_members'][4] = "reason";

$arrTableColumns['memberapps'][0] = "memberapp_id";
$arrTableColumns['memberapps'][1] = "username";
$arrTableColumns['memberapps'][2] = "password";
$arrTableColumns['memberapps'][3] = "password2";
$arrTableColumns['memberapps'][4] = "email";
$arrTableColumns['memberapps'][5] = "applydate";
$arrTableColumns['memberapps'][6] = "ipaddress";
$arrTableColumns['memberapps'][7] = "memberadded";
$arrTableColumns['memberapps'][8] = "seenstatus";

$arrTableColumns['members'][0] = "member_id";
$arrTableColumns['members'][1] = "username";
$arrTableColumns['members'][2] = "password";
$arrTableColumns['members'][3] = "password2";
$arrTableColumns['members'][4] = "rank_id";
$arrTableColumns['members'][5] = "profilepic";
$arrTableColumns['members'][6] = "avatar";
$arrTableColumns['members'][7] = "email";
$arrTableColumns['members'][8] = "maingame_id";
$arrTableColumns['members'][9] = "birthday";
$arrTableColumns['members'][10] = "datejoined";
$arrTableColumns['members'][11] = "lastlogin";
$arrTableColumns['members'][12] = "lastseen";
$arrTableColumns['members'][13] = "lastseenlink";
$arrTableColumns['members'][14] = "loggedin";
$arrTableColumns['members'][15] = "lastpromotion";
$arrTableColumns['members'][16] = "lastdemotion";
$arrTableColumns['members'][17] = "timesloggedin";
$arrTableColumns['members'][18] = "recruiter";
$arrTableColumns['members'][19] = "ipaddress";
$arrTableColumns['members'][20] = "profileviews";
$arrTableColumns['members'][21] = "defaultconsole";
$arrTableColumns['members'][22] = "disabled";
$arrTableColumns['members'][23] = "disableddate";
$arrTableColumns['members'][24] = "notifications";
$arrTableColumns['members'][25] = "topicsperpage";
$arrTableColumns['members'][26] = "postsperpage";
$arrTableColumns['members'][27] = "freezerank";
$arrTableColumns['members'][28] = "forumsignature";
$arrTableColumns['members'][29] = "promotepower";
$arrTableColumns['members'][30] = "onia";
$arrTableColumns['members'][31] = "inactivedate";

$arrTableColumns['membersonlypage'][0] = "page_id";
$arrTableColumns['membersonlypage'][1] = "pagename";
$arrTableColumns['membersonlypage'][2] = "pageurl";
$arrTableColumns['membersonlypage'][3] = "dateadded";

$arrTableColumns['menu_category'][0] = "menucategory_id";
$arrTableColumns['menu_category'][1] = "section";
$arrTableColumns['menu_category'][2] = "name";
$arrTableColumns['menu_category'][3] = "sortnum";
$arrTableColumns['menu_category'][4] = "headertype";
$arrTableColumns['menu_category'][5] = "headercode";
$arrTableColumns['menu_category'][6] = "accesstype";
$arrTableColumns['menu_category'][7] = "hide";

$arrTableColumns['menu_item'][0] = "menuitem_id";
$arrTableColumns['menu_item'][1] = "menucategory_id";
$arrTableColumns['menu_item'][2] = "name";
$arrTableColumns['menu_item'][3] = "itemtype";
$arrTableColumns['menu_item'][4] = "itemtype_id";
$arrTableColumns['menu_item'][5] = "accesstype";
$arrTableColumns['menu_item'][6] = "hide";
$arrTableColumns['menu_item'][7] = "sortnum";

$arrTableColumns['menuitem_customblock'][0] = "menucustomblock_id";
$arrTableColumns['menuitem_customblock'][1] = "menuitem_id";
$arrTableColumns['menuitem_customblock'][2] = "blocktype";
$arrTableColumns['menuitem_customblock'][3] = "code";

$arrTableColumns['menuitem_custompage'][0] = "menucustompage_id";
$arrTableColumns['menuitem_custompage'][1] = "menuitem_id";
$arrTableColumns['menuitem_custompage'][2] = "custompage_id";
$arrTableColumns['menuitem_custompage'][3] = "prefix";
$arrTableColumns['menuitem_custompage'][4] = "linktarget";
$arrTableColumns['menuitem_custompage'][5] = "textalign";

$arrTableColumns['menuitem_image'][0] = "menuimage_id";
$arrTableColumns['menuitem_image'][1] = "menuitem_id";
$arrTableColumns['menuitem_image'][2] = "imageurl";
$arrTableColumns['menuitem_image'][3] = "width";
$arrTableColumns['menuitem_image'][4] = "height";
$arrTableColumns['menuitem_image'][5] = "link";
$arrTableColumns['menuitem_image'][6] = "linktarget";
$arrTableColumns['menuitem_image'][7] = "imagealign";

$arrTableColumns['menuitem_link'][0] = "menulink_id";
$arrTableColumns['menuitem_link'][1] = "menuitem_id";
$arrTableColumns['menuitem_link'][2] = "link";
$arrTableColumns['menuitem_link'][3] = "linktarget";
$arrTableColumns['menuitem_link'][4] = "prefix";
$arrTableColumns['menuitem_link'][5] = "textalign";

$arrTableColumns['menuitem_shoutbox'][0] = "menushoutbox_id";
$arrTableColumns['menuitem_shoutbox'][1] = "menuitem_id";
$arrTableColumns['menuitem_shoutbox'][2] = "width";
$arrTableColumns['menuitem_shoutbox'][3] = "height";
$arrTableColumns['menuitem_shoutbox'][4] = "percentwidth";
$arrTableColumns['menuitem_shoutbox'][5] = "percentheight";
$arrTableColumns['menuitem_shoutbox'][6] = "textboxwidth";

$arrTableColumns['news'][0] = "news_id";
$arrTableColumns['news'][1] = "member_id";
$arrTableColumns['news'][2] = "newstype";
$arrTableColumns['news'][3] = "dateposted";
$arrTableColumns['news'][4] = "postsubject";
$arrTableColumns['news'][5] = "newspost";
$arrTableColumns['news'][6] = "lasteditmember_id";
$arrTableColumns['news'][7] = "lasteditdate";
$arrTableColumns['news'][8] = "hpsticky";

$arrTableColumns['notifications'][0] = "notification_id";
$arrTableColumns['notifications'][1] = "member_id";
$arrTableColumns['notifications'][2] = "datesent";
$arrTableColumns['notifications'][3] = "message";
$arrTableColumns['notifications'][4] = "status";
$arrTableColumns['notifications'][5] = "icontype";

$arrTableColumns['plugin_config'][0] = "pluginconfig_id";
$arrTableColumns['plugin_config'][1] = "plugin_id";
$arrTableColumns['plugin_config'][2] = "name";
$arrTableColumns['plugin_config'][3] = "value";

$arrTableColumns['plugin_pages'][0] = "pluginpage_id";
$arrTableColumns['plugin_pages'][1] = "plugin_id";
$arrTableColumns['plugin_pages'][2] = "page";
$arrTableColumns['plugin_pages'][3] = "pagepath";
$arrTableColumns['plugin_pages'][4] = "sortnum";

$arrTableColumns['plugins'][0] = "plugin_id";
$arrTableColumns['plugins'][1] = "name";
$arrTableColumns['plugins'][2] = "filepath";
$arrTableColumns['plugins'][3] = "apikey";
$arrTableColumns['plugins'][4] = "dateinstalled";

$arrTableColumns['poll_memberaccess'][0] = "pollmemberaccess_id";
$arrTableColumns['poll_memberaccess'][1] = "poll_id";
$arrTableColumns['poll_memberaccess'][2] = "member_id";
$arrTableColumns['poll_memberaccess'][3] = "accesstype";

$arrTableColumns['poll_options'][0] = "polloption_id";
$arrTableColumns['poll_options'][1] = "poll_id";
$arrTableColumns['poll_options'][2] = "optionvalue";
$arrTableColumns['poll_options'][3] = "color";
$arrTableColumns['poll_options'][4] = "sortnum";

$arrTableColumns['poll_rankaccess'][0] = "pollrankaccess_id";
$arrTableColumns['poll_rankaccess'][1] = "poll_id";
$arrTableColumns['poll_rankaccess'][2] = "rank_id";
$arrTableColumns['poll_rankaccess'][3] = "accesstype";

$arrTableColumns['poll_votes'][0] = "pollvote_id";
$arrTableColumns['poll_votes'][1] = "poll_id";
$arrTableColumns['poll_votes'][2] = "polloption_id";
$arrTableColumns['poll_votes'][3] = "member_id";
$arrTableColumns['poll_votes'][4] = "ipaddress";
$arrTableColumns['poll_votes'][5] = "datevoted";
$arrTableColumns['poll_votes'][6] = "votecount";

$arrTableColumns['polls'][0] = "poll_id";
$arrTableColumns['polls'][1] = "member_id";
$arrTableColumns['polls'][2] = "question";
$arrTableColumns['polls'][3] = "accesstype";
$arrTableColumns['polls'][4] = "multivote";
$arrTableColumns['polls'][5] = "displayvoters";
$arrTableColumns['polls'][6] = "resultvisibility";
$arrTableColumns['polls'][7] = "maxvotes";
$arrTableColumns['polls'][8] = "pollend";
$arrTableColumns['polls'][9] = "dateposted";
$arrTableColumns['polls'][10] = "lastedit_date";
$arrTableColumns['polls'][11] = "lastedit_memberid";

$arrTableColumns['privatemessage_folders'][0] = "pmfolder_id";
$arrTableColumns['privatemessage_folders'][1] = "member_id";
$arrTableColumns['privatemessage_folders'][2] = "name";
$arrTableColumns['privatemessage_folders'][3] = "ordernum";
$arrTableColumns['privatemessage_folders'][4] = "sortnum";

$arrTableColumns['privatemessage_members'][0] = "pmmember_id";
$arrTableColumns['privatemessage_members'][1] = "pm_id";
$arrTableColumns['privatemessage_members'][2] = "member_id";
$arrTableColumns['privatemessage_members'][3] = "grouptype";
$arrTableColumns['privatemessage_members'][4] = "group_id";
$arrTableColumns['privatemessage_members'][5] = "seenstatus";
$arrTableColumns['privatemessage_members'][6] = "deletestatus";
$arrTableColumns['privatemessage_members'][7] = "pmfolder_id";

$arrTableColumns['privatemessages'][0] = "pm_id";
$arrTableColumns['privatemessages'][1] = "sender_id";
$arrTableColumns['privatemessages'][2] = "receiver_id";
$arrTableColumns['privatemessages'][3] = "datesent";
$arrTableColumns['privatemessages'][4] = "subject";
$arrTableColumns['privatemessages'][5] = "message";
$arrTableColumns['privatemessages'][6] = "status";
$arrTableColumns['privatemessages'][7] = "originalpm_id";
$arrTableColumns['privatemessages'][8] = "deletesender";
$arrTableColumns['privatemessages'][9] = "deletereceiver";
$arrTableColumns['privatemessages'][10] = "senderfolder_id";
$arrTableColumns['privatemessages'][11] = "receiverfolder_id";

$arrTableColumns['profilecategory'][0] = "profilecategory_id";
$arrTableColumns['profilecategory'][1] = "name";
$arrTableColumns['profilecategory'][2] = "ordernum";

$arrTableColumns['profileoptions'][0] = "profileoption_id";
$arrTableColumns['profileoptions'][1] = "profilecategory_id";
$arrTableColumns['profileoptions'][2] = "name";
$arrTableColumns['profileoptions'][3] = "optiontype";
$arrTableColumns['profileoptions'][4] = "sortnum";

$arrTableColumns['profileoptions_select'][0] = "selectopt_id";
$arrTableColumns['profileoptions_select'][1] = "profileoption_id";
$arrTableColumns['profileoptions_select'][2] = "selectvalue";
$arrTableColumns['profileoptions_select'][3] = "sortnum";

$arrTableColumns['profileoptions_values'][0] = "values_id";
$arrTableColumns['profileoptions_values'][1] = "profileoption_id";
$arrTableColumns['profileoptions_values'][2] = "member_id";
$arrTableColumns['profileoptions_values'][3] = "inputvalue";

$arrTableColumns['rank_privileges'][0] = "privilege_id";
$arrTableColumns['rank_privileges'][1] = "rank_id";
$arrTableColumns['rank_privileges'][2] = "console_id";

$arrTableColumns['rankcategory'][0] = "rankcategory_id";
$arrTableColumns['rankcategory'][1] = "name";
$arrTableColumns['rankcategory'][2] = "imageurl";
$arrTableColumns['rankcategory'][3] = "ordernum";
$arrTableColumns['rankcategory'][4] = "hidecat";
$arrTableColumns['rankcategory'][5] = "useimage";
$arrTableColumns['rankcategory'][6] = "description";
$arrTableColumns['rankcategory'][7] = "imagewidth";
$arrTableColumns['rankcategory'][8] = "imageheight";
$arrTableColumns['rankcategory'][9] = "color";

$arrTableColumns['ranks'][0] = "rank_id";
$arrTableColumns['ranks'][1] = "rankcategory_id";
$arrTableColumns['ranks'][2] = "name";
$arrTableColumns['ranks'][3] = "description";
$arrTableColumns['ranks'][4] = "imageurl";
$arrTableColumns['ranks'][5] = "imagewidth";
$arrTableColumns['ranks'][6] = "imageheight";
$arrTableColumns['ranks'][7] = "ordernum";
$arrTableColumns['ranks'][8] = "autodays";
$arrTableColumns['ranks'][9] = "hiderank";
$arrTableColumns['ranks'][10] = "promotepower";
$arrTableColumns['ranks'][11] = "autodisable";
$arrTableColumns['ranks'][12] = "color";

$arrTableColumns['social'][0] = "social_id";
$arrTableColumns['social'][1] = "name";
$arrTableColumns['social'][2] = "icon";
$arrTableColumns['social'][3] = "iconwidth";
$arrTableColumns['social'][4] = "iconheight";
$arrTableColumns['social'][5] = "url";
$arrTableColumns['social'][6] = "tooltip";
$arrTableColumns['social'][7] = "ordernum";

$arrTableColumns['social_members'][0] = "socialmember_id";
$arrTableColumns['social_members'][1] = "social_id";
$arrTableColumns['social_members'][2] = "member_id";
$arrTableColumns['social_members'][3] = "value";

$arrTableColumns['squadapps'][0] = "squadapp_id";
$arrTableColumns['squadapps'][1] = "member_id";
$arrTableColumns['squadapps'][2] = "squad_id";
$arrTableColumns['squadapps'][3] = "message";
$arrTableColumns['squadapps'][4] = "applydate";
$arrTableColumns['squadapps'][5] = "dateaction";
$arrTableColumns['squadapps'][6] = "status";
$arrTableColumns['squadapps'][7] = "squadmember_id";

$arrTableColumns['squadinvites'][0] = "squadinvite_id";
$arrTableColumns['squadinvites'][1] = "squad_id";
$arrTableColumns['squadinvites'][2] = "sender_id";
$arrTableColumns['squadinvites'][3] = "receiver_id";
$arrTableColumns['squadinvites'][4] = "datesent";
$arrTableColumns['squadinvites'][5] = "dateaction";
$arrTableColumns['squadinvites'][6] = "status";
$arrTableColumns['squadinvites'][7] = "message";
$arrTableColumns['squadinvites'][8] = "startingrank_id";

$arrTableColumns['squadnews'][0] = "squadnews_id";
$arrTableColumns['squadnews'][1] = "squad_id";
$arrTableColumns['squadnews'][2] = "member_id";
$arrTableColumns['squadnews'][3] = "newstype";
$arrTableColumns['squadnews'][4] = "dateposted";
$arrTableColumns['squadnews'][5] = "postsubject";
$arrTableColumns['squadnews'][6] = "newspost";
$arrTableColumns['squadnews'][7] = "lasteditmember_id";
$arrTableColumns['squadnews'][8] = "lasteditdate";

$arrTableColumns['squadranks'][0] = "squadrank_id";
$arrTableColumns['squadranks'][1] = "squad_id";
$arrTableColumns['squadranks'][2] = "name";
$arrTableColumns['squadranks'][3] = "sortnum";
$arrTableColumns['squadranks'][4] = "postnews";
$arrTableColumns['squadranks'][5] = "managenews";
$arrTableColumns['squadranks'][6] = "postshoutbox";
$arrTableColumns['squadranks'][7] = "manageshoutbox";
$arrTableColumns['squadranks'][8] = "addrank";
$arrTableColumns['squadranks'][9] = "manageranks";
$arrTableColumns['squadranks'][10] = "editprofile";
$arrTableColumns['squadranks'][11] = "sendinvites";
$arrTableColumns['squadranks'][12] = "acceptapps";
$arrTableColumns['squadranks'][13] = "setrank";
$arrTableColumns['squadranks'][14] = "removemember";

$arrTableColumns['squads'][0] = "squad_id";
$arrTableColumns['squads'][1] = "member_id";
$arrTableColumns['squads'][2] = "name";
$arrTableColumns['squads'][3] = "description";
$arrTableColumns['squads'][4] = "logourl";
$arrTableColumns['squads'][5] = "recruitingstatus";
$arrTableColumns['squads'][6] = "datecreated";
$arrTableColumns['squads'][7] = "privateshoutbox";
$arrTableColumns['squads'][8] = "website";

$arrTableColumns['squads_members'][0] = "squadmember_id";
$arrTableColumns['squads_members'][1] = "squad_id";
$arrTableColumns['squads_members'][2] = "member_id";
$arrTableColumns['squads_members'][3] = "squadrank_id";
$arrTableColumns['squads_members'][4] = "datejoined";
$arrTableColumns['squads_members'][5] = "lastpromotion";
$arrTableColumns['squads_members'][6] = "lastdemotion";

$arrTableColumns['tableupdates'][0] = "tableupdate_id";
$arrTableColumns['tableupdates'][1] = "tablename";
$arrTableColumns['tableupdates'][2] = "updatetime";

$arrTableColumns['tournament_managers'][0] = "tournamentmanager_id";
$arrTableColumns['tournament_managers'][1] = "tournament_id";
$arrTableColumns['tournament_managers'][2] = "member_id";

$arrTableColumns['tournamentmatch'][0] = "tournamentmatch_id";
$arrTableColumns['tournamentmatch'][1] = "tournament_id";
$arrTableColumns['tournamentmatch'][2] = "round";
$arrTableColumns['tournamentmatch'][3] = "team1_id";
$arrTableColumns['tournamentmatch'][4] = "team2_id";
$arrTableColumns['tournamentmatch'][5] = "team1score";
$arrTableColumns['tournamentmatch'][6] = "team2score";
$arrTableColumns['tournamentmatch'][7] = "outcome";
$arrTableColumns['tournamentmatch'][8] = "replayteam1url";
$arrTableColumns['tournamentmatch'][9] = "replayteam2url";
$arrTableColumns['tournamentmatch'][10] = "adminreplayurl";
$arrTableColumns['tournamentmatch'][11] = "team1approve";
$arrTableColumns['tournamentmatch'][12] = "team2approve";
$arrTableColumns['tournamentmatch'][13] = "nextmatch_id";
$arrTableColumns['tournamentmatch'][14] = "sortnum";

$arrTableColumns['tournamentplayers'][0] = "tournamentplayer_id";
$arrTableColumns['tournamentplayers'][1] = "tournament_id";
$arrTableColumns['tournamentplayers'][2] = "team_id";
$arrTableColumns['tournamentplayers'][3] = "member_id";
$arrTableColumns['tournamentplayers'][4] = "displayname";

$arrTableColumns['tournamentpools'][0] = "tournamentpool_id";
$arrTableColumns['tournamentpools'][1] = "tournament_id";
$arrTableColumns['tournamentpools'][2] = "finished";

$arrTableColumns['tournamentpools_teams'][0] = "poolteam_id";
$arrTableColumns['tournamentpools_teams'][1] = "tournament_id";
$arrTableColumns['tournamentpools_teams'][2] = "pool_id";
$arrTableColumns['tournamentpools_teams'][3] = "team1_id";
$arrTableColumns['tournamentpools_teams'][4] = "team2_id";
$arrTableColumns['tournamentpools_teams'][5] = "team1score";
$arrTableColumns['tournamentpools_teams'][6] = "team2score";
$arrTableColumns['tournamentpools_teams'][7] = "team1approve";
$arrTableColumns['tournamentpools_teams'][8] = "team2approve";
$arrTableColumns['tournamentpools_teams'][9] = "replayteam1url";
$arrTableColumns['tournamentpools_teams'][10] = "replayteam2url";
$arrTableColumns['tournamentpools_teams'][11] = "winner";

$arrTableColumns['tournaments'][0] = "tournament_id";
$arrTableColumns['tournaments'][1] = "member_id";
$arrTableColumns['tournaments'][2] = "gamesplayed_id";
$arrTableColumns['tournaments'][3] = "name";
$arrTableColumns['tournaments'][4] = "seedtype";
$arrTableColumns['tournaments'][5] = "startdate";
$arrTableColumns['tournaments'][6] = "timezone";
$arrTableColumns['tournaments'][7] = "eliminations";
$arrTableColumns['tournaments'][8] = "playersperteam";
$arrTableColumns['tournaments'][9] = "maxteams";
$arrTableColumns['tournaments'][10] = "status";
$arrTableColumns['tournaments'][11] = "description";
$arrTableColumns['tournaments'][12] = "password";
$arrTableColumns['tournaments'][13] = "requirereplay";
$arrTableColumns['tournaments'][14] = "access";

$arrTableColumns['tournamentteams'][0] = "tournamentteam_id";
$arrTableColumns['tournamentteams'][1] = "tournament_id";
$arrTableColumns['tournamentteams'][2] = "name";
$arrTableColumns['tournamentteams'][3] = "seed";

$arrTableColumns['websiteinfo'][0] = "websiteinfo_id";
$arrTableColumns['websiteinfo'][1] = "name";
$arrTableColumns['websiteinfo'][2] = "value";

?>