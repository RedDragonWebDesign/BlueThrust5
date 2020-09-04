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

	include_once("btmysql.php");
	include_once("rank.php");
	include_once("rankcategory.php");
	include_once("member.php");
	
	
	class Access {
		
		protected $MySQL;
		protected $objMember;
		protected $objRankCat;
		protected $objRank;
		protected $objMemberAccess;
		protected $objRankAccess;
		
		
		public $cacheID;
		public $arrAccessFor;			// Primary Key & value for the table access is being used for.  I.E. poll_id for Poll Access, forumboard_id for Board Access
		public $arrAccessTables;
		public $arrAccessTypes;
		public $rankAccessDiv;
		public $memberAccessDiv;
		
		public function __construct($sqlConnection, $accessTableArray=array(), $accessTypesArray=array()) {
		
			$this->MySQL = $sqlConnection;
			$this->arrAccessTables = $accessTableArray;
			$this->arrAccessTypes = $accessTypesArray;
			$this->cacheID = md5(time().uniqid());
			
			$this->objMember = new Member($sqlConnection);
			$this->objRankCat = new RankCategory($sqlConnection);
			$this->objRank = new Rank($sqlConnection);

			$this->objMemberAccess = new Basic($sqlConnection, filterText($accessTableArray['member']['tableName']), filterText($accessTableArray['member']['tableID']));
			$this->objRankAccess = new Basic($sqlConnection, filterText($accessTableArray['rank']['tableName']), filterText($accessTableArray['rank']['tableID']));
		}
		
		
		public function saveAccess() {
		
			$arrBasicObj = array();
			
			foreach($this->arrAccessTables as $key => $accessTableInfo) {
				$accessTableInfo['tableName'] = filterText($accessTableInfo['tableName']);
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix().$accessTableInfo['tableName']." WHERE ".$this->arrAccessFor['keyName']." = '".$this->arrAccessFor['keyValue']."'");
				$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix().$accessTableInfo['tableName']."`");
			}
			
			foreach($_SESSION['btMemberAccess'][$this->cacheID] as $memberID => $accessTypeValue) {
			
				if(is_numeric($memberID) && is_numeric($accessTypeValue)) {
					$arrColumns = array($this->arrAccessFor['keyName'], "member_id", "accesstype");
					$arrValues = array($this->arrAccessFor['keyValue'], $memberID, $accessTypeValue);
					$this->objMemberAccess->addNew($arrColumns, $arrValues);
				}
			}
			
			foreach($_SESSION['btAccessCache'][$this->cacheID] as $checkBoxName => $accessTypeValue) {
				$rankID = str_replace("rankaccess_", "", $checkBoxName);

				if($this->objRank->select($rankID) && is_numeric($accessTypeValue)) {
					$arrColumns = array($this->arrAccessFor['keyName'], "rank_id", "accesstype");
					$arrValues = array($this->arrAccessFor['keyValue'], $rankID, $accessTypeValue);
					$this->objRankAccess->addNew($arrColumns, $arrValues);			
				}
				
			}
			
		}
		
		public function dispSetRankAccess($blnShowFull=true) {
			global $MAIN_ROOT, $THEME;
			$rankCounter = 0;
			$rankoptions = "";
			
			$result = $this->MySQL->query("SELECT rankcategory_id FROM ".$this->MySQL->get_tablePrefix()."rankcategory ORDER BY ordernum DESC");	
			while($row = $result->fetch_assoc()) {
				
				$this->objRankCat->select($row['rankcategory_id']);
				$arrRanks = $this->objRankCat->getRanks();
				$rankCatName = $this->objRankCat->get_info_filtered("name");
				
				if(count($arrRanks) > 0) {
					$rankoptions .= "<b><u>".$rankCatName."</u></b> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('rankcat_".$row['rankcategory_id']."', 1)\">Check All</a> - <a href='javascript:void(0)' onclick=\"selectAllCheckboxes('rankcat_".$row['rankcategory_id']."', 0)\">Uncheck All</a><br>";
					$rankoptions .= "<div id='rankcat_".$row['rankcategory_id']."'>";
					foreach($arrRanks as $rankID) {
						
						$dispRankAccess = "";
						
						foreach($this->arrAccessTypes as $accessTypeInfo) {
							
							if($_SESSION['btAccessCache'][$this->cacheID]["rankaccess_".$rankID] == $accessTypeInfo['value']) {
								$dispRankAccess = " - <span class='".$accessTypeInfo['css']."' style='font-style: italic'>".$accessTypeInfo['displayValue']."</span>";
							}
							
						}
						
						$this->objRank->select($rankID);
						$rankName = $this->objRank->get_info_filtered("name");
						$rankoptions .= "<input type='checkbox' name='rankaccess_".$rankID."' value='1' data-rankaccess='1'> ".$rankName.$dispRankAccess."<br>";
						$rankCounter++;
					}
					
					$rankoptions .= "</div><br>";
				}	
				
			}
			
			$rankOptionsHeight = $rankCounter*20;
			
			if($rankOptionsHeight > 300) {
				$rankOptionsHeight = 300;
			}
			
			
			if($blnShowFull) {
				echo "
					<div id='loadingSpiralRankAccess' class='loadingSpiral'>
						<p align='center'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
				
				<div id='".$this->rankAccessDiv."' style='margin-left: auto; margin-right: auto; overflow-y: auto; height: ".$rankOptionsHeight."px; width: 90%'>";
			}
			
			echo $rankoptions;
			
			if($blnShowFull) {
				echo "</div>
					<div class='main' style='overflow: auto; position: relative; margin-left: auto; margin-right: auto; margin-top: 20px; width: 95%'>
						<div style='display: inline-block; margin-right: 80px'><b>With Selected:</b></div>
						<div style='display: inline-block'>
							<select id='selectRankAccess' class='textBox'><option value='0'>Remove Access Rules</option>
						";
				
						$this->dispAccessOptions();
						
				echo "	
								
							</select>
							<input type='button' class='submitButton' id='setRankAccess' value='Set'>
						</div>
					</div>
					
					<script type='text/javascript'>
					
						$('#setRankAccess').click(function() {
							var intAccessCount = 0;
							var objRankAccess = {};
							$(\"input[data-rankaccess='1']\").each(function(index) {
								if($(this).is(':checked')) {
									objRankAccess[$(this).attr('name')] = $('#selectRankAccess').val();
								}
							});
							
							var jsonRankAccess = JSON.stringify(objRankAccess);
							
							$('#loadingSpiralRankAccess').show();
							$('#".$this->rankAccessDiv."').hide();
							
							$.post('".$MAIN_ROOT."members/include/accesscache/setaccess.php', { accessType: 'rank', cacheID: '".$this->cacheID."', accessInfo: jsonRankAccess }, function(data) {
							
								$('#loadingSpiralRankAccess').hide();
								$('#".$this->rankAccessDiv."').html(data);				
								$('#".$this->rankAccessDiv."').fadeIn(250);
							
							});
							
						});
					
					</script>
				";
			}
			
		}
		
		
		public function dispSetMemberAccess($blnShowFull=true) {
			global $MAIN_ROOT, $THEME;
			
			if($blnShowFull) {
				$membersTable = $this->MySQL->get_tablePrefix()."members";
				$ranksTable = $this->MySQL->get_tablePrefix()."ranks";
				$query = "SELECT ".$membersTable.".member_id FROM ".$membersTable.", ".$ranksTable." WHERE ".$membersTable.".rank_id = ".$ranksTable.".rank_id AND ".$membersTable.".disabled = '0' AND ".$membersTable.".rank_id != '1' ORDER BY ".$ranksTable.".ordernum DESC";
			
				$result = $this->MySQL->query($query);
				while($row = $result->fetch_assoc()) {
					
					$this->objMember->select($row['member_id']);
					$this->objRank->select($this->objMember->get_info("rank_id"));
					
					$memberName = $this->objMember->get_info_filtered("username");
					$rankName = $this->objRank->get_info_filtered("name");
					
					$memberOptions .= "<option value='".$row['member_id']."'>".$rankName." ".$memberName."</option>";
					
				}
				
				echo "
					<table class='formTable'>
						<tr>
							<td class='formLabel'>Member:</td>
							<td class='main'><select class='textBox' id='selectMemberAccessMID'><option value='0'>[SELECT]</option>".$memberOptions."</select></td>
						</tr>
						<tr>
							<td class='formLabel'>Access:</td>
							<td class='main'>
								<select class='textBox' id='selectMemberAccessType'>
									<option value='0'>Remove Access Rules</option>
									";
						$this->dispAccessOptions();
				
				echo "
								</select> <input type='button' id='setMemberAccess' class='submitButton' value='Set'>
							</td>
						</tr>
					</table>
			
					
					<div id='loadingSpiralMemberAccess' class='loadingSpiral'>
						<p align='center'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
				<div id='".$this->memberAccessDiv."'>
				";
			}
			
			echo "
				
					<table class='formTable' style='width: 80%'>
						<tr>
							<td class='formTitle' width=\"60%\">Member:</td>
							<td class='formTitle' width=\"20%\">Access:</td>
							<td class='formTitle' width=\"20%\">Actions:</td>
						</tr>
						";
				
				foreach($_SESSION['btMemberAccess'][$this->cacheID] as $memberID => $accessTypeValue) {
					
					if($this->objMember->select($memberID)) {
						
						$this->objRank->select($this->objMember->get_info("rank_id"));
					
						$memberName = $this->objMember->get_info_filtered("username");
						$rankName = $this->objRank->get_info_filtered("name");
						
						foreach($this->arrAccessTypes as $accessTypeInfo) {

							if($_SESSION['btMemberAccess'][$this->cacheID][$memberID] == $accessTypeInfo['value']) {
								$dispAccessValue = "<span class='".$accessTypeInfo['css']."'>".$accessTypeInfo['displayValue']."</span>";
							}
							
						}
						
						echo "
							<tr>
								<td class='main manageList' style='padding-left: 5px'><a href='".$MAIN_ROOT."profile.php?mID=".$memberID."' target='_blank'>".$rankName." ".$memberName."</a></td>
								<td class='main manageList' align='center'>".$dispAccessValue."</td>
								<td class='main manageList' align='center'><a href='javascript:void(0)'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' data-deleteMemberAccess='".$memberID."'></a></td>
							</tr>					
						";
					}
					
				}
			
			echo "
					</table>
				";
			
			if(count($_SESSION['btMemberAccess'][$this->cacheID]) == 0) {
				echo "
					<p class='main' align='center'>
						<i>No special member access rules set!</i>
					</p>
				";
			}
			
			if($blnShowFull) {
				echo "
					</div>	
				
					<script type='text/javascript'>	
						$(document).ready(function() {
						
							$('#setMemberAccess').click(function() {
	
								var objMemberAccess = {};
								
								objMemberAccess[$('#selectMemberAccessMID').val()] = $('#selectMemberAccessType').val();
								var jsonMemberAccess = JSON.stringify(objMemberAccess);
								
								$('#loadingSpiralMemberAccess').show();
								$('#".$this->memberAccessDiv."').hide();
								
								$.post('".$MAIN_ROOT."members/include/accesscache/setaccess.php', { accessType: 'member', cacheID: '".$this->cacheID."', accessInfo: jsonMemberAccess }, function(data) {
								
									$('#loadingSpiralMemberAccess').hide();
									$('#".$this->memberAccessDiv."').html(data);				
									$('#".$this->memberAccessDiv."').fadeIn(250);
								
								});
							
							});
							
							$('body').delegate('img[data-deleteMemberAccess]', 'click', function() {
								
								var prevAccessType = $('#selectMemberAccessType').val();
								var prevAccessMID = $('#selectMemberAccessMID').val();
							
								$('#selectMemberAccessType').val('0');
								$('#selectMemberAccessMID').val($(this).attr('data-deleteMemberAccess'));
							
								$('#setMemberAccess').click();
														
								$('#selectMemberAccessType').val(prevAccessType);
								$('#selectMemberAccessMID').val(prevAccessMID);
								
							});
						
						});
					</script>
				";
			}
						
		}
		
		
		public function dispAccessOptions() {
			
			foreach($this->arrAccessTypes as $accessTypeInfo) {
				echo "<option value='".$accessTypeInfo['value']."'>".$accessTypeInfo['displayValue']."</option>";
			}
			
		}
		
		public function getAccessInfo($memberObj) {

			$memberInfo = $memberObj->get_info();

			$returnArr = array("member" => "", "rank" => "");
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->arrAccessTables['member']['tableName']." WHERE ".filterText($this->arrAccessFor['keyName'])." = '".filterText($this->arrAccessFor['keyValue'])."' AND member_id = '".$memberInfo['member_id']."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$returnArr['member'] = $row['accesstype'];	
			}
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->arrAccessTables['rank']['tableName']." WHERE ".filterText($this->arrAccessFor['keyName'])." = '".filterText($this->arrAccessFor['keyValue'])."' AND rank_id = '".$memberInfo['rank_id']."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$returnArr['rank'] = $row['accesstype'];	
			}
			
			return $returnArr;
			
		}
		
		public function loadCache() {

			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix().$this->arrAccessTables['member']['tableName']." WHERE ".filterText($this->arrAccessFor['keyName'])." = '".filterText($this->arrAccessFor['keyValue'])."'");
			while($row = $result->fetch_assoc()) {
				$_SESSION['btMemberAccess'][$this->cacheID][$row['member_id']] = $row['accesstype'];
			}
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix().$this->arrAccessTables['rank']['tableName']." WHERE ".filterText($this->arrAccessFor['keyName'])." = '".filterText($this->arrAccessFor['keyValue'])."'");
			while($row = $result->fetch_assoc()) {
				$sessionName = "rankaccess_".$row['rank_id'];
				$_SESSION['btAccessCache'][$this->cacheID][$sessionName] = $row['accesstype'];
			}
			
		}
		
		
	}
	
	


?>