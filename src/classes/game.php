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


include_once("rank.php");
class Game extends Rank {
	
	function __construct($sqlConnection) {
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."gamesplayed";
		$this->strTableKey = "gamesplayed_id";
		$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."gamestats";
		$this->strAssociateKeyName = "gamestats_id";
		
	}
	
	
	function countMembers() {
		
		$returnVal = 0;
		if(isset($this->intTableKeyValue)) {
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."gamesplayed_members WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			$returnVal = $result->num_rows;
	
		}
		
		return $returnVal;
	}
	
	
	function getMembersWhoPlayThisGame() {
		
		$returnArr = array();
		if(isset($this->intTableKeyValue)) {
			$membersGamesTable = $this->MySQL->get_tablePrefix()."gamesplayed_members";
			$membersTable = $this->MySQL->get_tablePrefix()."members";
			$query = "SELECT ".$membersGamesTable.".member_id FROM ".$membersGamesTable.", ".$membersTable." WHERE ".$membersGamesTable.".member_id = ".$membersTable.".member_id AND ".$membersGamesTable.".".$this->strTableKey." = '".$this->intTableKeyValue."' AND ".$membersTable.".disabled = '0'";
			$result = $this->MySQL->query($query);
			while($row = $result->fetch_assoc()) {
				
				$returnArr[] = $row['member_id'];
				
			}
		
		}
		
		return $returnArr;
		
	}
	
	
	/*
	 *  - getGameList Function -
	 *  
	 *  Returns an array of IDs of all gamesplayed ordered by ordernum
	 * 
	 */
	
	function getGameList() {
		
		$returnArr = array();
		
		$result = $this->MySQL->query("SELECT * FROM ".$this->strTableName." ORDER BY ordernum DESC");
		while($row = $result->fetch_assoc()) {
			$returnArr[] = $row[$this->strTableKey];
		}
		
		return $returnArr;
		
	}

	
	
	function calcStat($gameStatID, $memberObj) {
		
		$calculatedValue = 0;
		
		$gameStatObj = new Basic($this->MySQL, "gamestats", "gamestats_id");
		
		if($gameStatObj->select($gameStatID) && isset($memberObj)) {
			
			$gameStatInfo = $gameStatObj->get_info_filtered();
			
			$gameStat1Obj = new Basic($this->MySQL, "gamestats", "gamestats_id");
			$gameStat2Obj = new Basic($this->MySQL, "gamestats", "gamestats_id");
			
			if($gameStatInfo['stattype'] == "calculate" && $gameStat1Obj->select($gameStatInfo['firststat_id']) && $gameStat2Obj->select($gameStatInfo['secondstat_id'])) {
				
				$gameStats1Info = $gameStat1Obj->get_info_filtered();
				$gameStats2Info = $gameStat2Obj->get_info_filtered();
				
				$gameStat1Type = $gameStats1Info['stattype'];
				$gameStat2Type = $gameStats2Info['stattype'];

				
				if($gameStat1Type == "calculate") {
					$gameStat1Value = $this->calcStat($gameStats1Info['gamestats_id'], $memberObj);
				}
				else {
					$gameStat1Value = $memberObj->getGameStatValue($gameStats1Info['gamestats_id']);
				}
				
				if($gameStat2Type == "calculate") {
					$gameStat2Value = $this->calcStat($gameStats2Info['gamestats_id'], $memberObj);
				}
				else {
					$gameStat2Value = $memberObj->getGameStatValue($gameStats2Info['gamestats_id']);
				}
				
				
				
				switch($gameStatInfo['calcop']) {
					case "div":
						if($gameStat2Value == 0) {
							$gameStat2Value = 1;	
						}
						
						$calculatedValue = round($gameStat1Value/$gameStat2Value, $gameStatInfo['decimalspots']);
						break;
					case "mul":
						$calculatedValue = round($gameStat1Value*$gameStat2Value, $gameStatInfo['decimalspots']);
						break;
					case "sub":
						$calculatedValue = round($gameStat1Value-$gameStat2Value, $gameStatInfo['decimalspots']);
						break;
					default:
						$calculatedValue = round($gameStat1Value+$gameStat2Value, $gameStatInfo['decimalspots']);
					
				}
				
			}

		}
		
		return $calculatedValue;
		
	}
	
	
	/*
	
	-Delete Method-
	
	Will delete the selected game from the database along with all stats associated with the game.  You must first "select" a table row using the select method in order to delete.
	
	*/
	
	public function delete() {
		$returnVal = false;
		if($this->intTableKeyValue != "") {
			$countErrors = 0;
			$info = $this->arrObjInfo;
			// Delete Game			
			
			$result = $this->MySQL->query("DELETE FROM ".$this->strTableName." WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."gamesplayed_members WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			if($this->MySQL->error) {
				$countErrors++;
			}
			
			
			// Delete Game Stats
			
			$gameStats = $this->getAssociateIDs();
			
			foreach($gameStats as $gameStatID) {
				
				$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."gamestats_members WHERE gamestats_id = '".$gameStatID."'");
				if($this->MySQL->error) {
					$countErrors++;
				}
				
			}
			
			
			$result = $this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."gamestats WHERE ".$this->strTableKey." = '".$this->intTableKeyValue."'");
			if($this->MySQL->error) {
				$countErrors++;
			}
			$this->resortOrder();
			
			if($countErrors == 0) {
				$returnVal = true;
				
				deleteFile(BASE_DIRECTORY.$info['imageurl']);
				
			}

	
		}
	
		return $returnVal;
	
	}
	
	public function get_privileges() {
		return true;
	}
	
}


?>