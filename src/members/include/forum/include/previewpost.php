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


include("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/forumboard.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$rankObj = new Rank($mysqli);

$boardObj = new ForumBoard($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Post Topic");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info_filtered();
	$rankObj->select($memberInfo['rank_id']);
	$posterRankInfo = $rankObj->get_info_filtered();
	
	$_POST['wysiwygHTML'] = str_replace("<?", "&lt;?", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("?>", "?&gt;", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("<script", "&lt;script", $_POST['wysiwygHTML']);
	$_POST['wysiwygHTML'] = str_replace("</script>", "&lt;/script&gt;", $_POST['wysiwygHTML']);
	
	if($memberInfo['avatar'] == "") {
		$memberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
	}
	else {
		$memberInfo['avatar'] = $MAIN_ROOT.$memberInfo['avatar'];
	}
	
	$dispSetAvatarWidth = "";
	$dispSetAvatarHeight = "";
	if($websiteInfo['forum_avatarwidth'] > 0) {
		$dispSetAvatarWidth = " width: ".$websiteInfo['forum_avatarwidth'].$websiteInfo['forum_avatarwidthunit'].";";
	}
	
	if($websiteInfo['forum_avatarheight'] > 0) {
		$dispSetAvatarHeight = " height: ".$websiteInfo['forum_avatarheight'].$websiteInfo['forum_avatarheightunit'].";";
	}
	
	echo "
		<div class='breadCrumbTitle'>Preview - ".filterText($_POST['previewSubject'])."</div>
		<table class='forumTable'>
			<tr>
				<td class='boardPosterInfo' valign='top'>
					<span class='boardPosterName'>".$member->getMemberLink()."</span><br>
					".$posterRankInfo['name']."<br>
					<img src='".$memberInfo['avatar']."' style='margin-top: 5px; margin-bottom: 5px;".$dispSetAvatarWidth.$dispSetAvatarHeight."'><br>
					Posts: ".$member->countForumPosts()."
				</td>
				<td class='boardPostInfo' valign='top'>
				<div class='postTime'>Posted ".getPreciseTime(time())."</div>
				
				".parseBBCode($_POST['wysiwygHTML'])."
				
				</td>
			</tr>
			<tr>
				<td class='boardPosterInfoFooter'></td>
				<td class='boardPostInfoFooter'></td>
			</tr>
		</table>
	";
	
	
}


?>