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
include_once("tournament.php");


class TournamentPool extends Basic {
	
	
	public $objTournament;
	public $objTournamentPoolMatch;

	
	public function __construct($sqlConnection) {
	
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."tournamentpools";
		$this->strTableKey = "tournamentpool_id";
		
		
		$this->objTournamentPoolMatch = new Basic($sqlConnection, "tournamentpools_teams", "poolteam_id");
		
		
	}
	

	
	public function getTeamsInPool() {
	
		$returnArr = array();
		if($this->intTableKeyValue != "" && is_numeric($this->intTableKeyValue)) {
			
			$result = $this->MySQL->query("SELECT team1_id,team2_id FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE pool_id = '".$this->intTableKeyValue."'");
			while($row = $result->fetch_assoc()) {
			
				if(!in_array($row['team1_id'], $returnArr)) {
					$returnArr[] = $row['team1_id'];
				}
			
				if(!in_array($row['team2_id'], $returnArr)) {
					$returnArr[] = $row['team2_id'];
				}
			
			}
	
	
		}
		
		return $returnArr;
	
	}
	
	
	
	public function getTeamRecord($teamID) {	
		
		$returnVal = "0 - 0";
		
		if($this->intTableKeyValue != "" && is_numeric($teamID)) {
			$resultWins = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE pool_id = '".$this->intTableKeyValue."' AND ((team1_id = '".$teamID."' AND winner = '1') || (team2_id = '".$teamID."' AND winner = '2'))");
			$countWins = $resultWins->num_rows;
			
			$resultLosses =  $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."tournamentpools_teams WHERE pool_id = '".$this->intTableKeyValue."' AND ((team1_id = '".$teamID."' AND winner = '2') || (team2_id = '".$teamID."' AND winner = '1'))");
			$countLosses = $resultLosses->num_rows;
			
			
			$returnVal = $countWins." - ".$countLosses;
			
		}
		
		return $returnVal;
		
	}
	
	
}

?>