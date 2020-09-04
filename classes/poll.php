<?php

	include_once("basicsort.php");
	include_once("access.php");
	
	class Poll extends Basic {
		
		public $objPollOption;
		public $cacheID;
		public $objAccess;
		public $objPollVote;
		
		public function __construct($sqlConnection) {

			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."polls";
			$this->strTableKey = "poll_id";
			
			
			$this->objPollOption = new BasicSort($sqlConnection, "poll_options", "polloption_id", "poll_id");
			$this->objPollVote = new Basic($sqlConnection, "poll_votes", "pollvote_id");
			
			
			$arrAccessTables = array(
				"rank" => array("tableName" => "poll_rankaccess", "tableID" => "pollrankaccess_id"),
				"member" => array("tableName" => "poll_memberaccess", "tableID" => "pollmemberaccess_id")
			);
			
			$arrAccessTypes = array(
			
				array("value" => 3, "css" => "failedFont", "displayValue" => "No Access"),
				array("value" => 1, "css" => "allowText", "displayValue" => "View Only Access"),
				array("value" => 2, "css" => "pendingFont", "displayValue" => "Full Access")
			
			);
						
			$this->objAccess = new Access($sqlConnection, $arrAccessTables, $arrAccessTypes);
			
		}
		
		public function savePollOptions() {
			
			
			if($this->intTableKeyValue != "") {	
			
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."poll_options WHERE poll_id = '".$this->intTableKeyValue."'");
				$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."poll_options`");
			
			}
			
			foreach($_SESSION['btPollOptionCache'][$this->cacheID] as $sortNum => $pollOptionInfo) {
				
				$arrColumns = array("poll_id", "optionvalue", "color", "sortnum");
				$arrValues = array($this->intTableKeyValue, $pollOptionInfo['value'], $pollOptionInfo['color'], $sortNum);
				
				if(isset($pollOptionInfo['polloption_id'])) {
					$arrColumns[] = "polloption_id";
					$arrValues[] = $pollOptionInfo['polloption_id'];
				}
				
				$this->objPollOption->addNew($arrColumns, $arrValues);
				
			}
			
			
		}
		
		
		/*
		 * - makeCacheRoom -
		 * 
		 * 
		 * Similar to the makeRoom functions on BasicOrder and BasicSort.
		 * Creates room for a new option either before or after intSpot.
		 * 
		 * Works with the $_SESSION['btPollOptionCache'] array
		 * 
		 * Returns the value of the display order for a new/modified poll option 
		 * 
		 */
	
		public function makeCacheRoom($strBeforeAfter, $intSpot) {
	
			$tempArr = array();
			
			$x = 0;
			$returnVal = count($_SESSION['btPollOptionCache'][$this->cacheID]);
			
			foreach($_SESSION['btPollOptionCache'][$this->cacheID] as $key => $value) {
				
				if($strBeforeAfter == "before" && $key == $intSpot) {
					$returnVal = $x;
					$tempArr[$x] = "";
					$x++;
					$tempArr[$x] = $_SESSION['btPollOptionCache'][$this->cacheID][$key];
				}
				elseif($strBeforeAfter == "after" && $key == $intSpot) {
					$tempArr[$x] = $_SESSION['btPollOptionCache'][$this->cacheID][$key];
					$x++;
					$returnVal = $x;
				}
				else {
					$tempArr[$x] = $_SESSION['btPollOptionCache'][$this->cacheID][$key];	
				}
				
				$x++;
			}
		
			$_SESSION['btPollOptionCache'][$this->cacheID] = $tempArr;
			
			return $returnVal;
		}
		
		/*
		 * - resortCacheOrder -
		 * 
		 * Resorts the cache order
		 * 
		 */
		
		public function resortCacheOrder() {
			ksort($_SESSION['btPollOptionCache'][$this->cacheID]);

			$tempArr = array();
			foreach($_SESSION['btPollOptionCache'][$this->cacheID] as $value) {
				$tempArr[] = $value;
			}
			
			$_SESSION['btPollOptionCache'][$this->cacheID] = $tempArr;
		}
		
		
		/*
		 * 
		 * - moveCache -
		 * 
		 * 
		 * Moves the poll option cache in the $intSpot order
		 * either up or down based on $strDirection
		 * 
		 */
		
		public function moveCache($strDirection, $intSpot) {
			
			$moveUp = $intSpot-1;
			$moveDown = $intSpot+1;
			
			$selectedValue = $_SESSION['btPollOptionCache'][$this->cacheID][$intSpot];
			
			
			$movedSpot = ($strDirection == "up") ? $movedSpot = $intSpot-1 : $movedSpot = $intSpot+1;
			
			$movingValue = $_SESSION['btPollOptionCache'][$this->cacheID][$movedSpot];
			
			$_SESSION['btPollOptionCache'][$this->cacheID][$intSpot] = $movingValue;
			$_SESSION['btPollOptionCache'][$this->cacheID][$movedSpot] = $selectedValue;
			
		}
		
		public function getPollOptions() {
			
			$returnArr = array();
			if($this->intTableKeyValue != "") {
				$result = $this->MySQL->query("SELECT polloption_id FROM ".$this->MySQL->get_tablePrefix()."poll_options WHERE poll_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
				while($row = $result->fetch_assoc()) {
					$returnArr[] = $row['polloption_id'];
				}
			}
			
			return $returnArr;
			
		}
		
		public function getPollResults() {
			
			$arrResults = array();
			
			if($this->intTableKeyValue != "") {
			
				foreach($this->getPollOptions as $pollOptionID) {
					$arrResults[$pollOptionID] = 0;
				}
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_votes WHERE poll_id = '".$this->intTableKeyValue."'");
				while($row = $result->fetch_assoc()) {

					$arrResults[$row['polloption_id']] += $row['votecount'];
					
				}
			}
			
			
			return $arrResults;
		}
		
		public function getVoterInfo() {
			
			$arrReturn = array();
			if($this->intTableKeyValue != "") {

				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_votes WHERE poll_id = '".$this->intTableKeyValue."'");
				while($row = $result->fetch_assoc()) {

					$arrReturn[$row['member_id']][$row['polloption_id']] = array("votes" => $row['votecount'], "lastvoted" => $row['datevoted']);
					
				}
				
			}
			
			return $arrReturn;
		}
		
		public function dispPollMenu($memberObj) {
			global $MAIN_ROOT, $member;
			if($this->intTableKeyValue != "" && $this->hasAccess($memberObj)) {
								
				$pollInfo = $this->get_info_filtered();
				
				$hideResultLink = ($pollInfo['resultvisibility'] == "votedonly" && !$this->hasVoted($memberObj->get_info("member_id"))) ? " style='display: none'" : "";
				$dispResultLink = "";
				if($pollInfo['resultvisibility'] == "open" || $pollInfo['resultvisibility'] == "votedonly" || ($pollInfo['resultvisibility'] == "pollend" && $pollInfo['pollend'] != 0 && $pollInfo['pollend'] < time())) {
					$dispResultLink = "<br><p class='main' id='pollResultsLink_".$pollInfo['poll_id']."' align='center'".$hideResultLink."><a href='".$MAIN_ROOT."polls/view.php?pID=".$pollInfo['poll_id']."'>View Results</a></p>";
				}
				
				echo "
					<div class='pollMenuDiv'>
						".$pollInfo['question']."
						
						<div class='pollMenuOptionsDiv'>
							<form action=''>
							";
				
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_options WHERE poll_id = '".$this->intTableKeyValue."' ORDER BY sortnum");
				while($row = $result->fetch_assoc()) {
					$row = filterArray($row);
					
					$inputType = ($pollInfo['multivote'] == 1) ? "checkbox" : "radio";

					echo "
						<div class='pollMenuOption'><input type='".$inputType."' id='poll_".$row['polloption_id']."' name='poll_".$row['poll_id']."' value='".$row['polloption_id']."'> <label for='poll_".$row['polloption_id']."'>".$row['optionvalue']."</label></div>
						<br>
					";
				}
				echo "
				
							<p align='center'>
							
								<input type='button' id='btnPollVote_".$pollInfo['poll_id']."' class='submitButton' value='Vote'>
							
							</p>
				
							".$dispResultLink."
							
							</form>
						</div>
						<div id='pollDialog_".$pollInfo['poll_id']."' style='display: none'></div>
					</div>
					
					<script type='text/javascript'>
						$('#document').ready(function() {
							$('#btnPollVote_".$pollInfo['poll_id']."').click(function() {
							
								var objVotes = {};
								$(\"input[name='poll_".$pollInfo['poll_id']."']\").each(function(index) {
									if($(this).is(':checked')) {
										
										objVotes[$(this).attr('id')] = $(this).val();
										
									}
								});
							
								$(\"input[name='poll_".$pollInfo['poll_id']."']\").attr('checked', false);
								
								var jsonVotes = JSON.stringify(objVotes);
								var dialogHTML = \"\";
								$.post('".$MAIN_ROOT."polls/vote.php', { pollID: '".$pollInfo['poll_id']."', pollOptionID: jsonVotes }, function(data) {
									
									try {
										postData = JSON.parse(data);
										
										if(postData['result'] == \"success\") {
											dialogHTML = \"Thank you for voting!\";
										";
				
										if($pollInfo['resultvisibility'] == "votedonly") {
						
											
											echo "
												dialogHTML += \"<br><br>You may now view the <a href='".$MAIN_ROOT."polls/view.php?pID=".$pollInfo['poll_id']."'><b>poll results</b></a>.\";
												$('#pollResultsLink_".$pollInfo['poll_id']."').show();
											";
											
										}
				echo "							
											
										}
										else {
											dialogHTML = \"Unable to vote due to the following reason:<br><br>\"+postData['errors'];
										}
										
										
									}
									catch(err) {
										dialogHTML = \"You do not have permission to vote on this poll.\";
									}
								
									$('#pollDialog_".$pollInfo['poll_id']."').html(\"<p align='center' class='main'>\"+dialogHTML+\"</p>\");
									
									$('#pollDialog_".$pollInfo['poll_id']."').dialog({
										title: 'Poll',
										zIndex: 999999,
										modal: true,
										show: 'scale',
										width: 400,
										resizable: false,
										buttons: {
											'Ok': function() {
												$(this).dialog('close');
											}
										}
									
									});
									
								});
							});
						});
					</script>
					
				";
				
			}
			else {
				echo "
					<p class='main' align='center'>
						You do not have permission to view this poll.<br><br>
					</p>
				";
			}
			
		}
		
		public function hasAccess($member) {
			
			$returnVal = false;
			if($this->arrObjInfo['accesstype'] == "memberslimited") {
				$accessInfo = $this->objAccess->getAccessInfo($member);
				
				
				if($accessInfo['rank'] == 2 || $member->get_info("rank_id") == 1) {
					$returnVal = true;
				}
				elseif($accessInfo['member'] == 2) {
					$returnVal = true;
				}
				
				if($returnVal && $accessInfo['member'] == 3) {
					$returnVal = false;
				}
			}
			elseif($this->arrObjInfo['accesstype'] == "members" && $member->get_info("datejoined") > 0 && $member->get_info("disabled") == 0) {
				$returnVal = true;
			}
			elseif($this->arrObjInfo['accesstype'] == "public") {
				$returnVal = true;
			}
			
			
			return $returnVal;
		}
		
		public function hasVoted($memberID) {
			$returnVal = false;
			if($this->intTableKeyValue != "" && is_numeric($memberID)) {

				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_votes WHERE poll_id = '".$this->intTableKeyValue."' AND member_id = '".$memberID."'");
				if($result->num_rows > 0) {
					$returnVal = true;
				}
				
			}
			
			return $returnVal;
		}
		
		public function vote($memberID, $pollOptionInfo) {
			
			$pollError = "";
			$returnArr = array("result"=>"fail");
			if($this->intTableKeyValue != "" && $pollOptionInfo['poll_id'] == $this->intTableKeyValue && in_array($pollOptionInfo['polloption_id'], $this->getPollOptions())) {
				
				$columnName = ($memberID == "") ? "ipaddress" : "member_id";
				$columnValue = ($memberID == "") ? $this->MySQL->real_escape_string($_SERVER['REMOTE_ADDR']) : $memberID;
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_votes WHERE poll_id = '".$this->intTableKeyValue."' AND ".$columnName." = '".$columnValue."'");
				if($result->num_rows > 0) {
					$countVotes = 0;
					while($row = $result->fetch_assoc()) {
						if($row['polloption_id'] == $pollOptionInfo['polloption_id']) {
							$selectedPollVote = $row['pollvote_id'];
						}
						$countVotes += $row['votecount'];
					}
					
					$pollEndCheck = ($this->arrObjInfo['pollend'] == 0 || $this->arrObjInfo['pollend'] > time());
					$maxVotesCheck = ($this->arrObjInfo['maxvotes'] == 0 || $this->arrObjInfo['maxvotes'] > $countVotes);
					if($maxVotesCheck && $pollEndCheck) {

						if($this->objPollVote->select($selectedPollVote)) {
							$newVoteCount = $this->objPollVote->get_info("votecount")+1;
							$this->objPollVote->update(array("datevoted", "votecount", "ipaddress"), array(time(), $newVoteCount, $_SERVER['REMOTE_ADDR']));
						}
						else {
							$this->objPollVote->addNew(array("poll_id", "polloption_id", "member_id", "ipaddress", "datevoted", "votecount"), array($this->intTableKeyValue, $pollOptionInfo['polloption_id'], $memberID, $_SERVER['REMOTE_ADDR'], time(), 1));							
						}
						
						$returnArr['result'] = "success";
						
						
						
					}
					elseif(!$pollEndCheck) {
						$pollError = "This poll has ended.";
					}
					else {
						$pollError = "Maximum number of votes allowed.";
					}
					
				}
				else {
					
					$returnArr['result'] = "success";
					$this->objPollVote->addNew(array("poll_id", "polloption_id", "member_id", "ipaddress", "datevoted", "votecount"), array($this->intTableKeyValue, $pollOptionInfo['polloption_id'], $memberID, $_SERVER['REMOTE_ADDR'], time(), 1));
					
				}
				
			}
			
			$returnArr['errors'] = $pollError;
			return $returnArr;
			
		}
		
		
		public function delete() {
			
			if($this->intTableKeyValue != "") {
				$pollInfo = $this->arrObjInfo;
				
				
				$dbprefix = $this->MySQL->get_tablePrefix();
				
				$this->MySQL->query("DELETE FROM ".$dbprefix."poll_options WHERE poll_id = '".$pollInfo['poll_id']."'");
				$this->MySQL->query("DELETE FROM ".$dbprefix."poll_memberaccess WHERE poll_id = '".$pollInfo['poll_id']."'");
				$this->MySQL->query("DELETE FROM ".$dbprefix."poll_rankaccess WHERE poll_id = '".$pollInfo['poll_id']."'");
				$this->MySQL->query("DELETE FROM ".$dbprefix."poll_votes WHERE poll_id = '".$pollInfo['poll_id']."'");
				
				$this->MySQL->query("OPTIMIZE TABLE `".$dbprefix."poll_options`");
				$this->MySQL->query("OPTIMIZE TABLE `".$dbprefix."poll_memberaccess`");
				$this->MySQL->query("OPTIMIZE TABLE `".$dbprefix."poll_rankaccess`");
				$this->MySQL->query("OPTIMIZE TABLE `".$dbprefix."poll_votes`");
				
				
				parent::delete();
				
			}
			
			return true;
		}
		
		public function totalVotes() {

			$returnVal = 0;
			if($this->intTableKeyValue != "") {
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."poll_votes WHERE poll_id = '".$this->intTableKeyValue."'");
					
				$returnVal = $result->num_rows;
			}
			
			return $returnVal;
			
		}
		
	}

?>