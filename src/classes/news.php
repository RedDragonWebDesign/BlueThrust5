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


class News extends Basic {
	
	
	protected $strCommentTableName;
	protected $strCommentTableKey;
	public $objComment;
	private $consoleObj;
	private $blnViewPrivateNews;
	
	public function __construct($sqlConnection, $newsTableName="news", $newsTableKey="news_id", $commentTableName="comments", $commentTableKey="comment_id") {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix().$newsTableName;
		$this->strTableKey = $newsTableKey;
		
		$this->strCommentTableName = $this->MySQL->get_tablePrefix().$commentTableName;
		$this->strCommentTableKey = $commentTableKey;
		
		$this->objComment = new Basic($sqlConnection, $commentTableName, $this->strCommentTableKey);
		
		$this->consoleObj = new ConsoleOption($sqlConnection);
		
		$htmlInNewsCID = $this->consoleObj->findConsoleIDByName("HTML in News Posts");
		$this->consoleObj->select($htmlInNewsCID);
		
		$this->determinePrivateNewsStatus();
	}
	
	
	
	public function getComments($orderBY="") {
	
		$returnArr = array();
		
		if($orderBY == "") {
			$orderBY = " ORDER BY dateposted DESC";	
		}
		
		if($this->intTableKeyValue != "") {
			
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->strCommentTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'".$orderBY);
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row[$this->strCommentTableKey];
			}
			
		}
		
