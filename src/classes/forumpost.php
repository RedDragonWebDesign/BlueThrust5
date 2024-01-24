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


require_once("basic.php");


class ForumPost extends Basic {

	public $objTopic;
	public $blnManageable = false;

	public function __construct($sqlConnection) {

		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."forum_post";
		$this->strTableKey = "forumpost_id";

		$this->objTopic = new BasicOrder($sqlConnection, "forum_topic", "forumtopic_id");

		$this->objTopic->set_assocTableName("forum_post");
		$this->objTopic->set_assocTableKey("forumpost_id");

	}


	public function addNew($arrColumns, $arrValues) {

		$returnVal = false;
		$addNew = parent::addNew($arrColumns, $arrValues);

		if ($addNew) {
			$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."forum_topicseen WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."'");
			$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."forum_topicseen`");
			$returnVal = true;
		}

		return $returnVal;

	}

	public function select($intIDNum, $numericIDOnly = true) {

		$this->blnManageable = false;

		return parent::select($intIDNum, $numericIDOnly);
	}

	public function getPostAttachments() {

		$returnArr = array();

		if ($this->intTableKeyValue != "") {

			$result = $this->MySQL->query("SELECT download_id FROM ".$this->MySQL->get_tablePrefix()."forum_attachments WHERE forumpost_id = '".$this->intTableKeyValue."'");
			while ($row = $result->fetch_assoc()) {
				$returnArr[] = $row['download_id'];
			}

		}

		return $returnArr;

	}

	public function show($showReplyLink=false, $template="") {
		global $websiteInfo, $MAIN_ROOT, $dbprefix, $mysqli, $member;

		if ($template == "") {

			require(BASE_DIRECTORY."forum/templates/post.php");

		}
		else {
			require(BASE_DIRECTORY."forum/templates/".$template);
		}

	}

	public function getTopicInfo($filtered=false) {
		$returnArr = array();
		if ($this->intTableKeyValue != "") {

			$temp = $this->intTableKeyValue;
			$tempManage = $this->blnManageable;
			$this->objTopic->select($this->arrObjInfo['forumtopic_id']);
			$this->select($this->objTopic->get_info("forumpost_id"));

			$returnArr = $filtered ? $this->get_info_filtered() : $this->get_info();

			$returnArr['forumboard_id'] = $this->objTopic->get_info("forumboard_id");

			$this->select($temp);
			$this->blnManageable = $tempManage;

		}

		return $returnArr;
	}


	public function getLink($fullLink=false, $individualPost=false) {
		global $websiteInfo, $memberInfo, $setPostsPerPage;

		$returnVal = "";
		if ($this->intTableKeyValue != "" && !$individualPost) {

			// Figure out num of posts
			$query = "SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' ORDER BY dateposted";
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' ORDER BY dateposted");
			$totalPosts = $result->num_rows;

			// Find the post ranking within the result set
			$findDepthResult = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' AND dateposted <= '".$this->arrObjInfo['dateposted']."'");
			$postRanking = $findDepthResult->num_rows;

			// Figure out posts per page
			if ($setPostsPerPage > 0) {
				$postsPerPage = $setPostsPerPage;
			}
			elseif ($websiteInfo['forum_postsperpage'] > 0) {
				$postsPerPage = $websiteInfo['forum_postsperpage'];
			}
			else {
				$postsPerPage = 25;
			}

			// Total Pages
			$totalPages = ceil($totalPosts/$postsPerPage);

			$returnVal = FULL_SITE_URL."forum/viewtopic.php?tID=".$this->arrObjInfo['forumtopic_id'];

			if ($totalPages > 1) {
				$pageNumber = ceil($postRanking/$postsPerPage);
				$returnVal .= "&pID=".$pageNumber;
			}

			$returnVal .= "#".$this->intTableKeyValue;

			if ($fullLink) {
				$topicInfo = $this->getTopicInfo(true);
				$returnVal = "<a href='".$returnVal."'>".$topicInfo['title']."</a>";
			}

		}
		elseif ($this->intTableKeyValue != "" && $individualPost) {

			$returnVal = FULL_SITE_URL."forum/viewpost.php?post=".$this->intTableKeyValue;

			if ($fullLink) {
				$topicInfo = $this->getTopicInfo(true);
				$returnVal = "<a href='".$returnVal."'>".$topicInfo['title']."</a>";
			}
		}

		return $returnVal;
	}


	public function delete() {
		$returnVal = false;
		if ($this->intTableKeyValue != "") {
			$returnVal = parent::delete();
			$downloadObj = new Download($this->MySQL);
			$arrAttachments = $this->getPostAttachments();

			foreach ($arrAttachments as $attachment) {
				$downloadObj->select($attachment);
				$downloadObj->delete();
			}

		}
	}


	/** Gets all member_id's of posters in a topic */
	private function getTopicPosters() {

		$arrReturn = array();
		$query = "SELECT DISTINCT member_id FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."'";
		$result = $this->MySQL->query($query);

		while ($row = $result->fetch_assoc()) {
			$arrReturn[] = $row['member_id'];
		}

	}

	public function sendNotifications() {

		if ($this->intTableKeyValue != "") {

			$mailObj = new btMail();
			$member = new Member($this->MySQL);
			$arrBCC = array();

			// Check if need to send notification to topic starter
			$topicInfo = $this->getTopicInfo();
			$sentTopicMember = false;

			$subject = "New Post: ".$topicInfo['title'];
			$message = "A new post has been made in the topic: ".$topicInfo['title']."<br><br><a href='".$this->getLink(false, true)."'>View Post</a>";

			if ($member->select($topicInfo['member_id']) && $member->getEmailNotificationSetting("forum_topic") == 1) {
				$member->email($subject, $message);
				$sentTopicMember = true;
			}

			foreach ($this->getTopicPosters() ?? [] as $memberID) {

				if ($member->select($memberID) && $member->getEmailNotificationSetting("forum_post") == 1 && $member->get_info("email") != "") {

					if (($topicInfo['member_id'] == $memberID && !$sentTopicMember) || $topicInfo['member_id'] != $memberID) {
						$arrBCC[] = $member->get_info("email");
					}

				}

			}

			if (count($arrBCC) > 0) {
				$mailObj->sendMail("", $subject, $message, array("bcc" => $arrBCC));
			}

		}

	}


}