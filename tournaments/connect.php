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

// Config File
$prevFolder = "../";


include_once($prevFolder."classes/member.php");
include_once($prevFolder."classes/tournament.php");



include($prevFolder."_setup.php");


$tournamentObj = new Tournament($mysqli);
$member = new Member($mysqli);

	if($tournamentObj->select($_GET['tID'])) {
		
		$tournamentPass = $tournamentObj->get_info("outsidepassword");
		
		if($tournamentPass != "" && $tournamentPass == md5($_POST['connectPass'])) {
			header(":", true, 200);
			
			
			$tournamentInfo['info'] = $tournamentObj->get_info_filtered();
			
			$arrPlayers = $tournamentObj->getPlayers();
			foreach($arrPlayers as $key => $value) {
				if($member->select($value)) {
					$arrPlayers[$key] = $member->get_info_filtered("username");
				}
			}
			
			$arrTeams = $tournamentObj->getTeams();
			foreach($arrTeams as $key => $value) {
				$tournamentObj->objTeam->select($value);
				
				//$arrTeams[$key]['seed'] = $tournamentObj->objTeam->get_info_filtered("seed");
				$arrTeams[$key] = $tournamentObj->objTeam->get_info_filtered("name");
				echo $tournamentObj->objTeam->get_info_filtered("name")."<br>";
			}
			
			
			
			$tournamentInfo['players'] = $arrPlayers;
			$tournamentInfo['teams'] = $arrTeams;
			
			
			echo json_encode($tournamentInfo);
			
		}
		else {
			header(":", true, 204);
		}
	}
	else {
		header(":", true, 404);
	}



?>