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


include_once("member.php");

class ShoutBox {
	
	
	protected $MySQL;
	protected $strTableName;
	protected $strTableKey;
	public $intDispWidth;
	public $intDispHeight;
	public $strEditLink;
	public $strDeleteLink;
	public $strPostLink;
	public $intWordWrap;
	protected $memberObj;
	public $strDivID;
	public $blnUpdateShoutbox;
	public $strSQLSort;
	public $blnMainShoutbox;
	
	public function __construct($sqlConnection, $tableName, $tableKey) {

		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix().$tableName;
		$this->strTableKey = $tableKey;
		$this->memberObj = new Member($sqlConnection);
		$this->intWordWrap = 75;
		$this->intDispWidth = 200;
		$this->intDispHeight = 350;
		$this->blnUpdateShoutbox = false;
		$this->blnMainShoutbox = false;
		
	}
	
	public function dispShoutbox($setWidth=0, $setHeight=0, $blnPercentWidth=false, $txtBoxWidth=0, $blnPercentHeight=false) {
		global $MAIN_ROOT, $THEME;
		if($setWidth > 0) {
			$this->intDispWidth = $setWidth;
		}
		
		if($setHeight > 0) {
			$this->intDispHeight = $setHeight;	
		}
		
		
		$dispWidthPX = "px";
		if($blnPercentWidth) {
			$dispWidthPX = "%";	
		}
		
		$dispHeightPX = "px";
		if($blnPercentHeight) {
			$dispHeightPX = "%";
		}
		
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." WHERE newstype = '3'".$this->strSQLSort." ORDER BY dateposted");
		while($row = $result->fetch_assoc()) {
			
			
			if($this->memberObj->select($row['member_id'])) {
				$memberLink = $this->memberObj->getMemberLink();
				$dispPost = nl2br(parseBBCode(wordwrap(filterText($row['newspost']), $this->intWordWrap)));
				$dispTime = "<p align='center' style='font-size: 9px'><br>".getPreciseTime($row['dateposted'])."</p>";
				
				$dispManagePost = "";
				if($this->strEditLink != "" && $this->strDeleteLink != "") {
					$dispManagePost = "<p align='center'><span class='loadingSpiral' id='".$this->strDivID."_loading_".$row[$this->strTableKey]."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif' width='30' height='30'></span><span class='tinyFont' id='".$this->strDivID."_manage_".$row[$this->strTableKey]."'><br><b><a href='".$this->strEditLink.$row[$this->strTableKey]."'>EDIT</a> - <a href='javascript:void(0)' onclick=\"deleteShoutbox('".$row[$this->strTableKey]."', '".$this->strDeleteLink."', '".$this->strDivID."')\">DELETE</a></b></span></p>";
				}
				
				
				$shoutBoxInfo .= "
					<b>".$memberLink.":</b><br>
					<div style='word-wrap: break-word;'>".$dispPost."</div>
					".$dispTime."
					".$dispManagePost."
					<div class='dottedLine' style='margin: 5px 0px'></div>
				";
			
			}
			
		}

		
		$addToReturn = "";
		$addToReturn2 = "";		
		
		$setMainShoutbox = "";
		if($this->blnMainShoutbox) {
			$setMainShoutbox = " data-shoutbox='main' ";	
		}
		
		if(!$this->blnUpdateShoutbox) {
			$addToReturn = "<div class='shoutBox' id='".$this->strDivID."'".$setMainShoutbox." style='width: ".$this->intDispWidth.$dispWidthPX."; height: ".$this->intDispHeight.$dispHeightPX."'>";
			$addToReturn2 = "</div>";
		}
		
		
		$returnVal = $addToReturn.$shoutBoxInfo.$addToReturn2;
		
		
		if($this->strPostLink != "") {
			
			$setTxtBoxWidth = $this->intDispWidth-10;
			if($txtBoxWidth > 0) {
				$setTxtBoxWidth = $txtBoxWidth;
			}
			
			$returnVal .= "
			<div class='shoutBoxPost' style='text-align: center; width: 100%' id='".$this->strDivID."_postShoutbox'>
				<div style='margin-left: auto; margin-right: auto; width: ".$setTxtBoxWidth.$dispWidthPX."'>
					<textarea class='textBox' rows='1' style='margin-left: auto; margin-right: auto; width: 100%; height: 25px' id='".$this->strDivID."_message'></textarea>
					<p align='right' style='margin-right: -3px; padding-top: 1px; margin-top: 3px'><input type='button' class='submitButton' value='POST' onclick=\"postShoutbox('".$this->strDivID."', '".$this->strPostLink."')\" style='padding: 5px'></p>
				</div>
			</div>";	
		}
		
		return $returnVal;
		
	}
	
	
	public function prepareLinks($memberObj) {
		
		$this->memberObj->select($_SESSION['btUsername']);
		$consoleObj = new ConsoleOption($this->MySQL);
		$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");
		$consoleObj->select($manageNewsCID);
		
		if(LOGGED_IN && $this->memberObj->hasAccess($consoleObj)) {
			$this->strEditLink = MAIN_ROOT."members/console.php?cID=".$manageNewsCID."&newsID=";
			$this->strDeleteLink = MAIN_ROOT."members/include/news/include/deleteshoutpost.php";
		}
		
		$postInShoutboxCID = $consoleObj->findConsoleIDByName("Post in Shoutbox");
		$consoleObj->select($postInShoutboxCID);
		
		if(LOGGED_IN && $this->memberObj->hasAccess($consoleObj)) {
			$this->strPostLink = MAIN_ROOT."members/include/news/include/postshoutbox.php";
		}
		
	}
	
	public function getShoutboxJS() {
		
		
		$returnVal = "
		
			<script type='text/javascript'>
							
				$(document).ready(function() {
						$('#".$this->strDivID."').animate({
							scrollTop:$('#".$this->strDivID."')[0].scrollHeight
						}, 1000);
					
		
					$('#".$this->strDivID."_message').keypress(function(eventObj) {
						if(eventObj.which == 13) {
							if($('#".$this->strDivID."_message').val() != \"\") {
								$('#".$this->strDivID."_postShoutbox input[type=button]').click();
							}
							return false;
						}
						else {
							return true;
						}
					});					
				
				
				});
			
			</script>
		
		";
		
		
		return $returnVal;
	}
	
}


?>