<?php

	if(!defined("MAIN_ROOT")) { exit(); }
	
	if($_POST['submit']) {
		
		$pmSessionID = $_POST['pmsessionid'];
		
		// Check To
		
		$arrReceivers = array();
		
		// Check Members
		
		if(count($_SESSION['btComposeList'][$pmSessionID]['member']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['member'] as $memberID) {
				if($memberID != "" && $member->select($memberID)) {
					
					$arrReceivers[] = $memberID;
					
				}
				
			}
		}
		
		
		// Check Rank Category
		
		if(count($_SESSION['btComposeList'][$pmSessionID]['rankcategory']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['rankcategory'] as $rankCatID) {
			
				if($rankCatID != "" && $rankCatObj->select($rankCatID)) {
				
					$arrRanks = $rankCatObj->getRanks();
					$rankSQL = "('".implode("','", $arrRanks)."')";
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE rank_id IN ".$rankSQL." AND member_id NOT IN ".$filterMembers." AND disabled = '0'");
					while($row = $result->fetch_assoc()) {
					
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("rankcategory", $rankCatID);
						
					}
				
				}
				
			}
		}
		
		
		// Check Ranks
		$member->select($memberInfo['member_id']);
		if(count($_SESSION['btComposeList'][$pmSessionID]['rank']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['rank'] as $rankID) {
		
				if($rankID != "" && $member->objRank->select($rankID)) {
		
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE rank_id = '".$rankID."' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {
		
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("rank", $rankID);
		
					}
		
				}
		
		
			}
		
		}
		
		
		// Check Squads
		$member->select($memberInfo['member_id']);
		$arrSquads = $member->getSquadList();
		if(count($_SESSION['btComposeList'][$pmSessionID]['squad']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['squad'] as $squadID) {
				
				if($squadID != "" && in_array($squadID, $arrSquads) && $squadObj->select($squadID)) {
					
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."squads_members WHERE squad_id = '".$squadID."' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {

						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("squad", $squadID);
						
					}
					
				}
				
			
			}
			
		}
		
		// Check Tournaments
		$arrTournaments = $member->getTournamentList(true);
		if(count($_SESSION['btComposeList'][$pmSessionID]['tournament']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['tournament'] as $tournamentID) {

				if($tournamentID != "" && in_array($tournamentID, $arrTournaments) && $tournamentObj->select($tournamentID)) {
					
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."tournamentplayers WHERE tournament_id = '".$tournamentID."' AND member_id != '' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {
						
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("tournament", $tournamentID);	
						
					}
					
				}
				
			}
		}
		
		
		
		if(count($arrReceivers) == 0) {
			$formObj->errors[] = "<b>&middot;</b> You need to select at least one member to send your message to.";
		}
		elseif(count($arrReceivers) == 1) {
			$arrReceivers = $arrReceivers[0];
		}
		
		
		
		// Check Subject
		if(trim($_POST['subject']) == "") {
			$_POST['subject'] = "untitled";
		}
		
		
		if($countErrors == 0) {
			
			$member->select($memberInfo['member_id']);
			$sendAsEmail = 0;
			$emailPMCID = $consoleObj->findConsoleIDByName("Email Private Messages");
			$consoleObj->select($emailPMCID);
			if(isset($_POST['emailpm']) && $_POST['emailpm'] == 1 && $member->hasAccess($consoleObj)) {
				$sendAsEmail = 1;
			}
			$consoleObj->select($cID);
			
			if(!$member->sendPM($arrReceivers, $_POST['subject'], $_POST['message'], $_POST['replypmid'], $arrGroup, $sendAsEmail)) {
				$formObj->errors[] = "Unable to save information to database! Please contact the website administrator.";
			}
			
		}
		
		if($countErrors > 0) {
			$_POST = filterArray($_POST);
			$_POST['submit'] = false;
		}
		
		
		unset($_SESSION['btComposeList'][$pmSessionID]);
		
	}