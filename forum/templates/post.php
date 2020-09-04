<?php

	if(!defined("SHOW_FORUMPOST")) {
		exit();	
	}

	$posterMemberObj = new Member($mysqli);
	$posterRankObj = new Rank($mysqli);
	$downloadCatObj = new DownloadCategory($mysqli);
	$attachmentObj = new Download($mysqli);
	$consoleObj = new ConsoleOption($mysqli);
	$topicObj = new Basic($mysqli, "forum_topic", "forumtopic_id");
	
	$intManagePostsCID = $consoleObj->findConsoleIDByName("Manage Forum Posts");
	$intPostTopicCID = $consoleObj->findConsoleIDByName("Post Topic");
	$downloadCatObj->selectBySpecialKey("forumattachments");
	
	
	$blnShowAttachments = false;
	if((LOGGED_IN == true && $downloadCatObj->get_info("accesstype") == 1) || $downloadCatObj->get_info("accesstype") == 0) {
		$blnShowAttachments = true;
	}
	
	
	$postInfo = $this->get_info_filtered();
	$topicInfo = $this->getTopicInfo();
	$topicObj->select($postInfo['forumtopic_id']);
	$topicInfo['forumboard_id'] = $topicObj->get_info("forumboard_id");
	
	$posterMemberObj->select($postInfo['member_id']);
	$postMemberInfo = $posterMemberObj->get_info_filtered();
	$postMessage = $this->arrObjInfo['message'];
	
	$postMessage = str_replace("<?", "&lt;?", $postMessage);
	$postMessage = str_replace("?>", "?&gt;", $postMessage);
	$postMessage = str_replace("<script", "&lt;script", $postMessage);
	$postMessage = str_replace("</script>", "&lt;/script&gt;", $postMessage);
	
	$dispPostedOn = "";
	if((time()-$postInfo['dateposted']) > (60*60*24)) {
		$dispPostedOn = " on";
	}
	
	
	$posterRankObj->select($postMemberInfo['rank_id']);
	$posterRankInfo = $posterRankObj->get_info_filtered();
	
	$dispLastEdit = "";
	if($postInfo['lastedit_date'] != 0) {
		$posterMemberObj->select($postInfo['lastedit_member_id']);
		$dispLastEdit = "<br><br><span class='tinyFont' style='font-style: italic'>Last edited by ".$posterMemberObj->getMemberLink()." - ".getPreciseTime($postInfo['lastedit_date'])."</span>";	
		$posterMemberObj->select($postInfo['member_id']);
	}
	
	
	$dispRankWidth = ($websiteInfo['forum_rankwidth'] <= 0) ? "" : "width: ".$websiteInfo['forum_rankwidth'].$websiteInfo['forum_rankwidthunit'].";";
	$dispRankHeight = ($websiteInfo['forum_rankheight'] <= 0) ? "" : "height: ".$websiteInfo['forum_rankheight'].$websiteInfo['forum_rankheightunit'].";";
	$dispRankDimensions = ($dispRankWidth != "" || $dispRankHeight != "") ? " style='".$dispRankWidth.$dispRankHeight."'" : "";
	$dispRankIMG = ($websiteInfo['forum_showrank'] == 1 && $posterRankInfo['rank_id'] != 1) ? "<div id='forumShowRank' style='text-align: center'><img src='".$posterRankInfo['imageurl']."'".$dispRankDimensions."></div>" : "";
	$dispMedals = "";
	if($websiteInfo['forum_showmedal'] == 1) {
		
		$medalObj = new Medal($mysqli);
		$medalCount = ($websiteInfo['forum_medalcount'] == 0) ? 5 : $websiteInfo['forum_medalcount'];
		
		$arrMedals = $posterMemberObj->getMedalList(false, $websiteInfo['medalorder']);
		
		$dispMedalWidth = ($websiteInfo['forum_medalwidth'] <= 0) ? "" : "width: ".$websiteInfo['forum_medalwidth'].$websiteInfo['forum_medalwidthunit'].";";
		$dispMedalHeight = ($websiteInfo['forum_medalheight'] <= 0) ? "" : "height: ".$websiteInfo['forum_medalheight'].$websiteInfo['forum_medalheightunit'].";";
		$dispMedalDimensions = ($dispMedalWidth != "" || $dispMedalHeight != "") ?  " style='".$dispMedalWidth.$dispMedalHeight."'" : "";
		
		$i = 1;
		foreach($arrMedals as $medalID) {
			$medalObj->select($medalID);
			$medalInfo = $medalObj->get_info_filtered();
			$resultMedal = $mysqli->query("SELECT * FROM ".$dbprefix."medals_members WHERE member_id = '".$postMemberInfo['member_id']."' AND medal_id = '".$medalInfo['medal_id']."'");
			$rowMedal = $resultMedal->fetch_assoc();
			
			$dispDateAwarded = "<b>Date Awarded:</b><br>".getPreciseTime($rowMedal['dateawarded']);
			
			$dispReason = "";
			if($rowMedal['reason'] != "") {
				$dispReason = "<br><br><b>Awarded for:</b><br>".filterText($rowMedal['reason']);
			}
			
			$dispMedalMessage = "<b>".$medalInfo['name']."</b><br><br>".$dispDateAwarded.$dispReason;
			
			
			$dispMedals .= "<div style='text-align: center; margin: 5px 0px'><img src='".$medalInfo['imageurl']."'".$dispMedalDimensions." onmouseover=\"showToolTip('".$dispMedalMessage."')\" onmouseout='hideToolTip()'></div>";
			
			$i++;
			if($i > $medalCount) { break; }
		}
		
		
	}
	
	$setAvatarWidth = ($websiteInfo['forum_avatarwidth'] > 0) ? $websiteInfo['forum_avatarwidth'] : "50";
	$setAvatarWidthUnit = ($websiteInfo['forum_avatarwidthunit'] == "%") ? "%" : "px";
	
	$setAvatarHeight = ($websiteInfo['forum_avatarheight'] > 0) ? $websiteInfo['forum_avatarheight'] : "50";
	$setAvatarHeightUnit = ($websiteInfo['forum_avatarheightunit'] == "%") ? "%" : "px";
	
	$dispForumPostText = ($websiteInfo['forum_linkimages'] == 1) ? autoLinkImage(parseBBCode($postMessage)) : parseBBCode($postMessage);

	
	echo "<div class='forumPostContainer'>
			<div class='forumPostPosterInfo main'><a name='".$postInfo['forumpost_id']."'></a>
				<span class='boardPosterName'>".$posterMemberObj->getMemberLink()."</span><br>
				".$posterRankInfo['name']."
				<div id='forumShowAvatar'>".$posterMemberObj->getAvatar($setAvatarWidth.$setAvatarWidthUnit, $setAvatarHeight.$setAvatarHeightUnit)."</div>
				<div id='forumShowPostCount'>Posts: ".$posterMemberObj->countForumPosts()."</div>
				".$dispRankIMG."
				<div id='forumShowMedals'>".$dispMedals."</div>
			</div>
			<div class='forumPostMessageInfo main'>
				<div class='dottedLine tinyFont'>Posted".$dispPostedOn." ".getPreciseTime($postInfo['dateposted'])."</div><br>
				
			
				".$dispForumPostText.$dispLastEdit."
				
			
			</div>
			<div class='forumPostNewSection'></div>
			<div class='forumPostPosterInfo'></div>
			<div class='forumPostMessageExtras'>
				";
				
		$arrAttachments = $this->getPostAttachments();
		
		if(count($arrAttachments) > 0 && $blnShowAttachments) {
			echo "
				<div class='forumAttachmentsContainer'>
					<b>Attachments:</b><br>
					";
				
				foreach($arrAttachments as $downloadID) {
					$attachmentObj->select($downloadID);
					$attachmentInfo = $attachmentObj->get_info_filtered();
					$addS = ($attachmentInfo['downloadcount'] != 1) ? "s" : "";
					$dispFileSize = $attachmentInfo['filesize']/1024;
					
					if($dispFileSize < 1) {
						$dispFileSize = $attachmentInfo['filesize']."B";	
					}
					elseif(($dispFileSize/1024) < 1) {
						$dispFileSize = round($dispFileSize, 2)."KB";	
					}
					else {
						$dispFileSize = round(($dispFileSize/1024),2)."MB";
					}
					
					echo "<a href='".$MAIN_ROOT."downloads/file.php?dID=".$downloadID."'>".$attachmentInfo['filename']."</a> - downloaded ".$attachmentInfo['downloadcount']." time".$addS." - ".$dispFileSize."<br>";
					
				}
		
				echo "
					</div>
					";
		}
		
		
		if($postMemberInfo['forumsignature'] != "" && $websiteInfo['forum_hidesignatures'] == 0) {
			echo "
				<div class='forumSignatureContainer'>".parseBBCode($posterMemberObj->get_info("forumsignature"))."</div>
			";
		}
		
		echo "<div class='forumManageLinks'>";
		if($this->blnManageable || $postMemberInfo['member_id'] == $memberInfo['member_id']) {

			echo "&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intManagePostsCID."&pID=".$postInfo['forumpost_id']."'>EDIT POST</a> &laquo;&nbsp&nbsp;&nbsp;";
			echo "&raquo; <a href='javascript:void(0)' onclick=\"deletePost('".$postInfo['forumpost_id']."')\">DELETE POST</a> &laquo;&nbsp&nbsp;&nbsp;";
			$countManagablePosts++;
			
		}
		
		if(LOGGED_IN && $topicInfo['lockstatus'] == 0) { 
			echo "&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intPostTopicCID."&bID=".$topicInfo['forumboard_id']."&tID=".$topicInfo['forumtopic_id']."&quote=".$postInfo['forumpost_id']."'>QUOTE</a> &laquo;"; 
		}
		
	
		echo "
			</div>
			</div>
		</div>";

?>