		return $returnArr;
		
	}
	
	
	public function countComments() {
		
		$returnVal = 0;
		
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->strCommentTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			$returnVal = $result->num_rows;
			
			
		}
		
		return $returnVal;
		
	}
	
	
	
	public function postComment($intMemberID, $strMessage) {
		
		$returnVal = false;

		if(is_numeric($intMemberID) && $this->intTableKeyValue != "" && trim($strMessage) != "") {
			
			if($this->objComment->addNew(array($this->strTableKey, "member_id", "message", "dateposted"), array($this->intTableKeyValue, $intMemberID, $strMessage, time()))) {
				$returnVal = true;
			}
			
		}
		
		return $returnVal;
		
	}
	

	public function delete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			
			
			$result1 = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			$result2 = $this->MySQL->query("DELETE FROM ".$this->strCommentTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			
			
			if($result1 && $result2) {
				$returnVal = true;	
			}
			
		}
		
		
		return $returnVal;
	
	}
	
	
	public function show() {
		global $hooksObj;
		if($this->intTableKeyValue != "") {
			$member = new Member($this->MySQL);
			$postInfo = $this->arrObjInfo;
			
			$checkHTMLAccess = "";
			if($member->select($postInfo['lasteditmember_id'])) {
				$checkHTMLAccess = $member->hasAccess($this->consoleObj);
				$dispLastEditTime = getPreciseTime($postInfo['lasteditdate']);
				$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";		
			}
			
			$dispNewsType = "";
			if($postInfo['newstype'] == 1) {
				$dispNewsType = " - <span class='publicNewsColor' style='font-style: italic'>public</span>";
			}
			elseif($postInfo['newstype'] == 2) {
				$dispNewsType = " - <span class='privateNewsColor' style='font-style: italic'>private</span>";
			}
			
			
			$member->select($postInfo['member_id']);
			
			$checkHTMLAccess = ($checkHTMLAccess == "") ? $member->hasAccess($this->consoleObj) : $checkHTMLAccess;
			$dispNews = $checkHTMLAccess ? parseBBCode($postInfo['newspost']) : nl2br(parseBBCode(filterText($postInfo['newspost'])));
			
			$GLOBALS['news_post']['id'] = $this->intTableKeyValue;
			$GLOBALS['news_post']['post'] = "
	
				<div class='newsDiv' id='newsDiv_".$postInfo['news_id']."'>
					
					<div class='postInfo'>
						<div id='newsPostAvatar' style='float: left'>".$member->getAvatar()."</div>
						<div id='newsPostInfo' style='float: left; margin-left: 15px'>posted by ".$member->getMemberLink()." - ".getPreciseTime($postInfo['dateposted']).$dispNewsType."<br>
						<span class='subjectText'>".filterText($postInfo['postsubject'])."</span></div>
						<div style='clear: both'></div>
					</div>
					<br>
					<div class='dottedLine' style='margin-top: 5px'></div>
					<div class='postMessage'>
						".$dispNews."
					</div>
					<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
					<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
					<p style='padding: 0px; margin: 0px' align='right'><b><a href='".MAIN_ROOT."news/viewpost.php?nID=".$postInfo['news_id']."#comments'>Comments (".$this->countComments().")</a></b></p>
				</div>
	
			";
			
			$hooksObj->run("newspost_show");
			echo $GLOBALS['news_post']['post'];
			
			unset($GLOBALS['news_POST']);
			
		}
		
	}
	
	
	public function calcPages($postType="") {
		global $websiteInfo;
		
		if($postType != "") {
			$newsPostSQL = "newstype = '".$postType."'";	
		}
		else {
			$showPrivateSQL = $this->blnViewPrivateNews ? " OR newstype = '2'" : "";
			$newsPostSQL = "newstype = '1'".$showPrivateSQL;
		}
		
		
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE ".$newsPostSQL." ORDER BY dateposted DESC");
		$totalPosts = $result->num_rows;
		
		$websiteInfo['news_postsperpage'] = ($websiteInfo['news_postsperpage'] <= 0) ? 1 : $websiteInfo['news_postsperpage'];
		
		$totalPages = ceil($totalPosts/$websiteInfo['news_postsperpage']);
		
		return $totalPages;
		
	}
	
	
	public function displayPageSelector($postType="", $pageURL="") {
		
		if(!isset($_GET['page'])) { $_GET['page'] = 1; }
		$totalPages = $this->calcPages($postType);
		
		$dispLink = ($pageURL == "") ? MAIN_ROOT."news/?page=" : $pageURL;
		
		$pageSelector = new PageSelector();
		
		$pageSelector->setPages($totalPages);
		$pageSelector->setLink($dispLink);
		
		$pageSelector->setCurrentPage($_GET['page']);
		
		$pageSelector->show();
		
		/*
		if($_GET['page'] <= $totalPages) {

			$nextPage = $_GET['page']+1;
			$prevPage = $_GET['page']-1;
			
			$dispLink = ($pageURL == "") ? MAIN_ROOT."news/?page=" : $pageURL;
			
			$dispPrevPage = ($prevPage > 0) ? "<a href='".$dispLink.$prevPage."'>NEWER ENTRIES</a>" : "";	
			$dispNextPage = ($nextPage <= $totalPages) ? "<a href='".$dispLink.$nextPage."'>OLDER ENTRIES</a>" : "";
			
			$pageSpacer = ($dispPrevPage != "" && $dispNextPage != "") ? "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;" : "";
			
			echo "
				<p align='center' class='largeFont'>
					<b>".$dispPrevPage.$pageSpacer.$dispNextPage."</b>
				</p>
			";
		}*/
		
	}
	
	public function getPosts($postType="") {
		global $websiteInfo;
		
		$totalPages = $this->calcPages($postType);
		
		if($postType != "") {
			$newsPostSQL = "newstype = '".$postType."'";	
		}
		else {
			$showPrivateSQL = $this->blnViewPrivateNews ? " OR newstype = '2'" : "";
			$newsPostSQL = "newstype = '1'".$showPrivateSQL;
		}
		
		
		if(!isset($_GET['page']) || $_GET['page'] > $totalPages) {
			$sqlLimit = " LIMIT 0, ".$websiteInfo['news_postsperpage'];
			$_GET['page'] = 1;
		}
		else {
			$sqlLimit = " LIMIT ".($_GET['page']-1)*$websiteInfo['news_postsperpage'].", ".$websiteInfo['news_postsperpage'];	
		}
		
		$returnArr = array();
		$result = $this->MySQL->query("SELECT news_id FROM ".$this->strTableName." WHERE ".$newsPostSQL." ORDER BY dateposted DESC ".$sqlLimit);
		while($row = $result->fetch_assoc()) {
			$returnArr[] = $row;
		}

		return $returnArr;
		
	}
	
	private function determinePrivateNewsStatus() {
		$member = new Member($this->MySQL);
		$member->select($_SESSION['btUsername']);
		$consoleObj = new ConsoleOption($this->MySQL);
		
		$privateNewsCID = $consoleObj->findConsoleIDByName("View Private News");
		$consoleObj->select($privateNewsCID);
		
		$this->blnViewPrivateNews = ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj));
	}
	
	public function getHTMLNewsConsole() {
		return $this->consoleObj;	
	}
	
}