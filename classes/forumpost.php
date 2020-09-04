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


include_once("basic.php");


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
		
		if($addNew) {
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
		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT download_id FROM ".$this->MySQL->get_tablePrefix()."forum_attachments WHERE forumpost_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['download_id'];				
			}
			
		}
		
		return $returnArr;
		
	}
	
	public function show($template="") {
		global $websiteInfo, $MAIN_ROOT, $dbprefix, $mysqli, $member;
		if($template == "") {
			
			include("templates/post.php");
			
		}
		else {
			include("templates/".$template);
		}
		
	}

	public function getTopicInfo($filtered=false) {
		$returnArr = array();
		if($this->intTableKeyValue != "") {
		
			$temp = $this->intTableKeyValue;
			$tempManage = $this->blnManageable;
			$this->objTopic->select($this->arrObjInfo['forumtopic_id']);
			$this->select($this->objTopic->get_info("forumpost_id"));
			
			$returnArr = $filtered ? $this->get_info_filtered() : $this->get_info();
			
			$this->select($temp);
			$this->blnManageable = $tempManage;
		}
		
		return $returnArr;
	}
	
	
	public function getLink() {
		global $websiteInfo, $MAIN_ROOT, $memberInfo;
		
		$returnVal = "";
		if($this->intTableKeyValue != "") {
			
			// Figure out num of posts
			$query = "SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' ORDER BY dateposted";
			$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' ORDER BY dateposted");
			$totalPosts = $result->num_rows;

			// Find the post ranking within the result set
			$findDepthResult = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE forumtopic_id = '".$this->arrObjInfo['forumtopic_id']."' AND dateposted <= '".$this->arrObjInfo['dateposted']."'");
			$postRanking = $findDepthResult->num_rows;
			
			// Figure out posts per page
			if($memberInfo['postsperpage'] > 0) {
				$postsPerPage = $memberInfo['postsperpage'];
			}
			elseif($websiteInfo['forum_postsperpage'] > 0) {
				$postsPerPage = $websiteInfo['forum_postsperpage'];
			}
			else {
				$postsPerPage = 25;
			}
			
			// Total Pages
			$totalPages = ceil($totalPosts/$postsPerPage);
		
			
			$returnVal = $MAIN_ROOT."forum/viewtopic.php?tID=".$this->arrObjInfo['forumtopic_id'];
			
			if($totalPages > 1) {
				$pageNumber = ceil($postRanking/$postsPerPage);
				$returnVal .= "&pID=".$pageNumber;
			}
			
			$returnVal .= "#".$this->intTableKeyValue;
			
		}

		
		return $returnVal;
	}
	
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$returnVal = parent::delete();
			$downloadObj = new Download($this->MySQL);
			$arrAttachments = $this->getPostAttachments();
			
			foreach($arrAttachments as $attachment) {
				$downloadObj->select($attachment);
				$downloadObj->delete();	
			}
			
		}
	}
	
	
}



?>