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
include_once("member.php");
include_once("tournamentpool.php");


class Tournament extends Basic {
	
	
	public $objPlayer;
	public $objTeam;
	public $objMatch;
	public $objTournamentPool;
	public $objPoolMatch;
	public $objManager;
	protected $objMember;
	protected $arrTeamIDs;
	protected $arrRoundsPerTeams;
	protected $arrPoolsPerTeams;
	
	
	
	public function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."tournaments";
		$this->strTableKey = "tournament_id";
		
		$this->objPlayer = new Basic($sqlConnection, "tournamentplayers", "tournamentplayer_id");
		$this->objTeam = new Basic($sqlConnection, "tournamentteams", "tournamentteam_id");
		$this->objMatch = new Basic($sqlConnection, "tournamentmatch", "tournamentmatch_id");
		$this->objTournamentPool = new TournamentPool($sqlConnection);
		$this->objPoolMatch = new Basic($sqlConnection, "tournamentpools_teams", "poolteam_id");
		$this->objMember = new Member($sqlConnection);
		$this->objManager = new Basic($sqlConnection, "tournament_managers", "tournamentmanager_id");
		
		$this->arrRoundsPerTeams = array(
				2 => 4, 
				3 => 8,
				4 => 16,
				5 => 32,
				6 => 64,
				7 => 128,
				8 => 256);
		
		
		$this->arrPoolsPerTeams = array(
				4 => 1,
				8 => 2,
				16 => 4,
				32 => 4,
				64 => 8,
				128 => 16,
				256 => 16);
		
		
	}
	
	
	
	public function addNew($arrColumns, $arrValues) {
		$returnVal = false;
		// Do the original stuff
		$result = parent::addNew($arrColumns, $arrValues);
		
		
		if($result && $this->intTableKeyValue != "") {
			// Add the Teams
			
			$this->arrTeamIDs = array();
			$countErrors = 0;
			$arrSeeds = range(1, $this->arrObjInfo['maxteams']);
			
			if($this->arrObjInfo['seedtype'] == 2) {
				shuffle($arrSeeds);	
			}
			elseif($this->arrObjInfo['seedtype'] == 3) {
				$arrSeeds = array_fill(0, $this->arrObjInfo['maxteams'], 0);	
			}
			
			for($i=0; $i<$this->arrObjInfo['maxteams']; $i++) {
				
				$teamNumber = $i+1;
				$teamName = "Team ".$teamNumber;
				if(!$this->objTeam->addNew(array("tournament_id", "seed", "name"), array($this->arrObjInfo['tournament_id'], $arrSeeds[$i], $teamName))) {
					$countErrors++;
				}

				
				$this->arrTeamIDs[] = $this->objTeam->get_info("tournamentteam_id");
				
			}
			
			
			if($this->arrObjInfo['seedtype'] == 3) {
				// Pools
				
				$numOfPools = $this->arrPoolsPerTeams[$this->arrObjInfo['maxteams']];
				$blnPoolsAdded = true;
				
				// Add the pools
				for($i=1; $i<=$numOfPools; $i++) {
					if(!$this->objTournamentPool->addNew(array("tournament_id"), array($this->arrObjInfo['tournament_id']))) {
						$blnPoolsAdded = false;
						$countErrors++;	
					}
				}
				
				if($blnPoolsAdded) {
					$arrPools = $this->getPoolList();
					$arrTeams = $this->arrTeamIDs;
					$teamsPerPool = $this->arrObjInfo['maxteams']/$numOfPools;
					
					$poolOffset = 0;
					shuffle($arrTeams);
					foreach($arrPools as $poolID) {					
						
						$arrPoolTeams[$poolID] = array_slice($arrTeams, $poolOffset, $teamsPerPool);
						$poolOffset += $teamsPerPool;
						
					}
					
					
					$arrNewPoolColumns = array("tournament_id", "pool_id", "team1_id", "team2_id");
					foreach($arrPoolTeams as $poolID => $tempTeamArr) {
						
						$teamStart = 1;
						foreach($tempTeamArr as $teamID) {
							
							$team1Index = $teamStart-1;
							for($i=$teamStart; $i<$teamsPerPool; $i++) {
								
								$arrNewPoolValues = array($this->intTableKeyValue, $poolID, $tempTeamArr[$team1Index], $tempTeamArr[$i]);
								$this->objPoolMatch->addNew($arrNewPoolColumns, $arrNewPoolValues);
								
							}
							$teamStart++;
						}
						
					}
					
					
				}
				
				
				
				
			}
			else {
				// Non-Pools
				
				$this->resetMatches();
				
			}
			
			
			/*
				-- OLD MATCH SPOT --
			*/
			
			
			if($countErrors == 0) {
				$returnVal = true;
			}
			else {
				
				// Unable to add complete tournament - Delete everything with the tournament's ID
				
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentpools WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentteams WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournaments WHERE tournament_id = '".$this->arrObjInfo['tournament_id']."'");
			}
		
		}
		
		return $returnVal;
		
	}
	
	public function select($intIDNum, $numericIDOnly = true) {
	
		$returnVal = parent::select($intIDNum, $numericIDOnly);
	
		$this->objTournamentPool->objTournament = $this;
	
		return $returnVal;
	
	}
	
	
	public function update($arrColumns, $arrValues) {
		$returnVal = false;
		
		$arrOriginalInfo = $this->arrObjInfo;
		// Do the original stuff
		$result = parent::update($arrColumns, $arrValues);
		
		if($result) {
			$returnVal = true;
			$arrNewInfo = $this->arrObjInfo;
			
			if($arrOriginalInfo['maxteams'] > $arrNewInfo['maxteams']) {
				// Less teams than originally
				
				$numToRemove = $arrOriginalInfo['maxteams'] - $arrNewInfo['maxteams'];
				$arrCurrentTeams = $this->getTeams(true, "ORDER BY seed DESC");
				
				for($i=0; $i<$numToRemove; $i++) {
					$arrRemovedTeams[] = $arrCurrentTeams[$i];
					
					$this->objTeam->select($arrCurrentTeams[$i]);
					$this->objTeam->delete();
					
					$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE team_id = '".$arrCurrentTeams[$i]."'");
					
				}
				
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE tournament_id = '".$this->intTableKeyValue."'");
				
				$this->resetMatches();
				
				
			}
			elseif($arrOriginalInfo['maxteams'] < $arrNewInfo['maxteams']) {
				// More teams than originally
				
				$numToAdd = $arrNewInfo['maxteams'] - $arrOriginalInfo['maxteams'];
				
				$nextSeed = $arrOriginalInfo['maxteams']+1;
				
				for($i=0; $i<$numToAdd; $i++) {
	
					$this->objTeam->addNew(array("tournament_id", "seed"), array($this->intTableKeyValue, $nextSeed));
					
					$nextSeed++;
				}
				
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE tournament_id = '".$this->intTableKeyValue."'");
				
				$this->resetMatches();
				
				
			}
		
		}
		return $returnVal;
		
	}
	
	
	public function resetMatches() {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			
			// Create Match Slots For Entire Tournament
			
			$totalRounds = $this->getTotalRounds();
			$intRoundNum = $totalRounds;
			
			
			for($i=1; $i<=$totalRounds; $i++) {
			
				if($i == 1) {
					// Last Round only 1 match
					$totalMatches = 1;
				}
				else {
					$totalMatches *= 2;
				}
				$nextMatchIndex = 1;
				$totalMatchesAdded = 0;
				for($x=1; $x<=$totalMatches; $x++) {
			
					$arrColumns = array("round", "tournament_id");
					$arrValues = array($intRoundNum, $this->arrObjInfo['tournament_id']);
			
					if($i != 1 && ($totalMatches/2) == 1) {
						$arrColumns[] = "nextmatch_id";
						$arrValues[] = $nextMatchID[$i-1][1];
					}
					elseif($i != 1) {
			
						$arrColumns[] = "nextmatch_id";
						$arrValues[] = $nextMatchID[$i-1][$nextMatchIndex];
			
					}
			
			
					$this->objMatch->addNew($arrColumns, $arrValues);
					$nextMatchID[$i][$x] = $this->objMatch->get_info("tournamentmatch_id");
			
			
					// Check to see if we need to increase the nextMatchIndex
					// Once 2 matches are created a new match index needs to be set
					$totalMatchesAdded++;
					if($totalMatchesAdded >= 2) {
						$nextMatchIndex++;
						$totalMatchesAdded = 0;
					}
			
			
				}
			
				$intRoundNum--;
			}
			
			
			
			// Update First Round Matches
			// Pair Teams Against Each Other
			
			
			$arrTeams = $this->getTeams(true, "ORDER BY seed");
			$maxArrTeamsIndex = $this->arrObjInfo['maxteams']-1;
			$arrMatches = $this->getMatches(1);
			$counter = 0;
			foreach($arrMatches as $matchID) {
			
				$team1ID = $arrTeams[$counter];
				$team2Index = $maxArrTeamsIndex-$counter;
				$team2ID = $arrTeams[$team2Index];
			
				$arrColumns = array("team1_id", "team2_id");
				$arrValues = array($team1ID, $team2ID);
			
				$this->objMatch->select($matchID);
				$this->objMatch->update($arrColumns, $arrValues);
			
				$counter++;
			}
		
		
			if($counter != 0) {
				$returnVal = true;
			}
		}
		
		return $returnVal;
		
	}
	
	
	
	public function getTeams($blnRefresh=false, $strOrderBy= "") {
		
		if($blnRefresh) {
			$this->arrTeamIDs = array();
		}
		
		if(count($this->arrTeamIDs) == 0) {
			
			$teamArr = array();
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentteams WHERE tournament_id = '".$this->intTableKeyValue."' ".$strOrderBy);
			while($row = $result->fetch_assoc()) {
				$teamArr[] = $row['tournamentteam_id'];				
			}
			
			$this->arrTeamIDs = $teamArr;
		}

		return $this->arrTeamIDs;

	}
	
	
	
	public function getPlayers($getPlayerIDs=false) {
		
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
			
			
			$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE tournament_id = '".$this->intTableKeyValue."'";
			$result = $this->MySQL->query($query);
			
			while($row = $result->fetch_array()) {
				
				if($getPlayerIDs) {
					$returnArr[] = $row['tournamentplayer_id'];
				}
				else {
					
					if($row['member_id'] != 0) {
						$returnArr[] = $row['member_id'];
					}
					else {
						$returnArr[] = $row['displayname'];	
					}
				
				}
				
			}
		
		
		}
		
		return $returnArr;
		
	}
	
	public function getTournamentPlayerID($memberID) {
		
		if($this->intTableKeyValue != "") {
			
			if(is_numeric($memberID)) {
				
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE tournament_id = '".$this->intTableKeyValue."' AND member_id = '".$memberID."'";
				$result = $this->MySQL->query($query);
			}
			else {
				$memberID = $this->MySQL->real_escape_string($memberID);
				$query = "SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE tournament_id = '".$this->intTableKeyValue."' AND displayname = '".$memberID."'";
				$result = $this->MySQL->query($query);
			}
			
			
			$row = $result->fetch_assoc();
			
			return $row['tournamentplayer_id'];
			
			
		}
		
	}
	
	/*
	 * - getTeamPlayers -
	 * 
	 * Returns an array memberIDs of players on the $teamID team
	 */
	public function getTeamPlayers($teamID, $returnPlayerID=false) {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentplayers WHERE team_id = '".$teamID."'");
			while($row = $result->fetch_assoc()) {
				
				if($returnPlayerID) {
					$returnArr[] = $row['tournamentplayer_id'];
				}
				else {
					$returnArr[] = $row['member_id'];
				}
				
			}
			
		}
		
		return $returnArr;
		
	}
	
	public function getUnfilledTeams() {
		
		$returnArr = array();
		if($this->intTableKeyValue != "") {
			
			$this->getTeams(true);
			
			foreach($this->arrTeamIDs as $teamID) {
				
				if(count($this->getTeamPlayers($teamID)) < $this->arrObjInfo['playersperteam']) {
					
					$returnArr[] = $teamID;
					
				}
				
			}
			
		}
		
		return $returnArr;
		
	}
	
	public function getTotalRounds($intMaxTeams="") {
		
		if($intMaxTeams == "") {
			$intMaxTeams = $this->arrObjInfo['maxteams'];
		}
		
		
		return array_search($intMaxTeams, $this->arrRoundsPerTeams);
		
	}
	
	
	public function getMatches($intRoundNumber, $intTeamID=0) {
		
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
			
			$filterByTeam;
			if($intTeamID != 0 && is_numeric($intTeamID)) {
				
				$filterByTeam = " AND (team1_id = '".$intTeamID."' OR team2_id = '".$intTeamID."') ";
				
			}
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE round = '".$intRoundNumber."' AND tournament_id = '".$this->intTableKeyValue."'".$filterByTeam." ORDER BY tournamentmatch_id");
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['tournamentmatch_id'];				
			}
		
		}
		
		return $returnArr;
		
	}
	
	
	public function getTeamIDBySeed($intSeed, $blnSelectTeam=true) {
		
		$returnVal = false;
		
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentteams WHERE tournament_id = '".$this->intTableKeyValue."' AND seed = '".$intSeed."'");
			if($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				
				$returnVal = $row['tournamentteam_id'];
				
			}
			
			
			if($blnSelectTeam) {
				$this->objTeam->select($returnVal);	
			}
			
		
		}
		
		return $returnVal;
	}
	
	
	
	/*
	 * 
	 * - getNextMatchTeamSpot Function -
	 * 
	 * Returns either "team1_id" or "team2_id".  Used to figure out which spot to display the winning team in the next match
	 * 
	 * 
	 */
	
	public function getNextMatchTeamSpot($intTeamID, $intMatchID=0) {
		
		$returnVal = false;
		if($intMatchID != 0 && is_numeric($intMatchID)) {
			
			$this->objMatch->select($intMatchID);
			
		}
		
		if($this->intTableKeyValue != "" && $this->objMatch->get_info("tournamentmatch_id") != "") {
			$nextMatchID = $this->objMatch->get_info("nextmatch_id");
			$thisMatchID = $this->objMatch->get_info("tournamentmatch_id");
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE tournamentmatch_id != '".$thisMatchID."' AND nextmatch_id = '".$nextMatchID."' ORDER BY tournamentmatch_id");
			
			$row = $result->fetch_assoc();

			if($row['tournamentmatch_id'] > $thisMatchID) {
				$returnVal = "team1_id";				
			}
			else {
				$returnVal = "team2_id";
			}
			
			
			
		}
		
		return $returnVal;
		
		
	}
	
	
	public function getTournamentWinner() {
		
		$returnVal = false;

		
		if($this->intTableKeyValue != "") {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentmatch WHERE nextmatch_id = '0' AND tournament_id = '".$this->intTableKeyValue."'");
			$row = $result->fetch_assoc();
			
			
			if($row['outcome'] == 1) {
				$returnVal = $row['team1_id'];
			}
			elseif($row['outcome'] == 2) {
				$returnVal = $row['team2_id'];				
			}
			
			
		}
		
		
		return $returnVal;
	
	}
	
	
	public function getPoolList() {
	
		$returnArr = array();
		
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT * FROM  ".$this->MySQL->get_tablePrefix()."tournamentpools WHERE tournament_id = '".$this->intTableKeyValue."' ORDER BY tournamentpool_id");
	
			while($row = $result->fetch_assoc()) {
				$returnArr[] = $row['tournamentpool_id'];
			}
		
		}
		
		return $returnArr;
	
	}
	
	
	/*
	 * - getPlayerName Function -
	 * 
	 * Returns the text value of a players name for tournaments with 1 player per team.  If there are > 1 player per team,
	 * this will just return the actual team name.  It returns blank if there is no player for the selected tead and there is only
	 * 1 player per team.
	 */
	public function getPlayerName($teamID="") {
		$returnVal = "";
		
		if($teamID != "" && is_numeric($teamID)) {
			$this->objTeam->select($teamID);
		}
		
		if($this->intTableKeyValue != "" && $this->objTeam->get_info("tournament_id") == $this->intTableKeyValue) {

			$teamID = $this->objTeam->get_info("tournamentteam_id");
			
			if($this->arrObjInfo['playersperteam'] == 1) {

				$playerID = $this->getTeamPlayers($teamID, true);
				
				if($this->objPlayer->select($playerID[0])) {
					$playerInfo = $this->objPlayer->get_info_filtered();
			
					if($this->objMember->select($playerInfo['member_id'])) {
						$returnVal = $this->objMember->get_info_filtered("username");
					}
					else {
						$returnVal = $playerInfo['displayname'];
					}

				}
				
			}
			else {
				$returnVal = $this->objTeam->get_info_filtered("name");	
			}
			
			
		}
		
		return $returnVal;
		
	}
	
	
	/*
	 * - poolsComplete Function -
	 * 
	 * Returns true when all pool matches for the tournament have a winner, false otherwise.
	 * 
	 */
	
	public function poolsComplete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && $this->arrObjInfo['seedtype'] == 3) {

			$result = $this->MySQL->query("SELECT winner FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE tournament_id = '".$this->intTableKeyValue."'");
			$totalPoolMatches = $result->num_rows;
			
			$result = $this->MySQL->query("SELECT winner FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE tournament_id = '".$this->intTableKeyValue."' AND winner != '0'");
			$totalPoolMatchesComplete = $result->num_rows;
			
			if($totalPoolMatches == $totalPoolMatchesComplete) {
				$returnVal = true;
			}
			
		}
		
		return $returnVal;
		
	}
	
	
	/*
	 * - getTeamPoolID Function -
	 * 
	 *  Returns the pool_id for a specified team
	 */
	
	
	public function getTeamPoolID($teamID) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
		
			$result = $this->MySQL->query("SELECT pool_id FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE (team1_id = '".$teamID."' OR team2_id = '".$teamID."') AND tournament_id = '".$this->intTableKeyValue."' LIMIT 1");
			$row = $result->fetch_assoc();
			
			$returnVal = $row['pool_id'];
			
		}
		
		return $returnVal;
	}
	
	
	/*
	 * - checkForPools Function -
	 * 
	 * Checks whether the tournament had pools to set up the seeding.  Used because once the pool rounds are over,
	 * the seedtype gets changed to normal seeding.
	 * 
	 * Returns true if pools were used.
	 */
	
	public function checkForPools() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {

			$result = $this->MySQL->query("SELECT tournamentpool_id FROM tournamentpools WHERE tournament_id = '".$this->intTableKeyValue."'");
			if($result->num_rows > 0) {
				$returnVal = true;
			}
			
		}
		
		return $returnVal;
		
	}
	
	public function delete() {
		
		if($this->intTableKeyValue != "") {
			
			// Array of Tournament Tables
			$arrTables = array("tournamentmatch", "tournamentplayers", "tournamentpools", "tournamentpools_teams", "tournaments", "tournamentteams");
			
			foreach($arrTables as $tableName) {
				
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix().$tableName." WHERE tournament_id = '".$this->intTableKeyValue."'");

			}
			
			
		}		
		
	}
	
	
	public function connect($connectURL, $connectPass) {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $connectURL);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "connectPass=".$connectPass);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
	
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
	
		echo $info['http_code']."<br><br>";
		
		return $result;
	
	}

	
	public function getManagers() {

		$arrReturn = array();
		if($this->intTableKeyValue != "") {
			
			$query = "SELECT ".$this->MySQL->get_tablePrefix()."tournament_managers.tournamentmanager_id, ".$this->MySQL->get_tablePrefix()."tournament_managers.member_id ".
						"FROM ".$this->MySQL->get_tablePrefix()."tournament_managers, ".
						$this->MySQL->get_tablePrefix()."members, ".$this->MySQL->get_tablePrefix()."ranks ".
						"WHERE ".$this->MySQL->get_tablePrefix()."tournament_managers.member_id = ".$this->MySQL->get_tablePrefix()."members.member_id ".
						"AND ".$this->MySQL->get_tablePrefix()."ranks.rank_id = ".$this->MySQL->get_tablePrefix()."members.rank_id ".
						"AND ".$this->MySQL->get_tablePrefix()."tournament_managers.tournament_id = '".$this->intTableKeyValue."' ".
						"ORDER BY ".$this->MySQL->get_tablePrefix()."ranks.ordernum DESC";
			
			//$result = $this->MySQL->query("SELECT ".$this->MySQL->get_tablePrefix()."tournament_managers.member_id FROM ".$this->MySQL->get_tablePrefix()."tournament_managers, ".$this->MySQL->get_tablePrefix()."members WHERE ".$this->MySQL->get_tablePrefix()."tournament_managers.member_id = ".$this->MySQL->get_tablePrefix()."members.member_id AND tournament_id = '".$this->intTableKeyValue."' ORDER BY ".$this->MySQL->get_tablePrefix()."members.username");	
			$result = $this->MySQL->query($query);
			while($row = $result->fetch_assoc()) {
				$arrReturn[$row['tournamentmanager_id']] = $row['member_id'];		
			}
			
		}
		
		return $arrReturn;
	}
	
	public function addManager($mID) {
		global $MAIN_ROOT;
		$returnVal = false;
		if($this->intTableKeyValue != "" && $this->objMember->select($mID) && $this->objManager->addNew(array("member_id", "tournament_id"), array($mID, $this->intTableKeyValue))) {
			
			$returnVal = true;
			$this->objMember->postNotification("You have been added as a manager on the tournament: <a href='".$MAIN_ROOT."tournaments/view.php?tID=".$this->intTableKeyValue."'>".filterText($this->arrObjInfo['name'])."</a>.");
			
		}
	
		return $returnVal;
	}
	
	public function deleteManager($mID) {
		global $MAIN_ROOT;
		$returnVal = false;
		if($this->intTableKeyValue != "" && $this->objManager->select($mID) && $this->objManager->get_info("tournament_id") == $this->intTableKeyValue && $this->objManager->delete()) {
			$returnVal = true;
			
			if($this->objMember->select($this->objManager->get_info("member_id"))) {
				$this->objMember->postNotification("You have been removed as a manager on the tournament: <a href='".$MAIN_ROOT."tournaments/view.php?tID=".$this->intTableKeyValue."'>".filterText($this->arrObjInfo['name'])."</a>.");
			}
		
		}
		
		return $returnVal;
	}
	
	public function isManager($mID) {
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && is_numeric($mID)) {
			
			$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournament_managers WHERE tournament_id = '".$this->intTableKeyValue."' AND member_id = '".$mID."'");
			if($result->num_rows > 0) {
				$returnVal = true;	
			}
		}
		
		return $returnVal;
	}
	
	public function memberCanJoin($memberID) {
		
		$member = new Member($this->MySQL);
		$consoleObj = new ConsoleOption($this->MySQL);
		
		$joinCID = $consoleObj->findConsoleIDByName("Join a Tournament");
		$consoleObj->select($joinCID);
		
		
		$returnVal = false;
		if($this->intTableKeyValue != "" && $member->select($memberID) && $member->hasAccess($consoleObj)) {
			
			$arrTournaments = $member->getTournamentList();
			
			$checkCount = 0;
			
			// Check Spots left
			
			$arrPlayers = $this->getPlayers();
			$maxPlayers = $this->arrObjInfo['playersperteam']*$this->arrObjInfo['maxteams'];
			if($maxPlayers == count($arrPlayers)) {
				$checkCount++;
			}
			
			// Check if already in tournament
			
			if(in_array($memberID, $arrTournaments)) {
				$checkCount++;
			}
			
			$returnVal = $checkCount == 0;
			
		}
		
		return $returnVal;
		
	}
	
}


?>