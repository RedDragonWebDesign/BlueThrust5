<?php

	function joinTournamentChecks() {
		global $formObj, $mysqli, $memberInfo, $arrTournaments;

		$tournamentObj = new Tournament($mysqli);

		if (!$tournamentObj->select($_POST['tournament'])) {
			$formObj->errors[] = "You selected an invalid tournament.";
		}
		else {

			$tournamentInfo = $tournamentObj->get_info_filtered();

			// Check Password

			if ($tournamentInfo['password'] != "" && $tournamentInfo['password'] != md5($_POST['tournamentpassword'])) {
				$formObj->errors[] = "You entered an incorrect password for the tournament.";
			}

			// Check Spots Left

			$arrPlayers = $tournamentObj->getPlayers();
			$maxPlayers = $tournamentInfo['playersperteam']*$tournamentInfo['maxteams'];
			if ($maxPlayers == count($arrPlayers)) {
				$formObj->errors[] = "This tournament is currently full.";
			}

			// Check if already in tournament

			if (in_array($memberInfo['member_id'], $arrTournaments)) {
				$formObj->errors[] = "You are already in this tournament.";
			}

		}

	}


	function updatePlayerTeam($tournamentObj) {
		global $tournamentObj;

		$tournamentInfo = $tournamentObj->get_info();

		if ($tournamentInfo['playersperteam'] == 1) {

			$arrUnfilledTeams = $tournamentObj->getUnfilledTeams();
			if (count($arrUnfilledTeams) > 0) {

				$newTeam = $arrUnfilledTeams[0];

				$tournamentObj->objPlayer->update(array("team_id"), array($newTeam));
			}
		}

	}